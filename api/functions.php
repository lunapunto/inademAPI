<?php
require_once '../global/funcs.php';
header('Content-Type: text/html;charset=utf-8');
header('Access-Control-Allow-Origin: *');

$db = new db;

function checkUser(){
  global $db;
  $g = $_GET;
  $email = $g['email'];
  $userpassword = $g['password'];
  $user = false;
  $table = false;
  $o = array();
  $o['msg'] = 'Error desconocido (52LP)';


  /*
  * Fetch users
  *
  * Check first the usuarios_representantes
  */
  $aq = 'SELECT usuarioid,usuario,password,email,nombre,status,islogged,last_login FROM usuarios_representantes WHERE email="'.$email.'" LIMIT 1';
  $ar = $db->fetch($aq);

  if(count($ar)){
    $user = $ar;
    $table = 'usuarios_representantes';
  }else{
    /*
    *
    * Asesor don't exists, check usuarios_microempresarios
    */
    $aq = 'SELECT usuario,password,email,nombre,status FROM usuarios_microempresarios WHERE usuario="'.$email.'" ORDER BY fechaalta DESC LIMIT 1';
    $ar = $db->fetch($aq);
    if(count($ar)){
      $user = $ar;
      $table = 'usuarios_microempresarios';
    }
  }
  if($user){
    $o['table'] = $table;
    $o['user'] = $user;
    $password = $user['password'];

    if(!$password){
      $o['passwordchange'] = true;
      $o['msg'] = 'NoPassword';
    }else{



        $passwordverify = ($userpassword == $password);
        if(!$passwordverify){
          $o['msg'] = 'Password';
        }else{
          if($user['status']){
            // Representante no puede iniciar en dos dispositivos
            if($table == 'usuarios_representantes'){
              if($user['islogged']){
                $timelastlogin = $user['last_login'];
                $timelastlogin = strtotime($timelastlogin);
                $t_days = 60*60*3;
                $c_time = time();
                if($t_days <= ($c_time - $timelastlogin)){
                  $o['msg'] = 'OK';
                  $db->u('usuarios_representantes', array('islogged'=>1), array('usuarioid'=>$user['usuarioid']));
                }else{
                  $o['msg'] = 'Device';
                }
              }else{
                $db->u('usuarios_representantes', array('islogged'=>1), array('usuarioid'=>$user['usuarioid']));
                $o['msg'] = 'OK';
              }
            }else{
              $o['msg'] = 'OK';
            }
          }else{
            $o['msg'] = 'Status';
          }
        }



    }
  }else{
    $o['msg'] = 'User';
  }
  header('Content-Type: application/json');
  echo json_encode($o);
  die();
}
function changePassword(){
  global $db;
  $g = $_GET;
  $o = array();
  $o['msg'] = false;
  $email = $g['email'];
  $password = $g['password'];
  $user = false;
  /*
  * Fetch users
  *
  * Check first the usuarios_representantes
  */
  $aq = 'SELECT usuario,password,email,nombre,status FROM usuarios_representantes WHERE email="'.$email.'"';
  $ar = $db->fetch($aq);
  if(count($ar)){
    $user = $ar;
    $table = 'usuarios_representantes';
  }else{
    /*
    *
    * Asesor don't exists, check usuarios_microempresarios
    */
    $aq = 'SELECT usuario,password,email,nombre,status FROM usuarios_microempresarios WHERE usuario="'.$email.'"';
    $ar = $db->fetch($aq);
    if(count($ar)){
      $user = $ar;
      $table = 'usuarios_microempresarios';
    }
  }

  if(!$user){
    $o['msg'] = 'User';
  }else{
    if($table == 'usuarios_microempresarios'){
      $db->u($table, array('password'=>$password), array('usuario'=>$email));
    }else{
      $db->u($table, array('password'=>$password), array('email'=>$email));
    }
    $o['msg'] = 'OK';

  }
  echo json_encode($o);
  die();
}
function get_microempresario(){
  global $db;
  $g = $_GET;
  if(isset($g['byid'])){
    $q = 'SELECT usuarioid,nombre,email,nombre_negocio,usuario,numero_telefonico,giroid,pic1,edad,convocatoria FROM usuarios_microempresarios WHERE usuarioid='.$g['id'].'';
  }else{
    $q = 'SELECT usuarioid,nombre,email,nombre_negocio,usuario,numero_telefonico,giroid,pic1,edad,convocatoria FROM usuarios_microempresarios WHERE usuario="'.$g['email'].'"';

  }
  $r = $db->fetch($q);

  $o = array(
              'nombre_negocio'  => san($r['nombre_negocio']),
              'nombre'          => san($r['nombre']),
              'usuario'         => $r['usuario'],
              'tel'             => $r['numero_telefonico'],
              'giro'            => get_giro_name($r['giroid']),
              'pic'             => filterpic($r['pic1']),
              'edad'            => $r['edad'],
              'convocatoria'    => san($r['convocatoria']),
              'giroid'          => $r['giroid'],
              'entregables'     => get_micro_entregables($r['usuarioid'], false)
            );
  echo json_encode($o);
  die();
}
function get_microempresario_reportes_meta(){
  global $db;
  $g = $_GET;
  $usuario = isset($g['email']) ? $g['email'] : '';
  $q = 'SELECT usuarioid, analisis, analisis_financiamiento, analisis_plan_accion, reportePDF,existeReportePDF, generandoReporte,  fecha_analisis_financiamiento, fecha_analisis_plan_accion, analisis_plan_accion_status, analisis_financiamiento_status, analisis_status FROM usuarios_microempresarios WHERE '.(isset($g['byid']) ? 'usuarioid='.$g['id']: 'usuario="'.$usuario.'"');
  $r = $db->fetch($q);
  $q_ = 'SELECT a.id_asesor, b.nombre as asesor_name FROM visita as a, usuarios_representantes as b WHERE a.id_negocio ='.$r['usuarioid'].' LIMIT 1';
  $pdfurl = 'http://52.161.23.253/ReporteINADEM/Reportes/INADEM_'.$r['usuarioid'].'.pdf';
  $as = $db->fetch($q_);
  $r['asesor_name'] = $as['asesor_name'];
  $r['fecha_analisis_plan_accion'] = pdate($r['fecha_analisis_plan_accion'], false);
  $r['fecha_analisis_financiamiento'] = pdate($r['fecha_analisis_financiamiento'], false);

  $r_ = array(
    'isok_plan_accion'    => (199 < strlen($r['analisis_plan_accion'])) && $r['fecha_analisis_plan_accion'] && $r['analisis_plan_accion_status'],
    'isok_financiamiento' => (199 < strlen($r['analisis_financiamiento'])) && $r['fecha_analisis_financiamiento'] && $r['analisis_financiamiento_status'],
    'isok_analisis'       => (199 < strlen($r['analisis'])) && $r['analisis_status'] ? true : false,
    'isok_reporte'        => UR_exists($pdfurl),
    'isgenerating'        => ($r['generandoReporte'] !== 0),
  );
  $r = array_merge($r, $r_);

  $o = json_encode($r);
  echo $o;
  die();
}
function get_microempresario_reporte(){
  global $db;
  $g = $_GET;
  $usuario = $g['email'];
  $col = $g['col'];
  $o = array();
  $q = 'SELECT '.$col.' FROM usuarios_microempresarios WHERE '.(0 < $g['id'] ? 'usuarioid='.$g['id'] : 'usuario="'.$usuario.'"');
  $r = $db->fetch($q);
  $msg = $r[0];
  $o['msg'] = $msg;
  echo json_encode($o);
  die();
}
function get_microempresario_reporte_pdf(){
  global $db;
  $g = $_GET;
  $usuario = $g['email'];
  $o = array();
  $o['msg'] = 'Unknown';
  $url = 'http://104.130.48.96/ReporteINADEM/Reportes/';
  $q = 'SELECT reportePDF FROM usuarios_microempresarios WHERE usuario="'.$usuario.'"';
  $r = $db->fetch($q);

  $o['msg'] = ($r['reportePDF'] ? $url.$r['reportePDF'] : 'NoFile');
  echo json_encode($o);
  die();
}
function set_encuesta_micro(){
  global $db;
  $g = $_GET;
  $o = array();
  $o['msg'] = false;

  $nq = 'SELECT usuarioid FROM usuarios_microempresarios WHERE usuario="'.$g['email'].'"';

  $negocio = $db->fetch($nq);
  $idnegocio = $negocio['usuarioid'];



  //Asesor
  $visita = $db->fetch("SELECT id_asesor FROM visita WHERE id_negocio=".$idnegocio." AND id_asesor<>'' LIMIT 1");
  $idasesor = $visita['id_asesor'];



  $i = array(
    'usuarioid'        => $idnegocio,
    'res1_1'          => isin_bool('Mercadotecnia', $g['temas']),
    'res1_2'          => isin_bool('Operaciones contables y finanzas',  $g['temas']),
    'res1_3'          => isin_bool('Atención a clientes y ventas',  $g['temas']),
    'res1_4'          => isin_bool('Análisis del micromercado',  $g['temas']),
    'res1_5'          => isin_bool('Capacitación de endeudamiento',  $g['temas']),
    'res1_6'          => isin_bool('Entrega de Hardware',  $g['temas']),
    'res2_1'          => $g['temashoras'],
    'res2_2_hrs'      => $g['six_hours'],
    'res3'            => $g['exp'],
    'res4'            => $g['reco'],
    'res5'            => $g['dispositivo'],
    'res6'            => $g['exphard'],
    'res7'            => $g['exphardware_combo']
  );

  $id = $db->i('micro_encuesta', $i);
  $o['msg'] = $id;


  simpleGet('http://www.descifrainadem.mx/Auxiliares/EnviarConstanciaConsultor.ashx?Id='.$idnegocio);

  echo json_encode($o);
  die();
}
function isEvaluacionPending(){
  global $db;
  $g = $_GET;
  $o = array();
  $o['msg'] = false;

  $nq = 'SELECT convocatoria FROM usuarios_microempresarios WHERE usuario="'.$g['email'].'"';

  $negocio = $db->fetch($nq);
  $convocatoria = $negocio['convocatoria'];
	$isok = false;
	if($convocatoria == '4.1 Desarrollo de Capacidades Empresariales para Microempresas.' || $convocatoria == "5.2  Desarrollo de Capacidades Empresariales para Microempresas a través de la incorporación de Tecnologías de la Información y Comunicaciones (TIC’s)." ){
		$isok = true;
	}

  $o['msg'] = $isok;
  echo json_encode($o);

}
function get_time_micro_api(){
  $r = get_time_micro($_GET['id']);
  var_dump($r);
}
function edit_microempresario(){
  global $db;
  $g = $_GET;
  $email = $g['email'];
  $o = array('OK');
  if($email){
    $id = $db->fetch('SELECT usuarioid FROM usuarios_microempresarios WHERE usuario="'.$g['oldemail'].'" LIMIT 1')['usuarioid'];
    $i = array(
      'usuario' => $email,
    );
    if(isset($g['tel'])){
      $i['numero_telefonico'] = $g['tel'];
    }
    $db->u('usuarios_microempresarios', $i, array('usuarioid'=>$id));
  }
}
function utf8ize($d) {
        if (is_array($d)) {
            foreach ($d as $k => $v) {
                $d[$k] = utf8ize($v);
            }
        } else if (is_string ($d)) {
            return utf8_encode($d);
        }
        return $d;
}


