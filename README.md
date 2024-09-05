
# Customer Support on your website

## Installation and Usage

1. Click the Copy button and then Download ZIP. Unzip and copy the subteno folder to the root of your site.

2. To work, you need 3 MySQL (MariaDB) tables. To do this, you need to enter the following in **/subteno/backend/config.php**:
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
3. In the index.php file of your site, before the **body** tag, you need to insert the line **require**
```
 require "/home/www/my-site/subteno/frontend/chat.php";
 </body>
```
4. Go to your website, a button with a chat icon should appear at the bottom right, click on it, enter any message, click on the button with a paper airplane icon, an incoming message will appear **Please check you internet connection.**, do not pay attention to him attention. Now the necessary data should be created in the database. Now in **/private/subteno/backend/settings.php** you can comment out the line
```
Subteno::install();
```
5. [Create a Telegram Bot](https://core.telegram.org/bots/tutorial#obtain-your-bot-token) using @BotFather. Write token в **/private/subteno/backend/config.php**.
```
define('BOT_TOKEN', 'bot_token_here');
```
6. You need to send it message to the bot *BOT_ADMIN_SECRET* from **/private/subteno/backend/config.php**
7. In the browser you need to enter **https://t.me/your_bot_name** go to the bot and send it a message *BOT_ADMIN_SECRET*

## Links

- [Initial code](https://github.com/ehsabd/telegram-live-chat)

## Donation

[<img width='110px' hieght="auto" alt="DONATE" src="/image/donate.png" href="https://donate.stream/yoomoney4100118809080436">](https://donate.stream/yoomoney4100118809080436)
