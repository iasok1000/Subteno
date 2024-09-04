
# Customer Support on your website

## Installation and Usage

1. Click the Copy button and then Download ZIP. Unzip the saved file and put the 'backend' folder on the server, for example in the '/home/www/my-site/private/backend' folder and the 'frontend' folder, for example in '/home/www/my-site/share/frontend'.
2. Откройте backend/config.php и введите наименование этих папок, например:
define('PATH_FRONTEND', '/private/backend');
define('PATH_BACKEND', '/share/backend');
3. В frontend/chat.js надо изменить 
    static ajax_url = 'ajax.php';
на примерно такую
    static ajax_url = '/home/www/my-site/share/fronten/ajax.php';
4. Для работы необходимв 3 таблицы MariaDB.Для этого надо в backend/config.php ввести следующее:
Database hostname
define('DB_HOST', 'localhost');
Database username
define('DB_USER', 'username_here');
Database password
define('DB_PASS', 'password_here');
The name of the database
define('DB_NAME', 'database_name_here');
Database port (usually 3306)
define('DB_PORT', 3306);
Database table prefix
define('DB_PREFIX', 'S_');
5. В файле index.php вашего сайта перед тегом 'body' надо вставить строку 'require'
...
require "/home/www/my-site/share/frontend/chat.php";
...
'</body>'
6. Зайдите на свой сайт, внизу справа должна пояаится кнопка с иконкой чата, скликните на нее, введите любое сообщение, кликните на кнопку с иконкой бумажного самолета, появится входящее сообщение 'All managers are busy, try again later.', не обращайте на него внимания. Сейчас должны создасться необходимые таюлицы в базе данных. Теперь в 'backend/settings.php' можно закомментировать строку
Subteno::install();
Она в самом конце.
7. [Create a Telegram Bot](https://core.telegram.org/bots/tutorial#obtain-your-bot-token) using @BotFather. Запишите token в backend/config.php.
define('BOT_TOKEN', 'bot_token_here');
8. Надо боту послать *BOT_ADMIN_SECRET* из 'backend/config.php'
9. В браузере ввести https://t.me/Dip_Stick_Bot перейти к боту и послать ему сообщение *BOT_ADMIN_SECRET*

## Благодарность
- Первоначальный код [отсюда](https://github.com/ehsabd/telegram-live-chat)

## Donation

- [<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#e12d2d" viewBox="0 0 512 512"><path d="M256 416c114.9 0 208-93.1 208-208S370.9 0 256 0 48 93.1 48 208s93.1 208 208 208zM233.8 97.4V80.6c0-9.2 7.4-16.6 16.6-16.6h11.1c9.2 0 16.6 7.4 16.6 16.6v17c15.5 .8 30.5 6.1 43 15.4 5.6 4.1 6.2 12.3 1.2 17.1L306 145.6c-3.8 3.7-9.5 3.8-14 1-5.4-3.4-11.4-5.1-17.8-5.1h-38.9c-9 0-16.3 8.2-16.3 18.3 0 8.2 5 15.5 12.1 17.6l62.3 18.7c25.7 7.7 43.7 32.4 43.7 60.1 0 34-26.4 61.5-59.1 62.4v16.8c0 9.2-7.4 16.6-16.6 16.6h-11.1c-9.2 0-16.6-7.4-16.6-16.6v-17c-15.5-.8-30.5-6.1-43-15.4-5.6-4.1-6.2-12.3-1.2-17.1l16.3-15.5c3.8-3.7 9.5-3.8 14-1 5.4 3.4 11.4 5.1 17.8 5.1h38.9c9 0 16.3-8.2 16.3-18.3 0-8.2-5-15.5-12.1-17.6l-62.3-18.7c-25.7-7.7-43.7-32.4-43.7-60.1 .1-34 26.4-61.5 59.1-62.4zM480 352h-32.5c-19.6 26-44.6 47.7-73 64h63.8c5.3 0 9.6 3.6 9.6 8v16c0 4.4-4.3 8-9.6 8H73.6c-5.3 0-9.6-3.6-9.6-8v-16c0-4.4 4.3-8 9.6-8h63.8c-28.4-16.3-53.3-38-73-64H32c-17.7 0-32 14.3-32 32v96c0 17.7 14.3 32 32 32h448c17.7 0 32-14.3 32-32v-96c0-17.7-14.3-32-32-32z"/></svg>](https://donate.stream/yoomoney4100118809080436)
- [Buy me a coffee](https://donate.stream/yoomoney4100118809080436)