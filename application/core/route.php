<?php
/**
 * Created by PhpStorm.
 * User: soulman
 * Date: 25.03.17
 * Time: 14:33
 */

/*
Класс-маршрутизатор для определения запрашиваемой страницы.
> цепляет классы контроллеров и моделей;
> создает экземпляры контролеров страниц и вызывает действия этих контроллеров.
*/
class Route
{
    static function start()
    {

            $routes = explode('/', $_SERVER['REQUEST_URI']);
            // получаем имя контроллера
            $controller_name = !empty($routes[1]) ? $routes[1] : 'Main';
            // получаем имя экшена
            $action_name = !empty($routes[2]) ? $routes[2] : 'index';

            // добавляем префиксы
            $model_name = $controller_name;
            $controller_class_name = 'Controller' . ucfirst($controller_name);
            $controller_name = 'controller_' . $controller_name;
            /*
            echo "Model: $model_name <br>";
            echo "Controller: $controller_name <br>";
            echo "Action: $action_name <br>";
            */
            // подцепляем файл с классом модели (файла модели может и не быть)
            $model_file = strtolower($model_name) . '.php';
            $model_path = "../application/models/" . $model_file;

            if (file_exists($model_path)) {
                require_once $model_path;
            }

//             тест модели
//            $model = new $model_name;
//            $model->where('title', 'asd')->where('id', [1,3])->where('id', 3, 'OR');
//        var_dump($model->toSql());

//            $model = new Portfolio();
//            $portfolio = $model->find(2);
//            $portfolio->setTitle('Title')->setYear(2020);
//            echo "<pre>";
//            var_dump($portfolio->save());
//            die;

        var_dump(
            Session::setValue('key', 123),
            Session::getValue('key'),
            Session::existsKey('key'),
            Session::deleteValue('key'),
            Session::existsKey('key')
        );
        die;

            // подцепляем файл с классом контроллера
            $controller_file = strtolower($controller_name) . '.php';
            $controller_path = "../application/controllers/" . $controller_file;
            if (!file_exists($controller_path)) {
                throw new NotFoundException();
            } else {
                require_once $controller_path;
            }

            // создаем контроллер
            $controller = new $controller_class_name;
            $action = $action_name;

            if (!method_exists($controller, $action)) {
                throw new NotFoundException();
            } else {
                // вызываем действие контроллера
                $controller->$action();
            }

        }


//    function ErrorPage404()
//    {
//        $host = 'http://'.$_SERVER['HTTP_HOST'].'/';
//        header('HTTP/1.1 404 Not Found');
////        header("Status: 404 Not Found");
//        header('Location:'.$host.'404.php');
//    }

}