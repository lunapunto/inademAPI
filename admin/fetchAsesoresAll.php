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
  $first_v = $v->getAll('a.usuarioid', 'DESC', 90000, 0, $search);

  foreach($first_v as $vis){
    $i = array(
      'id'              => $vis['usuarioid'],
      'nombre'          => san($vis['nombre']),
      'email'           => $vis['email'],
      'conv_1'          => i('conv_1', $vis),
      'conv_2'          => i('conv_2', $vis),
      'conv_3'          => i('conv_3', $vis),
      'conv_4'          => i('conv_4', $vis),
      'conv_5'          => i('conv_5', $vis),
      'conv_6'          => i('conv_6', $vis),
      'conv_7'          => i('conv_7', $vis),
      'password'        => $vis['password'],
      'islogged'        => $vis['islogged'] ? true : false,
      'status'          => $vis['status']
    );
    $asesores[] = $i;
  }
  break;
  case "POST":
    $p = $_POST;
    $type = $p['type'];

    switch($type){
      case "insert":
      $convid = $p['convocatoria'];
      $i = array(
            'nombre'          => $p['nombre'],
            'email'           => $p['email'],
            'usuario'         => $p['usuario'],
            'password'        => $p['password'],
            'status'          => 1
          );
      $id = $db->i('usuarios_representantes', $i);
      $r = $db->fetch('SELECT * FROM usuarios_representantes WHERE usuarioid='.$id);
      file_get_contents('http://www.descifrainadem.mx/Auxiliares/EnviarCertificadoConsultor.ashx?Id='.$id);
      for($m = 1; $m<=7; $m++){
        $mp = $p['conv_'.$m];
        if($mp == 'true'){
          $db->i('relacionconvrepre', array('idRepresentante'=>$id, 'idConvocatoria'=>$m));
        }
      }
      break;
      case "delete":
      $db->query('DELETE FROM relacionconvrepre WHERE idRepresentante='.$p['id']);
      $db->query('DELETE FROM usuarios_representantes WHERE usuarioid='.$p['id']);
      break;
      case "update":
      $i = array(
            'nombre'          => $p['nombre'],
            'email'           => $p['email'],
            'password'        => $p['password'],
            'islogged'        => $p['islogged'] == 'true' ? 1 : 0,
            'status'          => $p['status'] == 'true' ? 1 : 0,
          );
      $db->u('usuarios_representantes', $i, array('usuarioid'=>$p['id']));
      $id = $p['id'];
      for($m = 1; $m<=7; $m++){
        $mp = $p['conv_'.$m];
        if($mp == 'true'){
          $db->i('relacionconvrepre', array('idRepresentante'=>$id, 'idConvocatoria'=>$m));
        }else{
          $qu = 'DELETE FROM relacionconvrepre WHERE idRepresentante='.$id.' AND idConvocatoria='.$m;
          $db->query($qu);
        }
      }
      break;
    }
  break;
}

header('Content-Type: application/json');
echo json_encode($asesores);
