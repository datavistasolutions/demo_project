<?php

ini_set('max_execution_time', 0);
set_time_limit(0);
ini_set('memory_limit','-1');
ini_set('display_errors',1);
error_reporting(E_ALL);
session_start();
date_default_timezone_set("Asia/Calcutta");
header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: GET, POST');


//Current Active Staff Id
$ActiveStaffLogin_Id = 1;


?>