function getVisitasFromAsesor(){
  global $db;
  $g = $_GET;
  $o = array();
  $email = $g['email'];

  $fq = 'SELECT usuarioid FROM usuarios_representantes WHERE email="'.$email.'" LIMIT 1';
  $fr = $db->fetch($fq);
  $id = $fr['usuarioid'];

  //$tq = 'SELECT * , (SUM(TIME_TO_SEC(TIMEDIFF(fecha_hora_final, fecha_hora_inicio))) / 3600) AS time FROM visita WHERE id_asesor='.$id.' GROUP BY id_negocio ORDER BY fecha_hora_final DESC';
  $tq = 'SELECT * FROM trasesormicroempresario WHERE idAsesor='.$id;
  $tr = $db->assoc($tq);
  foreach($tr as $tr_){
    $r = 'SELECT * FROM usuarios_microempresarios WHERE usuarioid='.$tr_['idMicroNegocio'].' AND status=1 ORDER BY usuarioid DESC';
    $r = $db->fetch($r);
    $status = true;
    if($r['status']){
    $time = strtotime($r['fechaalta']);
    $month = date('n', $time);
    $monthn = $month;
    $month = monthName($month);
    $year = date('Y', $time);
    $ent = get_micro_entregables($r['usuarioid']);
    $isokconv = $ent['isokconv'];
    $status = $ent['status'];
    $entno = $ent['ent'];
    $totalent = $ent['totalent'];
    if($entno == $totalent){
      $status = 1;
      $color = 'rgb(3, 194, 62)';
      $msg = 'Visita concluída';
    }else if($entno == ($totalent - 1) && $ent['falta'][0] == 'Microempresario no ha llenado encuesta.'){
      $status = 2;
      $color = 'rgb(236, 183, 26)';
      $msg = 'Microempresario no ha llenado encuesta.';
    }else if($isokconv && $ent['timeFalta'] && $status == 4){
      $status = 4;
      $color = 'rgb(208, 18, 69)';
      $msg = 'Falta cubrir tiempo y análisis';
    }else if($isokconv && $status == 4){
      $status = 4;
      $color = 'rgb(208, 18, 69)';
      $msg = 'Faltan análisis';
    }else if($isokconv && $ent['timeFalta']){
      $status = 4;
      $color = 'rgb(208, 18, 69)';
      $msg = 'Falta cubrir tiempo';
    }else{
      $status = 3;
      $color = 'rgb(208, 18, 69)';
      $msg = 'Faltan entregables ['.$entno.'/'.$totalent.']';
    }

    $i = array(
      'id_negocio'           => $r['usuarioid'],
      'fecha_inicio'         => pdate($time),
      'fecha_final'          => pdate($time),
      'simple_date'          => date('d.m.Y', $time),
      'entregables'          => $ent,
      'microempresario_info' => get_microempresario_info($r['usuarioid']),
      'entmsg'               => array('status'=>$status,'color'=>$color,'msg'=>$msg)
    );

    $o[$month.' '.$year][] = $i;
  }

  }
  $o = array_reverse($o);
  $o = (object) $o;
  $r = json_encode($o);
  echo $r;
  die();
}

