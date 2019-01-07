<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/12/23
 * Time: 1:32
 */

/*
 * 打印函数
 */
function p($arr){

    if(is_array($arr)){
        var_dump($arr);
    }elseif (is_null($arr)){
        var_dump(null);
    }else{
        print_r($arr,true);
    }
}



/*
 * 跳转函数
 */
function go($url,$time=0,$msg=''){

    if(!headers_sent()){
        $time = 0 ? header('Location:' . $url) : header("refresh:{$time};url={$url}");
        die($msg);
    }else{
        echo "<meta http-equiv='Refresh' content='{$time};URL={$url}}'>";
        if($time){
            die($msg);
        }
    }
}




/*
 * 错误捕获
 */
function halt($error,$level='ERROR',$type=3,$dest=null){

    if(is_array($error)){
        log::write($error['message'],$level,$type,$dest);
    }else{
        log::write($error,$level,$type,$dest);
    }

    $e = array();
    if(DEBUG){
        if(!is_array($error)){
            $trace = debug_backtrace();
            $e['message'] = $error;
            $e['file'] = $trace[0]['file'];
            $e['line'] = $trace[0]['line'];
            $e['class'] = isset($trace[0]['class']) ? $trace[0]['class'] : '';
            $e['function'] = isset($trace[0]['function']) ? $trace[0]['function'] : '';

            //开启缓冲区
            ob_start();
            debug_print_backtrace();
            $e['trace'] = htmlspecialchars(ob_get_clean());
        }else{
            $e = $error;
        }
    }else{
        if($url = C('ERROR_URL')){
            go($url);
        }else{
            $e['message'] = C('ERROR_MSG');
        }
    }

    include DATA_PATH . '/tpl/halt.html';
}






/*
 * 配置项操作函数
 */
function C($var = NULL, $value = NULL){

    static $config = array();

    if(is_array($var)){
        $config = array_merge($config,array_change_key_case($var,CASE_UPPER));
        return;
    }

    if(is_string($var)){
        $var = strtoupper($var);
        if(!is_null($value)){
            $config[$var] = $value;
            return;
        }

        return isset($config[$var]) ? $config[$var] : null;
    }

    if(is_null($var) && is_null($value)){
        return $config;
    }
}



function M($table){
    $obj = new Model($table);
    return $obj;
}
?>