<?php
/**
 * Created by PhpStorm.
 * User: soulman
 * Date: 01.04.17
 * Time: 12:37
 */

class ControllerMain extends Controller{
    function index(){
        $this->view->generate('main_view.php', 'template_view.php');
    }
}