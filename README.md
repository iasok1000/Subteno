
# Customer Support on your website

## Installation and Usage

1. Click the Copy button and then Download ZIP. Unzip and copy the **subteno** folder to the root of your site.
2. The file **/subteno/backend/config_sample.php** should be renamed to **/subteno/backend/config.php**
2. To work, you need 3 MySQL (MariaDB) tables. To do this, you need to enter the following in **/subteno/backend/config.php**:
```
define('DB_HOST', 'localhost');
define('DB_USER', 'username_here');
define('DB_PASS', 'password_here');
define('DB_NAME', 'database_name_here');
define('DB_PORT', 3306);
define('DB_PREFIX', 'S_');
```
4. In the index.php file of your site, before the **body** tag, you need to insert the line
```
 require $_SERVER["DOCUMENT_ROOT"] . "/subteno/frontend/chat.php";
 </body>
```
5. Go to your website, a button with a chat icon should appear at the bottom right, click on it, enter any message, click on the button with a paper airplane icon, an incoming message will appear **All managers are busy, try again later.**, do not pay attention to him attention. Now the necessary data should be created in the database. Now in **/private/subteno/backend/settings.php** you can comment out the line
```
Subteno::install();
```
6. [Create a Telegram Bot](https://core.telegram.org/bots/tutorial#obtain-your-bot-token) using @BotFather. Write token в **/private/subteno/backend/config.php**. The name of the bot can be anything, it is not necessary to choose a beautiful one, this name will not be widely available and is only needed for support managers who will communicate with clients.
```
define('BOT_TOKEN', 'bot_token_here');
```
7. In the browser you need to enter **https://t.me/your_bot_name** go to the bot and send it a message *BOT_ADMIN_SECRET* from **/private/subteno/backend/config.php**

## Safety

For security, you need to deny access to the **backend** folder directly via the Internet, for this, for example, in Nginx
```
location /subteno/backend/ {
    deny all;
}
```

## Links

- [Initial code](https://github.com/ehsabd/telegram-live-chat)

## Donation

[<img width='110px' hieght="auto" alt="DONATE" src="/img/donate.png" href="https://donate.stream/yoomoney4100118809080436">](https://donate.stream/yoomoney4100118809080436)
