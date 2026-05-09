<?php
require_once('ws/conexion.php');
require_once('ws/funciones.php');

// Destruir la sesión completamente
if(isset($_SESSION['usuario'])){
	$_SESSION['usuario'] = NULL;
	unset($_SESSION['usuario']);
}

// Destruir todas las variables de sesión
session_unset();

// Destruir la sesión
session_destroy();

// Redirigir al login
header("Location: login.php");
die();
?>
