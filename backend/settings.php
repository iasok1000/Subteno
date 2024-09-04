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
            // die("error!");
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
            // die("error!");
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
            // die("error!");
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
        // error_log(json_encode($r));
        if (count($r) == 0) {
            return false;
        }
        return true;
    }
}
class Subteno
{
    // приветственное сообщение менеджеры после того как он послал BOT_ADMIN_SECRET, оно приходит с 1 сообщением от клиента
    const TO_ADMIN_WELCOME = 'Ваш чат с этим ботом приаязан к чату с клиентоами. Вам придет сообщение о начале чата с клиентом и его первое сообщение.';
    // сообщение менеджеру после того как он отвязался от чата с клиентами послав FROM_ADMIN_CONNECTION_FINISH
    const TO_ADMIN_BYE = 'Ваш чат с этим ботом отвязан от чатов с клиентоами.';
    const TO_ADMIN_NEW_CLIENT = '<b>Системное сообщение</b> Быстрее заканивайте (можно ' . self::FROM_ADMIN_CHAT_FINISH . '), новый клиент.';
    // сообщение "конец связи", менеджер хочет отвязаться от чатов с клиентами
    const FROM_ADMIN_CONNECTION_FINISH = 'кс';
    // после посылки админом такого сообщения он "отключается" от чата с клиентом и его вообщения будут оставаться в чате с ботом
    const FROM_ADMIN_CHAT_FINISH = 'дс';
    // короткие сообщения менеджера которые транслируются 
    const ABBREV = [
        'дд' => 'Добрый день!',
        'дс' => 'До свидания, если будут еще вопросы - пишите.'
    ];

    const ERROR_UNKNOWN = 'unknown_error';
    const ERROR_UNKNOWN_MESS = 'Неизветная ошибка.';
    const ERROR_1 = 'manager_timeout';
    const ERROR_1_MESS = 'Менеджер не смог ответить за ' . self::LAST_MESSAGE_TIMEOUT_MINUTES . ' мин. поэтому чат закрыт, повторите ваш вопрос попозже.';
    const ERROR_2 = 'admin_is_busy';
    const ERROR_2_MESS = 'Все менеджеры заняты.';
    const ERROR_3 = 'wrong_token';
    const ERROR_3_MESS = 'Все менеджеры заняты, попробуйте попозже.'; // на самом деле просто ни одного нет менеджера
    const ERROR_4 = 'admin_chat_current_session_id_is_null';
    const ERROR_4_MESS = 'Сессия null';
    const ERROR_5 = 'wrong_session_id';
    const ERROR_5_MESS = 'Неверный номер сессии.';
    const ERROR_6 = 'error api';
    const ERROR_6_MESS = 'Ошибка при передаче сообщения';
    const ERROR_7 = 'current_session_id_is_null';
    const ERROR_7_MESS = 'Автоматическое закрытие сеанса. Если будут еще вопросы - пишите.';

    const LAST_MESSAGE_TIMEOUT_MINUTES = 3;
    const LAST_HEART_BEAT_TIMEOUT_SECONDS = 30;
    const DEBUG_LOG_ENABLE = false;
    // const OLD_DB = false; // false true

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

