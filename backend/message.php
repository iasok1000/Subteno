<?php
ini_set('default_charset', 'utf-8');
mb_internal_encoding("UTF-8");
ini_set('error_log', PATH_BACKEND . '/error_and_debug.log');

if ($_SERVER["CONTENT_TYPE"] == 'application/x-www-form-urlencoded' || $_SERVER["CONTENT_TYPE"] == 'multipart/form-data') {
    $a_in = json_decode($_POST['json'], true);
} else if ($_SERVER["CONTENT_TYPE"] == 'application/json') {
    $a_in = json_decode(file_get_contents('php://input'), true);
} else if (strpos($_SERVER["CONTENT_TYPE"], 'multipart/form-data') !== false) {
    $a_in = $_POST;
} else {
    $a_out['CONTENT_TYPE'] = $_SERVER["CONTENT_TYPE"];
    $a_out['error'] = "unknown type CONTENT_TYPE";
}
$a_out = array();
$a_out['messages'] = Subteno::ERROR_UNKNOWN_MESS;
$a_out['result'] = Subteno::ERROR_UNKNOWN;

if (isset($a_in['token']) && $a_in['token'] !== '') {
    $token = $a_in['token'];
    $token = strtolower($token);

    // We record a message with time and chat_id in `admin_chat_message`. The session_id when running this script could be an attacker trying to steal someone else's chat, so we do not store session_ids and retrieve messages based on the assumption that the administrator (manager) can only communicate with one person at a time, and who has a current_chat_start_date_utc

    // we receive incoming updates for the bot starting from $offse
    $messages = array();
    $offset = Subteno::get_setting("last_telegram_bot_offset");
    $updates = Subteno::apiRequest('getUpdates', array('offset' => $offset));
    if ($updates !== false) {
        foreach ($updates as $update) {
            if (!isset($update['message'])) {
                Subteno::error('Wrong message ' . json_encode($update));
                $offset = $update['update_id'] + 1;
                continue;
            }
            $message = $update['message'];
            $chat_id = $message['chat']['id'];
            $message_text = $message['text'];
            if ($message_text == BOT_ADMIN_SECRET) {
                // “link” the manager’s chat with the bot to the site
                Subteno::add_admin_chat(ADMIN_CHAT_TOKEN, $chat_id);
                // send a welcome message to the manager
                Subteno::apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => Subteno::TO_ADMIN_WELCOME));
            } else if ($message_text == Subteno::FROM_ADMIN_CONNECTION_FINISH) {
                // “untie” the manager’s chat with the bot from the site
                Subteno::del_admin_chat(ADMIN_CHAT_TOKEN, $chat_id);
                // send a message to the manager
                Subteno::apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => Subteno::TO_ADMIN_BYE));
            } else if ((strpos($message_text, "/") === 0)) {
                // we ignore the bot's service commands
            } else {
                array_push($messages,  array(
                    'message_text' => $message_text,
                    'message_date_utc' => Subteno::getDateFromEpcoch($message['date']),
                    'chat_id' => $chat_id
                ));
            }
            $offset = $update['update_id'] + 1;
        }
        Subteno::update_setting("last_telegram_bot_offset", $offset);
    }
    // We save new messages to the database. In general, there may be messages from other Telegram users who also launched this bot, it’s unlikely, but someone could simply spam this bot in their chat and then our database will become spammed, so you can filter only those messages with the chat_id of “our” manager ( here he is called the admin) and is equal to $admin_chat['chat_id']
    foreach ($messages as $k => $v) {
        $DB_SUBTENO->prepare("INSERT INTO " . DB_PREFIX . "admin_chat_message SET message_text=?,message_date_utc=?,chat_id=?", [$v['message_text'], $v['message_date_utc'], $v['chat_id']], false);
    }
    $admin_chat = Subteno::get_admin_chat_by_token($token);
    if ($admin_chat !== false) {
        $admin_chat['dirty'] = false;
        $current_session_id = null;
        // we check that the manager and the client are “in touch”, and if not, then we send the manager in a chat with the bot that the chat is finished with this client and set $admin_chat['current_session_id'] = null so that the Ajax request func_X is not executed further
        if (array_key_exists('last_message_date_utc', $admin_chat)) {
            $now_utc = new DateTime('now', new DateTimeZone("UTC"));
            $last_message_date_utc = $admin_chat['last_message_date_utc'];
            $last_heart_beat_utc = $admin_chat['last_heart_beat_utc'];
            if (!empty($last_message_date_utc)) {
                if ($now_utc > date_create($last_message_date_utc . ' UTC')->add(new DateInterval('PT' . Subteno::LAST_MESSAGE_TIMEOUT_MINUTES . 'M'))) {
                    Subteno::end_chat($admin_chat, 'Время истекло ' . Subteno::LAST_MESSAGE_TIMEOUT_MINUTES . ' мин.');
                    $a_out['messages'] = Subteno::ERROR_1_MESS;
                    $a_out['result'] = Subteno::ERROR_1;
                }
            }
            if (!empty($last_heart_beat_utc)) {
                Subteno::log_with_sid('$last_heart_beat_utc:' . json_encode($last_heart_beat_utc));
                if ($now_utc > date_create($last_heart_beat_utc . ' UTC')->add(new DateInterval('PT' . Subteno::LAST_HEART_BEAT_TIMEOUT_SECONDS . 'S'))) {
                    Subteno::end_chat($admin_chat, 'No Heartbeat ' . Subteno::LAST_HEART_BEAT_TIMEOUT_SECONDS . ' sec');
                }
            }
            $current_session_id = $admin_chat['current_session_id'];
        }
        $wrong_session_id = false;
        if ($current_session_id != null) {
            if ($current_session_id != session_id()) {
                $wrong_session_id = true;
                Subteno::error(Subteno::ERROR_2_MESS . ' $admin_chat=' . json_encode($admin_chat));
                $a_out['messages'] = Subteno::ERROR_2_MESS;
                $a_out['result'] = Subteno::ERROR_2;
                // we send to the manager that another client wants support
                Subteno::apiRequest("sendMessage", [
                    'chat_id' => $admin_chat['chat_id'],
                    'parse_mode' => 'HTML',
                    'text' => Subteno::TO_ADMIN_NEW_CLIENT
                ]);
            }
        }
        // execute ajax request
        if (!$wrong_session_id) {
            if ($current_session_id != null) {
                if ($a_in['func'] == 'func_2') {
                    // read unread chat messages chat_id that came after current_chat_start_date_utc and mark them as read is_read=1
                    [$result_out, $messages_out] = Subteno::get_messages($admin_chat);
                    $a_out['messages'] = $messages_out;
                    $a_out['result'] = $result_out;
                }
            } else {
                // you can send a service message that the session with the manager has ended
                // $a_out['messages'] = Subteno::ERROR_7_MESS;
                // or send nothing so as not to spam the client with useless messages
                $a_out['messages'] = '';
                $a_out['result'] = Subteno::ERROR_7;
            }
            if ($a_in['func'] == 'func_1') {
                [$result_out, $messages_out]  = Subteno::send_new_message($admin_chat, $a_in);
                $a_out['messages'] = $messages_out;
                $a_out['result'] = $result_out;
            }
        } else {
            Subteno::error('Wrong session_id ' . $current_session_id);
            $a_out['messages'] = self::ERROR_5_MESS;
            $a_out['result'] = Subteno::ERROR_5;
        }
        if ($admin_chat['dirty']) {
            Subteno::log_with_sid('INFO: $admin_chat is dirty, going to update.');
            unset($admin_chat['dirty']);
            Subteno::update_admin_chat($admin_chat);
        }
    } else {
        Subteno::error(Subteno::ERROR_3_MESS_LOG . $token);
        $a_out['messages'] = Subteno::ERROR_3_MESS;
        $a_out['result'] = Subteno::ERROR_3;
    }
}

header('Content-Type: application/json');
echo json_encode($a_out, JSON_UNESCAPED_UNICODE);
