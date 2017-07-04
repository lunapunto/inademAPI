<?php
require_once '../global/funcs.php';
require_once '../classes/db.php';
require_once '../classes/asesores.php';
require_once '../classes/microempresario.php';
require_once '../classes/users.php';
require_once '../classes/visitas.php';
use \Eventviva\ImageResize;
define('admin', dir.'/admin');
define('adminasset', dir.'/assets/admin');

$db = new db;

function create_admin($arr, $login = false){
  global $db;
  $fields = array(
            'nombre'   => $arr['name'],
            'usuario'   => $arr['email'],
            'email'     => $arr['email'],
            'password'  => password_hash($arr['password'], PASSWORD_DEFAULT),
            'status'    => 1,
            'perfilid'  => 1,
            'role'      => $arr['role']
            );

  $id = $db->i('usuarios', $fields);

  if(is_numeric($id) && !$login){
    return $id;
  }else if(is_numeric($id)){
    logadmin($id);
  }else{
    return false;
  }
}
function add_admin(){
  global $db;
  $p = $_POST;
  $url = false;
  if($p['rpassword'] !== $p['password']){
    $url = build_alert(admin.'/addadmin.php', 'Las contraseñas no coinciden.','Error', array('name'=>$p['name'],'email'=>$p['email']));
  }else{
    $exists = $db->query('SELECT usuarioid FROM usuarios WHERE email="'.$p['email'].'"')->num_rows;
    if($exists){
      $url = build_alert(admin.'/addadmin.php', 'El administrador con el correo '.$p['email'].' ya está registrado.', 'Error', array('name'=>$p['name'],'email'=>$p['email']));
    }else{
      create_admin($p, false);
      $url = build_alert(admin.'/admins.php', 'El administrador con el correo '.$p['email'].' fue registrado con éxito.', 'Éxito');
    }
  }
  header('Location:'.$url);
}
function create_asesor($arr, $login = false){
  global $db;
  $fields = array(
            'nombre'   => $arr['name'],
            'usuario'   => $arr['email'],
            'email'     => $arr['email'],
            'status'    => 1,
            'perfilid'  => 1
            );

  $id = $db->i('usuarios_representantes', $fields);

  if(is_numeric($id) && !$login){
    return $id;
  }else if(is_numeric($id)){
    logadmin($id);
  }else{
    return false;
  }
}
function add_asesor(){
  global $db;
  $p = $_POST;
  $url = false;
    $exists = $db->query('SELECT usuarioid FROM usuarios_representantes WHERE email="'.$p['email'].'"')->num_rows;
    if($exists){
      $url = build_alert(admin.'/addasesor.php', 'El asesor con el correo '.$p['email'].' ya está registrado.', 'Error', array('name'=>$p['name'],'email'=>$p['email']));
    }else{
      create_asesor($p, false);
      $url = build_alert(admin.'/asesores.php', 'El asesor con el correo '.$p['email'].' fue registrado con éxito.', 'Éxito');
    }
  header('Location:'.$url);
}
function get_current_admin($cookie){
  global $db;
  $admincookie = i(SLUG.'_admin_cookie', $cookie);
  if($admincookie){
    $adminobject = json_decode($admincookie);
    $id = $adminobject->id;
    $scs = $adminobject->scs;
    $query = 'SELECT * FROM usuarios WHERE usuarioid='.$id.' AND email="'.$scs.'"';
    $result_rows = $db->query($query)->num_rows;
    if($result_rows){
      $isloggedin = true;
      return $db->fetch($query);
    }
  }
}
function deleteadmin(){
  global $db;
  $db->d('usuarios', array('usuarioid'=>$_GET['id']));
  $url = build_alert(admin.'/admins.php', 'El administrador ha sido elminado.', 'Éxito');
  header('Location:'.$url);
}
function deleteasesor(){
  global $db;
  $db->d('usuarios_representantes', array('usuarioid'=>$_GET['id']));
  $url = build_alert(admin.'/asesores.php', 'El asesor ha sido elminado.', 'Éxito');
  header('Location:'.$url);
}
function deletemicroempresario(){
  global $db;
  $db->d('usuarios_microempresarios', array('usuarioid'=>$_GET['id']));
  $url = build_alert(admin.'/microempresarios.php', 'El microempresario ha sido elminado.', 'Éxito');
  header('Location:'.$url);
}
function admin_verify($email, $password){
  global $db;
  $query = 'SELECT * FROM usuarios WHERE email="'.$email.'"';
  $r = $db->fetch($query);
  $o = array('email'=>$email);
  if(count($r)){
    $pass = $r['password'];
    $islpsystem = strpos($pass, '$2y');
    $islpsystem = ($islpsystem === false) ? false : true;
    if(!$islpsystem){
      $o['error'] = true;
      $o['redirectChange'] = true;
      $o['msg']   = 'Es necesario que se realice un cambio de contraseña por migración de sistema.';
    }else{
      $passwordisok = password_verify($password,$pass);
      if($passwordisok){
        if($r['status']){
          $o['error'] = false;
          $o['msg']   = false;
        }else{
          $o['error'] = true;
          $o['msg']   = 'Este administrador ha sido desactivado.';
        }
      }else{
        $o['error'] = true;
        $o['msg']   = 'La contraseña es incorrecta.';
      }
    }
  }else{
    $o['error'] = true;
    $o['msg']   = 'El administrador con el correo '.$email.' no está registrado.';
  }
  return $o;
}
function checkLogin(){
  global $db;
  $p = $_POST;
  $o = admin_verify($p['email'],$p['pass']);
  if($i = i('msg', $o)){
    if(i('redirectChange', $o)){
      $url = build_alert(admin.'/changepassword.php', $i, 'Alerta', array('email'=>$p['email']));
    }else{
      $url = build_alert(admin.'/login.php', $i, 'Error', array('email'=>$p['email']));
    }
    header('Location: '. $url);
    die();
  }else{
    $user = $db->fetch('SELECT usuarioid FROM usuarios WHERE email="'.$p['email'].'"');
    logadmin($user['usuarioid']);
  }
}
function logadmin($id){
  global $db;
  $query = 'SELECT email FROM usuarios WHERE usuarioid='.$id;
  $r = $db->fetch($query);
  if(count($r)){
    $arr = array('id'=>$id,'scs'=>$r['email']);
    $str = json_encode($arr);
    setcookie(SLUG.'_admin_cookie', $str, time() + (60*60*24*60), '/');
    header('Location: '.admin.'/index.php');
  }else{
    header('Location: '.admin.'/login.php');
  }
  die();
}
function admin_filter($queries, $cookie){
  global $db;
  $screen = i('screen', $queries);
  $isloggedin = false;
  $url = false;
  $admincookie = i(SLUG.'_admin_cookie', $cookie);
  if($admincookie){
    $adminobject = json_decode($admincookie);
    $id = $adminobject->id;
    $scs = $adminobject->scs;
    $query = 'SELECT usuarioid,status FROM usuarios WHERE usuarioid='.$id.' AND email="'.$scs.'"';
    $result_rows = $db->query($query)->num_rows;
    if($result_rows){
      $isactive = $db->fetch($query)['status'];
      if($isactive){
        $isloggedin = true;
      }
    }
  }
  switch($screen){
    case "loggedin":
      if(!$isloggedin){
        $url = 'login';
      }
    break;
    case "notloggedin":
    if($isloggedin){
      $url = 'index';
    }
    break;
  }
  if($url){
    header('Location: '.admin.'/'.$url.'.php');
  }
}

