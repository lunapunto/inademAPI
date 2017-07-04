<?php

#DIRECTORY
/*
define('dir','https://beta.lunapunto.com/inadem');

*/
define('dir','http://'.$_SERVER['HTTP_HOST'].'/INADEM');

define('asset',dir.'/assets');


#Sitename
define('SITENAME', 'INADEM');
define('GLOBAL_SEP', '-');

#Site slug
define('SLUG','inadem');

#DEFINE DB VARIABLES
define('DATABASE', 'inademdescifra');
define('USERNAME','muntts');
define('PASSWORD', 'honi1921');
define('HOST', 'localhost');

/*
define('DATABASE', 'lunapunt_in');
define('USERNAME','lunapunt_in');
define('PASSWORD', 'Arsnonverba7');
define('HOST', 'localhost');
*/





#DEFINE TIMEZONE
date_default_timezone_set ('America/Mexico_City');
