<?php
/**
 * Created by PhpStorm.
 * User: soulman
 * Date: 01.04.17
 * Time: 14:34
 */

class ControllerPortfolio extends Controller
{
    function __construct()
    {
        $this->model = new Portfolio();
        parent::__construct();
    }


    function index()
    {
        /**
         * $data = здесь лежит массив строк из БД, который потом будет аккуратно
         * раскладываться во вьюшке в таблицу списка товаров или услуг
         */
        $data = $this->model->get();
        $this->view->generate('portfolio_view.php', 'template_view.php', $data);
    }
}