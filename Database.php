<?php
//namespace Model;

require_once __DIR__.'/CrudsInterface.php';
require_once __DIR__.'/Config.php';

Class Database implements CrudsInterface {

    public  $db;

    function __construct() {
        $this->open();
    }

    public function open() {
        if (!is_object($this->db)) {
            $config = new Config();
            //echo $config->server." ".$config->user." ".$config->password." ".$config->database." ";exit;
            $mysqli = new mysqli($config->server, $config->user, $config->password, $config->database);
            if ($mysqli->connect_error) {
                error_log('Database connection failed: ' . $mysqli->connect_error);
                return false;
            }
            $this->db = $mysqli;
        }
    }
    
    public function close() {
        $this->db->close();
        unset($this->db);
    }
    
    public function create(stdClass $Obj) {
        if (!is_object($Obj)) {
            error_log(__METHOD__ . ' Invalid input expecting stdClass object: ');
            return false;
        }
        if (count(get_object_vars($Obj)) != count($this->fields)) {
            error_log(__METHOD__ . ' Invalid number of fields to insert: '.print_r($Obj,true));
            return false;
        }
        $sql = "INSERT INTO `{$this->table_name}` (";
        foreach ($Obj as $field => $value) {
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
            error_log(__METHOD__ . ' DB ERROR query: '.$sql);
            return false;
        } else {
            return $this->db->insert_id;
        }
    }

    public function set (stdClass $Obj){
        if(!is_object($Obj) || $Obj->id == '' || $Obj->id == 0) {
            error_log(__METHOD__ . ' Invalid input expecting stdClass object: ' );
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
            error_log(__METHOD__ . ' DB ERROR msqli query on update: ' );
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
            error_log(__METHOD__ . 'ERROR mysqli query : ' . $sql);
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
                error_log(__METHOD__ . 'ERROR mysqli query : ' . $sql);
                return false;
            }
            return $result->fetch_assoc();
        } else {
            error_log(__METHOD__ . 'ERROR Invalid input : ');
            return false;
        }
    }

    public function get_all (){        
            $sql = "SELECT * FROM `{$this->table_name}` ORDER BY id DESC";
            $result = $this->db->query($sql);
            if($result === false) {
                error_log(__METHOD__ . 'ERROR mysqli query : '.$sql );
                return false;
            }
            $rows = array();
            while ($row = $result->fetch_assoc() ) {
                $rows[] = $row;
            }
            return $rows;
    }
    
    public function getCollection (){        
            $rows = $this->get_all();
            return $rows;
    }

}