function dayname($index){
  $o = array(
        'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'
      );
  return $o[$index];
}
function toggle_user(){
  global $db;
  $p = $_POST;
  $q = 'SELECT status FROM '.$p['group'].' WHERE usuarioid='.$p['id'];
  $cs = $db->fetch($q)['status'];
  $ncs = $cs == 1 ? 0 : 1;
  $db->u($p['group'], array('status'=>$ncs), array('usuarioid'=>$p['id']));
}
function toggle_entregable(){
  global $db;
  $p = $_POST;
  $q = 'SELECT '.$p['group'].' FROM  usuarios_microempresarios WHERE usuarioid='.$p['id'];
  $cs = $db->fetch($q)[$p['group']];
  $ncs = $cs == 1 ? 0 : 1;
  $db->u('usuarios_microempresarios', array($p['group']=>$ncs), array('usuarioid'=>$p['id']));
}

function hrs_color($hrs){
  $base = 6;

  if($base < $hrs){
    $code = 'hr_over';
  }else if($base == $hrs){
    $code = 'hr_just';
  }else{
    $code = 'hr_sub';
  }
  return $code;
}
function changepassword(){
  global $db;
  $p = $_POST;
  $username = $p['username'];
  $newpass = $p['pass'];
  $newpass = password_hash($newpass, PASSWORD_DEFAULT);
  $db->u('usuarios', array('password'=>$newpass), array('email'=>$username));
  $url = build_alert(admin.'/login.php', 'Tu contraseña ha sido cambiada, por favor ingresa de nuevo.', 'Éxito', array('email'=>$p['username']));
  header('Location:'.$url);
}
function admin_logout(){
  unset($_COOKIE[SLUG.'_admin_cookie']);
  setcookie(SLUG.'_admin_cookie', '', time() - 10, '/');
  header('Location: '.admin);
}
function send_notification(){
  global $db;
  $p = $_POST;
  if(password_verify($p['pin'], '$2y$10$TN2oQSao3WAlzQGKU8eyG.pf5nvdUv.qjOO2YxjCkF0AvOPpHgLo6')){

  $g = array();
  $iDB = array(
    'title' => $p['name'],
    'msg'   => $p['msg'],
    'asesores'  => 0,
    'microempresarios' => 0
  );
  foreach($p['group'] as $group){
    $g[] = 'groupTable="'.$group.'"';
    if($group == 'asesor'){
      $iDB['asesores'] = 1;
    }
    if($group == 'microempresario'){
      $iDB['microempresarios'] = 1;
    }
  }
  $db->i('notificacionesEnviadas', $iDB);
  $groups = implode($g, ' OR ');
  $q = 'SELECT deviceID FROM notificaciones WHERE '.$groups;

  $ids = $db->assoc($q);

  $appids = array();
  foreach($ids as $id){
    $appids[] = $id['deviceID'];
  }

  $content = array(
			"en" => $p['msg']
			);
      $heading = array(
          "en" => $p['name']
          );

		$fields = array(
			'app_id' => "2f9c5b83-2619-44ad-8506-ab3fd819ba3e",
			'include_player_ids' => $appids,
			'contents' => $content,
      'headings'  => $heading
		);

		$fields = json_encode($fields);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
												   'Authorization: Basic YjNjZDE5ZDItOTUyYS00ODIyLWEyNGQtNmQ0MWI5YTJkMWI0'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
		curl_close($ch);
  $fq = http_build_query(array('alertmsg'=>'Aviso enviado con éxito.', 'alerttile'=>'OK'));
}else{
  $fq = http_build_query(array('alertmsg'=>'PIN Incorrecto', 'alerttile'=>'Error'));

}
	header('Location:'.dir.'/admin/sendnotification.php?'.$fq);
}

