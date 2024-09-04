
# Customer Support on your website

## Installation and Usage

1. Click the Copy button and then Download ZIP. Unzip the saved file and put the **backend** folder on the server, for example in the **/home/www/my-site/private/backend** folder and the **frontend** folder, for example in **/home/www/my-site/share/frontend**.
2. Open **backend/config.php** and enter the name of these folders, for example:
```
define('PATH_FRONTEND', '/private/backend');
define('PATH_BACKEND', '/share/backend');
```
3. In frontend/chat.js need to change
```
static ajax_url = 'ajax.php';
```
about this
```
static ajax_url = '/home/www/my-site/share/frontend/ajax.php';
```
4. To work, you need 3 MariaDB tables. To do this, you need to enter the following in **backend/config.php**:
```
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
```
5. In the index.php file of your site, before the **body** tag, you need to insert the line **require**
```
 require "/home/www/my-site/share/frontend/chat.php";
 </body>
```
6. Go to your website, a button with a chat icon should appear at the bottom right, click on it, enter any message, click on the button with a paper airplane icon, an incoming message will appear **All managers are busy, try again later.**, do not pay attention to him attention. Now the necessary data should be created in the database. Now in **backend/settings.php** you can comment out the line
```
Subteno::install();
```
7. [Create a Telegram Bot](https://core.telegram.org/bots/tutorial#obtain-your-bot-token) using @BotFather. Write token в **backend/config.php**.
```
define('BOT_TOKEN', 'bot_token_here');
```
8. You need to send it message to the bot *BOT_ADMIN_SECRET* from **backend/config.php**
9. In the browser you need to enter **https://t.me/your_bot_name** go to the bot and send it a message *BOT_ADMIN_SECRET*

## Links

- [Initial code](https://github.com/ehsabd/telegram-live-chat)

## Donation

[<img width='200px' hieght="auto" alt="DONATE" src="/image/donate.png" href="https://donate.stream/yoomoney4100118809080436">](https://donate.stream/yoomoney4100118809080436)
