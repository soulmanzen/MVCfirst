<?php
/**
 * Created by PhpStorm.
 * User: soulman
 * Date: 25.03.17
 * Time: 14:10
 */
class Model
{
    /**
     * @var PDO $db_connection = DBConnect::connect(). Таким образом через $db_connection-> мы получаем доступ ко всем встроенным функциям PDO. Т.к. в connect() создаётся новый объект  PDO
     */
    private $db_connection;

    /**
     * @var integer (id текущего объекта - соответствует id из бд)
     */
    protected $id;

    /**
     * @var string #Название таблицы модели
     */
    public $table_name;

    /**
     * @var string #Тип запроса
     */
    private $query_type = 'SELECT';

    /**
     * @var $condition #Условие выборки
     */
    private $condition;

    /**
     * @var string (Список полей для запроса, если нужно)
     */
    private $field_list = '*';

    /**
     * @var array (Список доступных полей для записи)
     */
    protected $fields = [];

    /**
     * @var собственно строка запроса типа "SELECT * FROM portfolios WHERE title = 'asd' AND id IN (1, 3) OR id = '3' " устанавливается в функции getQueryString()
     */
    private $query_string;

    /**
     * @var string (Задание лимита для получения определенного количества записей)
     * Пока реализация отсутсвуюет
     */
    private $query_limit;

    /**
     * @var string (Задание сортировки для выборки 'ORDER BY title DESC')
     * Пока реализация отсутсвуюет
     */
    private $query_order;

    /*
     * Инициализируем подключение к бд
     */
    public function __construct()
    {
        $this->db_connection = DBConnect::connect();
    }

    public function getConnection()
    {
        return $this->db_connection;
    }

    /*
     * Строит строку запроса, путем объдинения значений массива 'implode'
     * Возвращает эту строку
     */
    private function getQueryString()
    {
        $this->query_string = implode([
            'query_type' => $this->query_type,
            'field_list' => $this->field_list,
            'from_table' =>
                ($this->query_type == 'SELECT' || $this->query_type == 'DELETE') ? 'FROM' : NULL,
            'table_name' => $this->table_name,
            'condition' => $this->condition,
            'query_order' => $this->query_order,
            'query_limit' => $this->query_limit,
        ], ' ');
        return $this->query_string;
    }

    /*
     * Метод для проверки - новая ли запись
     */
    public function isNew(){
        return !$this->getId();
    }

    /*
   * Публичный метод, для просмотра строки запроса, который на данном этапе
   * будет выполнен(использовать для дебага)
   */
    public function toSql()
    {
        return $this->getQueryString();
    }

    /**
     * @param $field имя колонки, по которому будет выборка
     * @param $param значение ячейки этой колонки
     * @param string $operator AND, OR ...
     * @return $this можно строить паровозик типа $model->where('title', 'asd')->where('id', [1,3])->where('id', 3, 'OR');
     * по сути формирует условие, если оно нужно, путём конкатенирования самого себя с новыми условиями.
     */
    public function where($field, $param, $operator = 'AND')
    {
        // 0. на данном этапе строка запроса выглядит так "SELECT * FROM portfolios"
        $condition = $this->condition ? " $operator" : "WHERE";
        // 1. теперь так "SELECT * FROM portfolios WHERE
        if (is_array($param)) {
            $this->condition .= "$condition $field IN (";
            // в $params соберется строка обработанного массива функцией,
            // которая ставит кавычки вокруг параметра, если он просто строка, и
            // возвращает сам параметр если он был массив.
            $params = implode(array_map(function ($item) {
                return is_string($item) ?
                    $this->db_connection->quote($item) : $item;
            }, $param), ', ');
            $this->condition .= $params;
            $this->condition .= ')';
            // 3. теперь так если добавляются еще условия помимо WHERE и
            // несколько айдишников
            // "SELECT * FROM portfolios WHERE title = 'asd' AND id IN (1, 3) OR id = '3' "
        } else {
            $this->condition .= "$condition $field = '$param'";
            // 2. теперь так "SELECT * FROM portfolios WHERE title = 'asd'
        }
        return $this;
        // сформировав условие, возвращает сам объект с новым условием
    }


    /*
      * Метод find() принимает на вход id нужной записи;
      * Возвращает объект найденой записи
      * или выбрасывает NotFoundException
      * Метод будет импользоваться на странице отображения
      * записи
      *
      */
    public function find($id)
    {
        $this->where('id', $id);
        $prepare = $this->db_connection->prepare($this->getQueryString());
        $prepare->execute();
        if (!$prepare->rowCount()) {
            throw new NotFoundException;
        }
        $prepare->setFetchMode(PDO::FETCH_CLASS, get_class($this));
        $object = $prepare->fetch();
        return $object;
    }

    /*
 * Метод get();
 * Возвращает массив объектов удовлетворяющих
 * критерию выборки;
 * Метод будет использоваться на странице отображения
 * списка записи
 *
 */
    public function get()
    {
        $this->collection = [];
        $result = $this->db_connection->prepare($this->getQueryString());
        $result->execute();
        $this->collection = $result->fetchAll(PDO::FETCH_CLASS, get_class($this));
        return $this->collection;
    }

    /*
     * Публичный метод-геттер для доступа к ID у объекта модели
     */
    public function getId()
    {
        return $this->id;
    }

    /*
     * Метод удаления каждого объекта из базы
     */
    public function delete()
    {
        $this->query_type = 'DELETE';
        $this->field_list = NULL;
        $this->where('id', $this->id);
        $this->db_connection->query($this->getQueryString());
        return $this;
    }

    /*
     * Метод схранения каждого объекта из базы
     */
    public function save()
    {
        var_dump($this->isNew());
        return $this->isNew() ? $this->create() : $this->update();
    }

    public function create(){
        $this->query_type = "INSERT INTO $this->table_name";
        $this->from_table = NULL;
        $this->field_list = '('.implode($this->fields, ', ').')';
        $this->table_name = NULL;
        $this->condition = 'VALUES (' . implode(array_map(function($value){
                return $this->getConnection()->quote($this->$value);
            }, $this->fields), ', ') . ')';
        $this->getConnection()->query($this->getQueryString());
        return $this;
    }

    public function update(){
        $this->query_type = "UPDATE $this->table_name SET ";
        $query_string = array_map(function($value){
            return "$value = {$this->getConnection()->quote($this->$value)}";
        }, $this->fields);
        $this->field_list = implode($query_string, ', ');
        $this->where('id', $this->getId());
        $this->table_name = NULL;
        $this->getConnection()->query($this->getQueryString());
        return $this;
    }

    public function getFields(){
        return get_class_vars('Portfolio');
    }
}