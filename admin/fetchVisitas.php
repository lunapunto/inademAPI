<?php
require_once 'functions.php';

switch($_SERVER["REQUEST_METHOD"]) {
  case "GET":
  $v = new visitas;
  $g = $_GET;
  $visitas = array();
  $s = $_GET;
  foreach($s as $key=>$em){
    if($em == '""'){
      $s[$key] = 0;
    }
  }
  $s = array_filter($s);
  $search = array();
  if(count($s)){
    foreach($s as $field=>$e){
      $key = '';
      switch($field){
        case "id":
          $key = 'a.usuarioid';
        break;
        case "rfc":
          $key = 'a.rfc';
        break;
        case "folio":
          $key = 'a.numero_folio';
        break;
        case "negocio":
          $key = 'a.nombre_negocio';
        break;
        case "microempresario":
          $key = 'a.nombre';
        break;
        case "giro":
          $key = 'gironame';
        break;
        case "consultor":
          $key = 'idasesor';
        break;
        case "email":
          $key = 'a.usuario';
        break;
      }
      $search[$key] = $e;
    }
  }
  $first_v = $v->get_visitas('a.usuarioid', 'DESC', 0, 9000, $search);

  foreach($first_v as $vis){
    $i = array(
      'id'              => $vis['usuarioid'],
      'negocio'         => san($vis['nombre_negocio']),
      'microempresario' => san($vis['nombre']),
      'email' => $vis['usuario'],
      'password'        => $vis['password'],
      'rfc'             => $vis['rfc'],
      'giro'            => san($vis['gironame']),
      'consultor'       => san($vis['nameasesor']),
      'convocatoria'    => $vis['convocatoria'],
      'folio'           => $vis['numero_folio'],
      'status'          => $vis['status'] ? true : false
    );
    $visitas[] = $i;
  }
  break;
  case "POST":
    $p = $_POST;
    $type = $p['type'];
    if($type == 'update'){
      $i = array(
            'nombre'          => $p['microempresario'],
            'nombre_negocio'  => $p['negocio'],
            'rfc'             => $p['rfc'],
            'numero_folio'    => $p['folio'],
            'status'          => ($p['status'] == 'true') ? 1 : 0,
            'password'        => $p['password'],
            'usuario'         => $p['email'],
            'convocatoria'    => $p['convocatoria']
          );
      $res = $db->u('usuarios_microempresarios', $i, array('usuarioid'=>$p['id']));
    }else if($type == 'delete'){
      $res = $db->d('usuarios_microempresarios', array('usuarioid'=>$p['id']));
    }


  break;
  /*
  case "PUT":
    parse_str(file_get_contents("php://input"), $_PUT);
    $p = $_PUT;
    $i = array(
          'nombre'          => $p['microempresario'],
          'nombre_negocio'  => $p['negocio'],
          'rfc'             => $p['rfc'],
          'numero_folio'    => $p['folio'],
          'status'          => ($p['status'] == 'true') ? 1 : 0,
          'password'        => $p['password']
        );
    $db->u('usuarios_microempresarios', $i, array('usuarioid'=>$p['id']));
  break;
  */

}
header('Content-Type: application/json');
$o = json_encode($visitas);
echo $o;
