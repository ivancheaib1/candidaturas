<?php 
require_once('conexion.php');
require_once('funciones.php');

encabezadoJSON(); //Imprimir Json
seguridadJSON();

// Debug: ver qué POST data se recibe
error_log("POST data: " . json_encode($_POST));

if(isset($_POST['accion']) and $_POST['accion'] == 'listarPersonas')
{
 
	$where = " WHERE u.estado <> 10 ";
	$query = "SELECT
	u.id,
	p.nombre,
	p.apellidos,
	p.documento,
	p.telefono,
	u.estado,
	u.nivel_acceso
	FROM
	".$prefijo_tabla."usuarios u
	INNER JOIN ".$prefijo_tabla."personas p ON u.id_persona = p.id
	".$where."
	ORDER BY p.nombre " ;


	$resultados = select_sql($query);
	$result_json = " ";
	if((!$resultados['error']) and count($resultados['resultado']) > 0 )
	{
		
		foreach($resultados["resultado"] as $resul) {
			$estado = "";
			if($resul["estado"] == 0){
				$estado = 'class="table-warning"';
			}
			//$fechaInicio=date('d', strtotime($resul["fecha_alta"])).' '.$meses[date('n', strtotime($resul["fecha_alta"]))].' '.date('Y', strtotime($resul["fecha_alta"]));
		
			$result_json .=   ' <tr '.$estado .'>
                                    <td>'.$resul["documento"].'</td>
									<td>'.$resul["nombre"].' '.$resul["apellidos"].'</td>
									<td>'.$resul["telefono"].'</td>
                                    <td>'.$niveles_acceso[$resul["nivel_acceso"]].'</td>                                 
                                    <td  class="hidden-print">   
										<button type="button" class="btn btn-xs btn-info" onClick="enviarPeticiones({\'accion\':\'editarPersona\' ,\'id\': '.$resul["id"].'}, editarRegistro)"><i class="fa fa-edit"></i></button>';
									//if(verificarRol('Sistema')){
										$result_json .= '<button type="button" class="btn btn-xs  btn-danger"style="margin:3px;" onClick="eliminarRegistro('.$resul["id"].',\'Persona : #'.$resul["documento"].' '.$resul["apellidos"].'\' )"><i class="fa fa-ban"></i></button>';
									//}
									$result_json .= '</td>
                                </tr>
';
				
		}
	}	
 
	$result_json = ($resultados['error']) ? $resultados['resultado'] :$result_json;
	imprimirJson($resultados['error'], $result_json);
 
		
}
 

if(isset($_POST['accion']) and $_POST['accion'] == 'agregarPersona')
{

	$result_json = array('titulo' => 'No se pudo guardar Usuario', 'mensaje' => 'Usuario ya existe!',  'error' => true );
	$estado = (isset($_POST['estado'])) ? 1 : 0;
	$distritos = (isset($_POST['distritos']) && is_array($_POST['distritos'])) ? implode(",",$_POST['distritos']) : "";


	$query = "SELECT id FROM ".$prefijo_tabla."personas WHERE  estado <> 10 AND documento = '".trim($_POST['documento'])."' LIMIT 1  ";
	$resultados = select_sql($query);
	if(count($resultados["resultado"]) == 0){
		$result_json = array('titulo' => 'No se pudo guardar Usuario', 'mensaje' => 'Ocurrio algun inconveniente al guardar Usuario!',  'error' => true );
		$query = "INSERT INTO ".$prefijo_tabla."personas (nombre,	documento,	apellidos, 	 ciudad, telefono,	estado,	fecha_alta,	usuario_alta)
		VALUES ('".$_POST['nombres']."', '".trim($_POST['documento'])."', '".$_POST['apellidos']."',
		'".$_POST['ciudad']."','".$_POST['telefono']."', 1 ,'".date('Y-m-d H:i:s')."', ".$usuario.")";

		$resultados = ejecutar_sql($query);

		if(!$resultados['error']){
			$idPersona = $resultados["resultado"];
			$query = "INSERT INTO ".$prefijo_tabla."usuarios (id_persona, contrasena, nivel_acceso, distritos,  estado,fecha_alta,usuario_alta)
			VALUES (".$idPersona.", '".contrasena(trim($_POST['contrasena']))."', '".$_POST['nivel_acceso']."', '".$distritos."',".$estado.",'".date('Y-m-d H:i:s')."', ".$usuario.")";
			$resultados = ejecutar_sql($query);


			if(!$resultados['error']){
				$result_json = array('titulo' => 'Usuario guardado', 'mensaje' => 'Usuario guardado correctamente!' ,  'error' => false, 'recargar' => true);

			}
		}
	}

	imprimirJson($resultados['error'], $result_json);
}

 
if(isset($_POST['accion']) and $_POST['accion'] == 'editarPersona')
{
	$where = " WHERE  u.estado <> (10) AND  u.id = ".$_POST['id'];
	if(isset($_POST['documento']) and $_POST['documento'] != ''){
		$where = " WHERE  u.estado <> (10) AND  p.documento = '".$_POST['documento']."'";
	}
	
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
		u.distritos
	FROM
	".$prefijo_tabla."usuarios u
	INNER JOIN ".$prefijo_tabla."personas p ON u.id_persona = p.id
	".$where ;
	$resultados = select_sql($query);
	$result_json = $resultados["resultado"][0];
	$result_json['encontro'] = false;
			  if($result_json['id'] != ""){
				  $result_json['encontro'] = true;
			  }
			$result_json['distritos'] = array_map('intval', explode(",",$result_json['distritos']));	
	imprimirJson($resultados['error'], $result_json);		
}
 
