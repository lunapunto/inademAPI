<?php
require_once 'functions.php';

set_meta('admin_last_logout', time());
setcookie('admin'.SLUG,'',time()-10,'/');
unset($_COOKIE['admin'.SLUG]);
header('Location:'.dir.'/admin');
 ?>
