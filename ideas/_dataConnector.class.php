<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of mysqlConnect
 *
 * @author atlant_is
 */
class DataConnector {

    //put your code here
    var $db, $error, $query, $affected_rows=0;
    //установки подключения к базе
    var $server = 'localhost';
    //var $server = '127.0.0.1';
    var $user = 'courseuser';
    var $password = 'aH2uR6xI3qhC0l';
    var $dbase = 'coursedb';

    //подключаем
    public function sqlConnect() {
        $this->db = new mysqli();
        $this->db->connect($this->server, $this->user, $this->password, $this->dbase);

        if ($this->db->connect_errno > 0) {
            $this->error = $this->db->connect_error;
            return false;
        }
        $this->db->set_charset("utf8");
        return true;
    }

    public function sqlConnect_old() {
        $this->db = mysqli_connect($this->server, $this->user, $this->password, $this->dbase);
        if (!$this->db || mysqli_connect_errno($this->db) > 0) {
            $this->error = mysqli_connect_error($this->db);
            return false;
        }
        mysqli_set_charset($this->db, "utf8");
        return true;
    }

    //отключаем
    public function sqlClose($db = null) {
        if ($db) {
            $this->db = $db;
        }
        mysqli_close($this->db);
        $this->db = null;
    }

    //возвращаем объект mysqli
    /**
     * Возвращает объект mysqli
     * @param string $query Можно сразу передать текст запроса и он будет выполнен
     * @return object mysqli
     */
    public function sqlQuery($queryText = null) {
        if (!$this->db) {
            if (!$this->sqlConnect()) {  
                $this->error= mysqli_connect_error();
                return FALSE;
            }
        }
        
        //echo $queryText.'fsdfsdf';
        if ($queryText) {
            $this->query=$this->db->query($queryText);
            if ($this->db->errno > 0) {
                $this->error = $this->db->error;
                return FALSE;
            }
            $this->affected_rows=$this->db->affected_rows;
            return $this->query;
        }    
        return $this->db;
    }

}
