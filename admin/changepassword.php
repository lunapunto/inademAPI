<?php
require_once 'functions.php';
$q = array('path'=>'login', 'title'=>'Ingresar', 'screen'=>'notloggedin');
admin_filter($q, $_COOKIE);
get_header($q,'admin');
?>

<script type="text/javascript">
  //Verify passwords
  $(function(){
    $('#changepassword').submit(function(e){
      var fp = $(this).find('input[name=pass]').val();
      var sp = $(this).find('input[name=rpass]').val();
      if(fp !== sp){
        alertS('Las contraseñas no coinciden.', 'Error');
        return false;
      }else{
        return true;
      }
    })
  })

</script>



<div id="logincontainer">

  <div class="cxy lc">
    <div class="logo">
      <img src="<?= adminasset.'/logo.svg';?>">
    </div>
    <div class="title">
      Cambio de contraseña
    </div>
    <form method="post" action="ajax.php" id="changepassword">

    <div class="ic">
      <input value="<?= v('email', $_GET);?>" disabled type="text" required name="email" placeholder="E-mail" />

    </div>
    <div class="ic">
      <input type="password" required name="pass" placeholder="Password" />
    </div>
    <div class="ic">
      <input type="password" required name="rpass" placeholder="Repetir Password" />
    </div>
    <div class="sc">
      <input type="submit" value="Cambiar" />
    </div>

    <input type="hidden" name="username" value="<?= v('email', $_GET);?>">
    <input type="hidden" name="action" value="changepassword" />
    </form>



  </div>


</div>



<?php get_footer(array(),'admin');?>
