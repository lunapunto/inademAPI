<?php
require_once 'functions.php';
?>


</div><!--END CONTENT-->

<div id="chips-container">

</div>


<?php
  $lastAct = filemtime('functions.php');
  $cssLastAct = filemtime('../assets/css/admin.css');
  $jsLastAct = filemtime('../assets/js/admin.js');
  $act = max($lastAct, $cssLastAct, $jsLastAct);
?>
<div id="copyright">
  Actualizado <em><?= pdate($act);?></em> | LP INADEM Admin v1.0.1 &copy; <?= date('Y');?> | <a href="mailto:developer@lunapunto.com">Asistencia</a>
</div>





</div>
<?php if($msg = i('alertmsg', $_GET)):?>
<script type="text/javascript">
$(document).ready(function(){
  alertS("<?= $msg;?>", "<?= v('alerttitle', $_GET);?>");

})
</script>
<?php endif;?>
<input type="hidden" id="globalSep" value="<?= GLOBAL_SEP;?>"/>
<input type="hidden" id="siteName" value="<?= SITENAME;?>">
<input type="hidden" id="originalDir" value="<?= dir;?>">
<input type="hidden" id="ajaxurl" value="<?= admin.'/ajax.php';?>">

<script src="https://unpkg.com/notie"></script>

</body>
</html>
