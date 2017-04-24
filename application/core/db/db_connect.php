<?php
/**
 * Created by PhpStorm.
 * User: soulman
 * Date: 05.04.17
 * Time: 19:18
 */


class DBConnect{

    private static $connect;

    protected $db_user;

    protected $db_password;

    protected $db_host;

    protected $db_port;

    protected $db_name;

    // пример паттерна синглтон, конструктор в приват, чтоб нельзя было создать экземпляр класса из вне. Обращаться к нему только через статичный метод connect(), который вызывает статичную переменную $connect, в которую, если она пустая, записывается метод подключения к БД и потом вызывается.
    private function __construct()
    {
//        var_dump(realpath(__DIR__.'/../../config/database.php'));
//        die();
        $config = (include_once realpath(__DIR__.'/../../config/database.php'));
        $this->db_user = $config['db_username'];
        $this->db_password = $config['db_user_password'];
        $this->db_name = $config['db_name'];
        $this->db_host = $config['db_host'];
        $this->db_port = $config['db_port'];
    }

    public function getConnect()
    {
        try{
            //класс PDO для создания подключения к БД 3 атрибута, со спец синтаксисом после указания типа БД и : charset=utf8 - для русского шрифта в БД и браузере.
            $app_connect = new PDO(
                "mysql:dbname=$this->db_name;host=$this->db_host;charset=utf8",
                $this->db_user, $this->db_password);

            return $app_connect;
            // PDO сам выбрасывает ошибку в случае неуд, нам только надо её поймать.
        } catch (PDOException $e){
            echo $e->getMessage();
        }

    }

    static public function connect()
    {
        if (empty(self::$connect)) {
            $connect = new self;
            return self::$connect = $connect->getConnect();
        }
        return self::$connect;

    }

//        $connect = mysqli_connect('localhost', $config['user'], $config['password']);
//
//        if(!$connect){
//            die('Ошибка подключения: '. mysqli_connect_error($connect));
//        }
//
//
//        return mysqli_select_db($connect, 'feed');

}