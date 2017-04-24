<?php
/**
 * Created by PhpStorm.
 * User: soulman
 * Date: 20.04.17
 * Time: 11:07
 */

class Flash
{
    protected static $prefix = 'flash_';
    public static function setMessage($key, $value){
        $key = self::$prefix . $key;
        return !!Session::setValue($key, $value);
    }
    public static function getMessage($key){
        $key = self::$prefix . $key;
        $message = Session::getValue($key);
        Session::deleteValue($key);
        return $message;
    }
}