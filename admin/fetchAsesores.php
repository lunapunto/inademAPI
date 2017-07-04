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
  $first_v = $v->get('a.usuarioid', 'DESC', 90000, 0, $search);

  foreach($first_v as $vis){
    $i = array(
      'id'              => $vis['usuarioid'],
      'nombre'          => san($vis['nombre']),
      'email'           => $vis['email'],
      'usuario'         => $vis['email'],
      'visitas'         => $vis['visitas'],
      'password'        => $vis['password'],
      'status'          => $vis['status'] ? true : false,
      'is_logged'       => $vis['islogged'] ? true : false
    );
    $asesores[] = $i;
  }
  break;
  case "POST":
    $p = $_POST;
    $i = array(
          'nombre'          => $p['nombre'],
          'email'           => $p['email'],
          'status'          => $p['status'] == 'true' ? 1 : 0,
          'islogged'        => $p['is_logged'] == 'true' ? 1 : 0,
          'password'        => $p['password']
        );
    $db->u('usuarios_representantes', $i, array('usuarioid'=>$p['id']));
  break;
  /*
  case "PUT":
    parse_str(file_get_contents("php://input"), $_PUT);
    $p = $_PUT;
    $i = array(
          'nombre'          => $p['nombre'],
          'email'           => $p['email'],
          'status'          => $p['status'] == 'true' ? 1 : 0,
          'islogged'        => $p['is_logged'] == 'true' ? 1 : 0
        );
    $db->u('usuarios_representantes', $i, array('usuarioid'=>$p['id']));
  break;
  case "POST":
    $p = $_POST;
    $i = array(
          'nombre'          => $p['nombre'],
          'email'           => $p['email'],
          'usuario'         => $p['email'],
          'status'          => $p['status'] == 'true' ? 1 : 0,
          'is_logged'       => $p['islogged'] == 'true' ? 1 : 0,
        );
    $id = $db->i('usuarios_representantes', $i);
    $r = $db->fetch('SELECT * FROM usuarios_representantes WHERE usuarioid='.$id);
    $asesores = array(
                      'id'              => $id,
                      'nombre'          => $p['nombre'],
                      'email'           => $p['email'],
                      'status'          => $p['status'] == 'true' ? 1 : 0
                      );
  break;
  */
}


header('Content-Type: application/json');
echo json_encode($asesores);
