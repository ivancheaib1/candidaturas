




<?php
require_once('ws/conexion.php');
require_once('ws/funciones.php');

if(!isset($empresa)){
    $query = "SELECT id,nombre,icono,logo imagen,correo,mapa,estado,facebook,whatsapp FROM ".$prefijo_tabla."sitio WHERE estado = 1 AND id = 1";
    $empresa = $_SESSION['empresa'] = select_sql($query)["resultado"][0];
    if($empresa["imagen"] == "" or  !file_exists("assets/img/".$empresa["imagen"])){
        $empresa["imagen"] = "logo.png";
    }
    if($empresa["icono"] == "" or !file_exists("assets/img/".$empresa["icono"])){
        $empresa["icono"] = "ico.png";
    }
    require_once('encabezado.php');
    $incluir_pie = true;
} else {
    $incluir_pie = false;
}

	if(isset($_SESSION['usuario'])){
	  $query = "SELECT DISTINCT
				cod_dpto,
				desc_dep,
				cod_dist,
				desc_dis 
			FROM
				".$prefijo_tabla."secciones" ;
	  $resultadosDistritos= select_sql($query);
	  $selectDistritos = '';
	  foreach($resultadosDistritos["resultado"] as $resul) { //'.(($selectSecciones=="") ? "selected" : "").'
		$selectDistritos .= '<option  data-subtext="'.$resul["desc_dep"].'" value="'.$resul["cod_dist"].'"  >'.$resul["desc_dis"].'</option>';
	  }
	}



	  $presupuestos = '<tr><td colspan="3" class="text-center">Sin registros para mostrar</td></tr>';

	  $query = "SELECT
					desc_dis,
					barrio,
					sum( presupuesto ) presupuesto 
				FROM
					".$prefijo_tabla."padron 
				WHERE
					presupuesto IS NOT NULL
				GROUP BY
					desc_dis,
					barrio" ;
	  $resultados= select_sql($query);
	if((!$resultados['error']) and count($resultados['resultado']) > 0 )
	{
	  $presupuestos = ''; $totalPresupuesto = 0;
	  foreach($resultados["resultado"] as $resul) { 
		//$selectDistritos .= '<option  data-subtext="'.$resul["desc_dep"].'" value="'.$resul["cod_dist"].'"  >'.$resul["desc_dis"].'</option>';
		 $totalPresupuesto  += $resul["presupuesto"]; 
		 $presupuestos .= '	<tr>
                                    <td>'.$resul["desc_dis"].'</td>
									<td>'.$resul["barrio"].'</td>
									<td>'.number_format($resul["presupuesto"], 0, ',', '.').'</td>
                             </tr>
							 
							';
	  }

		 $presupuestos .= '	<tr>
                                    <td colspan="2" ><b>TOTAL PRESUPUESTO </b></td>
									<td>'.number_format( $totalPresupuesto, 0, ',', '.').'</td>
                             </tr>
							 
							';
							
	}
 ?>
<?php
// Asegurar que $wsUrl esté siempre definida y con el valor correcto
if(!isset($wsUrl) || empty($wsUrl)) {
	// Si viene del GET (routing), usar ese valor
	if(isset($_GET['page']) && !empty($_GET['page'])) {
		$wsUrl = ucwords($_GET['page']);
	} else {
		// Si no, usar el nombre del archivo
		$wsUrl = ucwords(basename($_SERVER['PHP_SELF'], '.php'));
	}
}
?>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4"> Reporte de Presupuestos </h1>
						
						<form  class="row" role="form" autocomplete="off" id="formBuscar" onsubmit="buscarRegistro();return false;">
						

							<div class="input-group col-sm-3"  style="margin-bottom:15px"; important! >
								<span class="input-group-text"><i class="fa fa-map-marker-alt"></i></span>
								<div class="form-floating form-floating-group flex-grow-1">
									<select class="selectpicker  form-control"  multiple data-selected-text-format="count"  aria-label="Distritos" id="distritos" name="distritos[]">
										<?php echo  $selectDistritos  ?>
									</select>
									<label for="distritos">Distritos</label>
								</div>				 
							</div>

							<div class="input-group col-sm-3" id="divbarrios" style="margin-bottom:15px"; important! >
								<span class="input-group-text"><i class="fa fa-map-marker-alt"></i></span>
								<div class="form-floating form-floating-group flex-grow-1">
									<select class="selectpicker  form-control" multiple data-actions-box="true" data-selected-text-format="count"  aria-label="Barrios" id="barrios" name="barrios[]">
										 
									</select>
									<label for="barrios">Barrios</label>
								</div>				 
							</div>


							<button type="button" class="btn btn-danger col-sm-2  col-sx-4" onclick="buscarRegistro()" style="margin-right:15px;"><i class="fa fa-search"></i> BUSCAR </button>
							<button type="button" class="btn btn-warning col-sm-2  col-sx-4 " style="display:none" id="btndescargar" onclick="descargar('descargarPresupuestos')""><i class="fa fa-file-excel"></i> EXCEL </button>

						</form>						
						
						
						
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="inicio">Inicio</a></li>
                            <li class="breadcrumb-item active">Presupuestos</li>
                        </ol>
						<div class="card mb-4">
                            
                        </div>
						
                       
             <div class="table-responsive" style="font-size: 12px; padding: 1px; margin:10px;">
                <table  id="file-datatable" class="table table-bordered text-nowrap key-buttons border-bottom"  >
                    <thead>
                        <tr> 
							<th class="border-bottom-0">DISTRITOS</th>
                            <th class="border-bottom-0">BARRIOS</th>                           
                            <th class="border-bottom-0">PRESUPUESTO</th>
                        </tr>
                    </thead>
                    <tbody>
							<?php echo  $presupuestos ?>
                    </tbody>
                </table>
            </div>
						 
						
					</div>


<script>
	$wsUrl = <?php echo '"'.$wsUrl.'"' ?>;
	function buscarRegistro(){
		  enviarPeticiones($("#formBuscar").serialize()+"&accion=listarPresupuestos", resultadosTabla);
	}

	function resultadosTabla(resultado){
	  try{if(table != null){table.destroy();}}catch(err){console.log(err)}

	  $('.table-responsive, #btndescargar').show();

	  $('#file-datatable tbody').html(resultado)
	  paratablas(true); 

	  
	}
	
	function resultadosBarrios(resultado){
		if(resultado != null){
			$('#formBuscar #divbarrios').show()
			$('#formBuscar #barrios').html(resultado).selectpicker('refresh');
			
		}else{
			$('#formBuscar #divbarrios').hide()
		}
	}		
$( document ).ready(function() {
		$('#formBuscar #divbarrios').hide()
		//$wsUrl = 'Padron.php';

		
		$('#formBuscar #distritos').change(function() {
			
			enviarPeticiones($("#formBuscar #distritos").serialize() + "&accion=listarBarrios", resultadosBarrios, 'Padron');
			
			console.log($("#formBuscar  #distritos").serialize())
		})
 
})
</script>
<?php
if($incluir_pie) require_once('pie.php');
?>