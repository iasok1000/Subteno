<?php
// folder on the server where the frontend folder will be located
define('PATH_FRONTEND', '/');
// folder on the server where the backend folder will be located
define('PATH_BACKEND', '/');
// Database hostname
define('DB_HOST', 'localhost');
// Database username
define('DB_USER', 'username_here');
// Database password
define('DB_PASS', 'password_here');
// The name of the database
define('DB_NAME', 'database_name_here');
// Database port (usually 3306)
define('DB_PORT', 3306);
// prefix of tables in the database
define('DB_PREFIX', 'S_');
// secret token received from BotFather
define('BOT_TOKEN', 'bot_token_here');
// telegram api secret url
define('API_URL', 'https://api.telegram.org/bot' . BOT_TOKEN . '/');
// this is not a secret token, everyone can see it in the JS code on the site, it is needed to separate chats from different domains
define('ADMIN_CHAT_TOKEN', 'default');
// secret message, if someone starts a chat with the BOT_TOKEN bot and sends him the message BOT_ADMIN_SECRET, then he will receive messages in telegram chat with the bot from clients whose chat_token in JS is the same as ADMIN_CHAT_TOKEN
define('BOT_ADMIN_SECRET', 'my_bot_admin_secret_here');

require_once PATH_BACKEND . 'settings.php';
