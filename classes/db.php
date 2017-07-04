<?php
class db{
    public $db = array();
    public $db_error = false;
    public $db_qstr;
    private $host = HOST;
    private $username = USERNAME;
    private $password = PASSWORD;
    private $database = DATABASE;

    /*
    *MySQL connect
    *@returns mysqli link
    */
    public function __construct(){
        $db = new mysqli($this->host, $this->username, $this->password, $this->database);
        if($db->connect_errno){
          throw new Exception('MYSQL DATABASE CONNECTION FAILED.');
        }
        $db->set_charset('utf8');
        return $this->db = $db;
    }
    public function array_utf8_encode($dat){
        return $dat;
    }
    /*
    *MySQL Query
    *@params MySQL query
    *@returns mysqli_result
    *@returns error, set db_error true.
    */
    public function query($query){

        if(!$this->db_error){
            $q = $this->db->query($query);
            if(!$q){
              $this->db_error = true;
              throw new Exception('Problema en la consulta de la BD: '.$this->db->error.'. Consulta original:'.$query);
            }
            $this->db_error = false;
            $this->db_qstr = $query;
            return $q;
        }else{
          return $this->db_error;
        }
        $this->db->close();
    }
    /*
    *Fetch a query (used for single result queries)
    *@params MySQL query
    *@returns array containing row
    *@returns error
    */
    public function fetch($query){
     $q = $this->query($query);
     if(!$this->db_error){
     return $this->array_utf8_encode(mysqli_fetch_array($q));
     }else{
     return $q;
     }
    }
    /*
    *Fetch_assoc (used for multiple result queries)
    *@params MySQL query
    *@returns array containing rows (2-dim array)
    *@returns error
    */
    public function assoc($query){
    $q = $this->query($query);
    $output = array();
        if(!$this->db_error){
        while($row = $q->fetch_assoc()){
        $output[] = $row;
        }
        return $this->array_utf8_encode($output);
        }else{
        return $q;
        }
    }
    /*
    *Insert function
    *@params table_name, array($col=>$value)
    *@returns last insert_id
    *@returns error
    */
    public function i($table, $colsvals){
    $c = array();
    $v = array();
    foreach($colsvals as $col=>$val){
    $c[] = $col;
    if(is_numeric($val)){
    $v[] = $val;
  }else{
    $v[] = '"'.htmlspecialchars($val).'"';

  }
    }
    foreach($c as $k=>$r){
      $c[$k] = '`'.$r.'`';
    }
    $c = implode(', ',$c);
    $v = implode(', ',$v);
    $pq = 'INSERT INTO `'.$table.'` ('.$c.') VALUES ('.$v.')';
    $q = $this->query($pq);
        if(!$this->db_error){
        return $this->db->insert_id;
        }else{
        trigger_error($this->db->error);
        return $q;
        }
    }
    /*
    *Update function
    *@params table_name, array($col=>$value), array($col=>$value) [WHERE $col=$value], (special requirements (string) [AND ID != * p.e])
    *@returns true
    *@returns error
    */
    public function u($table,$colsvals,$col_val, $special = NULL){
    $cv = array();
        foreach($colsvals as $col=>$val){
           $cv[] = $col.'="'.$val.'"';
        }
        foreach($col_val as $col=>$val){
        $w = $col.'="'.$val.'"';
        }
        $w .= ($special ? ' '.$special : '');
        $cv = implode(',',$cv);
        $pq = 'UPDATE '.$table.' SET '.$cv.' WHERE '.$w;
        $q = $this->query($pq);
        if(!$this->db_error){
        return;
        }else{
        return $q;
        }
    }
    /*
    *Insert, on duplicate key update
    *@params table_name, array($col=>$value)
    *@returns insert_id
    *@returns error
    */
    public function ioru($table, $colsvals){
    $c = array();
    $v = array();
    foreach($colsvals as $col=>$val){
    $c[] = $col;
    $v[] = '"'.$val.'"';
    }

    $c = implode(',',$c);
    $v = implode(',',$v);
    $cv = array();
    foreach($colsvals as $col=>$val){
    $cv[] = $col.'="'.$val.'"';
    }
    $cv = implode(',',$cv);
    $pq = 'INSERT INTO '.$table.' ('.$c.') VALUES ('.$v.') ON DUPLICATE KEY UPDATE '.$cv;
    $q = $this->query($pq);
    if(!$this->db_error){
    return $this->db->insert_id;
    }else{
    return $q;
    }
    }
    /*
    *Delete a row
    *@params table_name, array($col=>$value) [WHERE $col=$value]
    */
    public function d($table,$colval){
        foreach($colval as $col=>$val){
            $d = $col.'="'.$val.'"';
        }
        $this->query('DELETE FROM '.$table.' WHERE '.$d);
    }
    /*
    *Checktables
    */
    private function checktables(){
      $tables = array();
      //TABLES

      $tables['meta'] = array(
        'ID int(55) PRIMARY KEY AUTO_INCREMENT',
        'name varchar(255) UNIQUE',
        'value varchar(255)'
      );
      $tables['verifycodes'] = array(
        'ID int(55) PRIMARY KEY AUTO_INCREMENT',
        'code varchar(255) UNIQUE',
        'email varchar(255) UNIQUE',
        'date TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
      );
      $tables['users'] = array(
        'ID int(55) PRIMARY KEY AUTO_INCREMENT',
        'name varchar(255)',
        'email varchar(255) UNIQUE',
        'password varchar(255)',
        'picture varchar(255)',
        'phone varchar(255)',
        'is_verified int(1) DEFAULT 0',
        'SCS varchar(255)',
        'role varchar(255) DEFAULT "user"'
      );
      $tables['admins'] = array(
        'ID int(55) PRIMARY KEY AUTO_INCREMENT',
        'email varchar(255) UNIQUE',
        'keyname varchar(255)',
        'password varchar(255)',
        'created TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
      );
      /*
      foreach($tables as $tablename=>$values){
        $e = $this->query("SHOW TABLES LIKE '".$tablename."'");
        $r = (0 < $e->num_rows) ? true : false;
        if(!$r){
          $st = 'CREATE TABLE '.$tablename.' ('.implode($values,',').')';
          $this->query($st);
        }
      }
      */
    }
}
