<?php 
require_once('conexion.php');
require_once('funciones.php');

encabezadoJSON(); //Imprimir Json
seguridadJSON();
function mayusculas($texto){
	return strtoupper(str_replace(array('ñ'), array('Ñ'), $texto));
}
if(isset($_POST['accion']) and $_POST['accion'] == 'listarCandidatos')
{
 

	if($_POST['paso'] ==2){
		$orden = $_POST['paso'];
		$query = "
		SELECT DISTINCT 
			pa.id paId,
			pa.nombre partido,
			pa.abreviatura,
			pa.lista,
			va.id vaId,
			va.nombre vacancia,
			va.orden,
			(12/va.columnas_distr) col
			FROM
			hc_simu_candidatos ca
			INNER JOIN hc_simu_partidos pa ON pa.estado = 1 AND  ca.id_partido = pa.id
			INNER JOIN hc_simu_vacancias va ON va.estado = 1 AND ca.id_vacancia = va.id
			WHERE ca.estado = 1 AND va.orden = ".$orden." 
			ORDER BY pa.lista, ca.opcion ";
	}else{
			$orden = str_replace(array(3), array(2), $_POST['paso']);
			$where = "";
			if(isset($_POST['partido']) and $_POST['partido'] != null){
				
				$where = " AND ca.id_partido = " . $_POST['partido'];
			}
			$query = "SELECT
			ca.id,
			ca.nombre,
			ca.foto,
			ca.nombre2,
			ca.foto2,	
			ca.opcion,
			pa.nombre partido,
			pa.abreviatura,
			pa.lista,
			va.id vaId,
			va.nombre vacancia,
			va.orden,
			(12/va.columnas_distr) col
			FROM
			".$prefijo_tabla."simu_candidatos ca
			INNER JOIN ".$prefijo_tabla."simu_partidos pa ON pa.estado = 1 AND  ca.id_partido = pa.id
			INNER JOIN ".$prefijo_tabla."simu_vacancias va ON va.estado = 1 AND ca.id_vacancia = va.id
			WHERE ca.estado = 1 AND va.orden = ".$orden." ".$where." 
			ORDER BY pa.lista, ca.opcion "  ;		
		
	}
	$resultados = select_sql($query);
	$vacancia = $resultados['resultado'][0];
	
	$result_json = '<div class="row">
					<div class="col-2"><img  class="w-100" src="assets/img/logo_eleccion.png" ></div>
					<div class="col-9 text-white" >
						<div style="background-color:#545859; padding: 5px">ELECCIONES INTERNAS SIMULTANEAS Y PARTIDARIAS 2022</div>
						<div style="background-color:black; padding: 5px; font-size:18px"><b>'.mayusculas($vacancia['vacancia']).'</b></div>
					</div>
					<div class="col-1"><img  class="w-100" src="assets/img/logo_paraguay.png" ></div>
					</div>
					</hr>
					';
	if($_POST['paso'] ==3){
		$result_json .= '<div>LISTA '.$vacancia['lista'].'</div>
					<div>'.mayusculas($vacancia['partido']).'</div>';
	}	
	if((!$resultados['error']) and count($resultados['resultado']) > 0 )
	{
		
		foreach($resultados["resultado"] as $resul) {
			
			if($_POST['paso'] == 1){//Si es paso presidenciales
				$result_json .= '<div class="col-'.(int)$vacancia['col'].'">
									<div class="cantidatos row" onClick="votar('.$_POST['paso'].', '.$resul['vaId'].', '.$resul['id'].')">
										<div>'.mayusculas($resul['partido']).'</div>
										<div class="col-5"><img  class="w-100" src="assets/img/candidatos/'.$resul['foto'].'" ></div>
										<div class="col-5"><img  class="w-100" src="assets/img/candidatos/'.$resul['foto2'].'"  ></div>
										<div class="col-2">LISTA</br>'.$resul['lista'].'</br>'.mayusculas($resul['abreviatura']).'</div>
										<div> Presidente : '.mayusculas($resul['nombre']).'</div>
										<div> Vice Presidente : '.mayusculas($resul['nombre2']).'</div>
									</div>
								</div>';
			}elseif($_POST['paso'] ==2){//Si es para seleccionar
				$result_json .= '<div class="col-'.(int)$vacancia['col'].'">
									<div class="cantidatos row" onClick="simular('.($_POST['paso']+1).', '.$resul['paId'].')">
										<div>LISTA '.$resul['lista'].'</div>
										<div>'.mayusculas($resul['partido']).'</div>
										<div>'.mayusculas($resul['abreviatura']).'</div>
									</div>
								</div>';
			}elseif($_POST['paso'] ==3){	
				$result_json .= '<div class="col-2">
									<div class="cantidatos row" onClick="votar('.$_POST['paso'].', '.$resul['vaId'].', '.$resul['id'].')">										
										<div class="col-6"><img  class="w-100" src="assets/img/candidatos/'.$resul['foto'].'" ></div>
										<div class="col-6">Opción </br>'.$resul['opcion'].'</div>
										<div>'.mayusculas($resul['nombre']).'</div>
									</div>
								</div>';			
			}	
		}
	}	
	if($_POST['paso'] > 1){
		$result_json .= '<div><button class="btn btn-lg btn-danger" onClick="simular('.($_POST['paso']-1).', null)"><i class="fa-sharp fa-solid fa-square-left"></i> Volver atrás</button></div>';
	}
	
	$result_json = ($resultados['error']) ? $resultados['resultado'] :$result_json;
	imprimirJson($resultados['error'], $result_json);
 
		
}
  
if(isset($_POST['accion']) and $_POST['accion'] == 'agregarVoto')
{
	$_SESSION['votacion'][$_POST['vacanciaId']] = $_POST['cantidatoId'];
	imprimirJson(false, '');
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
