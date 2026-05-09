<?php 
require_once('conexion.php');
require_once('funciones.php');

encabezadoJSON(); //Imprimir Json
seguridadJSON();

if(isset($_POST['accion']) and $_POST['accion'] == 'listarPersonas')
{
 
	$where = " WHERE numero_ced > 1  ";
	
	if(isset($_POST['documento']) and $_POST['documento'] != '')
	{	
		$where .= " AND numero_ced = ". $_POST['documento'];
	}
	if(isset($_POST['distritos']) and count($_POST['distritos'])  > 0 )
	{	
		$where .= " AND cod_dist IN (". implode(",",$_POST['distritos']). ") ";
	}
	if(isset($_POST['barrios']) and count($_POST['barrios'])  > 0 )
	{	
		$where .= " AND barrio IN ('". implode("','",$_POST['barrios']). "') ";
	}	

	if(isset($_POST['mesa']) and $_POST['mesa']  > 0 )
	{	
		$where .= " AND mesa = ".$_POST['mesa']." ";
	}
	
	$query = "
	SELECT
	nombre, 
	apellido, 
	numero_ced,
  	telefono1,	
	telefono2,
	direccion, 
	barrio, 
	cod_dist,
	desc_dis, 
	desc_dep,
	voto_registrado,
	mesa,
	orden
FROM
".$prefijo_tabla."padron 
".$where."
ORDER BY mesa, orden  
LIMIT 500 -- NO TRAER TODOS
" ;
// LIMIT 500 -- NO TRAER TODOS

	$resultados = select_sql($query);
	$result_json = " ";
	if((!$resultados['error']) and count($resultados['resultado']) > 0 )
	{
		
		foreach($resultados["resultado"] as $resul) {

			//$fechaInicio=date('d', strtotime($resul["fecha_alta"])).' '.$meses[date('n', strtotime($resul["fecha_alta"]))].' '.date('Y', strtotime($resul["fecha_alta"]));
		
			$result_json .=   ' <tr >
                                    <td>'.$resul["numero_ced"].'</td>
									<td>'.$resul["nombre"].' </br>  '.$resul["apellido"].'</td>									
									<td>0'.$resul["telefono1"].'</br> 0'.$resul["telefono2"].'</td>
									<td>Mesa: '.$resul["mesa"].'</br> Orden: '.$resul["orden"].'</td>
									<td>'.$resul["barrio"].'  </br> '.$resul["direccion"].'</td> 
									<td>'.$resul["desc_dep"].' </br> '.$resul["desc_dis"].'</td> ';
									
									
									if(isset($_SESSION['usuario'])){
									$result_json .=   '<td  class="hidden-print">'; 
										if(in_array($resul["cod_dist"], explode(",",$_SESSION['usuario']['distritos'])) or $_SESSION['usuario']['nivel_acceso'] == 0){
											if($_SESSION['usuario']['nivel_acceso'] == "0" or $_SESSION['usuario']['nivel_acceso'] == "1"){
												$result_json .=   '<button type="button" class="btn btn-xs btn-info" style="font-size:12px; margin:3px; padding: 10px;" onClick="enviarPeticiones({\'accion\':\'editarVotantes\' ,\'numero_ced\': '.$resul["numero_ced"].'}, editarRegistro)"><i class="fa fa-edit" ></i></button>';
											}
											if($_SESSION['usuario']['nivel_acceso'] == "0" or $_SESSION['usuario']['nivel_acceso'] == "2"){
												if($resul["voto_registrado"]){
													if($_SESSION['usuario']['nivel_acceso'] == "0"){
														$result_json .=   '<button type="button" class="btn btn-xs btn-success" style="font-size:12px; margin:3px; padding: 10px;" onClick="enviarPeticiones({\'accion\':\'votacion\' ,\'voto\':0 ,\'numero_ced\': '.$resul["numero_ced"].'}, buscarRegistro)">SiVotó</button>';
													}else{
														$result_json .=   '<button type="button" class="btn btn-xs btn-success" style="font-size:12px; margin:3px; padding: 10px;">Si Votó</button>';
													}
												}else{
													$result_json .=   '<button type="button" class="btn btn-xs btn-warning" style="font-size:12px; margin:3px; padding: 10px;" onClick="enviarPeticiones({\'accion\':\'votacion\' ,\'voto\':1 ,\'numero_ced\': '.$resul["numero_ced"].'}, buscarRegistro)">NoVotó</button>';
												}
											}
											
											
											
										}
										
									 $result_json .=   '</td>';									 
									 }
           $result_json .=   '</tr>';
				
		}
	}	
 
	$result_json = ($resultados['error']) ? $resultados['resultado'] :$result_json;
	imprimirJson($resultados['error'], $result_json);
 
		
}
 

 
if(isset($_POST['accion']) and $_POST['accion'] == 'editarVotantes' and isset($_SESSION['usuario']))
{

		$intension_voto = array("HC"=> "#radiohc","FR"=> "#radiofr", "DUDOSO"=> "#radiodudoso");
		$query = "SELECT 
		nombre, 
		apellido, 
		numero_ced,
		telefono1,	
		operador,	
		direccion, 
		barrio, 
		desc_dis, 
		desc_dep,
		presupuesto,
		intension_voto,
		codigo_sec
		FROM
		".$prefijo_tabla."padron 
		 WHERE  numero_ced = ".$_POST['numero_ced']; 
		$resultados = select_sql($query);
		$result_json = $resultados["resultado"][0];			
	

		$query = "SELECT DISTINCT barrio
				FROM	".$prefijo_tabla."padron
				WHERE (barrio IS not null AND barrio != '') AND codigo_sec = ".$result_json['codigo_sec'] ;
		$resultadosBarrios = select_sql($query);
		$selectBarrios = "<option></option>";
		foreach($resultadosBarrios["resultado"] as $resul) {		
			$selectBarrios .= '<option  '.(($resul["barrio"]==$result_json["barrio"]) ? "selected" : "").' value="'.$resul["barrio"].'">'.$resul["barrio"].'</option>';
		}
		$result_json["selectBarrios"] = $selectBarrios;
		$result_json["intension_voto"] = $intension_voto[$result_json["intension_voto"]];	  
		
		
		imprimirJson($resultados['error'], $result_json);			
}
 
