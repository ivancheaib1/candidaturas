<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if(!isset($_SESSION)){@session_start();}
date_default_timezone_set('America/Asuncion');

require_once('ws/conexion.php');
require_once('ws/funciones.php');
?>
