<?php
/**
 * Created by PhpStorm.
 * User: soulman
 * Date: 01.04.17
 * Time: 15:06
 */

class NotFoundException extends Exception{
    public function action(){
        header('Location:/404', 'HTTP/1.1 404 Not Found');
        die;
    }
}