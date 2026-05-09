<?php 
require_once('conexion.php');
require_once('funciones.php');

encabezadoJSON(); //Imprimir Json
seguridadJSON();

if(isset($_POST['accion']) and $_POST['accion'] == 'listarBarrios')
{
 
	$query = "SELECT DISTINCT 
					barrio
				FROM
					".$prefijo_tabla."padron
				WHERE (barrio IS not null AND barrio != '') AND codigo_sec = ".$_POST['codigo_sec'] ;


	$resultados = select_sql($query);
	$result_json = " ";
	if((!$resultados['error']) and count($resultados['resultado']) > 0 )
	{
		
		foreach($resultados["resultado"] as $resul) {	
			$result_json .=   ' <tr>
									<td>'.$resul["barrio"].'</td>                              
                                    <td  class="hidden-print">   
										<button type="button" class="btn btn-xs btn-info" onClick="enviarPeticiones({\'accion\':\'editarBarrio\' ,\'id\': \''.$_POST['codigo_sec'].'|'.$resul["barrio"].'\'}, editarRegistro)"><i class="fa fa-edit"></i></button>
										<button type="button" class="btn btn-xs" onClick="eliminarRegistro(\''.$_POST['codigo_sec'].'|'.$resul["barrio"].'\',\'Barrio : #'.$resul["barrio"].'\' )"><i class="fa fa-ban"></i></button>

									</td>
								</tr>';
				
		}
	}	
 
	$result_json = ($resultados['error']) ? $resultados['resultado'] :$result_json;
	imprimirJson($resultados['error'], $result_json);
 
		
}
  
if(isset($_POST['accion']) and $_POST['accion'] == 'agregarBarrio')
{
 

	$result_json = array('titulo' => 'No se pudo guardar Barrio', 'mensaje' => 'Incovenientes al guardar !',  'error' => true );
	$estado = (isset($_POST['estado'])) ? 1 : 0;

		$query = "INSERT INTO ".$prefijo_tabla."padron (barrio,	codigo_sec) VALUES ('".$_POST['barrio']."',".$_POST['codigo_sec'].")";	
		$resultados = ejecutar_sql($query);
		
		if(!$resultados['error']){
			$result_json = array('titulo' => 'Barrio guardado', 'mensaje' => 'Barrio guardado correctamente!' ,  'error' => false, 'recargar' => false);						
	
		}

	imprimirJson($resultados['error'], $result_json);
}

 
if(isset($_POST['accion']) and $_POST['accion'] == 'editarBarrio')
{
	$ditar = explode('|', $_POST['id']);
	$result_json = array('codigo_sec' => $ditar[0], 'barrio' => $ditar[1]);
	imprimirJson(false, $result_json);		
}
 
if(isset($_POST['accion']) and $_POST['accion'] == 'actualizarBarrio')
{

	$result_json = array('titulo' => 'No se pudo actualizar Barrio', 'mensaje' => 'Ocurrio algun inconveniente al guardar el Barrio!',  'error' => true );
	$estado = (isset($_POST['estado'])) ? 1 : 0;

	$query = "UPDATE  ".$prefijo_tabla."padron  SET
	barrio = '".$_POST['barrio']."', codigo_sec = ".$_POST['codigo_sec']." 
	WHERE codigo_sec = ".$_POST['codigo_sec_2']." AND barrio = '".$_POST['barrio_2']."'";
	
	$resultados = ejecutar_sql($query);
	
	if(!$resultados['error']){
		$result_json = array('titulo' => 'Actualización de Barrio', 'mensaje' => 'Barrio actualizado correctamente!' ,  'error' => false, 'recargar' => false );						
	
	}
	imprimirJson($resultados['error'], $result_json);

		
}




if(isset($_POST['accion']) and $_POST['accion'] == 'eliminarRegistro')
{
	$ditar = explode('|', $_POST['id']);
	
	$result_json = array('titulo' => 'No se pudo eliminar', 'mensaje' => 'Ocurrio algun error!' ,  'error' => true);
    $query = "UPDATE ".$prefijo_tabla."padron SET  barrio = null 
	WHERE codigo_sec = ".$ditar[0]." AND barrio = '".$ditar[1]."'";
	$resultados = ejecutar_sql($query);
	if(!$resultados['error']){
		$result_json = array('titulo' => 'Eliminación de Barrio', 'mensaje' => 'Barrio eliminada correctamente!' ,  'error' => false, 'recargar' => false);
	}	
	imprimirJson($resultados['error'], $result_json);
}

// Fallback: si no se ejecutó ninguna acción, devolver error JSON
imprimirJson(true, "Acción no reconocida");

?>
