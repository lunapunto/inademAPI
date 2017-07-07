<?php
#Config file
require_once dirname(__DIR__).'/config.php';
require_once dirname(__DIR__).'/vendor/autoload.php';





#Static VARIABLES
$assets = dir.'/assets';
$css = $assets.'/css';
$js = $assets.'/js';
$main = dir.'/main';

#IMPORT ALL THE CLASSES
/*
$allClasses = scandir(dirname(__DIR__).'/classes');
$classes = array_diff($allClasses, array('.', '..'));
$dbFirst = array_search('db.php', $classes);
$dbClass = $classes[$dbFirst];
require_once dirname(__DIR__).'/classes/'.$dbClass;

array_splice($classes, $dbFirst, 1);
foreach($classes as $class){
  require_once dirname(__DIR__).'/classes/'.$class;
}
*/
require_once '../classes/db.php';
require_once '../classes/closeclient.php';

#PARTS
function get_part($part,$queries=array(),$dirname='main'){
    $dir = dir;
    $current_server = i('PHP_SELF', $_SERVER);
    $current_server = basename($current_server);
    $queries['current_server'] = $current_server;
    $r = count($_REQUEST) ? $_REQUEST : array();
    $queries = array_merge($queries,$_COOKIE, $_GET);
    $q = http_build_query($queries);
    $uri = $dir.'/'.$dirname.'/'.$part.'.php?'.$q;
    return file_get_contents($uri);
}
function get_header($queries=array(), $dirname = 'main'){
    echo get_part('header',$queries,$dirname);
}
function get_menu($queries=array(),$dirname = 'main'){
    echo get_part('menu', $queries,$dirname);
}
function get_footer($query = array(),$dirname='main'){
    echo get_part('footer',$query,$dirname);
}

#Format title
function get_title($title = false){
  $subfix = SITENAME;
  $middle = ' '.GLOBAL_SEP.' ';
  if($title){
  $o = $title.$middle.$subfix;
  }else{
  $o = $subfix;
  }
  echo $o;
}

#Scripts and styles
function scriptsstyles($isadmin = false){
    global $js,$css;
    $styles = array();
    $scripts = array();
    $scripts['pace'] = $js.'/pace.js';
    $styles['material_icons'] = 'https://fonts.googleapis.com/icon?family=Material+Icons';
    $styles['font'] = 'https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i';
    $styles['open'] = 'https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i';
    $scripts['jquery'] = 'https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js';
    $scripts['picker'] = $js.'/picker.js';
    $scripts['chart'] = $js.'/chart.bundle.js';
    $scripts['picker_date'] = $js.'/picker.date.js';
    $scripts['picker_time'] = $js.'/picker.time.js';
    $scripts['picker_es'] = $js.'/es_ES.js';
    $styles['picker'] = $css.'/default.css';
    $styles['picker_date'] = $css.'/default.date.css';
    $styles['picker_time'] = $css.'/default.time.css';
    $scripts['mask'] = $js.'/mask.js';
    $scripts['hammer'] = 'https://ajax.googleapis.com/ajax/libs/hammerjs/2.0.8/hammer.min.js';
    $scripts['jqueryui_js'] = 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js';
    $scripts['velocity'] = $js.'/velocity.js';
    #$scripts['fontawesome'] = 'https://use.fontawesome.com/e24e16b876.js';
    $scripts['colorpicker'] = $js.'/jscolor.min.js';
    $styles['pace'] = $css.'/pace.css';
    if($isadmin){
      $styles['lightbox'] = '//cdn.rawgit.com/noelboss/featherlight/1.7.1/release/featherlight.min.css';
      $scripts['lightbox'] = '//cdn.rawgit.com/noelboss/featherlight/1.7.1/release/featherlight.min.js';
      $styles['grid'] = 'https://cdnjs.cloudflare.com/ajax/libs/jsgrid/1.5.3/jsgrid.min.css';
      $styles['grid-theme'] = 'https://cdnjs.cloudflare.com/ajax/libs/jsgrid/1.5.3/jsgrid-theme.min.css';
      $scripts['grid'] = 'https://cdnjs.cloudflare.com/ajax/libs/jsgrid/1.5.3/jsgrid.min.js';
      $styles['admin'] = $css.'/admin.css?version=4.0';
      $scripts['admin'] = $js.'/admin.js?version=4.0';
    }else{
      $styles['master'] = $css.'/main.css';
      $scripts['master'] = $js.'/main.js';
    }

    $o = '';
    foreach($scripts as $key=>$script){
     $o.= '<script  type="text/javascript" name="'.$key.'" src="'.$script.'"></script>';
    }
    foreach($styles as $key=>$style){
     $o.= '<link rel="stylesheet" type="text/css" href="'.$style.'" name="'.$key.'"/>';
    }
    echo $o;
}
function i($element, $array = false){
  if(!$array){
    $array = $_GET;
  }
  if(isset($array[$element])){
    return $array[$element];
  }else{
    return false;
  }
}
function v($element, $array = false){
  if(!$array){
    $array = $_POST;
  }
  $i = i($element, $array);
  return ( $i ? $i : '');
}
function build_alert($url, $msg, $title, $additional = array()){
  $q = array('alertmsg'=>$msg, 'alerttitle'=>$title);
  $q = array_merge($q, $additional);
  return $url.'?'.http_build_query($q);
}
