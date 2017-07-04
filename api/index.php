<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: text/html;charset=utf-8');
require_once 'functions.php';
call_user_func($_REQUEST['action']);
