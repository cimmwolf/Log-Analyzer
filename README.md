Log Analyzer
============
Requirements
------------
* PHP (with sqlite3) >= 5.4;
* Composer;
* NPM.

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
   `TIMEZONE` — logs dates timezone. List of values: http://php.net/manual/ru/timezones.php;

3. open index.html from browser.

Требования
----------
* PHP (with pdo-sqlite3) >= 5.4
* Composer;
* NPM.

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
   `TIMEZONE` — временная зона для анализа даты в логах. Список значений: http://php.net/manual/ru/timezones.php;

3. откройте файл index.html в браузере.