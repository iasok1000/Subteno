<?php
class DIV_DB
{
    var $db = null;

    function exec($sql)
    {
        try {
            $count = $this->db->exec($sql);
        } catch (PDOException $e) {
            error_log($e->getMessage() . '" sql="' . $sql . '" ' . __FILE__ . ' ' . __LINE__);
            exit();
        }
        return $count;
    }
    function prepare($sql, $arr = array(), $all = true)
    {
        try {
            $s = $this->db->prepare($sql);
            $s->execute($arr);
            if ($all == true) {
                $result = $s->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $result = $s->rowCount();
            }
        } catch (PDOException $e) {
            error_log($e->getMessage() . '" sql="' . $sql . '" ' . __FILE__ . ' ' . __LINE__);
            exit();
        }
        return $result;
    }
    function connect($host, $user, $pass, $name, $port)
    {
        try {
            $this->db = new PDO("mysql:host=" . $host . ";port=" . $port . ";dbname=" . $name, $user, $pass);
            // $this->db->exec("SET sql_mode = ''");
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            error_log($e->getMessage() . ' ' . __FILE__ . ' ' . __LINE__);
            exit();
        }
    }
    function close()
    {
        $this->db = null;
    }
    function lock_tables($a_tables)
    {
        $sql_add = implode(" WRITE, ", $a_tables) . " WRITE ";
        $this->db->exec("LOCK TABLES " . $sql_add);
    }
    function unlock_tables()
    {
        $this->db->exec("UNLOCK TABLES");
    }
    function is_exist_table($table_schema, $table_name)
    {
        $r = $this->prepare('SELECT * FROM information_schema.tables WHERE table_schema=? AND table_name=?', [$table_schema, $table_name]);
        if (count($r) == 0) {
            return false;
        }
        return true;
    }
}
class Subteno
{
    // welcome message to managers after he sent BOT_ADMIN_SECRET, it comes with 1 message from the client
    const TO_ADMIN_WELCOME = 'Your chat with this bot is linked to the chat with clients. You will receive a message about the start of a chat with the client and his first message.';
    // message to the manager after he left the chat with clients by sending FROM_ADMIN_CONNECTION_FINISH
    const TO_ADMIN_BYE = 'Your chat with this bot is disconnected from chats with clients.';
    const TO_ADMIN_NEW_CLIENT = '<b>System message</b> Hurry up, new client (you can "' . self::FROM_ADMIN_CHAT_FINISH . '")';
    // “end of connection” message, the manager wants to disconnect from chats with clients
    const FROM_ADMIN_CONNECTION_FINISH = 'ec';
    // after the admin sends such a message, he “disconnects” from the chat with the client and his communications will remain in the chat with the bot
    const FROM_ADMIN_CHAT_FINISH = 'gb';
    // short messages from the manager that are replaced
    const ABBREV = [
        'ga' => 'Good afternoon!',
        'gb' => 'Goodbye, if you have any more questions, write.'
    ];

    const ERROR_UNKNOWN = 'unknown_error';
    const ERROR_UNKNOWN_MESS = 'Unknown error.';
    const ERROR_1 = 'manager_timeout';
    const ERROR_1_MESS = 'The manager could not answer for ' . self::LAST_MESSAGE_TIMEOUT_MINUTES . ' min. Therefore the chat is closed, please repeat your question later.';
    const ERROR_2 = 'admin_is_busy';
    const ERROR_2_MESS = 'All managers are busy.';
    const ERROR_3 = 'wrong_token';
    const ERROR_3_MESS = 'All managers are busy, try again later.'; // in fact there is simply no manager
    const ERROR_3_MESS_LOG = 'Invalid token. You need to bind the manager by asking him to send the message BOT_ADMIN_SECRET to his chat with the bot.';
    const ERROR_4 = 'admin_chat_current_session_id_is_null';
    const ERROR_4_MESS = 'Session is null';
    const ERROR_5 = 'wrong_session_id';
    const ERROR_5_MESS = 'Invalid session id.';
    const ERROR_6 = 'error api';
    const ERROR_6_MESS = 'Error while sending message.';
    const ERROR_7 = 'current_session_id_is_null';
    const ERROR_7_MESS = 'Automatic session closure. If you have any more questions, write.';