if(isset($_POST['accion']) and $_POST['accion'] == 'actualizarPersona' and isset($_SESSION['usuario']))
{
 
	$telefono1 = (isset($_POST['telefono1']) and $_POST['telefono1'] != '') ? $_POST['telefono1'] : 'NULL';
//	$telefono2 = (isset($_POST['telefono2']) and $_POST['telefono2'] != '') ? $_POST['telefono2'] : 'NULL';
	$operador = (isset($_POST['operador']) and $_POST['operador'] != '') ? $_POST['operador'] : 'NULL';
//	print_r($operador );
	$presupuesto = (isset($_POST['presupuesto']) and $_POST['presupuesto'] != '') ? $_POST['presupuesto'] : 'NULL';
	$result_json = array('titulo' => 'No se pudo actualizar Persona', 'mensaje' => 'Ocurrio algun inconveniente al guardar Persona!',  'error' => true );
	$query = "UPDATE  ".$prefijo_tabla."padron  SET
	barrio = '".strtoupper($_POST['barrio'])."',
	direccion = '".strtoupper($_POST['direccion'])."',  
	telefono1 = ".$telefono1.",
	operador = '".$operador."',
	presupuesto = ".$presupuesto .", 
	intension_voto = '".$_POST['intensionvoto']."', 
	usuario_mod = ".$_SESSION['usuario']["id"].", 
	fecha_mod = '".date('Y-m-d H:i:s')."' 
	WHERE numero_ced = ".$_POST['numero_ced'];
	$resultados = ejecutar_sql($query);
	if(!$resultados['error']){
		$result_json = array('titulo' => 'Actualización de Persona', 'mensaje' => 'Persona actualizado correctamente!' ,  'error' => false, 'recargar' => true );
	}
	imprimirJson($resultados['error'], $result_json);

		
}
if(isset($_POST['accion']) and $_POST['accion'] == 'listarBarrios' and isset($_SESSION['usuario']))
{
	
	$where = " WHERE barrio != '' ";
	
	if(isset($_POST['distritos']) and count($_POST['distritos'])  > 0 )
	{	
		$where .= " AND cod_dist IN (". implode(",",$_POST['distritos']). ") ";
	}
	
	$query = "
	SELECT DISTINCT 
		cod_dpto,
		desc_dep,
		cod_dist,
		desc_dis,
		codigo_sec,
		desc_sec,
		barrio
	FROM
		".$prefijo_tabla."padron ".$where ;


	$resultados = select_sql($query);
	$result_json = null;
	if((!$resultados['error']) and count($resultados['resultado']) > 0 )
	{		
	  foreach($resultados["resultado"] as $resul) { //'.(($selectSecciones=="") ? "selected" : "").'
		$result_json .= '<option  value="'.$resul["barrio"].'"  >'.$resul["barrio"].'</option>';
	  }
	}	
 
	$result_json = ($resultados['error']) ? $resultados['resultado'] :$result_json;
	imprimirJson($resultados['error'], $result_json);	
	
}


