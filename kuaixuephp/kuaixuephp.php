<?php

final class kuaixuephp{

    public static function run(){
        self::_set_const();

        if(DEBUG){
            self::_create_dir();
            self::_import_file();
        }else{
            error_reporting(0);
            require TEMP_PATH . '/~boot.php';
        }

        application::run();
    }


    private static function _set_const(){

        $path = str_replace('\\','/',__FILE__);
        //框架目录
        define('KUAIXUE_PATH',dirname($path));
        //核心类和方法目录
        define('CONFIG_PATH', KUAIXUE_PATH . '/config');
        define('DATA_PATH', KUAIXUE_PATH . '/data');
        define('LIB_PATH', KUAIXUE_PATH . '/lib');
        define('CORE_PATH', LIB_PATH . '/core');
        define('FUNCTION_PATH', LIB_PATH . '/function');
        //应用目录及自动生成（APP_PATH，APP_CONFIG_PATH，APP_CONTROLLER_PATH，APP_PUBLIC_PATH）
        define('ROOT_PATH',dirname(KUAIXUE_PATH));
        define('APP_PATH',ROOT_PATH . '/' . APP_NAME);
        define('APP_CONFIG_PATH',APP_PATH . '/config');
        define('APP_CONTROLLER_PATH',APP_PATH . '/controller');
        define('APP_TPL_PATH',APP_PATH . '/tpl');
        define('APP_PUBLIC_PATH',APP_TPL_PATH . '/public');
        //临时目录存放log
        define('TEMP_PATH', ROOT_PATH . '/temp');
        define('LOG_PATH', TEMP_PATH . '/log');
        //smarty编译缓存目录
        define('APP_COMPILE_PATH', TEMP_PATH . '/' . APP_NAME . '/compile');
        define('APP_CACHE_PATH', TEMP_PATH . '/' . APP_NAME . '/cache');
        //提交方式
        define('IS_POST', ($_SERVER['REQUEST_METHOD']) == 'POST' ? true:false);
        if(isset($_SERVER['HTTP_X_REQUEST_WITH']) && $_SERVER['HTTP_X_REQUEST_WITH'] == 'XMLHttpRequest'){
            define('IS_AJAX',true);
        }else{
            define('IS_AJAX',false);
        }
        //公共
        define('COMMON_PATH',ROOT_PATH . '/Common');
        define('COMMON_CONFIG_PATH',COMMON_PATH . '/Config');
        define("COMMON_MODEL_PATH",COMMON_PATH . '/Model');
        define("COMMON_LIB_PATH",COMMON_PATH . '/Lib');
        //框架功能扩展
        define('EXTENDS_PATH',KUAIXUE_PATH . '/extends');
        define('TOOL_PATH',EXTENDS_PATH . '/tool');
        define('ORG_PATH',EXTENDS_PATH . '/org');
    }



    /*
     * 生成前台后台应用文件
     */
    private static function _create_dir(){

        $arr = array(
            //应用
            APP_PATH,
            APP_CONFIG_PATH,
            APP_CONTROLLER_PATH,
            APP_TPL_PATH,
            APP_PUBLIC_PATH,
            //临时
            TEMP_PATH,
            LOG_PATH,
            //common
            COMMON_CONFIG_PATH,
            COMMON_MODEL_PATH,
            COMMON_LIB_PATH,
            //smarty
            APP_COMPILE_PATH,
            APP_CACHE_PATH,
        );

        foreach($arr as $v){
            if(!is_dir($v)){
                mkdir($v,0777,true);
            }
        }

        //加载应用的提示模板
        is_file(APP_TPL_PATH . '/success.html') || copy(DATA_PATH . '/tpl/success.html',APP_TPL_PATH . '/success.html');
        is_file(APP_TPL_PATH . '/error.html') || copy(DATA_PATH . '/tpl/error.html',APP_TPL_PATH . '/error.html');
    }




    private static function _import_file(){

        $filearr = array(
            FUNCTION_PATH . '/function.php',
            //载入smarty
            ORG_PATH . '/smarty/smarty.class.php',
            CORE_PATH . '/smartyview.class.php',

            CORE_PATH . '/controller.class.php',
            CORE_PATH . '/application.class.php',
            CORE_PATH . '/log.class.php',
        );

        /*
         * 将文件合并成一个文件
         * 引入文件
         */
        $str = '';
        foreach ($filearr as $v){
            $str .= trim(substr(file_get_contents($v),5,-2));
            require_once $v;
        }

        $str = "<?php\r\n" . $str;
        file_put_contents(TEMP_PATH . '/~boot.php',$str) || die('access has been denied');
    }
}



kuaixuephp::run();































?>