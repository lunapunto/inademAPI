<?php
class visitas extends db{
  public $query;
  public $rows;
  public $rawquery;
  public $result;
  public function __construct(){
    parent::__construct();
  }
  /*
  * @params orderby = [time, a.id_negocio, b.nombre]
  */
  public function get_visitas($orderby = 'a.id_negocio', $order = 'DESC', $offset = 0, $limit = 50, $search = false){
        $q = 'SELECT a.usuarioid, a.usuario, a.rfc, a.convocatoria, a.numero_folio, a.nombre_negocio, a.nombre, a.password, a.giroid, a.status, IF(EXISTS(SELECT  idAsesor FROM trasesormicroempresario WHERE idMicroNegocio=a.usuarioid), (SELECT  idAsesor FROM trasesormicroempresario WHERE idMicroNegocio=a.usuarioid), 0) as idasesor,IF(EXISTS(SELECT nombre FROM usuarios_representantes WHERE usuarioid=idasesor), (SELECT nombre FROM usuarios_representantes WHERE usuarioid=idasesor), "") as nameasesor , IF(EXISTS(SELECT giroid FROM cat_giro WHERE giroid=a.giroid),(SELECT descripcion FROM cat_giro WHERE giroid=a.giroid),"") as gironame FROM usuarios_microempresarios as a WHERE a.usuarioid > 0';
      if(count($search)){
        $sstr = '';
        foreach($search as $key=>$field){
          if($key == 'idasesor'){
            break;
          }
          $sstr .= ' AND '.$key.' LIKE "%'.$field.'%"';
        }
        $q.= $sstr;
      }
      // Having Clause
      $hx = 0;
      if(count($search)){
        $sstr = '';

        foreach($search as $key=>$field){

          if($key == 'idasesor' && 0 < $field){
            if(!$hx){
              $sstr .= ' HAVING ';
            }
            if($hx){
              $sstr = ' ';
            }
            $sstr .= $key.' LIKE "%'.$field.'%"';
            $hx++;
          }
        }
        $q.= $sstr;
      }
      $this->rawquery = $q;
      $this->rows = $this->query($q)->num_rows;
      $q .= ' ORDER BY '.$orderby.' '.$order.' LIMIT '. $limit.' OFFSET '.$offset;

      $this->query = $q;
      $r = $this->assoc($q);

      $this->result = $r;
      return $this->result;

  }

  /*
  * @params id (int)
  */
  public function get_visitas_from_microempresario($id, $orderby='fecha_hora_final', $order='DESC', $limit = 20, $offset = 0){
    $q = 'SELECT *, (TIME_TO_SEC(SUM(TIMEDIFF(fecha_hora_final, fecha_hora_inicio))) / 3600) AS time FROM visita WHERE id_negocio='.$id. ' GROUP BY id_visita';
    $this->rawquery = $q;
    $this->rows = $this->query($q)->num_rows;
    $q .= ' ORDER BY '.$orderby.' '.$order.' LIMIT '.$limit.' OFFSET '.$offset;

    $this->query = $q;
    $this->result = $this->assoc($q);
    return $this->result;
  }


  public function get_visitas_from_asesor($id, $orderby='fecha_hora_final', $order='DESC', $limit = 120, $offset = 0){
    $q = 'SELECT * FROM visita WHERE id_asesor='.$id;
    $this->rawquery = $q;
    $this->rows = $this->query($q)->num_rows;
    $q .= ' ORDER BY '.$orderby.' '.$order.' LIMIT '.$limit.' OFFSET '.$offset;

    $this->query = $q;
    $this->result = $this->assoc($q);
    return $this->result;
  }
  public function get_asesor_last_visita($id){
    $q  = 'SELECT fecha_hora_final FROM visita WHERE id_asesor='.$id.' ORDER BY fecha_hora_final DESC LIMIT 1';
    $this->query = $q;
    $this->rawquery = $q;
    $this->rows = 1;
    $this->result = $this->fetch($q)['fecha_hora_final'];
    return $this->result;
  }
  public function get_visitas_timetotal($id, $field = 'negocio'){
    $q = 'SELECT id_negocio, (TIME_TO_SEC(SUM(TIMEDIFF(fecha_hora_final, fecha_hora_inicio))) / 3600) as time FROM visita WHERE id_'.$field.'='.$id;
    $this->query = $q;
    $this->rawquery = $q;
    $this->rows = 1;
    $this->result = $this->fetch($q)['time'];
    return $this->result;
  }

  public function get_visita_timetotal($idvisita){
    $q = 'SELECT (TIME_TO_SEC(SUM(TIMEDIFF(fecha_hora_final,fecha_hora_inicio))) / 3600) as time FROM visita WHERE id_visita='.$idvisita;
    $this->query = $q;
    $this->rawquery = $q;
    $this->rows = 1;
    $this->result = $this->fetch($q)['time'];
    return $this->result;
  }
  public function get_last_visita($idnegocio){
    $q = 'SELECT fecha_hora_inicio FROM visita WHERE id_negocio='.$idnegocio.' ORDER BY fecha_hora_inicio DESC LIMIT 1';
    $this->query = $q;
    $this->rawquery = $q;
    $this->rows = 1;
    $this->result = $this->fetch($q)['fecha_hora_inicio'];
    $this->result = pdate($this->result, false);
    return $this->result;
  }

  public function get_visita($id){
    $q = 'SELECT *, (TIME_TO_SEC(SUM(TIMEDIFF(fecha_hora_final,fecha_hora_inicio))) / 3600) as time FROM visita WHERE id_visita='.$id;
    $this->query = $q;
    $this->rawquery = $q;
    $this->rows = 1;
    $this->result = $this->fetch($q);
    return $this->result;
  }

}
