<?php
require_once('ws/conexion.php');
require_once('ws/funciones.php');

$mesa = (int)$_POST['mesa'];
$orden = (int)$_POST['orden'];
$distrito = (int)$_POST['distrito'];
$where = " WHERE mesa = $mesa AND orden = $orden AND cod_dist = $distrito";

$query = "SELECT
	mesa,
	orden,
	numero_ced,
	apellido,
	nombre,
	voto_registrado
FROM ".$prefijo_tabla."padron".$where;

$resultados = select_sql($query);

if($resultados['error'] || empty($resultados['resultado'])){
	header("Location: " . construirURL("carga_datos.php", array("status" => "no")));
	die();
}

$result_json = $resultados["resultado"][0];

$query_update = "UPDATE ".$prefijo_tabla."padron SET voto_registrado = 1".$where;
$update_resultado = ejecutar_sql($query_update);

if(!$update_resultado['error']){
	header("Location: " . construirURL("carga_datos.php", array("status" => "ok", "mesa" => $mesa, "orden" => $orden)));
	die();
} else {
	header("Location: " . construirURL("carga_datos.php", array("status" => "no")));
	die();
}
?>