    const LAST_MESSAGE_TIMEOUT_MINUTES = 3;
    const LAST_HEART_BEAT_TIMEOUT_SECONDS = 30;
    const DEBUG_LOG_ENABLE = false;

    static function getDateFromEpcoch($epoch)
    {
        $dt = new DateTime("@$epoch"); // convert UNIX timestamp to PHP DateTime
        return $dt->format('Y-m-d H:i:s');
    }
    static function get_messages(&$admin_chat)
    {
        $result = '';
        $messages = json_encode([]);

        if ($admin_chat['current_session_id'] == null) {
            return [self::ERROR_4, self::ERROR_4_MESS];
        }

        $admin_chat['last_heart_beat_utc'] = self::now_utc_formatted();
        $admin_chat['dirty'] = true;

        global $DB_SUBTENO;
        $r = $DB_SUBTENO->prepare("SELECT id,message_date_utc, message_text FROM " . DB_PREFIX . "admin_chat_message WHERE chat_id=? AND message_date_utc>? AND is_read=?", [$admin_chat['chat_id'], $admin_chat['current_chat_start_date_utc'], 0]);
        if (count($r) > 0) {
            $messages = $r;
            foreach ($messages as &$message) {
                // abbreviation processing
                if ($message['message_text'] == Subteno::FROM_ADMIN_CHAT_FINISH) {
                    self::end_chat($admin_chat, 'Admin order');
                }
                // replacing abbreviations
                if (array_key_exists($message['message_text'], Subteno::ABBREV)) {
                    $message['message_text'] = Subteno::ABBREV[$message['message_text']];
                }
                $message['message_date_utc'] = strtotime($message['message_date_utc']) . '000';
                $DB_SUBTENO->prepare("UPDATE " . DB_PREFIX . "admin_chat_message SET is_read=? WHERE id=?", [1, $message['id']], false);
            }
            $result = 'ok';
            $messages = json_encode($messages);
        } else {
            // if there are no messages that's ok
            $result = 'ok';
            $messages = json_encode([]);
        }
        return [$result, $messages];
    }
    static function send_new_message(&$admin_chat, $a_in)
    {
        $is_first_message = $admin_chat['current_session_id'] == null;
        if ($is_first_message) {
            $admin_chat['current_session_id'] = '' . session_id();
            $admin_chat['current_chat_start_date_utc'] = self::now_utc_formatted();
            $result = self::apiRequest("sendMessage", [
                'chat_id' => $admin_chat['chat_id'],
                'parse_mode' => 'HTML',
                'text' => '<b>' . $admin_chat['current_session_id'] . '</b>' . ' a chat with the client has begun, you must respond within ' . self::LAST_MESSAGE_TIMEOUT_MINUTES . 'min.'
            ]);
            if ($result === false) {
                return [self::ERROR_6, self::ERROR_6_MESS];
            }
        }
        $admin_chat['last_message_date_utc'] = self::now_utc_formatted();
        $admin_chat['dirty'] = true;
        $result = self::apiRequest("sendMessage", [
            'chat_id' => $admin_chat['chat_id'],
            'parse_mode' => 'HTML',
            'text' => "<b>Message from the client:</b> " . $a_in['message_text']
        ]);
        if ($result === false) {
            return [self::ERROR_6, self::ERROR_6_MESS];
        }
        return ["ok", ''];
    }
    static function now_utc_formatted()
    {
        return (new DateTime('now', new DateTimeZone("UTC")))->format('Y-m-d H:i:s');
    }
    static function end_chat(&$admin_chat, $cause = '')
    {
        //IN END CHAT 
        //NOTE that we don't have to check for the session here
        //Because this method occurs after satisfying the conditions for ending. It may happen in another client.
        $ending_session_id = $admin_chat['current_session_id'];
        self::log_with_sid('INFO: Ending chat with ' . $ending_session_id);
        $admin_chat['current_session_id'] = null;
        $admin_chat['current_chat_start_date_utc'] = null;
        $admin_chat['last_message_date_utc'] = null;
        $admin_chat['last_heart_beat_utc'] = null;
        $admin_chat['dirty'] = true;
        self::apiRequest("sendMessage", [
            'chat_id' => $admin_chat['chat_id'],
            'parse_mode' => 'HTML',
            'text' => "<b>" . $ending_session_id . "</b>" . " chat ended, reason: " . $cause
        ]);
    }
    static function add_admin_chat($token, $chat_id)
    {
        global $DB_SUBTENO;
        $DB_SUBTENO->lock_tables([DB_PREFIX . "admin_chat"]);
        $r = $DB_SUBTENO->prepare("SELECT count(*) as cnt FROM " . DB_PREFIX . "admin_chat WHERE chat_id=? AND token=?", [$chat_id, $token]);
        if ($r[0]['cnt'] == 0) {
            $DB_SUBTENO->prepare("INSERT INTO " . DB_PREFIX . "admin_chat (chat_id, token) VALUES (?,?)", [$chat_id, $token], false);
        }
        $DB_SUBTENO->unlock_tables();
    }
    static function del_admin_chat($token, $chat_id)
    {
        global $DB_SUBTENO;
        $DB_SUBTENO->prepare("DELETE FROM " . DB_PREFIX . "admin_chat WHERE chat_id=? AND token=?", [$chat_id, $token], false);
    }
    static function get_admin_chat_by_token($token)
    {
        Subteno::log_with_sid('$token:' . json_encode($token));
        global $DB_SUBTENO;
        $result = $DB_SUBTENO->prepare("SELECT * FROM " . DB_PREFIX . "admin_chat WHERE token=?", [$token]);
        if (count($result) > 0) {
            return $result[0];
        } else {
            return false;
        }
        Subteno::log_with_sid('$admin_chat:' . json_encode($admin_chat));
    }
    static function update_admin_chat($admin_chat)
    {
        $chat_id = false;
        if (array_key_exists('chat_id', $admin_chat)) {
            $chat_id = $admin_chat['chat_id'];
            unset($admin_chat['chat_id']);
            unset($admin_chat['token']);
        }
        $update_cmd = "UPDATE " . DB_PREFIX . "admin_chat SET";
        $set_array = array();
        $par_array = array();
        foreach ($admin_chat as $k => $v) {
            array_push($set_array, " $k = ?");
            array_push($par_array, $v);
        }
        $update_cmd .= implode(',', $set_array);
        if ($chat_id) {
            $update_cmd .= " WHERE chat_id=?";
            array_push($par_array, $chat_id);
        } else {
            return false;
        }
        self::log_with_sid('INFO:' . $update_cmd);
        global $DB_SUBTENO;
        $result = $DB_SUBTENO->prepare($update_cmd, $par_array, false);
        return $result;
    }
    static function exec_curl_request($handle)
    {
        $response = curl_exec($handle);
        self::log_with_sid('$response:' . $response);
        if ($response === false) {
            $errno = curl_errno($handle);
            $error = curl_error($handle);
            self::log_with_sid("Curl returned error $errno: $error\n");
            curl_close($handle);
            return false;
        }
        $http_code = intval(curl_getinfo($handle, CURLINFO_HTTP_CODE));
        curl_close($handle);

        if ($http_code >= 500) {
            return false;
        } else if ($http_code != 200) {
            $response = json_decode($response, true);
            self::log_with_sid("Request has failed with error {$response['error_code']}: {$response['description']}\n");
            if ($http_code == 401) {
                return false;
            }
            return false;
        } else {
            $response = json_decode($response, true);
            if (isset($response['description'])) {
                self::log_with_sid("Request was successful: {$response['description']}\n");
            }
            $response = $response['result'];
        }
        return $response;
    }
    static function apiRequest($method, $parameters)
    {
        if (!is_string($method)) {
            self::log_with_sid("Method name must be a string\n");
            return false;
        }
        if (!$parameters) {
            $parameters = array();
        } else if (!is_array($parameters)) {
            self::log_with_sid("Parameters must be an array\n");
            return false;
        }
        foreach ($parameters as $key => &$val) {
            // encoding to JSON array parameters, for example reply_markup
            if (!is_numeric($val) && !is_string($val)) {
                $val = json_encode($val);
            }
        }
        $url = API_URL . $method . '?' . http_build_query($parameters);
        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($handle, CURLOPT_TIMEOUT, 60);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, 0);
        self::log_with_sid('$url:' . $url);
        return self::exec_curl_request($handle);
    }
    static function log_with_sid($message)
    {
        if (self::DEBUG_LOG_ENABLE == true) {
            error_log('[sid:' . session_id() . '] ' . $message);
        }
    }
    static function error($message)
    {
        error_log($message);
    }
    static function get_setting($key)
    {
        global $DB_SUBTENO;
        $result = $DB_SUBTENO->prepare("SELECT setting_value FROM " . DB_PREFIX . "setting WHERE setting_key=?", [$key]);
        if (count($result) > 0) {
            return $result[0]['setting_value'];
        } else {
            return false;
        }
    }
    static function get_settings()
    {
        global $DB_SUBTENO;
        $result = $DB_SUBTENO->prepare("SELECT setting_key,setting_value FROM " . DB_PREFIX . "setting");
        if (count($result) == 0) {
            return false;
        }
        $output = array();
        foreach ($result as $row) {
            $output[$row['setting_key']] = $row['setting_value'];
        }
        return $output;
    }
    static function update_setting($key, $value)
    {
        global $DB_SUBTENO;
        $result = $DB_SUBTENO->prepare("UPDATE " . DB_PREFIX . "setting SET setting_value=? WHERE setting_key=?", [$value, $key], false);
        return $result;
    }
    static function has_string_keys(array $array)
    {
        return count(array_filter(array_keys($array), 'is_string')) > 0;
    }
    static function install()
    {
        global $DB_SUBTENO;
        if (!$DB_SUBTENO->is_exist_table(DB_NAME, DB_PREFIX . 'admin_chat')) {
            $DB_SUBTENO->exec('CREATE TABLE ' . DB_NAME . '.' . DB_PREFIX . 'admin_chat (  `id` int(11) NOT NULL AUTO_INCREMENT,  `chat_id` bigint(20) DEFAULT NULL,  `token` varchar(255) DEFAULT NULL,  `current_session_id` varchar(255) DEFAULT NULL,  `current_chat_start_date_utc` datetime DEFAULT NULL,  `last_heart_beat_utc` datetime DEFAULT NULL,  `last_message_date_utc` datetime DEFAULT NULL,  `dirty` tinyint(1) DEFAULT NULL,  PRIMARY KEY (`id`) USING BTREE) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;');
        }
        if (!$DB_SUBTENO->is_exist_table(DB_NAME, DB_PREFIX . 'admin_chat_message')) {
            $DB_SUBTENO->exec('CREATE TABLE ' . DB_NAME . '.' . DB_PREFIX . 'admin_chat_message (  `id` int(11) NOT NULL AUTO_INCREMENT,  `chat_id` bigint(20) DEFAULT NULL,  `message_date_utc` datetime DEFAULT NULL,  `message_text` text DEFAULT NULL,  `is_read` tinyint(1) DEFAULT 0,  PRIMARY KEY (`id`) USING BTREE) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;');
        }
        if (!$DB_SUBTENO->is_exist_table(DB_NAME, DB_PREFIX . 'setting')) {
            $DB_SUBTENO->exec('CREATE TABLE ' . DB_NAME . '.' . DB_PREFIX . 'setting (  `setting_key` varchar(150) NOT NULL,  `setting_value` varchar(255) DEFAULT NULL,  PRIMARY KEY (`setting_key`)) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;');
            $DB_SUBTENO->exec("INSERT INTO " . DB_NAME . '.' . DB_PREFIX . "setting (setting_key, setting_value) VALUES ('last_telegram_bot_offset','0')");
        }
    }
}

$DB_SUBTENO = new DIV_DB();
$DB_SUBTENO->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

// It only needs to run once, then you can comment it out
Subteno::install();
