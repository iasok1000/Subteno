<?php
class Language
{
    public static $locale = 'en';

    static function trans($mess)
    {
        if (strtolower($mess) == 'welcome to admin') {
            // welcome message to managers after he sent BOT_ADMIN_SECRET, it comes with 1 message from the client
            if (self::$locale == 'en') {
                return 'Your chat with this bot is linked to the chat with clients. You will receive a message about the start of a chat with the client and his first message.';
            } else if (self::$locale == 'ru') {
                return 'Ваш чат с этим ботом привязан к чату с клиентами. Вы получите сообщение о начале чата с клиентом и его первое сообщение.';
            } else if (self::$locale == 'es') {
                return 'Tu chat con este bot está vinculado al chat con los clientes. Recibirás un mensaje sobre el inicio de un chat con el cliente y su primer mensaje.';
            } else if (self::$locale == 'fr') {
                return 'Votre chat avec ce bot est lié au chat avec les clients. Vous recevrez un message concernant le début d\'une conversation avec le client et son premier message.';
            } else if (self::$locale == 'de') {
                return 'Ihr Chat mit diesem Bot ist mit dem Chat mit Kunden verknüpft. Sie erhalten eine Nachricht über den Beginn eines Chats mit dem Kunden und seine erste Nachricht.';
            }
        } else if (strtolower($mess) == 'time is up') {
            if (self::$locale == 'en') {
                return 'Time is up';
            } else if (self::$locale == 'ru') {
                return 'Время истекло';
            } else if (self::$locale == 'es') {
                return 'El tiempo ha terminado';
            } else if (self::$locale == 'fr') {
                return 'Le temps s\'est écoulé';
            } else if (self::$locale == 'de') {
                return 'Die Zeit ist um';
            }
        } else if (strtolower($mess) == 'no heartbeat') {
            if (self::$locale == 'en') {
                return 'There is no signal from the client';
            } else if (self::$locale == 'ru') {
                return 'От клиента нет сигнала';
            } else if (self::$locale == 'es') {
                return 'No hay señal del cliente';
            } else if (self::$locale == 'fr') {
                return 'Il n\'y a aucun signal du client';
            } else if (self::$locale == 'de') {
                return 'Es gibt kein Signal vom Client';
            }
        } else if (strtolower($mess) == 'by to admin') {
            // message to the manager after he left the chat with clients by sending Language::trans('abbrev end connection')
            if (self::$locale == 'en') {
                return 'Your chat with this bot is disconnected from chats with clients.';
            } else if (self::$locale == 'ru') {
                return 'Ваш чат с этим ботом отключен от чатов с клиентами.';
            } else if (self::$locale == 'es') {
                return 'Tu chat con este bot está desconectado de los chats con clientes.';
            } else if (self::$locale == 'fr') {
                return 'Votre conversation avec ce bot est déconnectée des discussions avec les clients.';
            } else if (self::$locale == 'de') {
                return 'Ihr Chat mit diesem Bot ist von Chats mit Kunden getrennt.';
            }
        } else if (strtolower($mess) == 'new client to admin') {
            if (self::$locale == 'en') {
                return '<b>System message</b> Hurry up, new client';
            } else if (self::$locale == 'ru') {
                return '<b>Системное сообщение</b> Поторопитесь, новый клиент';
            } else if (self::$locale == 'es') {
                return '<b>Mensaje del sistema</b> Date prisa, nuevo cliente';
            } else if (self::$locale == 'fr') {
                return '<b>Message système</b> Dépêchez-vous, nouveau client';
            } else if (self::$locale == 'de') {
                return '<b>Systemmeldung</b> Beeilen Sie sich, neuer Kunde';
            }
        } else if (strtolower($mess) == 'unknown error') {
            if (self::$locale == 'en') {
                return 'Unknown error.';
            } else if (self::$locale == 'ru') {
                return 'Неизвестная ошибка.';
            } else if (self::$locale == 'es') {
                return 'Error desconocido.';
            } else if (self::$locale == 'fr') {
                return 'Erreur inconnue.';
            } else if (self::$locale == 'de') {
                return 'Unbekannter Fehler.';
            }
        } else if (strtolower($mess) == 'error 1') {
            if (self::$locale == 'en') {
                return 'The manager didn\'t answer. Therefore the chat is closed, please repeat your question later.';
            } else if (self::$locale == 'ru') {
                return 'Менеджер не ответил. Поэтому чат закрыт, пожалуйста, повторите свой вопрос позже.';
            } else if (self::$locale == 'es') {
                return 'El gerente no respondió. Por lo tanto el chat está cerrado, por favor repite tu pregunta más tarde.';
            } else if (self::$locale == 'fr') {
                return 'Le gérant n\'a pas répondu. Le chat est donc fermé, veuillez répéter votre question plus tard.';
            } else if (self::$locale == 'de') {
                return 'Der Manager antwortete nicht. Daher ist der Chat geschlossen. Bitte wiederholen Sie Ihre Frage später.';
            }
        } else if (strtolower($mess) == 'error 2') {
            if (self::$locale == 'en') {
                return 'All managers are busy.';
            } else if (self::$locale == 'ru') {
                return 'Все менеджеры заняты.';
            } else if (self::$locale == 'es') {
                return 'Todos los gerentes están ocupados.';
            } else if (self::$locale == 'fr') {
                return 'Tous les managers sont occupés.';
            } else if (self::$locale == 'de') {
                return 'Alle Manager sind beschäftigt.';
            }
        } else if (strtolower($mess) == 'error 3') {
            if (self::$locale == 'en') {
                return 'All managers are busy, try again later.';
            } else if (self::$locale == 'ru') {
                return 'Все менеджеры заняты, повторите попытку позже.';
            } else if (self::$locale == 'es') {
                return 'Todos los gerentes están ocupados, intente de nuevo más tarde.';
            } else if (self::$locale == 'fr') {
                return 'Tous les managers sont occupés, réessayez plus tard.';
            } else if (self::$locale == 'de') {
                return 'Alle Manager sind beschäftigt. Versuchen Sie es später noch einmal.';
            }
        } else if (strtolower($mess) == 'error 4') {
            if (self::$locale == 'en') {
                return 'Session is null';
            } else if (self::$locale == 'ru') {
                return 'Сессия null';
            } else if (self::$locale == 'es') {
                return 'La sesión es null';
            } else if (self::$locale == 'fr') {
                return 'La session est null';
            } else if (self::$locale == 'de') {
                return 'Sitzung ist null';
            }
        } else if (strtolower($mess) == 'error 5') {
            if (self::$locale == 'en') {
                return 'Invalid session id.';
            } else if (self::$locale == 'ru') {
                return 'Неверное знвчение session id.';
            } else if (self::$locale == 'es') {
                return 'ID de sesión no válida.';
            } else if (self::$locale == 'fr') {
                return 'Identifiant de session invalide.';
            } else if (self::$locale == 'de') {
                return 'Ungültige Sitzungs-ID.';
            }
        } else if (strtolower($mess) == 'error 6') {
            if (self::$locale == 'en') {
                return 'Error while sending message.';
            } else if (self::$locale == 'ru') {
                return 'Ошибка при отправке сообщения.';
            } else if (self::$locale == 'es') {
                return 'Error al enviar mensaje.';
            } else if (self::$locale == 'fr') {
                return 'Erreur lors de l\'envoi du message.';
            } else if (self::$locale == 'de') {
                return 'Fehler beim Senden der Nachricht.';
            }
        } else if (strtolower($mess) == 'error 7') {
            if (self::$locale == 'en') {
                return 'Automatic session closure. If you have any more questions, write.';
            } else if (self::$locale == 'ru') {
                return 'Автоматическое закрытие сессии. Если будут еще вопросы, пишите.';
            } else if (self::$locale == 'es') {
                return 'Cierre automático de sesión. Si tienes más dudas escribe.';
            } else if (self::$locale == 'fr') {
                return 'Fermeture automatique de la session. Si vous avez d\'autres questions, écrivez.';
            } else if (self::$locale == 'de') {
                return 'Automatischer Sitzungsabschluss. Wenn Sie weitere Fragen haben, schreiben Sie.';
            }
        } else if (strtolower($mess) == 'admin order') {
            if (self::$locale == 'en') {
                return 'Admin order';
            } else if (self::$locale == 'ru') {
                return 'Инициатива менеджера';
            } else if (self::$locale == 'es') {
                return 'Orden de administrador';
            } else if (self::$locale == 'fr') {
                return 'Commande administrative';
            } else if (self::$locale == 'de') {
                return 'Admin-Anordnung';
            }
        } else if (strtolower($mess) == 'chat start') {
            if (self::$locale == 'en') {
                return 'a chat with the client has begun, you must respond within';
            } else if (self::$locale == 'ru') {
                return 'начался чат с клиентом, необходимо ответить в течение';
            } else if (self::$locale == 'es') {
                return 'ha comenzado un chat con el cliente, debes responder dentro de';
            } else if (self::$locale == 'fr') {
                return 'une conversation avec le client a commencé, vous devez répondre dans les';
            } else if (self::$locale == 'de') {
                return 'Ein Chat mit dem Kunden hat begonnen. Sie müssen innerhalb dieser Zeit antworten';
            }
        } else if (strtolower($mess) == 'chat end') {
            if (self::$locale == 'en') {
                return 'chat ended, reason';
            } else if (self::$locale == 'ru') {
                return 'чат закончился, причина';
            } else if (self::$locale == 'es') {
                return 'el chat terminó, motivo';
            } else if (self::$locale == 'fr') {
                return 'chat terminé, raison';
            } else if (self::$locale == 'de') {
                return 'Chat beendet, Grund';
            }
        } else if (strtolower($mess) == 'abbrev end connection') {
            // “end of connection” message, the manager wants to disconnect from chats with clients
            if (self::$locale == 'en') {
                return 'en';
            } else if (self::$locale == 'ru') {
                return 'кс';
            } else if (self::$locale == 'es') {
                return 'kf';
            } else if (self::$locale == 'fr') {
                return 'fc';
            } else if (self::$locale == 'de') {
                return 'vb';
            }
        } else if (strtolower($mess) == 'abbrev chat end') {
            // after the admin sends such a message, he “disconnects” from the chat with the client and his communications will remain in the chat with the bot
            if (self::$locale == 'en') {
                return 'gb';
            } else if (self::$locale == 'ru') {
                return 'дс';
            } else if (self::$locale == 'es') {
                return 'ad';
            } else if (self::$locale == 'fr') {
                return 're';
            } else if (self::$locale == 'de') {
                return 've';
            }
        } else if (strtolower($mess) == 'abbrev good afternoon') {
            if (self::$locale == 'en') {
                return 'ga';
            } else if (self::$locale == 'ru') {
                return 'дд';
            } else if (self::$locale == 'es') {
                return 'bt';
            } else if (self::$locale == 'fr') {
                return 'ba';
            } else if (self::$locale == 'de') {
                return 'gt';
            }
        } else if (strtolower($mess) == 'good afternoon') {
            if (self::$locale == 'en') {
                return 'Good afternoon!';
            } else if (self::$locale == 'ru') {
                return 'Добрый день!';
            } else if (self::$locale == 'es') {
                return '¡Buenas tardes!';
            } else if (self::$locale == 'fr') {
                return 'Bon après-midi!';
            } else if (self::$locale == 'de') {
                return 'Guten Tag!';
            }
        } else if (strtolower($mess) == 'bye') {
            if (self::$locale == 'en') {
                return 'Goodbye, if you have any more questions, write.';
            } else if (self::$locale == 'ru') {
                return 'До свидания, если будут еще вопросы, пишите.';
            } else if (self::$locale == 'es') {
                return 'Adiós, si tienes más dudas escribe.';
            } else if (self::$locale == 'fr') {
                return 'Au revoir, si vous avez d\'autres questions, écrivez.';
            } else if (self::$locale == 'de') {
                return 'Auf Wiedersehen, wenn Sie noch weitere Fragen haben, schreiben Sie.';
            }
        } else if (strtolower($mess) == 'message from client') {
            if (self::$locale == 'en') {
                return 'Message from the client';
            } else if (self::$locale == 'ru') {
                return 'Сообщение от клиента';
            } else if (self::$locale == 'es') {
                return 'Mensaje del cliente';
            } else if (self::$locale == 'fr') {
                return 'Message du client';
            } else if (self::$locale == 'de') {
                return 'Nachricht vom Kunden';
            }
        } else if (strtolower($mess) == '') {
            if (self::$locale == 'en') {
                return '';
            } else if (self::$locale == 'ru') {
                return '';
            } else if (self::$locale == 'es') {
                return $mess;
            } else if (self::$locale == 'fr') {
                return $mess;
            } else if (self::$locale == 'de') {
                return $mess;
            }
        }
    }
}
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
    const ERROR_0 = 'unknown_error';
    const ERROR_1 = 'manager_timeout';
    const ERROR_2 = 'admin_is_busy';
    const ERROR_3 = 'wrong_token';
    const ERROR_4 = 'admin_chat_current_session_id_is_null';
    const ERROR_5 = 'wrong_session_id';
    const ERROR_6 = 'error api';
    const ERROR_7 = 'current_session_id_is_null';

    const LAST_MESSAGE_TIMEOUT_MINUTES = 3;
    const LAST_HEART_BEAT_TIMEOUT_SECONDS = 30;
    const DEBUG_LOG_ENABLE = false;

    static function abbrev()
    {
        // short messages from the manager that are replaced
        return [
            Language::trans('abbrev good afternoon') => Language::trans('good afternoon'),
            Language::trans('abbrev chat end') => Language::trans('bye')
        ];
    }
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
            return [self::ERROR_4, Language::trans('error 4')];
        }

        $admin_chat['last_heart_beat_utc'] = self::now_utc_formatted();
        $admin_chat['dirty'] = true;

        global $DB_SUBTENO;
        $r = $DB_SUBTENO->prepare("SELECT id,message_date_utc, message_text FROM " . DB_PREFIX . "admin_chat_message WHERE chat_id=? AND message_date_utc>? AND is_read=?", [$admin_chat['chat_id'], $admin_chat['current_chat_start_date_utc'], 0]);
        if (count($r) > 0) {
            $messages = $r;
            foreach ($messages as &$message) {
                // abbreviation processing
                if ($message['message_text'] == Language::trans('abbrev chat end')) {
                    self::end_chat($admin_chat, Language::trans('admin order'));
                }
                // replacing abbreviations
                $abbrev = Subteno::abbrev();
                if (array_key_exists($message['message_text'], $abbrev)) {
                    $message['message_text'] = $abbrev[$message['message_text']];
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
                'text' => '<b>' . $admin_chat['current_session_id'] . '</b> ' . Language::trans('chat start') . ' ' . self::LAST_MESSAGE_TIMEOUT_MINUTES . 'min.'
            ]);
            if ($result === false) {
                return [self::ERROR_6, Language::trans('error 6')];
            }
        }
        $admin_chat['last_message_date_utc'] = self::now_utc_formatted();
        $admin_chat['dirty'] = true;
        $result = self::apiRequest("sendMessage", [
            'chat_id' => $admin_chat['chat_id'],
            'parse_mode' => 'HTML',
            'text' => '<b>' . Language::trans('message from client') . ':</b> ' . $a_in['message_text']
        ]);
        if ($result === false) {
            return [self::ERROR_6, Language::trans('error 6')];
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
            'text' => "<b>" . $ending_session_id . "</b>" . ' ' . Language::trans('chat end') . ': ' . $cause
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

Language::$locale = LANG;

$DB_SUBTENO = new DIV_DB();
$DB_SUBTENO->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

// It only needs to run once, then you can comment it out
Subteno::install();