if(isset($_POST['accion']) and $_POST['accion'] == 'actualizarPersona')
{


	$result_json = array('titulo' => 'No se pudo actualizar Usuario', 'mensaje' => 'Ocurrio algun inconveniente al guardar Usuario!',  'error' => true );
	$estado = (isset($_POST['estado'])) ? 1 : 0;
	$distritos = (isset($_POST['distritos']) && is_array($_POST['distritos'])) ? implode(",",$_POST['distritos']) : "";
	//documento = '".$_POST['documento']."',
	$query = "UPDATE  ".$prefijo_tabla."personas  SET
	nombre = '".$_POST['nombres']."',
	apellidos = '".$_POST['apellidos']."',
	telefono = '".$_POST['telefono']."',
	ciudad = '".$_POST['ciudad']."',
    fecha_mod = '".date('Y-m-d H:i:s')."',
	usuario_mod = 	".$usuario."
	WHERE id = ".$_POST['idpersona'];

	$resultados = ejecutar_sql($query);

	if(!$resultados['error']){

		$contasena = ($_POST['contrasena'] != "") ? " contrasena = '".contrasena(trim($_POST['contrasena']))."'," : "";
		$query = "UPDATE  ".$prefijo_tabla."usuarios  SET
		nivel_acceso = ".$_POST['nivel_acceso'].",
		distritos = '".$distritos."',
		 ".$contasena."
		estado = ".$estado.",
		fecha_mod = '".date('Y-m-d H:i:s')."',
		usuario_mod = 	".$usuario."
		WHERE id = ".$_POST['id'];

		$resultados = ejecutar_sql($query);
		if(!$resultados['error']){

				$result_json = array('titulo' => 'Actualización de Usuario', 'mensaje' => 'Usuario actualizado correctamente!' ,  'error' => false, 'recargar' => true );

		}


	}
	imprimirJson($resultados['error'], $result_json);


}




if(isset($_POST['accion']) and $_POST['accion'] == 'eliminarRegistro')
{
	$result_json = array('titulo' => 'No se pudo eliminar', 'mensaje' => 'Ocurrio algun error!' ,  'error' => true);
    $query = "UPDATE ".$prefijo_tabla."usuarios SET  estado = 10, fecha_mod = '".date('Y-m-d H:i:s')."', usuario_mod = ".$usuario." WHERE id = " .$_POST['id'];
	$resultados = ejecutar_sql($query);
	if(!$resultados['error']){
		$result_json = array('titulo' => 'Eliminación de chofer', 'mensaje' => 'Usuario eliminado correctamente!' ,  'error' => false, 'recargar' => true);
	}	
	imprimirJson($resultados['error'], $result_json);
}



// Fallback: si no se ejecutó ninguna acción, devolver error JSON
imprimirJson(true, "Acción no reconocida");

?>
