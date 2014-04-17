# My Simple MVC
---

Простой MVC фреймворк для создания очень простых сайтов на PHP >= 5.4 + MySQL.
Устанавливается в отдельную папку (по умолчанию - core/) в корне сайта.

## Установка

1. Склонить mysmvc в папку core/.
2. Если используем Apache, то переместить core/examples/.htaccess в корень сайта. Если Nginx, то скопировать core/examples/nginx.conf в %nginx%/sites-enabled/ и заменить @project на правильное значение.
3. Использовать следующие команды для создания директорий по умолчанию

        mkdir assets
        mkdir configs
        mkdir controllers
        mkdir core
        mkdir models
        mkdir vendor
        mkdir views
        mkdir views/template
        mkdir views/zones

4. Скопировать core/examples/config.php в configs/config.php и поправить конфигурацию.
5. Скопировать core/examples/errors/ в errors/.

## Инструкция по использованию

Авто-подключение файлов осуществляется по стандарту [PSR-0](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md).

### Мой первый контроллер

    namespace myapp\controllers;
    use msmvc\core\controller;

    class base extends controller {

        function before() {

            parent::before();

            // Подключение шаблона для представления
            // где example - имя файла (views/template/example.php),
            // а content - имя переменной в example.php, в которую
            // будет помещена область представления
            // автоматически подключает example.css & example.js
            $this->getView()->setViewTemplate('example', 'content');
        }

        function index() {

            $this->getView()->setViewTemplateData('example', 'content');

            // Подключение области представления (views/zones/index.php)
            // автоматически подключает index.css & index.js
            $this->getView()->setViewZone('index')->setViewZoneParam('greetings', 'Hello, World!');


        }

        function after() {
            parent::after();
        }

    }

### Мое первое представление

Например, файл views/template/example.php может выглядет так:

    <!DOCTYPE html>
    <html>
    <head><title>My First App</title></head>
    <body>
        <header></header>
        <?=$content;?>
        <footer></footer>
    </body>
    </html>

А views/zones/index.php:

    <?=$this->getViewZoneParam('greetings');?>

### Вложенные представления

_Дописать_

### Допотопное ORM

Создадим файл models/user.php:

    namespace myapp\models;

    use msmvc\help\record;

    class user extends record {

        static protected $unicKey = 'id';
        static protected $unicType = self::COL_TYPE_INT;
        static protected $tbl_name = 'users';

    }

и в контроллере index.php, например, может делать так:

    namespace myapp\controllers;
    use myapp\models\user;

    class test extends base {

        function index() {

            $user = user::add()
                ->set('name', 'Nikita')
                ->set('surname', 'Orlov');

            $user_id = $user->save();

            // пользователь сохранен, теперь его можно получить из БД
            $user = user::load($user_id);

            // или получить полный список пользователей из БД
            $users = user::get_list();

        }

    }

### Сложное ORM

_Дописать_

### Хуки в ORM

_Дописать_