function restriccion_convocatoria($convocatoria, $oldconvocatoria){
  $r = false;

  if($oldconvocatoria == $convocatoria){
    $r = true;
  }

  switch($convocatoria){
    case $oldconvocatoria:
      $r = true;
    break;
    case "4.1 Desarrollo de Capacidades Empresariales para Microempresas.":
      if($oldconvocatoria == "Proyecto: MI TIENDA."){
        $r = true;
      }else if($oldconvocatoria == 'Proyecto: MI TIENDA 2016.'){
        $r = true;
      }else if($oldconvocatoria == "5.2  Desarrollo de Capacidades Empresariales para Microempresas a través de la incorporación de Tecnologías de la Información y Comunicaciones (TIC’s)."){
        $r = true;
      }
    break;

    case "Proyecto: MI TIENDA.":

    break;

    case  "4.1 a)Formación Empresarial para MIPYMES.":
      if($oldconvocatoria == '4.1 b)Formación Empresarial para MIPYMES.'){
        $r = true;
      }
    break;

    case  "4.1 b)Formación Empresarial para MIPYMES.":
      if($oldconvocatoria == '4.1 a)Formación Empresarial para MIPYMES.'){
        $r = true;
      }
    break;

    case  "5.1 Incorporación de Tecnologías de Información y Comunicaciones a las Micro y Pequeñas Empresas.":
      if($oldconvocatoria == 'Proyecto: MI TIENDA.'){
        $r = true;
      }else if($oldconvocatoria == "5.2  Desarrollo de Capacidades Empresariales para Microempresas a través de la incorporación de Tecnologías de la Información y Comunicaciones (TIC’s)."){
        $r = true;
      }
    break;

    case  "5.2  Desarrollo de Capacidades Empresariales para Microempresas a través de la incorporación de Tecnologías de la Información y Comunicaciones (TIC’s).":
      if($oldconvocatoria == '4.1 Desarrollo de Capacidades Empresariales para Microempresas.'){
        $r = true;
      }else if($oldconvocatoria == 'Proyecto: MI TIENDA.'){
        $r = true;
      }else if($oldconvocatoria == 'Proyecto: MI TIENDA 2016.'){
        $r = true;
      }else if($oldconvocatoria == '5.1 Incorporación de Tecnologías de Información y Comunicaciones a las Micro y Pequeñas Empresas.'){
        $r = true;
      }
    break;
  }
  return $r;
}

