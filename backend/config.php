<?php


// TODO в яве в getNewMessages надо останаливать уже запущенные setTimeout что не было дублей сообщений об ошибке
define('ABSPATH', __DIR__ . '/');

define('DB_HOST', 'localhost');
define('DB_USER', 'username_here');
define('DB_PASS', 'password_here');
define('DB_NAME', 'database_name_here');
define('DB_PORT', 3306);
// префикс таблиц в БД
define('DB_PREFIX', 'S_');

// секретный токен полученный от BotFather
define('BOT_TOKEN', 'bot_token_here');
// секретный урл апи телеграма
define('API_URL', 'https://api.telegram.org/bot' . BOT_TOKEN . '/');
// это не секретный токен, его видят все в коде явы на сайте, он нужен чтобы разделить чаты от разных доменов
define('ADMIN_CHAT_TOKEN', 'default');
// секретное сообщение, если кто-то начнет чат с ботом BOT_TOKEN и пошлет ему сообщение BOT_ADMIN_SECRET то ему в телеграм в чат с ботом будут приходить сообщения от клиентов у которых в яве chat_token такой же как ADMIN_CHAT_TOKEN
define('BOT_ADMIN_SECRET', 'my_bot_admin_secret_here');

require_once ABSPATH . 'settings.php';
