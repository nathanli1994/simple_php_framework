<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/12/27
 * Time: 19:06
 */
class smartyview{

    private static $smarty = null;


    public function __construct()
    {
        if(!is_null(self::$smarty)) return;

        $smarty = new Smarty();
        //模板目录
        $smarty->template_dir = APP_TPL_PATH . '/' . CONTROLLER . '/';
        //编译目录
        $smarty->compile_dir = APP_COMPILE_PATH;
        //缓存目录
        $smarty->cache_dir = APP_CACHE_PATH;
        $smarty->caching = C('CACHE_ON');
        $smarty->cache_lifetime = C('CACHE_TIME');
        //渲染符号
        $smarty->left_delimiter = C('LEFT_DELIMITER');
        $smarty->right_delimiter = C('RIGHT_DELIMITER');

        self::$smarty = $smarty;
    }


    protected function display($tpl){
        self::$smarty->display($tpl,$_SERVER['REQUEST_URI']);
    }

    protected function assign($var,$value){
        self::$smarty->assign($var,$value);
    }

    protected function is_cached($tpl=null){
        if(!C('SMARTY_ON')){
            halt('使用cache需要开启smarty');
        }else{
            $tpl = $this->get_tpl($tpl);
            return self::$smarty->display($tpl,$_SERVER['REQUEST_URI']);
        }
    }
}