<?php
require_once('ws/conexion.php');
require_once('ws/funciones.php');

$usuario = trim($_POST['usuario']);
 $pass = contrasena(trim($_POST['contrasena']));
$where = " where p.documento = $usuario and contrasena = '$pass'";
//'".contrasena(trim($_POST['contrasena']))."'
 $query = "SELECT 
		p.id idpersona,
		p.documento,
		p.nombre,
		p.apellidos,
		p.ciudad,
		p.telefono,
		u.id,
		u.estado,
		u.nivel_acceso,
		u.distritos,
		u.contrasena
	FROM
	".$prefijo_tabla."usuarios u
	INNER JOIN ".$prefijo_tabla."personas p ON u.id_persona = p.id
	".$where ;
	$resultados = select_sql($query);
	$result_json = $resultados["resultado"][0];
	
	
	if($result_json['documento'] == $usuario and $result_json['contrasena'] == $pass){
		$_SESSION['usuario'] = $result_json['documento'];
		header("Location: http://".$_SERVER["SERVER_NAME"]."/".$carpeta_sistema."carga_datos.php");die();
		}
		else
		{
		header("Location: http://".$_SERVER["SERVER_NAME"]."/".$carpeta_sistema."veedor.php?error=si");die();	
			}
	/*
	$result_json = $resultados["resultado"][0];
	$result_json['encontro'] = false;
			  if($result_json['id'] != ""){
				  $result_json['encontro'] = true;
			  }
	*/		  
?>