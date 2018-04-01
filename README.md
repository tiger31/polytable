# POLYTABLE
### Заметки по пользованю репозитория:
* Не коммитить в ветку `master`. Для работы создайте свои ветки в которые вы сможете коммитить что захотите.
* Проверяйте отставания своей ветки от `master`. Некоторые изменения в `master` могут сильно влиять на работу проетка.
* Ветка `debug` используется для изменений, которые были внесены в проект, но не закончены или не проверены до конца.

### Как мне запустить весь проект у себя
1. Установка web-сервера
  Для Windows - [OPENSERVER (бесплатное скачивание с оф. сайта)](https://ospanel.io/)
  Для Linux - LAMP [(Гайд по настройке)](https://www.digitalocean.com/community/tutorials/how-to-install-linux-apache-mysql-php-lamp-stack-on-ubuntu-16-04)
  Конфигурация сервера:
    * Apache-PHP-7-x64
    * PHP версии 7.0 и выше
    * MySQL 5.7 (при установке на локальной машине оставлять пароль пустым для root)
2. После установки сервера склонировать (`git clone`) проект в папку *ваш_сервер/domains/localhost* и запустить сервер
3. Установите текущую версию БД [(Файл `groups_sch.sql`)](https://github.com/tiger31/polytable/blob/master/groups_sch.sql).
    * Для этого нажмите *ПКМ* по значку OPENSERVER в трее и выберите **Дополнительно>MySQL Менеджер** 
    * В открывшемся окне подключитесь к своему MySQL серверу c параметрами: Хост - 127.0.0.1, Пользователь - root, Поле "Пароль" пустое
    (если вы его, конечно, не ставили, чего я не советую делать на локальной машине)
    * После подключения к базе выберите **Файл>Выполнить SQL файл"** и выберите файл `groups_sch.sql`
4. Если вы все же меняли пользователя или пароль MySQL, то в файле [Connect.php](https://github.com/tiger31/polytable/blob/master/modules/Connect.php) выставить свои параметры подключения к БД
```php 
  $host = "127.0.0.1";
  $database = "groups_sch";
  $user = "root";
  $password = "";
```
5. ???
6. PROFIT

Вот и все, теперь вы сами можете вершить историю и делать ~~мир~~ учебу студентов лучше и проще. 
Если появятся какие-либо вопросы, вы знаете к кому обращаться.
