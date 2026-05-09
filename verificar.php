<?php
require_once('ws/conexion.php');
require_once('ws/funciones.php');

// Verificar si hay sesión de usuario
if(!isset($_SESSION['usuario'])){
	die("NO HAY SESION DE USUARIO");
}

// Obtener el nivel de acceso - sin conversión por ahora
$nivel_acceso = isset($_SESSION['usuario']['nivel_acceso']) ? $_SESSION['usuario']['nivel_acceso'] : 'UNDEFINED';

// MOSTRAR INFORMACIÓN DE DEBUG
echo "<h1>DEBUG VERIFICAR.PHP</h1>";
echo "<p><strong>nivel_acceso:</strong> " . htmlspecialchars($nivel_acceso) . "</p>";
echo "<p><strong>tipo:</strong> " . gettype($nivel_acceso) . "</p>";
echo "<p><strong>var_dump:</strong> <pre>";
var_dump($nivel_acceso);
echo "</pre></p>";

echo "<p><strong>SESSION completa:</strong> <pre>";
var_dump($_SESSION['usuario']);
echo "</pre></p>";

// Comparaciones
echo "<p><strong>Comparaciones:</strong></p>";
echo "<p>\$nivel_acceso === '1': " . (($nivel_acceso === '1') ? 'TRUE' : 'FALSE') . "</p>";
echo "<p>\$nivel_acceso === '2': " . (($nivel_acceso === '2') ? 'TRUE' : 'FALSE') . "</p>";
echo "<p>\$nivel_acceso === 2: " . (($nivel_acceso === 2) ? 'TRUE' : 'FALSE') . "</p>";
echo "<p>\$nivel_acceso == '2': " . (($nivel_acceso == '2') ? 'TRUE' : 'FALSE') . "</p>";
echo "<p>\$nivel_acceso == 2: " . (($nivel_acceso == 2) ? 'TRUE' : 'FALSE') . "</p>";

// Ahora redirigir basado en comparación flexible
if($nivel_acceso == 1){
	header("Location: usuarios.php");
	die();
} else if($nivel_acceso == 2){
	header("Location: inicio.php");
	die();
} else if($nivel_acceso == 3){
	header("Location: invitados.php");
	die();
} else {
	echo "<p>No se pudo determinar la redirección. Valor: " . htmlspecialchars($nivel_acceso) . "</p>";
}
?>
