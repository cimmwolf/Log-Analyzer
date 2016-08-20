Log Analyzer
============
Requirements
------------
* PHP (with sqlite3) >= 5.6;
* Composer;
* NPM;
* Bower;
* Gulp.

Get started
-----------
1. download project into web accessible directory on the server;
2. run `composer install`;
3. run `npm install`;
4. run `bower install`;
4. run `gulp`;
2. run `php fill-db.php -s LOG_SOURCE -n PROJECT_NAME -t TIMEZONE`  
   where:  
   `LOG_SOURCE` — comma separated paths to log files. It's may be urls. Analyzer recognize format automatically.  
   `PROJECT_NAME` — name of your project.  
   `TIMEZONE` — logs dates timezone. List of values: http://php.net/manual/ru/timezones.php. Be careful, Nginx and Yii 
                 logs can use different timezones.

3. open index.html from browser.

Cron job
--------
When this app is opened in browser, the script updates data every minute. When you are offline cron can 
update log's data. In this case you should send request to file /cron.php. 
Example: wget -O /dev/null -o /dev/null (app.webroot.path)/cron.php

Быстрый старт:
-------------
1. скачайте проект в директорию доступную из сети;
2. запустите команду `composer install`;
3. запустите команду `npm install`;
4. запустите команду `bower install`;
4. запустите команду `gulp`;
2. запустите команду: `php fill-db.php -s LOG_SOURCE -n PROJECT_NAME -t TIMEZONE`  
   где:  
   `LOG_SOURCE` — пути до логов, разделённые запятыми. Пути могут быть URL. Анализатор самостоятельно определит тип логов.  
   `PROJECT_NAME` — название вашего проекта.  
   `TIMEZONE` — временная зона для анализа даты в логах. Список значений: http://php.net/manual/ru/timezones.php. Внимание,
                 логи Nginx и Yii могут использовать разные часовые пояса.

3. откройте файл index.html в браузере.

Обновление данных по расписанию
-------------------------------
Пока приложение открыто в браузере оно будет обновлять данные каждую минуту. Также, вы можете настроить cron для 
автоматического обновления данных. Пример: wget -O /dev/null -o /dev/null (app.webroot.path)/cron.php