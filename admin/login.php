<?php
require_once 'functions.php';
$q = array('path'=>'login', 'title'=>'Ingresar', 'screen'=>'notloggedin');
admin_filter($q, $_COOKIE);
get_header($q,'admin');
?>





<div id="logincontainer">

  <div class="cxy lc">
    <div class="logo">
      <img src="<?= adminasset.'/logo.svg';?>">
    </div>
    <div class="title">
      Inicio de sesión
    </div>
    <form method="post" action="ajax.php">

    <div class="ic">
      <input value="<?= v('email', $_GET);?>" type="text" required name="email" placeholder="Usuario" />

    </div>
    <div class="ic">
      <input type="password" required name="pass" placeholder="Password" />
    </div>
    <div class="sc">
      <input type="submit" value="Ingresar" />
    </div>


    <input type="hidden" name="action" value="checklogin" />
    </form>


    <div class="sub">
      Olvidé mi contraseña
    </div>

  </div>


</div>



<?php get_footer(array(),'admin');?>