function setDeviceToken(){
  global $db;
  $g = $_GET;
  $i = array(
          'email'       => $g['email'],
          'deviceID'    => $g['pushId'],
          'groupTable'  => $g['table']
        );

  $db->i('notificaciones', $i);
}
function verifyRFC(){
  global $db;
  $g = $_GET;
  $o = array();
  $rfc = $g['rfc'];
  $convocatoria = $g['convocatoria'];
  $tipo = $g['tipo'];
  $rfc = strtoupper($rfc);

  if($tipo == 'Física'){
    $isrfc = preg_match('/^([A-Z,Ñ,&]{4}([0-9]{2})(0[1-9]|1[0-2])(0[1-9]|1[0-9]|2[0-9]|3[0-1])[A-Z|\d]{3})$/', $rfc);
  }else{
    $isrfc = preg_match('/^([A-Z,Ñ,&]{3}([0-9]{2})(0[1-9]|1[0-2])(0[1-9]|1[0-9]|2[0-9]|3[0-1])[A-Z|\d]{3})$/', $rfc);
  }



  if($isrfc){
    $u = $db->assoc('SELECT convocatoria FROM usuarios_microempresarios WHERE rfc="'.$rfc.'" AND status="1"');
    foreach($u as $c){
      $oldconvocatoria = $c['convocatoria'];

      if(restriccion_convocatoria($oldconvocatoria, $convocatoria) || restriccion_convocatoria($convocatoria,$oldconvocatoria)){
        $o['msg'] = 'restricted';
      }
    }
  }else{
    $o['msg'] = 'invalid';
  }
  echo json_encode($o);
  die();
}
function newMicro(){
  global $db;
  $p = (object) $_POST;



  echo json_encode($o);
  die();
}
function getAvisos(){
  global $db;
  $o = array();
  $r = $db->assoc('SELECT * FROM notificacionesEnviadas ORDER BY created DESC');
  foreach($r as $a){
    $o[] = array(
      'title' => $a['title'],
      'msg'   => $a['msg'],
      'date'  => pdate($a['created'], false)
    );
  };
  echo json_encode($o);
  die();
}
function search_folio(){
  global $db;
  $g = $_GET;
  $o = array();
  $o['msg'] = 'false';

  $folio = $g['folio'];
  $convocatoria = $g['convocatoria'];

  if($convocatoria == '4.1 a)Formación Empresarial para MIPYMES.'){
    $idconvocatoria = 1;
  }else{
    $idconvocatoria = 3;
  }

  $q = 'SELECT * FROM cargas_rfc WHERE folio="'.$folio.'" AND idConvocatoria="'.$idconvocatoria.'"';

  $r = $db->fetch($q);

  if(count($r)){
    $type = $r['tipoPersona'];

    if($type == 'F'){
      $type = 'Física';
    }else if($type == 'M'){
      $type = 'Moral';
    }

    $o = array(
      'msg'     => 'ok',
      'nombre'  => san($r['nombre']),
      'rfc'     => $r['rfc'],
      'email'   => $r['correo'],
      'tipo'    => $type
    );
  }


  echo json_encode($o);
  die();
}
function logoutRep(){
  global $db;
  $g = $_GET;

  $db->u('usuarios_representantes', array('islogged'=>0), array('email'=>$g['email']));

}
function get_negocio_coords(){
  global $db;
  $g = $_GET;
  $o = array();


  $r = $db->fetch('SELECT latitud,longitud FROM usuarios_microempresarios WHERE usuarioid='.$g['id']);

  $o = array(
    'lat' => $r['latitud'],
    'lng' => $r['longitud']
  );

  echo json_encode($o);
  die();

}

function new_visita(){
  global $db;
  $g = $_GET;

  $o = array();

  $ent = get_micro_entregables($g['id']);

  $time = $ent['rawTime'];

  $current_seconds = $time * 60 * 60;
  $o['ent'] = $ent;


  $rawhours = $current_seconds / 3600;
  $hours = floor($rawhours);

  $r_hours = $rawhours - $hours;

  $rawminutes = $r_hours * 60;
  $minutes = floor($rawminutes);


  $r_minutes = $rawminutes - $minutes;

  $seconds = floor($r_minutes * 60);




  $o['currents'] = array(
    'original'  => $current_seconds,
    'hours'     => $hours,
    'minutes'   => $minutes,
    'seconds'   => $seconds
  );



  // Create visita

  $email = $g['email'];

  $fq = 'SELECT usuarioid FROM usuarios_representantes WHERE email="'.$email.'" LIMIT 1';
  $fr = $db->fetch($fq);
  $idasesor = $fr['usuarioid'];

  $start = date('Y-m-d H:i:s');



  $idvisita = $db->i('visita', array(
      'id_asesor'         => $idasesor,
      'id_negocio'        => $g['id'],
      'latitud_inicio'    => $g['lat'],
      'longitud_inicio'   => $g['lng'],
      'fecha_hora_inicio'  => $start
    ));

  $o['idvisita'] = $idvisita;

  echo json_encode($o);
  die();

}


function presave_visita(){
  echo 'ok';
}

