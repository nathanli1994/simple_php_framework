<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/12/23
 * Time: 13:19
 */

class controller extends smartyview {

    public function __construct(){

        if(C('SMARTY_ON')){
            parent::__construct();
        }
        /*
         * 子类通过命名__init，来定义子类构造函数
         * 孙子类通过命名__auto，来定义孙子类构造函数
         */
        if(method_exists($this,'__init')){
            $this->__init();
        }
        if(method_exists($this,'__auto')){
            $this->__auto();
        }
    }

    protected function success($msg,$url=null,$time=3){
        $url = $url ? "window.location.href ='" . $url . "'" : 'window.history.back(-1)';
        include APP_TPL_PATH . '/success.html';
    }


    protected function error($msg,$url=null,$time=3){
        $url = $url ? "window.location.href ='" . $url . "'" : 'window.history.back(-1)';
        include APP_TPL_PATH . '/error.html';
    }





    private $var = array();
    protected function assign($var,$value){

        if(C('SMARTY_ON')){
            parent::assign($var,$value);
        }else{
            $this->var[$var] = $value;
        }
    }




    protected function get_tpl($tpl){
        if(is_null($tpl)){
            $path = APP_TPL_PATH . '/' . CONTROLLER . '/' . ACTION . '.html';
        }else{
            $suffix = strrchr($tpl,'.');
            $tpl = empty($suffix) ? $tpl . '.html' : $tpl;
            $path = APP_TPL_PATH . '/' . CONTROLLER . '/' . $tpl;
        }
        return $path;
    }




    protected function display($tpl=null){

        $this->get_tpl($tpl);
        if(!is_file($path)){
            halt($path . '模板不存在');
        }

        if(C('SMARTY_ON')){
            parent::display($path);
        }else{
            //提取数组中的键值对
            extract($this->var);
            include $path;
        }
    }



}
?>