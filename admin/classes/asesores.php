<?php
class asesores extends db{
  public $query;
  public $rows;
  public $result;

  public function __construct(){
    parent::__construct();
  }
  public function get($orderby = 'a.usuarioid', $order = 'DESC', $limit = 20, $offset = 0, $search = false){
    $q = 'SELECT a.*, COUNT(b.id_visita) as visitas, b.id_asesor FROM usuarios_representantes as a, visita as b WHERE a.usuarioid = b.id_asesor ';
    if(count($search)){
      $sstr = '';
      foreach($search as $key=>$field){
        $sstr .= ' AND '.$key.' LIKE "%'.$field.'%"';
      }
      $q.= $sstr;
    }
    $q .= ' GROUP BY a.usuarioid';

    $this->query = $q;

    $this->rows = $this->query($q)->num_rows;
    $q .= ' ORDER BY '.$orderby.' '.$order.' LIMIT '.$limit.' OFFSET '.$offset;
    $this->result = $this->assoc($q);
    return $this->result;
  }
  public function getAll(){
    $q = 'SELECT a.* FROM usuarios_representantes as a WHERE usuarioid NOT IN (SELECT id_asesor FROM visita GROUP BY id_asesor) ORDER BY usuarioid DESC';
    $this->query = $q;
    $this->rows = $this->query($q)->num_rows;
    $this->result = $this->assoc($q);
    return $this->result;
  }
  public function get_single($id){
    $q = 'SELECT a.*, COUNT(b.id_visita) as visitas, SUM(TIME_TO_SEC(TIMEDIFF(b.fecha_hora_final, b.fecha_hora_inicio))) / 3600 AS time, b.* FROM usuarios_representantes as a, visita as b WHERE a.usuarioid='.$id.' AND b.id_asesor='.$id;
    $this->query = $q;
    $this->rows = 1;
    $this->result = (object) $this->fetch($q);
    return $this->result;
  }
}
