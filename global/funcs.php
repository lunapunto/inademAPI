<?php
require_once('prefuncs.php');

function pc_money($qty){
    $qty = round($qty);
    return '$'.number_format($qty,0);
}
function str_to_array($str, $delimiter = ',', $filter = true){
    $exploded = explode($delimiter,$str);
    if($filter){
    $exploded = array_filter($exploded);
    }
    return $exploded;
}
function array_to_str($array,$glue = ',',$filter = true){
    if($filter){
    $array = array_filter($array);
    }
    $imploded = implode($glue,$array);
    return $imploded;
}
function sdate($time, $istime = false){
  return date('d/m/Y H:i', strtotime($time));
}
function cdates($start, $end){
  $start_t = strtotime($start);
  $end_t = strtotime($end);
  $add = '';
  if($end_t < $start_t){
    $extradays = day_difference($start, $end);
    if($extradays){
      $add = '+'.$extradays.' día'.($extradays !== 1 ? 's' : '');
    }
  }
  return date('H:i', $start_t).$add;
}
function day_difference($start, $end){
  $start = date_create($start);
  $end = date_create($end);
  return date_diff($start, $end)->days;
}
function pdate($timestamp, $istime = true){
    $months = array();
    $months[] = 'enero';
    $months[] = 'febrero';
    $months[] = 'marzo';
    $months[] = 'abril';
    $months[] = 'mayo';
    $months[] = 'junio';
    $months[] = 'julio';
    $months[] = 'agosto';
    $months[] = 'septiembre';
    $months[] = 'octubre';
    $months[] = 'noviembre';
    $months[] = 'diciembre';
    $badtime = false;
    if($istime){
       $ts = $timestamp;
    }else{
      $ts = strtotime($timestamp);
    }

    if(!$ts){
      $badtime = true;
    }

    $month = date('n',$ts);
    $month = $months[$month-1];
    $day = date('j',$ts);
    $year = date('Y',$ts);

    $h = date('H:i',$ts).'hrs.';

    $rspns = $day.' de '.$month.' del '.$year.', '.$h;
    if($badtime){
      $rspns = 'Sin fecha';
    }
    return $rspns;
}
function set_meta($key,$value){
  $db = new db;
  return $db->ioru('meta', array('name'=>$key, 'value'=>$value));
}
function get_meta($key){
  $db = new db;
  $r = $db->fetch('SELECT * FROM meta WHERE name="'.$key.'"');
  if(count($r)){
  return $r['value'];
  }else{
  return false;
  }
}
function get_user_meta($key,$id){
  $str = $key.'_'.$id;
  return get_meta($str);
}
function set_user_meta($key,$value,$id){
  $str = $key.'_'.$id;
  return set_meta($str,$value);
}
function delete_meta($key){
  $db = new db;
  $q = 'DELETE FROM meta WHERE name="'.$key.'"';
  $db->query($q);
}
function san($str){
  return ($str) ? utf8_encode(ucwords(strtolower($str))) : '-';
  //return ($str) ? utf8_decode(ucwords(strtolower($str))) : '';
  //return ($str) ? ucwords(strtolower($str)) : '';

}
function safe_json_encode($o){
  return utf8_encode(json_encode($o));
}
function get_asesor_name($id){
  global $db;
  $q = 'SELECT nombre FROM usuarios_representantes WHERE usuarioid='.$id;
  $name = $db->fetch($q)['nombre'];
  return san($name);
}
function get_microempresario_name($id){
  global $db;
  $q = 'SELECT nombre FROM usuarios_microempresarios WHERE usuarioid='.$id;
  $name = $db->fetch($q)['nombre'];

  return san($name);
}
function filterpic($src){
  $urlPics = 'http://www.descifrainadem.mx/AplicacionMovil/uploads/';
  $isnew = filter_var($src, FILTER_VALIDATE_URL);
  if($isnew){
    return $src;
  }else{
    return $urlPics.$src;
  }
}
function get_microempresario_info($id){
  global $db;
  $urlPics = 'http://www.descifrainadem.mx/AplicacionMovil/uploads/';
  $q = 'SELECT nombre,nombre_negocio,rfc,numero_folio,generandoReporte,giroid,convocatoria,longitud,latitud,convocatoria_year,pic1 FROM usuarios_microempresarios WHERE usuarioid='.$id;
  $name = $db->fetch($q);
  $o = array(
    'nombre'          => san($name['nombre']),
    'rfc'             => $name['rfc'],
    'folio'           => $name['numero_folio'],
    'giro'            => $name['giroid'],
    'lng'             => $name['longitud'],
    'lat'             => $name['latitud'],
    'convocatoria'    => $name['convocatoria'].' ['.$name['convocatoria_year'].']',
    'pic1'            => filterpic($name['pic1']),
    'nombre_negocio'  => $name['nombre_negocio'],
    'generandoReporte'=> $name['generandoReporte']
  );
  return $o;
}
function get_microempresario_giro($id, $showname = true){
  global $db;
  $q = 'SELECT giroid FROM usuarios_microempresarios WHERE usuarioid='.$id;
  $giro = $db->fetch($q)['giroid'];

  if($showname){
    return get_giro_name($giro);
  }else{
    return $giro;
  }
}
function get_giro_name_negocio($id){
  global $db;
  $q = 'SELECT giroid FROM usuarios_microempresarios WHERE usuarioid='.$id;
  return get_giro_name($db->fetch($q)['giroid']);
}
function get_giro_name($id){
  global $db;
  $q = 'SELECT descripcion FROM cat_giro WHERE giroid='.$id;
  $name = $db->fetch($q)['descripcion'];
  return san($name);
}
function hrs($time, $raw = false){
  $o = '';
  $hours = floor($time);
  $mins_dec = $time - $hours;
  $mins = floor($mins_dec * 60);
  if($hours){
    $o .= $hours.' hr'.(1 !== $hours ? 's' : '').'.';
  }
  if($mins){
    $o .= ' '.$mins.' min'.(1 !== $mins ? 's' : '').'.';
  }

  if(!$hours && !$mins){
    $o = '-';
  }
  return $o;
}
function get_microempresario_negocio($id){
  global $db;
  $q = 'SELECT nombre_negocio FROM usuarios_microempresarios WHERE usuarioid='.$id;
  return $db->fetch($q)['nombre_negocio'];
}
function fdec($search, $haystack){
  $search = (string) $search;
  if(0 < strlen($search)){
    if(!(strpos($haystack, $search) === FALSE)){
      return true;
    }else{
      return false;
    }
  }else{
    return false;
  }
}
function get_convocatoria_name($id){
  global $db;
  $q = 'SELECT nomConvocatoria, yearConvocatoria FROM convocatoria WHERE idConvocatoria='.$id;
  $r = $db->fetch($id);
  return $r['nomConvocatoria'].' ['.$r['yearConvocatoria'].']';
}
function get_time_micro($id){
  global $db;

  $time = '2017-05-14 00:01:00';
  $extra = 0;

  $q = 'SELECT (SUM(TIME_TO_SEC(TIMEDIFF(fecha_hora_final, fecha_hora_inicio))) / 3600) AS time FROM visita WHERE id_negocio='.$id;
  $aq = 'SELECT fecha_hora_final FROM visita WHERE id_negocio='.$id.' ORDER BY fecha_hora_final DESC LIMIT 1';
  $aq = $db->fetch($aq);
  if(count($aq)){
    $lq = $aq['fecha_hora_final'];
    $lq_t = strtotime($lq);
    $real = strtotime($time);


    if($lq_t < $real){
      $extra = 1;
    }

  }

  return abs($db->fetch($q)['time'] + $extra);
}
function UR_exists($url){
   $headers=get_headers($url);
   return stripos($headers[0],"200 OK")?true:false;
}

