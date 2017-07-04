<?php
require_once('../global/prefuncs.php');
global $assets;
$title = isset($_GET['title']) ? $_GET['title'] : false;
$classes = array();
if($path = i('path', $_GET)){
  $classes[] = 'body_'.$path;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <title><?php get_title($title);?></title>
  <meta charset="utf-8" />
  <meta content="width=device-width" name="viewport" />
  <link href="<?= asset.'/favicon.png'?>" rel="icon" type="image/png" />
  <meta name="author" content="Luna Punto" />

  <!--FILL!!!
  <meta name="keywords" content="" />
  <meta name="description" content="" />
  <meta property="og:url"                content="http://www.nytimes.com/2015/02/19/arts/international/when-great-minds-dont-think-alike.html" />
  <meta property="og:type"               content="article" />
  <meta property="og:title"              content="When Great Minds Don’t Think Alike" />
  <meta property="og:description"        content="How much does culture influence creative thinking?" />
  <meta property="og:image"              content="http://static01.nyt.com/images/2015/02/19/arts/international/19iht-btnumbers19A/19iht-btnumbers19A-facebookJumbo-v2.jpg" />
  -->
  <?php
    scriptsstyles(false);
  ?>
</head>
<body class="<?= implode(' ', $classes);?>">
  <div id="wrapper">
    <div id="container">
