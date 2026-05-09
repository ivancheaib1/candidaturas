<?php 
require_once('conexion.php');
require_once('funciones.php');

encabezadoJSON(); //Imprimir Json
seguridadJSON();

if(isset($_POST['accion']) and $_POST['accion'] == 'listarPresupuestos')
{
	$where = " WHERE presupuesto IS NOT NULL ";
	
	if(isset($_POST['distritos']) and count($_POST['distritos'])  > 0 )
	{	
		$where .= " AND cod_dist IN (". implode(",",$_POST['distritos']). ") ";
	}
	if(isset($_POST['barrios']) and count($_POST['barrios'])  > 0 )
	{	
		$where .= " AND barrio IN ('". implode("','",$_POST['barrios']). "') ";
	}
	
	
				$query = "SELECT
					desc_dis,
					barrio,
					sum( presupuesto ) presupuesto 
				FROM
					".$prefijo_tabla."padron 
					".$where."  
 				
				GROUP BY
					desc_dis,
					barrio" ;
					
					
	$resultados = select_sql($query);
	$result_json = " ";
	if((!$resultados['error']) and count($resultados['resultado']) > 0 )
	{
		
		foreach($resultados["resultado"] as $resul) {

		 $totalPresupuesto  += $resul["presupuesto"]; 
		 $result_json .= '	<tr>
                                    <td>'.$resul["desc_dis"].'</td>
									<td>'.$resul["barrio"].'</td>
									<td>'.number_format($resul["presupuesto"], 0, ',', '.').'</td>
                             </tr>
							 
							';
				
		}
		
		 $result_json .= '	<tr>
                                    <td colspan="2" ><b>TOTAL PRESUPUESTO </b></td>
									<td>'.number_format( $totalPresupuesto, 0, ',', '.').'</td>
                             </tr>
							 
							';		
	}	
 
	$result_json = ($resultados['error']) ? $resultados['resultado'] :$result_json;
	imprimirJson($resultados['error'], $result_json);
 
		
}
if(isset($_POST['accion']) and $_POST['accion'] == 'descargarPresupuestos')
{
	
 

	$where = " WHERE presupuesto IS NOT NULL ";
	$nombre_archivo ="Presupuesto.csv";
 
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
	
				$query = "SELECT
					desc_dis distrito,
					barrio,
					sum( presupuesto ) presupuesto 
				FROM
					".$prefijo_tabla."padron 
					".$where."  
 				
				GROUP BY
					desc_dis,
					barrio" ;
	
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

// Fallback: si no se ejecutó ninguna acción, devolver error JSON
imprimirJson(true, "Acción no reconocida");

?>

