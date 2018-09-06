<?php
/**
 * @Author: Jax Wang
 * @Date:   2018-09-07 01:01:50
 * @Last Modified by:   Jax Wang
 * @Last Modified time: 2018-09-07 01:33:43
 */

spl_autoload_register('Jax_Autoload');

function Jax_Autoload($class_name) {
    $FILE_PATH =  './lib/'. $class_name . '.class.php';
    if(is_file($FILE_PATH) && is_readable($FILE_PATH)){
        require $FILE_PATH;
        return true;
    }
    return false;
}

function __val($obj, $key , $default = ''){
    return isset($obj[$key])?$obj[$key]:$default;
}

function __echo($foobar){
    if(is_array($foobar) || is_object($foobar)){
        print_r($foobar);
    }else{
        echo $foobar."\n";
    }
}

function __sourcelist($key = false){
    $arr = array(
        'qq' => 'QQ音乐',
        'kw' => '酷我音乐',
        'xm' => '虾米音乐',
        'kg' => '酷狗音乐',
        'bd' => '百度音乐',
        'wy' => '网易云音乐'
    );
    if($key){
        return array_keys($arr);
    }else{
        return $arr;
    }
}

function __killslash($str){
    return str_replace(array('/','\\'), '' , strip_tags(html_entity_decode($str, ENT_QUOTES|ENT_HTML5)));

}

function whoami(){
    exec('whoami',$res);
    return $res[0];
}
