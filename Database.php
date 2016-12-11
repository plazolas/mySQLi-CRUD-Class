<?php

Class Database implements Cruds {

    public  $db;
    private $_server = '';
    private $_user = '';
    private $_password = '';
    private $_database = '';
    private $_config = '';

    function __construct() {
        if(file_exists(__DIR__.'../config/config.php')){
            $config = require __DIR__ . '/../config/config.php';
            print_r($config);exit;
        } else {
            trigger_error('Missing config.php file: ', E_USER_ERROR);
        }
        
        
            $this->_server   = config['server'];
            $this->_user     = config['user'];
            $this->_password = config['password'];
            $this->_database = config['database'];
        
        if (!is_object($this->db)) {
            $mysqli = new mysqli($this->_server, $this->_user, $this->_password, $this->_database);
            if ($mysqli->connect_error) {
                trigger_error('Database connection failed: ' . $mysqli->connect_error, E_USER_ERROR);
                return false;
            }
            $this->db = $mysqli;
            return $this->db;
        } else {
            return $this->db;
        }
    }

    public function close() {
        $this->db->close();
        unset($this->db);
    }
    
    public function create(stdClass $Obj) {
        if (!is_object($Obj)) {
            trigger_error(__METHOD__ . ' Invalid input expecting stdClass object: ', E_USER_WARNING);
            return false;
        }
        if (count(get_object_vars($Obj)) != count($this->fields)) {
            trigger_error(__METHOD__ . ' Invalid number of fields to insert: ', E_USER_ERROR);
            return false;
        }
        $sql = "INSERT INTO `{$this->table_name}` (";
        foreach ($this->fields as $field) {
            $sql .= $field . ",";
        }
        $sql = substr($sql, 0, -1);
        $sql .= ") VALUES (";
        foreach ($Obj as $key => $value) {
            $sql .= "'" . $value . "',";
        }
        $sql = substr($sql, 0, -1);
        $sql .= ")";
        $result = $this->db->query($sql);
        if ($result === false) {
            trigger_error(__METHOD__ . ' DB ERROR query: ', E_USER_ERROR);
            return false;
        } else {
            return $this->db->insert_id;
        }
    }

    public function set (stdClass $Obj){
        if(!is_object($Obj) || $Obj->id == '' || $Obj->id == 0) {
            trigger_error(__METHOD__ . ' Invalid input expecting stdClass object: ' , E_USER_WARNING);
            return false;
        }
        $sql = "UPDATE `{$this->table_name}` SET ";
        foreach ($Obj as $key => $value){
            if($key == 'id') continue;
            $sql .= " `".$key."` = '".$value."' ,";
        }
        $sql = substr($sql, 0, -1);
        $sql .= " WHERE `id` = ".$Obj->id;
        $result = $this->db->query($sql);
        if($result === false) {
            trigger_error(__METHOD__ . ' DB ERROR msqli query on update: ' , E_USER_ERROR);
            return false;
        }
    }

    public function delete($id) {
        if ($id == 0 || $id == '') {
            return true;
        }
        $sql = "DELETE FROM `{$this->table_name}` WHERE `id` = " . $id;
        $result = $this->db->query($sql);
        if ($result == false) {
            trigger_error(__METHOD__ . 'ERROR mysqli query : ' . $sql, E_USER_ERROR);
            return false;
        } else {
            return true;
        }
    }

    public function get($id) {
        if ($id != '' && strlen($id) > 0 && $id != 0) {
            $sql = "SELECT * FROM `{$this->table_name}` WHERE `id` = '" . $id . "'";
            $result = $this->db->query($sql);
            if ($result == false) {
                trigger_error(__METHOD__ . 'ERROR mysqli query : ' . $sql, E_USER_ERROR);
                return false;
            }
            return $result->fetch_assoc();
        } else {
            trigger_error(__METHOD__ . 'ERROR Invalid input : ', E_USER_WARNING);
            return false;
        }
    }

    public function getCollection (){        
            $sql = "SELECT * FROM `{$this->table_name}` ORDER BY id DESC";
            $result = $this->db->query($sql);
            if($result === false) {
                trigger_error(__METHOD__ . 'ERROR mysqli query : '.$sql , E_USER_ERROR);
                return false;
            }
            $rows = array();
            while ($row = $result->fetch_assoc() ) {
                $rows[] = $row;
            }
            return $rows;
    }

}