function cancel_visita(){
  global $db;
  $g = $_GET;

  $visita = $db->fetch('SELECT * FROM visita WHERE id_visita='.$g['id']);
  $t = $g['totalTime'];



  $ent = get_micro_entregables($visita['id_negocio']);

  $time = $ent['rawTime'];

  $current_seconds = $time * 60 * 60;
  $o['ent'] = $ent;


  $rawhours = $current_seconds / 3600;
  $hours = floor($rawhours);

  $r_hours = $rawhours - $hours;

  $rawminutes = $r_hours * 60;
  $minutes = floor($rawminutes);


  $r_minutes = $rawminutes - $minutes;

  $seconds = floor($r_minutes * 60);




  $o['currents'] = array(
    'original'  => $current_seconds,
    'hours'     => $hours,
    'minutes'   => $minutes,
    'seconds'   => $seconds
  );


  $f_hours = substr($t, 0, 2);
  $f_minutes = substr($t, 3, 2);
  $f_seconds = substr($t, 6, 2);


  $d_hours = $f_hours - $hours;
  $d_minutes = $f_minutes - $minutes;
  $d_seconds = $f_seconds - $seconds;


  $addseconds = ($d_hours * 3600) + ($d_minutes * 60) + $d_seconds ;


  $initial = $visita['fecha_hora_final'] ? $visita['fecha_hora_final'] : $visita['fecha_hora_inicio'];

  $mm = time();
  $end = date('Y-m-d H:i:s', strtotime($initial) + ($mm - strtotime($initial)));


  $db->u('visita', array(
                      'fecha_hora_final'  => $end,
                      'latitud_final'     => $g['lat'],
                      'longitud_final'    => $g['lng']
                   ),
                   array(
                     'id_visita'  => $g['id']
                   )
  );

  $o['isokconv'] = $ent['isokconv'];
  echo json_encode($o);

}
function sino_bool($str){
  if($str == 'Sí' || $str == 'Si' || $str == 'si' || $str == 'sí' || (2 < count($str))){
    return 1;
  }else{
    return 0;
  }
}
function isin_bool($em,$arr){
  if(!(strpos($arr, $em) === false)){
    return 1;
  }else{
    return 0;
  }
}
function datestamp($time = false){
  if(!$time){
    $time = time();
  }
  return date('Y-m-d G:i:s', $time);
}
function getAsesorConvs(){
  global $db;
  $g = $_GET;
  $email = $g['email'];
  $id = $db->fetch('SELECT usuarioid FROM usuarios_representantes WHERE email="'.$email.'" ORDER BY usuarioid DESC')['usuarioid'];
  $dbconvs = $db->assoc('SELECT * FROM relacionconvrepre WHERE idRepresentante='.$id.' GROUP BY idConvocatoria');
  $convs = array(
    1 => '4.1 a)Formación Empresarial para MIPYMES.',
    2 => '4.2 Fomento a la adquisición del modelo de Microfranquicias.',
    3 => '5.1 Incorporación de Tecnologías de Información y Comunicaciones a las Micro y Pequeñas Empresas.',
    4 => "5.2  Desarrollo de Capacidades Empresariales para Microempresas a través de la incorporación de Tecnologías de la Información y Comunicaciones (TIC’s).",
    5 => '4.1 Desarrollo de Capacidades Empresariales para Microempresas.',
    6 => 'Proyecto: MI TIENDA.',
    7 => '4.1 b)Formación Empresarial para MIPYMES.',
    8 => 'Proyecto: MI TIENDA 2016.'
  );
  $o = array();
  foreach($dbconvs as $dbconv){
    $dbconv = (int) $dbconv['idConvocatoria'];
    $o[] = $convs[$dbconv];
  }

  echo json_encode($o);
  die();
}
function new_microempresario(){
  global $db, $slack;
  $g = file_get_contents("php://input");
  $g = (array) json_decode($g);
  if(!count($g)){
    $g = $_GET;
  }
  $o = array();

  $time = $g['finaltime'];

  $minutes = $time->minutes + 2;
  $seconds = $time->seconds;
  $hours = $time->hours;
  $days = $time->days;

  $addTime = $seconds;
  $addTime += ($minutes * 60);
  $addTime += ($hours * 60 * 60);
  $addTime += ($days * 60 * 60 * 24);

  $start = datestamp($g['timestamp']);
  $end = datestamp($g['timestamp'] + $addTime);
  $e = (array) json_decode($g['encuesta']);
  $geo = (array) json_decode($g['geoloc']);
  $lat = $geo['lat'];
  $lng = $geo['lng'];
  $password = rand(999999, 9999999);
  $photos = (array) json_decode($g['photos']);
  $giros = array(
    'Abarrotes' => 1,
    'Tienda de Ropa' => 3,
    'Papelería' => 4,
    'Frutelería y Verdulería' => 9,
    'Carnicería' => 10,
    'Ferretería y Tlapalería' => 6,
    'Zapatería' => 11,
    'Tienda de regalos' => 12,
    'Pollería' => 13,
    'Farmacia' => 14,
    'Tienda de segunda mano' => 15,
    'Dulcería' => 16,
    'Cervecería' => 17,
    'Refaccionaria' => 18,
    'Florería' => 19,
    'Centro de distribución telefónico' => 20,
    'Venta de artículos de limpieza' => 21,
    'Accesorios de vestir' => 22,
    'Otro' => 8,
    'Salón de belleza' => 23,
    'Restaurante' => 2,
    'Mecánico' => 5,
    'Ciber-Café' => 24,
    'Cafetería' => 25,
    'Dentista' => 26,
    'Lavandería y Tintorería' => 27,
    'Reparaciones' => 28,
    'Médico' => 29,
    'Vulcanizadora' => 30,
    'Otro' => 8,
    'Tortillería' => 7,
    'Herrería' => 30,
    'Panadería' => 32,
    'Carpintería' => 33,
    'Imprenta' => 34,
    'Embotelladora de agua' => 35,
    'Costurera' => 36,
    'Jarciería (instrumentos de limpieza)' => 37,
    'Alfaería (barro)' => 38,
    'Sastería' => 39,
    'Heladería' => 40,
    'Zapatería' => 41,
    'Otro' => 8
  );

  //Insert microempresario
  $em = $e['microempresario'];
  $en = $e['negocio'];
  $enx = $e['negociox'];
  $ed = $e['direccion'];
  $esa = $e['salario'];
  $ess = $e['seguro'];
  $ef = $e['financiera'];
  $i = array(
    'longitud'            => $lng,
    'latitud'             => $lat,
    'convocatoria_year'   => $e['year'],
    'convocatoria'        => $e['convocatoria'],
    'sectorEmpresa'       => $e['sector'],
    'cantidad_empleados'  => $e['empresaSize'],
    'giroid'              => $giros[$e['giro']],
    'numero_folio'        => $e['folio'],
    'fechaalta'           => datestamp(),
    'dueno_empleado'      => 0,
    // Start microempresario indent
    'nombre'              => $em->nombre,
    'edad'                => intval($em->edad),
    'sexo'                => $em->sexo,
    'estudios_usu'        => $em->estudios,
    'usuario'             => $em->email,
    'password'            => $password,
    'status'              => 1,
    'numero_telefonico'   => $em->phone,
    'expe_anios'          => $em->experiencia,
    'tiempo_dedicado'     => $em->tiempodedica,
    'motivo_negocio'      => $em->porquenegocio,
    'dependientes_eco'    => intval($em->dependientes),
    'otro_negocio'        => sino_bool($em->espropietariootro),
    'visita_InstitucionDependencia' => sino_bool($em->visitasinstitucionales),
    'visita_InstitucionDependencia_Quien' => $em->visitasinstitucionales,
    'servicio_internet_casa' => sino_bool($em->internetcasa),
    'haz_programa_prospera'  => sino_bool($em->ayudaprospera),
    // Negocio indent
    'nombre_negocio'      => $en->nombre,
    'pertenece_negocio'   => sino_bool($en->negocionombre),
    'tipoNegocio'         => $en->personalidadjuridica,
    'rfc'                 => $en->rfc,
    'email'               => sino_bool($en->tienecorreoempresa),
    'EmailEmpresa'        => $en->correoempresa,
    'TelEmpresa'          => $en->telefonoempresa,
    //Direccion
    'dir_calle'           => $ed->calle,
    'dir_num_ext'         => $ed->ext,
    'dir_num_int'         => $ed->int,
    'dir_colonia'         => $ed->colonia,
    'dir_delegacion'      => $ed->mpo,
    'dir_CP'              => $ed->cp,
    // Negocio X
    'metros_cuadrados'    => $enx->m2,
    'atraso_pago_proveedores' => sino_bool($enx->debe),
    'cuanto_debe'         => sino_bool($enx->cuanto),
    'como_registra'       => $enx->registro,
    'cuantos_clientes'    => intval($enx->clientes),
    'vende_digitales'     => sino_bool($enx->digitales),
    'cobra_servicios'     => sino_bool($enx->pagoserv),
    'recibe_tarjetassociales'   => sino_bool($enx->pagosprogramas),
    'servicio_internet'   => sino_bool($enx->serviciosinternet),
    //Salario
    'SalarioTiene'        => sino_bool($esa->asigna),
    'SalarioMensual'      => intval($esa->promedio),
    'SalarioEs'           => sino_bool($esa->esparte),
    'SalarioRazones'      => sino_bool($esa->esutilidades),
    'SalarioRazonCual'    => $esa->razon,
    //Seguro
    'SeguroCotizaIMSS'      => sino_bool($ess->cotiza),
    'SeguroCotizaTrabIMSS'  => intval($ess->cuantoscot),
    'SeguroTrabajadores'    => sino_bool($ess->segpoptra),
    'PorqueNoCotizaIMSS'    => $ess->pqnocot,
    //'SeguroDueño'           => sino_bool($ess->segpopmicro),
    //Financiera
    'firma_sat'             => sino_bool($ef->tienefirmafis),
    'firma_sat_negocio'     => sino_bool($ef->tienefirmaneg),
    'cuenta_bancaria'       => sino_bool($ef->banconeg),
    'cuenta_bancaria_personal'  => sino_bool($ef->bancoper)
    /* RECORDAR AGREGAR EN USUARIOS MICROEMPRESARIOS FINANCIERO */
  );

  $id = $db->i('usuarios_microempresarios', $i);
  //$id = 0;
  /* ME Financiero */
  $fjs = array(
    'Luz' => 'gastos_fijos_luz',
    'Teléfono' => 'gastos_fijos_telefono',
    'Sueldos' => 'gastos_fijos_sueldos',
    'Renta' => 'gastos_fijos_renta',
    'Predial' => 'gastos_fijos_predial'
  );
  $inst = array(
    'Institución Bancaria' => 'credito_con_institucion_bancaria',
    'Microfinanciera' => 'credito_con_microfinanciera',
    'Prestamistas (usureros)' => 'credito_con_particular',
    'Otros' => 'credito_con_otros'
  );
  $i_financiero = array(
    'usuarioid'     => $id,
    'ventas_almes'  => $ef->ventasmes,
    'ingreso_extra_almes' => $ef->extrasmes,
    'compras_almes' => $ef->comprasmes,
    'tiene_credito' => sino_bool($ef->credsneg),
    'monto_credito' => intval($ef->credsnegmonto),

  );
  if(is_array($ef->gastosfijos)){
    foreach($ef->gastosfijos as $fijo){
      if(isset($fjs[$fijo])){
        $i_financiero[$fjs[$fijo]] = 1;
      }
    }
  }


  if(is_array($ef->credsneginst)){
    foreach($ef->credsneginst as $in){
      if(isset($inst[$in])){
        $i_financiero[$inst[$in]] = 1;
      }
    }
  }

  $db->i('usuario_microempresarios_financiamiento', $i_financiero);


  $i_folios = array(
    'folio'         => $e['folio'],
    'tipoPersona'          => ($en->personalidadjuridica == 'Física' ? 'F' : 'M'),
    'correo'        => $em->email,
    'rfc'           => $en->rfc,
    'nombre'        => $em->nombre
  );

  $db->i('cargas_rfc', $i_folios);



  // Adicionales
  $giro = $e['giro'];
  $t = false;
  if($giro == 'Abarrotes'){
    $t = 'abarrotes_datos';
    $et = $e['extras']->abarrotes;
    $it = array(
      'usuarioid'             => $id,
      'numero_refrigeradores' => $et->enfriadores,
      'numero_refrigeradores_propios' => $et->enfriadorespropios,
      'vitrina_carnes_fria'   => sino_bool($et->vitrinacarnes),
      'vitrina_carnes_fria_propios'   => $et->vitrinaspropias,
      'estanteria_num_pan_botana' => $et->estantes,
      'rebanador_bascula'         => sino_bool($et->rebanadora),
      'productos_venta_dia_a'     => $et->productosdiferentes,
      'cobro_codigo_barras_a'     => $et->cobrabarras
    );
  }else if($giro == 'Restaurante'){
    $t = 'restaurante_datos';
    $et = $e['extras']->restaurantes;
    $it = array(
      'usuarioid'             => $id,
      'mesas_num'      => $et->mesas,
      'refri_num_proveedores_r' => $et->refrigeradores,
      'platillo_num_dia'    => $et->platillos,
      'exclusividad_refresco_cerveza'  => $et->exclusividad
    );
  }else if($giro == 'Tienda de Ropa'){
    $t = 'ropa_datos';
    $et = $e['extras']->ropa;
    $it = array(
      'usuarioid'     => $id,
      'tipo_prendas_num' =>  $et->prendas,
      'marcas_ropa'      =>  $et->marcas,
      'piezas_num_ropa'  => $et->piezas,
      'productos_venta_dia_ropa'  => $et->venta
    );
  }else if($giro == 'Papelería'){
    $t = 'papeleria_datos';
    $et = $e['extras']->papelerias;
    $it = array(
      'usuarioid'     => $id,
      'marcas_vende_p'  => $et->marcas,
      'productos_maneja_p'  => $et->productos,
      'productos_venta_dia_p' => $et->productosdiario,
      'renta_compu_p'       => $et->rentacomputadoras,
      'servicio_copias_impre' => $et->copias
    );
  }else if($giro == 'Mecánico'){
    $t = 'taller_mecanico_datos';
    $et = $e['extras']->mecanico;
    $it = array(
      'usuarioid'     => $id,
      'venta_refacciones' => sino_bool($et->refacciones),
      'escaner_diagnostico' => sino_bool($et->escaner),
      'control_reparacion'  => sino_bool($et->control),
      'tiempos_entrega_establecidos' => sino_bool($et->tiempos),
      'presupuesta_reparacion'       => sino_bool($et->presupuestar),
      'venta_aceite_anticongelante'  => sino_bool($et->aceites),
      'cuantas_reparaciones_aldia'   => sino_bool($et->reparacionesdia)
    );
  }else if($giro == 'Ferretería y Tlapalería'){
    $t = 'ferreteria_datos';
    $et = $e['extras']->ferreteria;
    $it = array(
      'usuarioid'     => $id,
      'num_marcas'    => $et->marcas,
      'num_productos' => $et->productos,
      'productos_venta_dia_f' => $et->productosdia,
      'distribuidor_autorizado_f' => sino_bool($et->distribuidor)
    );
  }else if($giro == 'Tortillería'){
    $t = 'tortilleria_datos';
    $et = $e['extras']->tortilleria;
    $it = array(
      'usuarioid'     => $id,
      'kilos_dia'     => $et->kilos,
      'otro_producto_t' => $et->diferente
    );
  }
  if($t){
    $db->i($t, $it);
  }

  //FALTA LATITUD LONGITUD, HORA FINAL, HORA INICIODASJHDSA
  $emailAsesor = $g['emailAsesor'];
  $emailAsesor = $emailAsesor->email;
  $idAsesor = $db->fetch('SELECT usuarioid FROM usuarios_representantes WHERE email="'.$emailAsesor.'" LIMIT 1')['usuarioid'];

  $o['id']  = $id;
  $o['conv'] = $e['convocatoria'];


  $db->i('trasesormicroempresario', array('idAsesor'=>$idAsesor, 'idMicroNegocio'=>$id));

  $i_v = array(
    'id_asesor' => $idAsesor,
    'id_negocio'  => $id,
    'fecha_hora_inicio' => $start,
    'fecha_hora_final'  => $end,
    'longitud_final'    => $lng,
    'longitud_inicio'   => $lng,
    'latitud_inicio'    => $lat,
    'latitud_final'     => $lat
  );

  $db->i('visita', $i_v);


  // FOTOS
  $pic164 = $photos['pic1'];
  $pic264 = $photos['pic2'];

  $relpath = 'microphotos/';
  $abspath = dir.'/api/'.$relpath;

  $pic1url = $abspath.save_base64_image($pic164, 'photo_1_'.time(), $relpath);
  $pic2url = $abspath.save_base64_image($pic264, 'photo_2_'.time(), $relpath);

  $db->u('usuarios_microempresarios', array(
    'pic1'  => $pic1url,
    'pic2'  => $pic2url,
    'fechapic1' => datestamp(),
    'fechapic2' => datestamp()
  ),
  array(
    'usuarioid' => $id
  )
  );
  simpleGet('http://www.descifrainadem.mx/Auxiliares/EnviarCorreoClave.ashx?Id='.$id);


  #$slack->send('Microempresario creado ID #'.$id);

  echo json_encode($o);
  die();
}

