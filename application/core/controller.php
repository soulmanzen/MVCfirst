<?php
/**
 * Created by PhpStorm.
 * User: soulman
 * Date: 25.03.17
 * Time: 14:10
 */

class Controller {

    public $model;
    public $view;

    function __construct()
    {
        $this->view = new View();
    }

    // действие (action), вызываемое по умолчанию
    function action_index()
    {
        // todo
    }
}