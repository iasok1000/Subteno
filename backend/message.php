<?php
ini_set('default_charset', 'utf-8');
mb_internal_encoding("UTF-8");
// ini_set('error_log', $_SERVER["DOCUMENT_ROOT"] . '/inc/subteno/backend/error_and_debug.log');
ini_set('error_log', __DIR__ . '/error_and_debug.log');

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

// Subteno::log_with_sid('ajax:' . json_encode($a_in));

// Subteno::cors();

if (isset($a_in['token']) && $a_in['token'] !== '') {
    $token = $a_in['token'];
    $token = strtolower($token);

    //1) We check if we have no admin And if the message equals BOT_ADMIN_SECRET We record ADMIN_CHAT_TOKEN and chat_id in `admin_chat`
    // Проверяем нет ли у нас админа И если сообщение равно BOT_ADMIN_SECRET Мы записываем ADMIN_CHAT_TOKEN и chat_id в `admin_chat`

    //2) if the bot message is anything else, we record it with time and chat_id into `admin_chat_message`, NOTE that the session_id when this script runs may be a malicious user trying to steal another one's chat so we don't store session_ids and we retrieve the messages based on the assumption that the admin can only chat with one person at a time and that has a current_chat_start_date_utc
    // если сообщение бота является чем-то другим, мы записываем его со временем и идентификатором чата в `admin_chat_message`, ЗАМЕЧАНИЕ, что идентификатор сеанса при запуске этого скрипта может быть злоумышленником, пытающимся украсть чужой чат, поэтому мы не сохраняем идентификаторы сеансов и извлекаем сообщения, основанные на предположении, что администратор может общаться только с одним человеком за раз, и у которого есть current_chat_start_date_utc

    // получаем входящие обновления для бота начиная с $offset
    $messages = array();
    $offset = Subteno::get_setting("last_telegram_bot_offset");
    $updates = Subteno::apiRequest('getUpdates', array('offset' => $offset));
    // Subteno::log_with_sid('$updates:' . json_encode($updates));
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
                // "привязываем" чат менеджера с ботом к сайту
                Subteno::add_admin_chat(ADMIN_CHAT_TOKEN, $chat_id);
                // посылаем менеджеру приветственное сообщение
                Subteno::apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => Subteno::TO_ADMIN_WELCOME));
            } else if ($message_text == Subteno::FROM_ADMIN_CONNECTION_FINISH) {
                // "отвязываем" чат менеджера с ботом от сайту
                Subteno::del_admin_chat(ADMIN_CHAT_TOKEN, $chat_id);
                // посылаем менеджеру сообщение
                Subteno::apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => Subteno::TO_ADMIN_BYE));
            } else if ((strpos($message_text, "/") === 0)) {
                // служебные команды бота игнорируем
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
    // сохраняем новые сообщения в базу. Вообще там могут быть сообщения от других пользователе телеграма которые тоже запустили этого бота, маловероятно но кто то может просто спамить этого бота у себя в чате и тогда у нас база заспамится, так что можно фильтровать только те сообщения у которых chat_id "нашего" менеджера (тут он админом зовется) и равен $admin_chat['chat_id']
    // if (count($messages) > 0) {
    //     if (Subteno::OLD_DB == true) {
    //         $insert_messages_cmd = Subteno::build_insert_command($messages);
    //         $livechat_db->query($insert_messages_cmd);
    //     } else {
    foreach ($messages as $k => $v) {
        $DB_SUBTENO->prepare("INSERT INTO " . DB_PREFIX . "admin_chat_message SET message_text=?,message_date_utc=?,chat_id=?", [$v['message_text'], $v['message_date_utc'], $v['chat_id']], false);
    }
    //     }
    // }

    $admin_chat = Subteno::get_admin_chat_by_token($token);
    if ($admin_chat !== false) {
        $admin_chat['dirty'] = false;
        $current_session_id = null;
        // проверяем что менеджер и клиент "на связи", а если нет то посылаем менеджеру в чат с ботом что чат закончен с этим клиентом и устанавливаем $admin_chat['current_session_id'] = null чтоб дальше не выполнялся запрос аякса func_X
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
                // посылаем менеджеру что еще один клиент хочет поддержки
                Subteno::apiRequest("sendMessage", [
                    'chat_id' => $admin_chat['chat_id'],
                    'parse_mode' => 'HTML',
                    'text' => Subteno::TO_ADMIN_NEW_CLIENT
                ]);
            }
        }
        // выполняем аякс запрос
        // $a_out['messages'] = 'выполняем аякс запрос ' . $wrong_session_id . ' ' . $current_session_id;
        if (!$wrong_session_id) {
            if ($current_session_id != null) {
                if ($a_in['func'] == 'func_2') {
                    // считываем непрочитанные сообщения чата chat_id пришедшие после current_chat_start_date_utc и помечаем их как прочитанные is_read=1
                    [$result_out, $messages_out] = Subteno::get_messages($admin_chat);
                    $a_out['messages'] = $messages_out;
                    $a_out['result'] = $result_out;
                    // Subteno::error('резутьтат:"' . $result_out . '" сообщение:"' . json_encode($messages_out) . '"');
                }
            } else {
                // можно послать служебное сообщение что сеанс с менеджером закончился
                // $a_out['messages'] = Subteno::ERROR_7_MESS;
                // или ничего не посылать чтоб не спамить клиента бесполезными сообщениями
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
        Subteno::error('Неверный токен ' . $token . ' Надо привязать менеджера попросив его послать сообщение BOT_ADMIN_SECRET в его чат с ботом.');
        $a_out['messages'] = Subteno::ERROR_3_MESS;
        $a_out['result'] = Subteno::ERROR_3;
    }
}

header('Content-Type: application/json');
echo json_encode($a_out, JSON_UNESCAPED_UNICODE);
