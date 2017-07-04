<?php
require_once 'functions.php';
global $assets;
$title = isset($_GET['title']) ? $_GET['title'] : false;
$classes = i('classes');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <script>
    window.paceOptions = {
      //ajax: false
    }
  </script>
  <title><?php get_title($title);?></title>
  <meta charset="utf-8" />
  <meta content="width=device-width" name="viewport" />
  <link href="<?php echo $assets;?>/favicon.png" rel="icon" type="image/png" />
  <link rel="stylesheet" type="text/css" href="https://unpkg.com/notie/dist/notie.min.css">

  <?php scriptsstyles(true);?>
</head>
<!--<script>document.write('<script src="http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1"></' + 'script>')</script>-->
<body class="admin <?= $classes;?>">
<div id="wrapper">
<div id="alert">
  <div id="alert_content">
    <div id="alert_binder">
    <h1>Mensaje de prueba</h1>
    <div class="alert_p">
      Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
    </div>
    <div class="alert_buttons">
      <div class="button alert_close">
        Entendido
      </div>
    </div>
    </div>

  </div>
  <div class="alert_close alert_bgclose"></div>
</div>


<div id="admin_content">