function ajax_save_analisis(){
  $db = new db;
  $text = $_POST['text'];
  $col = $_POST['to'];
  $id = $_POST['id'];

  $u = array(
    $col => htmlspecialchars($text),
    $col.'_status'  => 1
  );




  if($col !== 'analisis'){
    $u['fecha_'.$col] = date('Y-m-d H:i:s');
  }

  $db->u('usuarios_microempresarios', $u, array('usuarioid'=>$id));

  $url = 'http://www.descifrainadem.mx/Auxiliares/GenerarAuxiliares.ashx?';

  switch($col){
    case "analisis_financiamiento":
      $t = 1;
    break;
    case "analisis_plan_accion":
      $t = 3;
    break;
    case "analisis":
      $t = 2;
    break;
  }

  $qu = array(
    'Id'    => $id,
    'Tipo'  => $t
  );

  $ul = $url.http_build_query($qu);

  simpleGet($ul);
  echo 'ok';
  die();
}

function addfalsehours(){
  global $db;

  $p = $_POST;
  $id = $p['id'];
  $hours = $p['hours'];

  $me = $db->fetch('SELECT id_asesor,latitud_inicio,longitud_inicio FROM visita WHERE id_negocio='.$id);

  $end = time();
  $start = $end - ((60*60) * $hours);

  $end = date('Y-m-d H:i:s', $end);
  $start = date('Y-m-d H:i:s', $start);

  $lat = $me['latitud_inicio'];
  $lng = $me['longitud_inicio'];
  $idasesor = $me['id_asesor'];

  $i = array(
    'id_asesor' => $idasesor,
    'id_negocio'=> $id,
    'fecha_hora_inicio' => $start,
    'fecha_hora_final'  => $end,
    'longitud_inicio'   => $lng,
    'longitud_final'    => $lng,
    'latitud_inicio'    => $lat,
    'latitud_final'     => $lat
  );

  $db->i('visita', $i);

  header('Location: '.dir.'/admin/visita.php?id='.$id);
  die();
}
