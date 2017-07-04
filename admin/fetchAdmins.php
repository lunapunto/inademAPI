<?php
require_once 'functions.php';
$asesores = array();
switch($_SERVER["REQUEST_METHOD"]) {
  case "GET":
  $v = new asesores;
  $g = $_GET;
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
        case "email":
          $key = 'a.email';
        break;
        case "nombre":
          $key = 'a.nombre';
        break;
      }
      $search[$key] = $e;
    }
  }
  $first_v = $v->getAdmins('a.usuarioid', 'DESC', 90000, 0, $search);

  foreach($first_v as $vis){
    $i = array(
      'id'              => $vis['usuarioid'],
      'nombre'          => san($vis['nombre']),
      'email'           => $vis['email'],
      'role'            => (int) $vis['role'],
      'status'          => (int) $vis['status']
    );
    $asesores[] = $i;
  }
  break;
  case "POST":
    $p = $_POST;
    $type = $p['type'];

    switch($type){
      case "insert":
      $i = array(
            'nombre'          => $p['nombre'],
            'email'           => $p['email'],
            'usuario'         => $p['usuario'],
            'status'          => $p['status'] == 'true' ? 1 : 0,
            'role'            => $p['role']
          );
      $id = $db->i('usuarios', $i);
      break;
      case "delete":
      $db->query('DELETE FROM usuarios WHERE usuarioid='.$p['id']);
      break;
      case "update":
      $i = array(
            'nombre'          => $p['nombre'],
            'email'           => $p['email'],
            'usuario'         => $p['usuario'],
            'status'          => $p['status'] == 'true' ? 1 : 0,
            'role'            => $p['role']
          );
      $db->u('usuarios', $i, array('usuarioid'=>$p['id']));
      $id = $p['id'];
      break;
    }
  break;
}

header('Content-Type: application/json');
echo json_encode($asesores);
