<?php

class microempresario extends db{
  public $query;
  public $rows;
  public $rawquery;
  public $result;
  public $info;
  public $id;
  public function __construct($id){
    parent::__construct();
    $q = 'SELECT * FROM usuarios_microempresarios WHERE usuarioid='.$id;
    $this->rawquery = $q;
    $this->query = $q;
    $this->rows = 1;

    $info = $this->fetch($q);
    $info = (object) $info;
    $this->info = $info;
  }
}

class micros extends db{
  public $query;
  public $rows;
  public $result;

  public function __construct(){
    parent::__construct();
  }
  public function get($orderby = 'a.usuarioid', $order = 'DESC', $limit = 20, $offset = 0, $search = false){
    $q = 'SELECT a.usuarioid,a.status, a.nombre, a.convocatoria, SUM(TIME_TO_SEC(TIMEDIFF(b.fecha_hora_final, b.fecha_hora_inicio))) / 3600 AS time FROM usuarios_microempresarios as a, visita as b WHERE b.id_negocio = a.usuarioid '.($search ? 'AND CONCAT(a.nombre,a.email) LIKE "%'.$search.'%"' : '' ).' GROUP BY a.usuarioid';
    $this->query = $q;
    $this->rows = $this->query($q)->num_rows;
    $q .= ' ORDER BY '.$orderby.' '.$order.' LIMIT '.$limit.' OFFSET '.$offset;
    $this->result = $this->assoc($q);
    return $this->result;
  }

}