function save_base64_image($base64_image_string, $output_file_without_extentnion, $path_with_end_slash="" ) {
    //usage:  if( substr( $img_src, 0, 5 ) === "data:" ) {  $filename=save_base64_image($base64_image_string, $output_file_without_extentnion, getcwd() . "/application/assets/pins/$user_id/"); }
    //
    //data is like:    data:image/png;base64,asdfasdfasdf
    $splited = explode(',', substr( $base64_image_string , 5 ) , 2);
    $mime=$splited[0];
    $data=$splited[1];

    $mime_split_without_base64=explode(';', $mime,2);
    $mime_split=explode('/', $mime_split_without_base64[0],2);
    if(count($mime_split)==2)
    {
        $extension=$mime_split[1];
        if($extension=='jpeg')$extension='jpg';
        //if($extension=='javascript')$extension='js';
        //if($extension=='text')$extension='txt';
        $output_file_with_extentnion = $output_file_without_extentnion.'.'.$extension;
    }
    $o = file_put_contents( $path_with_end_slash . $output_file_with_extentnion, base64_decode($data) );
    return $output_file_with_extentnion;
}
function saveAnalisis(){
  global $db;
  $g = $_GET;
  $g = file_get_contents("php://input");
  $g = (array) json_decode($g);
  $text = $g['text'];
  $id = $g['id'];
  $col = $g['col'];

  $u = array(
    $col => htmlspecialchars($text),
    $col.'_status'  => 1
  );




  if($col !== 'analisis'){
    $u['fecha_'.$col] = datestamp();
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

  die();
}

function toggle_me(){
  global $db;
  $id = $_GET['id'];
  $s = $db->fetch('SELECT status FROM usuarios_microempresarios WHERE usuarioid='.$id);
  $s = $s['status'];

  if($s == 0){
    $ns = 1;
  }else{
    $ns = 0;
  }

  $db->u('usuarios_microempresarios', array('status'=>$ns), array('usuarioid'=>$id));
  echo 'OK';
  die();

}




function createReporte(){
  global $db, $slack;
  $g = $_GET;
  //http://104.130.48.96/ReporteINADEM/GenerarReporte.ashx?Identificador=ID&Latitud=LAT&Longitud=LON&Giro=GIRO&Empresa=EMP&Empresario=EMPRE&Consultor=CONS
  $url = 'http://52.161.23.253/ReporteINADEM/GenerarReporte.ashx';
  $id = $g['id'];
  $o = array();
  $me = get_microempresario_info($id);

  $generando = ($me['generandoReporte'] == 1) ? true : false;
  $o['generando'] = $generando;
  $o['megenerando'] = $me['generandoReporte'] ;
  $fq = 'SELECT id_asesor FROM visita WHERE id_negocio='.$id.' LIMIT 1';
  $rid = $db->fetch($fq)['id_asesor'];
  $o['msg'] = 'wait';
  $q = array(
    'Identificador' => $id,
    'Latitud'       => $me['lat'],
    'Longitud'      => $me['lng'],
    'Giro'          => get_giro_name($me['giro']),
    'Empresa'       => $me['nombre_negocio'],
    'Empresario'    => $me['nombre'],
    'Consultor'     => get_asesor_name($rid)
  );

  $lurl = $url.'?'.http_build_query($q);
  $pdfurl = 'http://52.161.23.253/ReporteINADEM/Reportes/INADEM_'.$id.'.pdf';
  $urexists = UR_exists($pdfurl);
  $o['urexists'] = $urexists;
  if(!$urexists && !$generando){
    $db->u('usuarios_microempresarios', array(
      'generandoReporte'  => 1
    ), array('usuarioid'=>$id));
		//$context = stream_context_create(array('http' => array('header'=>'Connection: close\r\n')));
    //$rspns = file_get_contents($lurl, false, $context);
    $rspns = simpleGet($lurl);
    $o['calledws'] = true;
    #$slack->send('Solicitud de Reporte Generada - ID #'.$id);
    if($rspns){
      $urexists = UR_exists($pdfurl);
    }
  }else if($urexists){
    $db->u('usuarios_microempresarios', array(
      'reportePDF'  => $rspns,
      'existeReportePDF'  => 1
    ), array('usuarioid'=>$id));
    $o['msg'] = 'ok';
  }
  echo json_encode($o);
  die();
}
function test(){
  var_dump(file_get_contents('http://52.161.23.253/ReporteINADEM/Reportes/INADEM_37097.pdf'));
}
function get_full_me(){
  global $db;
  if(isset($_GET['id'])){
    $id = $_GET['id'];
    $r = $db->fetch('SELECT * FROM usuarios_microempresarios WHERE usuarioid='.$id);
  }else if(isset($_GET['email'])){
    $email = $_GET['email'];
    $r = $db->fetch('SELECT * FROM usuarios_microempresarios WHERE usuario="'.$email.'"');
    $id = $r['usuarioid'];
  }

  $r['fechaalta'] = pdate($r['fechaalta'], false);
  $r['giro'] = get_giro_name($r['giroid']);
  $r['pic1'] = filterpic($r['pic1']);
  $r['pic2'] = filterpic($r['pic2']);
  $r['isokconv'] = get_micro_entregables($id)['isokconv'];
  $r = $db->array_utf8_encode($r);
  echo json_encode($r);
}
function get_link_anarep(){
  global $db;
  $id = isset($_GET['id']) ? $_GET['id'] : false;
  if(!$id){
    $email = $_GET['email'];
    $id = $db->fetch('SELECT usuarioid FROM usuarios_microempresarios WHERE usuario="'.$email.'"')['usuarioid'];
  }
  $table = $_GET['table'];

  switch($table){
    case "plan_financiamiento":
      $t = 1;
    break;
    case "plan_accion":
      $t = 3;
    break;
    case "analisis_micromercado":
      $t = 2;
    break;
    default:
      $t = false;
    break;
  }

  if($t){
    //Es análisis
    $urlbase = 'http://www.descifrainadem.mx/Reporte/Reportes/';
    $trasesor = $db->fetch('SELECT * FROM trasesormicroempresario WHERE idMicroNegocio='.$id);
    $sub = $trasesor['id'].'-'.$trasesor['idAsesor'].'-'.$id.'-'.$t.'.pdf';
    $url = $urlbase.$sub;
  }else{
    $urlbase = 'http://52.161.23.253/ReporteINADEM/Reportes/';
    $q = 'SELECT reportePDF FROM usuarios_microempresarios WHERE usuarioid='.$id;
    $r = $db->fetch($q)['reportePDF'];
    $url = $urlbase.'INADEM_'.$id.'.pdf';
  }
  $o = array('url'=>$url);

  echo json_encode($o);
  die();
}
function checkme_email(){
  global $db;
  $email = $_GET['email'];
  $convocatoria = $_GET['convocatoria'];
  $qu = 'SELECT usuarioid, convocatoria FROM usuarios_microempresarios WHERE usuario="'.$email.'" AND status="1"';
  $r = $db->assoc($qu);
  $o =  array('msg'=>'OK');
  if(count($r)){
    foreach($r as $c){
      $oldconvocatoria = $c['convocatoria'];
      if(restriccion_convocatoria($oldconvocatoria, $convocatoria) || restriccion_convocatoria($convocatoria,$oldconvocatoria)){
        $o['msg'] = 'NOTOK';
      }
    }
  }
  echo json_encode($o);
}
function get_micro_entregables_ajax(){
  $r = get_micro_entregables($_GET['id']);
  echo json_encode($r);
  die();
}

function curso_create_me(){
  global $db;
  $p = $_GET;
  $i = array(
        'nombre'          => $p['name'],
        'email'           => $p['email'],
        'usuario'         => $p['email'],
        'password'        => rand(9999, 999999),
        'status'          => 1
      );
  $id = $db->i('usuarios_representantes', $i);
  $r = $db->fetch('SELECT * FROM usuarios_representantes WHERE usuarioid='.$id);
  $db->i('relacionconvrepre', array('idRepresentante'=>$id, 'idConvocatoria'=>$p['convocatoria']));
  simpleGet('http://www.descifrainadem.mx/Auxiliares/EnviarCertificadoConsultor.ashx?Id='.$id);
  echo $id;
  die();
}
