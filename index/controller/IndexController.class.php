<?php

class IndexController extends Controller{
    
    public function index(){

        $obj = new Model('visa');
        $sql = 'select * from visa';
        $res = $obj->query($sql);
        p($res);
    }
}

?>