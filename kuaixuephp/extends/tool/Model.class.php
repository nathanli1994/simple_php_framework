<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/12/27
 * Time: 17:15
 */

class Model{
    //保存连接信息
    public static $link = null;
    //保存表明
    protected $table = null;
    //初始化信息
    private $opt;
    //记录发送的sql
    public static $sqls = array();






    public function __construct($table=null)
    {
        $this->table = is_null($table) ? C('DB_PREFIX') . $this->table : C('DB_PREFIX') . $table;
        $this->_connect();
        $this->_opt();
    }


    //查询条件
    private function _opt(){
        $this->opt = array(
            'filed'=>'*',
            'where'=>'',
            'group'=>'',
            'having'=>'',
            'order'=>'',
            'limit'=>'',
        );
    }


    //数据库连接
    private function _connect(){

        if(is_null(self::$link)){
            $db = C('DB_DATABASE');
            if(empty($db)){
                halt('先配置数据库');
            }
            $link = new mysqli(C('DB_HOST'),C('DB_USER'),C('DB_PASSWORD'),C('DB_DATABASE'),C('DB_PORT'));
            if($link->connect_error){
                halt('数据库连接出错，请检查配置项');
            }
            $link->set_charset(C('DB_CHARSET'));
            //保存连接信息
            self::$link = $link;
        }
    }



    public function query($sql){

        //记录sql语句
        self::$sqls[] = $sql;

        $link = self::$link;
        if($link->errno){
            halt('mysql error:' . $link->error . '<br/>SQL:' . $sql);
        }
        $result = $link->query($sql);

        $rows = array();
        while ($row = $result->fetch_assoc()){
            $rows[] = $row;
        }
        $result->free();
        $this->_opt();
        return $rows;
    }



    public function field($filed){
        $this->opt['filed'] = $filed;
        return $this;
    }

    public function where($where){
        $this->opt['where'] = ' where ' . $where;
        return $this;
    }

    public function order($order){
        $this->opt['order'] = ' order by ' . $order;
        return $this;
    }

    public function limit($limit){
        $this->opt['limit'] = ' limit ' . $limit;
        return $this;
    }


    public function find(){
        $data = $this->limit(1)->all();
        $data = current($data);
        return $data;
    }


    public function all(){

        $sql= "select " . $this->opt['filed'] . " from " . $this->table . $this->opt['where'] . $this->opt['group'] . $this->opt['having'] . $this->opt['order'] . $this->opt['limit'];
        return $this->query($sql);
    }










    public function exe($sql){

        self::$sqls[] = $sql;
        $link = self::$link;
        $bool = $link->query($sql);
        $this->_opt();

        if(is_object($bool)){
            halt('sql语句发送错误');
        }

        if($bool){

            return $link->insert_id ? $link->insert_id : $link->affcted_rows;
        }else{

            halt('mysql错判：' . $link->error . '<br/>SQL:' . $sql);
        }
    }


    public function delete(){
        if(empty($this->opt['where'])){
            halt('不带where将删除所有数据');
        }
        $sql = "delete from " . $this->table . $this->opt['where'];
        return $this->exe($sql);
    }


    public function add($data=null){
        if(is_null($data))  $data = $_POST;
        $fileds = '';
        $values = '';

        foreach ($data as $f=>$v){
            $fileds .= "'" . $this->_safe_str($f) . "',";
            $values .= "'" . $this->_safe_str($v) . "',";
        }
        $fileds = trim($fileds,',');
        $values = trim($values,',');

        $sql = "insert into " . $this->table . '(' . $fileds . ') values (' . $values . ')';
        return $this->exe($sql);
    }




    public function update($data=null){

        if(empty($this->opt['where'])){
            halt('需要写入where条件');
        }
        if(is_null($data)){
            $data = $_POST;
        }

        $values = '';
        foreach ($data as $f=>$v){

            $values .= "'" . $this->_safe_str($f) . "'='" . $this->_safe_str($v) . "',";
        }

        $values = trim($values,',');
        $sql = "update " . $this->table . ' set ' . $values . $this->opt['where'];
        return $this->exe($sql);
    }














    private function _safe_str($str){
        if(get_magic_quotes_gpc()){
            $str = stripcslashes($str);
        }
        return self::$link->real_escape_string($str);
    }
}