if(isset($_POST['accion']) and $_POST['accion'] == 'votacion' and isset($_SESSION['usuario']))
{
	$result_json = array('titulo' => 'No se pudo actualizar Persona', 'mensaje' => 'Ocurrio algun inconveniente al guardar Persona!',  'error' => true );
	$query = "UPDATE  ".$prefijo_tabla."padron  SET
	voto_registrado = ".$_POST['voto'].", 
	usuario_mod = ".$_SESSION['usuario']["id"].", 
	fecha_mod = '".date('Y-m-d H:i:s')."' 
	WHERE numero_ced = ".$_POST['numero_ced'];
	
	$resultados = ejecutar_sql($query);
	if(!$resultados['error']){
		$result_json = array('titulo' => 'Actualización de Persona', 'mensaje' => 'Persona actualizado correctamente!' ,  'error' => false, 'recargar' => true  );
	}
	imprimirJson($resultados['error'], $result_json);	
}	
	
if(isset($_POST['accion']) and $_POST['accion'] == 'descargarPersonas' and isset($_SESSION['usuario']))
{ 

	$where = " WHERE  numero_ced > 1   ";
	$nombre_archivo ="PADRON.csv";
	if(isset($_POST['documento']) and $_POST['documento'] != '')
	{	
		$where .= " AND numero_ced = ". $_POST['documento'];
	}
	if(isset($_POST['distritos']) and count($_POST['distritos'])  > 0 )
	{	
		$where .= " AND cod_dist IN (". implode(",",$_POST['distritos']). ") ";	
	}
	if(isset($_POST['barrios']) and count($_POST['barrios'])  > 0 )
	{	
		$where .= " AND barrio IN ('". implode("','",$_POST['barrios']). "') ";
		$barrios = implode("_",$_POST['barrios']);
		$nombre_archivo ="PADRON_".str_replace(" ", "-",$barrios).".csv";
	}	
	
	$query = "
	SELECT
	nombre, 
	apellido, 
	numero_ced,
  	telefono1,	
	operador,
	direccion, 
	barrio, 
	cod_dist,
	desc_dis, 
	desc_dep,
	presupuesto,
	intension_voto,
	mesa,
 	orden,
	case when voto_registrado = 0 then 'NO'
	when isnull(voto_registrado)  then 'NO'
	when voto_registrado = null then 'NO'
	when voto_registrado = 1 then 'SI'
	end as voto_registrado 
	FROM
		".$prefijo_tabla."padron 
		".$where." 	
			" ;
	
	$resultados = select_sql($query);

	$fp = fopen("../csv/".$nombre_archivo, 'w');
 
    $cabecera = array_keys($resultados["resultado"][0]);
	fputcsv($fp, $cabecera, ";");
	foreach($resultados["resultado"] as $resul) {

		fputcsv($fp, $resul, ";");
	}
	
	fclose($fp);

	imprimirJson($resultados['error'], $nombre_archivo);
 
		
}
 /*
 
-- Elimina la tabla si existe 
DROP TABLE IF EXISTS hc_secciones;
-- Crea la tabla segun secciones existan en la tabla padron 
CREATE TABLE hc_secciones AS 
SELECT DISTINCT 
	cod_dpto,
	desc_dep,
	cod_dist,
	desc_dis,
	codigo_sec,
	desc_sec 
FROM
	hc_padron
 */

// Fallback: si no se ejecutó ninguna acción, devolver error JSON
imprimirJson(true, "Acción no reconocida");

?>