        // if (self::OLD_DB == true) {
        //     global $livechat_db;
        //     $current_chat_start_date_utc = $livechat_db->real_escape_string($admin_chat['current_chat_start_date_utc']);
        //     $chat_id = $livechat_db->real_escape_string($admin_chat['chat_id']);
        //     $select_cmd = "SELECT message_date_utc, message_text FROM " . DB_PREFIX . "admin_chat_message WHERE chat_id='$chat_id' AND message_date_utc > '$current_chat_start_date_utc' AND is_read = '0' LOCK IN SHARE MODE";
        //     $r = $livechat_db->query($select_cmd);
        //     if ($r) {
        //         $messages = $r->fetch_all(MYSQLI_ASSOC);
        //         foreach ($messages as &$message) {
        //             if ($message['message_text'] == Subteno::FROM_ADMIN_CHAT_FINISH) {
        //                 self::end_chat($admin_chat, 'Admin order');
        //             }
        //             $message['message_date_utc'] = strtotime($message['message_date_utc']) . '000';
        //         }
        //         $result = 'ok';
        //         $messages = json_encode($messages);
        //         $update_is_read_cmd = "UPDATE " . DB_PREFIX . "admin_chat_message SET is_read = '1' WHERE chat_id = '$chat_id'";
        //         $livechat_db->query($update_is_read_cmd);
        //     }
        // } else {
            global $DB_SUBTENO;
            $r = $DB_SUBTENO->prepare("SELECT id,message_date_utc, message_text FROM " . DB_PREFIX . "admin_chat_message WHERE chat_id=? AND message_date_utc>? AND is_read=?", [$admin_chat['chat_id'], $admin_chat['current_chat_start_date_utc'], 0]);
            if (count($r) > 0) {
                $messages = $r;
                foreach ($messages as &$message) {
                    // обработка сокращений
                    if ($message['message_text'] == Subteno::FROM_ADMIN_CHAT_FINISH) {
                        self::end_chat($admin_chat, 'Admin order');
                    // } else if ($message['message_text'] == Subteno::FROM_ADMIN_CONNECTION_FINISH) {
                    }
                    // конвертация сокращений
                    if (array_key_exists($message['message_text'], Subteno::ABBREV)) {
                        $message['message_text'] = Subteno::ABBREV[$message['message_text']];
                    }
                    $message['message_date_utc'] = strtotime($message['message_date_utc']) . '000';
                    $DB_SUBTENO->prepare("UPDATE " . DB_PREFIX . "admin_chat_message SET is_read=? WHERE id=?", [1, $message['id']], false);
                }
                $result = 'ok';
                $messages = json_encode($messages);

                // $DB_SUBTENO->prepare("UPDATE " . DB_PREFIX . "admin_chat_message SET is_read=? WHERE chat_id=? AND message_date_utc>? AND is_read=?", [1, $admin_chat['chat_id'], $admin_chat['current_chat_start_date_utc'], 0], false);
            } else {
                // если нет сообщений это нормально
                $result = 'ok';
                $messages = json_encode([]);
            }
        // }
        return [$result, $messages];
    }
    static function send_new_message(&$admin_chat, $a_in)
    {
        $is_first_message = $admin_chat['current_session_id'] == null;
        // $is_first_message = (!isset($admin_chat['current_session_id']) || $admin_chat['current_session_id'] == null);
        // $email = '';
        if ($is_first_message) {
            $admin_chat['current_session_id'] = '' . session_id();
            $admin_chat['current_chat_start_date_utc'] = self::now_utc_formatted();
            $result = self::apiRequest("sendMessage", [
                'chat_id' => $admin_chat['chat_id'],
                'parse_mode' => 'HTML',
                'text' => "<b>" . $admin_chat['current_session_id'] . "</b>" . " начался чат с клиентом, надо ответить в течение " . self::LAST_MESSAGE_TIMEOUT_MINUTES . ' мин.'
            ]);
            if ($result === false) {
                return [self::ERROR_6, self::ERROR_6_MESS];
            }
        }

        $admin_chat['last_message_date_utc'] = self::now_utc_formatted();
        $admin_chat['dirty'] = true;
        // $new_message_json = file_get_contents('php://input');
        // $a_out['message_in'] = $new_message_json;
        // $new_message = json_decode($new_message_json, true); //assoc array
        // $send_message_text = $new_message['message_text'];
        // if (array_key_exists('email', $new_message)) {
        //     $send_message_text = '[' . $new_message['email'] . ']:' . $send_message_text;
        // }
        // $send_message_text = $a_in['message_text'];

        $result = self::apiRequest("sendMessage", [
            'chat_id' => $admin_chat['chat_id'],
            'parse_mode' => 'HTML',
            'text' => "<b>Сообщение от клиента:</b> " . $a_in['message_text']
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

        // self::log_with_sid('$admin_chat:' . json_encode($admin_chat));

        self::apiRequest("sendMessage", [
            'chat_id' => $admin_chat['chat_id'],
            'parse_mode' => 'HTML',
            'text' => "<b>" . $ending_session_id . "</b>" . " чат закончен, причина: " . $cause
        ]);
    }
    static function add_admin_chat($token, $chat_id)
    {
        // if (false) {
        // if (self::OLD_DB == true) {
        //     global $livechat_db;
        //     $cmd = "INSERT INTO " . DB_PREFIX . "admin_chat (chat_id, token) VALUES ('$chat_id','$token');";
        //     $livechat_db->query($cmd);
        // } else {
            global $DB_SUBTENO;
            $DB_SUBTENO->lock_tables([DB_PREFIX . "admin_chat"]);
            $r = $DB_SUBTENO->prepare("SELECT count(*) as cnt FROM " . DB_PREFIX . "admin_chat WHERE chat_id=? AND token=?", [$chat_id, $token]);
            if ($r[0]['cnt'] == 0) {
                $DB_SUBTENO->prepare("INSERT INTO " . DB_PREFIX . "admin_chat (chat_id, token) VALUES (?,?)", [$chat_id, $token], false);
            }
            $DB_SUBTENO->unlock_tables();
        // }
    }
    static function del_admin_chat($token, $chat_id)
    {
        global $DB_SUBTENO;
        $DB_SUBTENO->prepare("DELETE FROM " . DB_PREFIX . "admin_chat WHERE chat_id=? AND token=?", [$chat_id, $token], false);
    }
    static function get_admin_chat_by_token($token)
    {
        Subteno::log_with_sid('$token:' . json_encode($token));
        // if (false) {
        // if (self::OLD_DB == true) {
        //     global $livechat_db;
        //     $token = $livechat_db->real_escape_string($token);
        //     $cmd = "SELECT * FROM " . DB_PREFIX . "admin_chat WHERE token='$token';";
        //     $result = $livechat_db->query($cmd);
        //     if ($result) {
        //         return $result->fetch_assoc();
        //     } else {
        //         return false;
        //     }
        // } else {
            global $DB_SUBTENO;
            $result = $DB_SUBTENO->prepare("SELECT * FROM " . DB_PREFIX . "admin_chat WHERE token=?", [$token]);
            if (count($result) > 0) {
                return $result[0];
            } else {
                // error_log('get_admin_chat_by_token: не найден токен ' . $token);
                return false;
            }
        // }
        Subteno::log_with_sid('$admin_chat:' . json_encode($admin_chat));
    }
    static function update_admin_chat($admin_chat)
    {
        // if (self::OLD_DB == true) {
        //     global $livechat_db;
        //     $chat_id = false;
        //     if (array_key_exists('chat_id', $admin_chat)) {
        //         $chat_id = $livechat_db->real_escape_string($admin_chat['chat_id']);
        //         unset($admin_chat['chat_id']);
        //         unset($admin_chat['token']);
        //     }
        //     $update_cmd = "UPDATE " . DB_PREFIX . "admin_chat SET";
        //     $set_array = array();
        //     foreach ($admin_chat as $k => $v) {
        //         if (empty($v)) {
        //             $v = "NULL";
        //         } else {
        //             $v = "'$v'";
        //         }
        //         array_push($set_array, " $k = $v");
        //     }
        //     $update_cmd .= implode(',', $set_array);
        //     if ($chat_id) {
        //         $update_cmd .= " WHERE `chat_id`='$chat_id';";
        //     } else {
        //         return false;
        //     }
        //     self::log_with_sid('INFO:' . $update_cmd);
        //     $result = $livechat_db->query($update_cmd);
        //     return $result;
        // } else {
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
        // }
    }
    static function apiRequestWebhook($method, $parameters)
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

        $parameters["method"] = $method;

        header("Content-Type: application/json");
        echo json_encode($parameters);
        return true;
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
            // do not wat to DDOS server if something goes wrong
            // буду ддосить, клиент важнее телеграма
            // sleep(10);
            return false;
        } else if ($http_code != 200) {
            $response = json_decode($response, true);
            self::log_with_sid("Request has failed with error {$response['error_code']}: {$response['description']}\n");
            if ($http_code == 401) {
                // throw new Exception('Invalid access token provided');
                // сделал чтоб просто возвращался false потому что проброс непонятно куда ведет да и не надо никому знать что 401
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
    static function apiRequestJson($method, $parameters)
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

        $parameters["method"] = $method;

        $handle = curl_init(API_URL);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($handle, CURLOPT_TIMEOUT, 60);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($parameters));
        curl_setopt($handle, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));

        return self::exec_curl_request($handle);
    }
    function processMessage($message)
    {
        // не используется
        // process incoming message
        $message_id = $message['message_id'];
        $chat_id = $message['chat']['id'];
        if (isset($message['text'])) {
            // incoming text message
            $text = $message['text'];
            if (strpos($text, "/start") === 0) {
                self::apiRequestJson("sendMessage", array('chat_id' => $chat_id, "text" => 'Hello', 'reply_markup' => array(
                    'keyboard' => array(array('Hello', 'Hi')),
                    'one_time_keyboard' => true,
                    'resize_keyboard' => true
                )));
            } else if ($text === "Hello" || $text === "Hi") {
                self::apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Nice to meet you'));
            } else if (strpos($text, "/stop") === 0) {
                // stop now
            } else {
                self::apiRequestWebhook("sendMessage", array('chat_id' => $chat_id, "reply_to_message_id" => $message_id, "text" => 'Cool'));
            }
        } else {
            self::apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'I understand only text messages'));
        }
    }
    /*
    define('WEBHOOK_URL', 'https://my-site.example.com/secret-path-for-webhooks/');
    if (php_sapi_name() == 'cli') {
      // if run from console, set or delete webhook
      apiRequest('setWebhook', array('url' => isset($argv[1]) && $argv[1] == 'delete' ? '' : WEBHOOK_URL));
      exit;
    }*/
    /**
     *  An example CORS-compliant method.  It will allow any GET, POST, or OPTIONS requests from any
     *  origin.
     *
     *  In a production environment, you probably want to be more restrictive, but this gives you
     *  the general idea of what is involved.  For the nitty-gritty low-down, read:
     *
     *  - https://developer.mozilla.org/en/HTTP_access_control
     *  - http://www.w3.org/TR/cors/
     *
     */
    static function cors()
    {
        // Allow from any origin
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: *");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }
        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            exit(0);
        }
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
        // if (self::OLD_DB == true) {
        //     global $livechat_db;
        //     $key = $livechat_db->real_escape_string($key);
        //     $cmd =  "SELECT setting_value FROM " . DB_PREFIX . "setting WHERE setting_key='$key' LOCK IN SHARE MODE";
        //     $result = $livechat_db->query($cmd);
        //     if ($result) {
        //         return $result->fetch_row()[0];
        //     } else {
        //         return false;
        //     }
        // } else {
            global $DB_SUBTENO;
            $result = $DB_SUBTENO->prepare("SELECT setting_value FROM " . DB_PREFIX . "setting WHERE setting_key=?", [$key]);
            if (count($result) > 0) {
                return $result[0]['setting_value'];
            } else {
                return false;
            }
        // }
    }
    static function get_settings()
    {
        // if (self::OLD_DB == true) {
        //     global $livechat_db;
        //     $cmd =  "SELECT setting_key,setting_value FROM " . DB_PREFIX . "setting";
        //     $result = $livechat_db->query($cmd);
        //     if ($result) {
        //         $output = array();
        //         foreach (($result->fetch_all(true)) as $row) {
        //             $output[$row['setting_key']] = $row['setting_value'];
        //         }
        //         return $output;
        //     } else {
        //         return false;
        //     }
        // } else {
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
        // }
    }
    static function update_setting($key, $value)
    {
        // if (self::OLD_DB == true) {
        //     global $livechat_db;
        //     $key = $livechat_db->real_escape_string($key);
        //     $value = $livechat_db->real_escape_string($value);
        //     $cmd =  "UPDATE " . DB_PREFIX . "setting SET setting_value = '$value' WHERE setting_key='$key'";
        //     $result = $livechat_db->query($cmd);
        //     return $result;
        // } else {
            global $DB_SUBTENO;
            $result = $DB_SUBTENO->prepare("UPDATE " . DB_PREFIX . "setting SET setting_value=? WHERE setting_key=?", [$value, $key], false);
            return $result;
        // }
    }
    // static function build_insert_command($arr)
    // {
    //     global $livechat_db;
    //     $insert_cmd = "INSERT INTO " . DB_PREFIX . "admin_chat_message ";
    //     $keys = array();

    //     if (self::has_string_keys($arr)) {
    //         $arr = array($arr);
    //     }

    //     foreach ($arr[0] as $k => $v) {
    //         array_push($keys, '`' . $livechat_db->real_escape_string((string)$k) . '`');
    //     }

    //     $all_vals = array();
    //     foreach ($arr as $item) {
    //         $item_vals = array();
    //         foreach ($item as $k => $v) {
    //             array_push($item_vals, "'" . $livechat_db->real_escape_string((string)$v) . "'");
    //         }
    //         // if ($has_created_date_utc) {
    //         //     array_push($item_vals, "UTC_TIMESTAMP()");
    //         // }
    //         array_push($all_vals, $item_vals);
    //     }

    //     $values_part = implode(', ', array_map(function ($item) {
    //         return '(' . implode(', ', $item) . ')';
    //     }, $all_vals));

    //     $insert_cmd .= '(' . implode(',', $keys) . ") VALUES $values_part ;";

    //     return $insert_cmd;
    // }
    static function has_string_keys(array $array)
    {
        return count(array_filter(array_keys($array), 'is_string')) > 0;
    }
    static function install()
    {
        global $DB_SUBTENO;
        // $db_init_cmds = array(
        //     "CREATE TABLE IF NOT EXISTS  " . DB_PREFIX . "admin_chat (    chat_id bigint,    token varchar(255),    current_session_id varchar(255),    current_chat_start_date_utc datetime,    last_heart_beat_utc datetime,    last_message_date_utc datetime,    dirty  TINYINT(1) );",
        //     "CREATE TABLE IF NOT EXISTS  " . DB_PREFIX . "admin_chat_message (    chat_id bigint,    message_date_utc datetime,     message_text text,    is_read TINYINT(1) default 0);",
        //     "CREATE TABLE IF NOT EXISTS  " . DB_PREFIX . "setting (setting_key varchar(150) PRIMARY KEY,    setting_value varchar(255) );",
        //     "INSERT INTO " . DB_PREFIX . "setting (setting_key, setting_value) VALUES ('last_telegram_bot_offset','0')"
        // );
        // foreach ($db_init_cmds as $cmd) {
        //     $DB_SUBTENO->exec($cmd);
        // }
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

// global $livechat_db;
// $livechat_db = new mysqli('localhost', 'dipstick', '3nwiCcsb6NIR', 'dipstick');
// if ($livechat_db->connect_errno) {
//     printf("Connect failed: %s\n", $livechat_db->connect_error);
//     exit();
// }

$DB_SUBTENO = new DIV_DB();
$DB_SUBTENO->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

// надо чтоб 1 раз только запустилось, потом можно закомментировать
Subteno::install();
