<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/12/24
 * Time: 12:52
 */

class log{

    public static function write($msg,$level='ERROR',$type=3,$dest=null){

        if(!C('SAVE_LOG')){
            return;
        }else{
            if(is_null($dest)){
                $dest = LOG_PATH . '/' . date('Y-m-d') . ".log";
            }
        }

        if(is_dir(LOG_PATH)){
            error_log("[TIME]:" . date('Y-m-d H:m:s') . "\r\n{$level}:{$msg}" . "\r\n*************\r\n",$type,$dest);
        }
    }
}

?>