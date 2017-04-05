<?php
/**
 * Created by PhpStorm.
 * User: soulman
 * Date: 05.04.17
 * Time: 17:41
 */

class Controller404 extends Controller{
    function index(){
        $this->view->generate('404_view.php', 'template_view.php');
    }
}