function get_micro_entregables($idnegocio, $time = false){
  global $db;
  $o = array();
  $ent = 0;
  $totalent = 4;
  $r = 'SELECT convocatoria, analisis_plan_accion_status, analisis_financiamiento_status, analisis_status, pic1,pic2, analisis, analisis_financiamiento, analisis_plan_accion, reportePDF, existeReportePDF FROM usuarios_microempresarios WHERE usuarioid='.$idnegocio;
  $r = $db->fetch($r);
  $convocatoria = $r['convocatoria'];
  $isokconv = false;
  $o['falta'] = array();
  $o['timeFalta'] = false;
  $time = get_time_micro($idnegocio);
  $o['formattedTime'] = hrs($time);
  if($convocatoria == '4.1 Desarrollo de Capacidades Empresariales para Microempresas.' || $convocatoria == "5.2  Desarrollo de Capacidades Empresariales para Microempresas a través de la incorporación de Tecnologías de la Información y Comunicaciones (TIC’s)." ){
      $isokconv = true;
      $totalent = 6;


      $isokTime = (6 <= $time) ? true : false;
      if($isokTime){
        $ent++;
      }else{
        $o['falta'][] = 'Tiempo de visita (Faltan '.hrs(6 - $time).')';
        $o['timeFalta'] = true;
      }
      $o['time'] = $isokTime;
      $o['rawTime'] = $time;


      $en = 'SELECT id_encuesta FROM micro_encuesta WHERE usuarioid='.$idnegocio;
      $enr = $db->fetch($en);
      $as = $db->fetch('SELECT res5 FROM micro_encuesta WHERE usuarioid='.$idnegocio);

      if(count($as)){
          $o['dispositivo'] = true;
          $ent++;
      }else{
        $o['dispositivo'] = false;
        $o['falta'][] = 'Microempresario no ha llenado encuesta.';
      }

      if(true){
        $o['encuesta'] = true;
        $ent++;
      }else{
        $o['encuesta'] = false;
        $o['falta'][] = 'Encuesta';
      }

      $analisis = 0;


      //Plan accion
      if(strlen($r['analisis_plan_accion']) && $r['analisis_plan_accion_status']){
        $analisis++;
        $o['plan_accion'] = true;
      }else{
        $o['plan_accion'] = false;
        $o['falta'][] = 'Plan de acción';
      }

      //Financiamiento
      if(strlen($r['analisis_financiamiento']) && $r['analisis_financiamiento_status']){
        $analisis++;
        $o['financiamiento'] = true;
      }else{
        $o['financiamiento'] = false;
        $o['falta'][] = 'Financiamiento';
      }

      //Financiamiento
      if(strlen($r['analisis']) && $r['analisis_status']){
        $analisis++;
        $o['analisis'] = true;
      }else{
        $o['analisis'] = false;
        $o['falta'][] = 'Análisis';
      }

      if($analisis == 3){
        $ent++;
        $o['total_analisis'] = true;
      }else{
        $o['total_analisis'] = false;
      }
      $pdfurl = 'http://52.161.23.253/ReporteINADEM/Reportes/INADEM_'.$idnegocio.'.pdf';

      //Reporte
      if(UR_exists($pdfurl)){
        $o['reporte'] = true;
        $ent++;
      }else{
        $o['reporte'] = false;
        $o['falta'][] = 'Reporte de Micromercado';
      }
  }else{
    $totalent = 2;
    $ent = 1;
    $o['falta'] = array();
  }

  //FOTOS
  if(strlen($r['pic2']) && strlen($r['pic1'])){
    $o['pics'] = true;
    $ent++;
  }else{
    $o['pics'] = false;
    $o['falta'][] = 'Una o dos fotos';
  }


  $o['ent'] = $ent;

  $entno = $ent;
  if($entno == $totalent){
    $status = 1;
    $color = 'rgb(3, 194, 62)';
    $msg = 'Visita concluída';
  }else if($entno == ($totalent - 1) && $ent['falta'][0] == 'Microempresario no ha llenado encuesta.'){
    $status = 2;
    $color = 'rgb(236, 183, 26)';
    $msg = 'Microempresario no ha llenado encuesta.';
  }else if($isokconv && $analisis < 3){
    $status = 4;
    $msg = 'Faltan análisis';
    $color = 'rgb(208, 18, 69)';
  }else{
    $status = 3;
    $color = 'rgb(208, 18, 69)';
    $msg = 'Faltan entregables ['.$entno.'/'.$totalent.']';
  }
  $o['status'] = $status;
  $o['meta'] = array('status'=>$status, 'color'=>$color,'msg'=>$msg);
  $o['formattedEnt'] = $entno.'/'.$totalent;
  $o['totalent'] = $totalent;
  $o['isokconv'] = $isokconv;
  return $o;
}

function monthName($index){
  $months = array(
    'Enero',
    'Febrero',
    'Marzo',
    'Abril',
    'Mayo',
    'Junio',
    'Julio',
    'Agosto',
    'Septiembre',
    'Octubre',
    'Noviembre',
    'Diciembre'
  );
  return $months[$index-1];
}
function is_url_exist($url){
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if($code == 200){
       $status = true;
    }else{
      $status = false;
    }
    curl_close($ch);
   return $status;
}
function simpleGet($url){
  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $url,
    CURLOPT_USERAGENT => 'LP SAMPLE'
  ));
  $resp = curl_exec($curl);
  curl_close($curl);
  return $resp;
}
