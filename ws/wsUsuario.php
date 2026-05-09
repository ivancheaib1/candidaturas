<?php 
require_once('conexion.php');
require_once('funciones.php');

encabezadoJSON(); //Imprimir Json
seguridadJSON();



 
///$color = array("warning","success","danger","danger");
if(isset($_POST['accion']) and $_POST['accion'] == 'formIniciar')
{
  
	$query = "SELECT
	u.id,
	u.nivel_acceso,	
	u.distritos,
	p.nombre,
	p.apellidos,
	p.documento,
	p.razon_social,
	p.ruc,
	p.telefono,
	p.correo
	FROM
	".$prefijo_tabla."usuarios u
	INNER JOIN ".$prefijo_tabla."personas p ON u.id_persona = p.id
	WHERE p.estado = 1 AND u.estado = 1
	AND (p.correo = '".trim($_POST['correo'])."' OR  p.documento = '".trim($_POST['correo'])."') AND u.contrasena = '".contrasena(trim($_POST['contrasena']))."'" ;
	$resultados = select_sql($query);
	$result_json = array('titulo' => 'No se pudo iniciar', 'mensaje' => 'Por favor verifique su contraseña',  'error' => true );
	
	if((!$resultados['error']) and count($resultados['resultado']) > 0 )
	{
		$_SESSION['usuario'] = $resultados["resultado"][0];
		$result_json = array(
			'titulo' => 'Iniciar sessión',
			'mensaje' => 'Iniciado correctamente!',
			'error' => false,
			'nivel_acceso' => $resultados["resultado"][0]['nivel_acceso'],
			'usuario' => $resultados["resultado"][0]['correo']
		);

	}	
	 
	$result_json = ($resultados['error']) ? $resultados['resultado'] :$result_json;
	imprimirJson($resultados['error'], $result_json);
 
		
}



if(isset($_POST['accion']) and $_POST['accion'] == 'nuevacontrasena')
{
	
		$result_json = array('titulo' => 'No se pudo actualizar', 'mensaje' => 'Ocurrio algun error!',  'error' => true );
	
		$query = "UPDATE  ".$prefijo_tabla."usuarios  SET 
		contrasena = '".contrasena(trim($_POST['recontrasena']))."', 
		usuario_mod = ".$_SESSION['usuario']["id"].", 
		fecha_mod = '".date('Y-m-d H:i:s')."'  
		WHERE id = ".$_SESSION['usuario']["id"];
		
		$resultados = ejecutar_sql($query);
		
		if(!$resultados['error']){
			$result_json = array('titulo' => 'Actualización', 'mensaje' => 'Usuario actualizado correctamente!' ,  'error' => false  );	
	
		}
	
	imprimirJson($resultados['error'], $result_json);


}

// Fallback: si no se ejecutó ninguna acción, devolver error JSON
imprimirJson(true, "Acción no reconocida");

?>
