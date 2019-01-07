<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/12/23
 * Time: 1:34
 */

final class application{

    public static function run(){

        self::_init();
        self::_user_import();
        self::_set_url();
        spl_autoload_register(array(__CLASS__,'_autoload'));//注册自动载入函数，并对类执行注册的方法
        set_error_handler(array(__CLASS__,'error'));
        register_shutdown_function(array(__CLASS__,'fatal_error'));
        self::_create_demo();
        self::_app_run();
    }



    /*
     * 运行c控制器下的a方法
     */
    private static function _app_run(){

        $c = isset($_GET[C('VAR_CONTROLLER')]) ? $_GET[C('VAR_CONTROLLER')] : 'Index';
        $c .= 'Controller';

        $a = isset($_GET[C('VAR_ACTION')]) ? $_GET[C('VAR_ACTION')] : 'index';

        define("CONTROLLER",$c);
        define("ACTION",$a);

        if(class_exists($c)){
            $obj = new $c();//触发_autoload函数，并把$c对应的类作为参数传递
            if(!method_exists($obj,$a)){
                if(method_exists($obj,'__empty')){
                    $obj->__empty();
                }else{
                    halt($c . '控制器下的' . $a . '方法不存在');
                }
            }else{
                $obj->$a();
            }
        }else{
            $obj = new EmptyController();
            $obj->index();
        }
    }

    /*
     * 当一个类未找到时，就会自动触发这个函数
     */
    private static function _autoload($classname){

        switch (true){
            case strlen($classname) >10 && substr($classname,-10) == 'Controller':
                $path = APP_CONTROLLER_PATH . '/' . $classname . '.class.php';
                if(!is_file($path)){
                    $emptyPath = APP_CONTROLLER_PATH . '/EmptyController.class.php';
                    if(is_file($emptyPath)){
                        include $emptyPath;
                        return;
                    }else{
                        halt($path . '控制器没找到');
                    }
                }
                include $path;
                break;

            case strlen($classname) > 5 && substr($classname,-5) == 'Model':
                $path = COMMON_MODEL_PATH . '/' . $classname . '.class.php';
                include $path;
                break;


            default:
                $path = TOOL_PATH . '/' . $classname . '.class.php';
                if(!is_file($path)){
                    halt($path . '类没找到');
                }
                include $path;
        }

    }



    public static function error($errorno,$error,$file,$line){

        switch ($errorno){
            case E_ERROR:
            case E_PARSE:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
            case E_CORE_ERROR:
                $msg = $error . $file . "第{$line}行";
                halt($msg);
                break;

            case E_STRICT:
            case E_USER_WARNING:
            case E_USER_NOTICE:
            default:
                if(DEBUG){
                    include DATA_PATH . '/tpl/notice.html';
                }
        }
    }




    public static function fatal_error(){

        if($e = error_get_last()){
            self::error($e['type'],$e['message'],$e['file'],$e['line']);
        }
    }






    private static function _create_demo(){

        $path = APP_CONTROLLER_PATH . '/IndexController.class.php';
        $str = <<<str
<?php

class IndexController extends Controller{
    
    public function index(){
        
        echo 'OK';
    }
}

?>
str;
        is_file($path) || file_put_contents($path,$str);

    }




    /*
     * 框架初始化,加载配置项
     */
    private static function _init(){

        //初始化配置项
        C(include CONFIG_PATH . '/config.php');

        //为应用加载公共配置项
        $commonpath = COMMON_CONFIG_PATH . '/config.php';
        $commonconfig = <<<str
<?php

return array(
//配置项   =>  值,

);

?>
str;
        is_file($commonpath) || file_put_contents($commonpath,$commonconfig);
        C(include $commonpath);

        //用户配置项最后加载
        $userpath = APP_CONFIG_PATH . '/config.php';
        $userconfig = <<<str
<?php

return array(
//配置项   =>  值,

);

?>
str;

        is_file($userpath) || file_put_contents($userpath,$userconfig);
        C(include $userpath);

        date_default_timezone_set(C('DEFAULT_TIME_ZONE'));
        C('SESSION_AUTO_START') && session_start();
    }



    /*
     * 设置应用之间的路径
     */
    private static function _set_url(){

        $path = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
        $path = str_replace('\\','/',$path);

        define('__APP__',$path);
        define('__ROOT__',dirname($path));
        define('__TPL__',__ROOT__ . '/' . APP_NAME . '/tpl');
        define('__PUBLIC__',__TPL__ . '/public');
    }




    /*
     * 加载用户定义方法
     */
    private static function _user_import(){

        $fileArr = C("AUTO_LOAD_FILE");

        if(is_array($fileArr) && !empty($fileArr)){
            foreach($fileArr as $v){
                require_once COMMON_LIB_PATH . '/' . $v;
            }
        }
    }






}
?>