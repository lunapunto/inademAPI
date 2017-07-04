<?php
require_once 'functions.php';
$action = $_REQUEST['action'];
call_user_func($action);
die();
