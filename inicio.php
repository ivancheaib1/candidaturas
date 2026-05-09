<?php
require_once('ws/conexion.php');
require_once('ws/funciones.php');

// Solo incluir encabezado si no está incluido ya (cuando se accede directamente)
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
 ?>


<div class="card mb-4" style="margin:20px;">
	<div class="card-body">
		<h5>Consultar Padrón Electoral</h5>
		<form  class="row" role="form" autocomplete="off" id="formBuscar" onsubmit="buscarRegistro();return false;">
		
		<?php if(isset($_SESSION['usuario'])){  ?>
			<div class="input-group col-sm-3"  style="margin-bottom:15px !important"; >
				<span class="input-group-text"><i class="fa fa-map-marker-alt"></i></span>
				<div class="form-floating form-floating-group flex-grow-1">
					<select class="selectpicker  form-control"  multiple data-selected-text-format="count"  aria-label="Distritos" id="distritos" name="distritos[]">
						<?php echo  $selectDistritos  ?>
					</select>
					<label for="distritos">Distritos</label>
				</div>				 
			</div>

			<div class="input-group col-sm-3" id="divbarrios" style="margin-bottom:15px !important"; >
				<span class="input-group-text"><i class="fa fa-map-marker-alt"></i></span>
				<div class="form-floating form-floating-group flex-grow-1">
					<select class="selectpicker  form-control" multiple data-actions-box="true" data-selected-text-format="count"  aria-label="Barrios" id="barrios" name="barrios[]">
						 
					</select>
					<label for="barrios">Barrios</label>
				</div>				 
			</div>

			<div class="input-group col-sm-3" style="margin-bottom:15px !important";>
				<span class="input-group-text"><i class="fa-duotone fa-table-picnic"></i></span>
				<div class="form-floating form-floating-group flex-grow-1">
					<input type="number" class="form-control"  name="mesa" id="mesa" placeholder="Mesa" style=" padding: 35px 5px;">
					<label for="documento">Mesa</label>
				</div>
			 </div>
			
		 <?php } ?>	
			<div class="input-group  col-sm-3" style="margin-bottom:15px !important"; >
					<span class="input-group-text">#</span>
					<div class="form-floating form-floating-group flex-grow-1">
						<input type="number" class="form-control" style="height:75px" name="documento" id="documento" placeholder="Nº Cédula">
						<label for="documento">Nº Cédula</label>
					</div>
			</div>	 
			<!-- <input type="hidden" id="accion" name="accion" value="listarPersonas" >-->	
			<div style="margin-top:10px;">
			<!-- <input type="hidden" id="accion" name="accion" value="listarPersonas" >-->
			<button type="button" class="btn btn-danger col-sm-2  col-sx-4" onclick="buscarRegistro()" style="margin-right:15px;"><i class="fa fa-search"></i> BUSCAR </button>
			<?php if(isset($_SESSION['usuario']) && $_SESSION['usuario']['nivel_acceso'] == 0){ ?>
			<button type="button" class="btn btn-warning col-sm-2  col-sx-4 " style="display:none" id="btndescargar" onclick="descargar('descargarPersonas')""><i class="fa fa-file-excel"></i> EXCEL </button>
			<?php } ?>
			</div>	
		</form>
	</div>
</div>


 




    <div class="card">
    
        <div class="card-body">
            <div class="table-responsive" style="font-size: 12px; padding: 1px; margin:10px;">
                <table  id="file-datatable" class="table table-bordered text-nowrap key-buttons border-bottom"  >
                    <thead>
                        <tr> 
							<th class="border-bottom-0">CEDULA</th>
                            <th class="border-bottom-0">VOTANTE</th>                           
                            <th class="border-bottom-0">TELEFONOS</th>
							<th class="border-bottom-0">MESA/ORD</th>
                            <th class="border-bottom-0">DIRECCION</th>
                            <th class="border-bottom-0">DISTRITO</th> 
							<?php if(isset($_SESSION['usuario'])){  ?>
                            <th class="border-bottom-0">ACCION</th>
							<?php } ?>	
                        </tr>
                    </thead>
                    <tbody>
							<tr>
                                    <td colspan="6" class="text-center">Sin Datos para mostrar</td>
                             </tr> 
                    </tbody>
                </table>
            </div>
			<div id="tablaVertical"></div>
        </div>
    </div>




<!-- inicio de modal---------------- -->



<div class="modal fade" id="modal-registro" tabindex="-1">
          <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title">Actualizar Datos del Vontante</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                    <form  class="row" role="form" autocomplete="off" id="formRegisto">
														<div class="row">
																 <div class="input-group campo">
																	<span class="input-group-text">#</span>
																	<div class="form-floating form-floating-group flex-grow-1">
																		<input type="number" class="form-control" disabled name="documento" id="documento" placeholder="Documento">
																		<label for="documento">Documento</label>
																	</div>
																  </div>

																<div class="input-group  campo" >
																	<span class="input-group-text">N</span>
																	<div class="form-floating form-floating-group flex-grow-1">
																		<input type="text" class="form-control " disabled style="height: 60px;" name="nombre" id="nombre" placeholder="Nombres">
																		<label for="nombre">Nombres</label>
																	</div>
																  </div>
																  
																  
																  <div class="input-group  campo">
																	<span class="input-group-text">A</span>
																	<div class="form-floating form-floating-group flex-grow-1">
																		<input type="text" class="form-control" disabled  style="height: 60px;" name="apellido" id="apellido" placeholder="Apellidos">
																		<label for="apellido">Apellidos</label>
																	</div>
																  </div>
 
		 

																<div class="input-group campo"  >
																	<span class="input-group-text"><i class="fa fa-location-dot"></i></span>
																	<div class="form-floating form-floating-group flex-grow-1">
																		<select class="selectpicker  form-control"   aria-label="barrio" id="barrio" name="barrio">
																			 
																		</select>
																		<label for="barrio">Barrio</label>
																	</div>				 
																</div>
					
					
															   <div class="input-group campo" >
																	<span class="input-group-text"><i class="fa fa-location-dot"></i></span>
																	<div class="form-floating form-floating-group flex-grow-1">
																		<input type="text" class="form-control" style="height: 60px;" name="direccion" id="direccion" placeholder="Dirección">
																		<label for="direccion">Dirección</label>
																	</div>
																 </div>	

																 
																  <div class="input-group campo" >
																	<span class="input-group-text"><i class="fa fa-circle-phone-flip"></i></span>
																	<div class="form-floating form-floating-group flex-grow-1">
																		<input type="number" class="form-control" style="height: 60px;"name="telefono1" id="telefono1" placeholder="Teléfono 1">
																		<label for="telefono1">Teléfono 1</label>
																	</div>
																  </div>				  

																  <div class="input-group campo " >
																	<span class="input-group-text"><i class="fa fa-user"></i></span>
																	<div class="form-floating form-floating-group flex-grow-1">
																		<input type="text" class="form-control" style="height: 60px;" name="operador" id="operador" placeholder="Operador">
																		<label for="operador">Operador</label>
																	</div>
																  </div>
																<?php if(isset($_SESSION['usuario'])){  ?>
																	<?php if($_SESSION['usuario']['nivel_acceso'] == 0){  ?>
																	  <div class="input-group campo " >
																		<span class="input-group-text"><i class="fa fa-money-bill-1-wave"></i></span>
																		<div class="form-floating form-floating-group flex-grow-1">
																			<input type="number" class="form-control" style="height: 60px;" name="presupuesto" id="presupuesto" placeholder="Presupuesto">
																			<label for="presupuesto">Presupuesto</label>
																		</div>
																	  </div>																  
																	  <?php } ?>	
																	  <h6>Intensión de voto</h6>
																	 <div class="input-group campo" >														
																		<input type="radio" class="btn-check" name="intensionvoto" id="radiohc"  value="HC" autocomplete="off">
																		<label style="width: 100px;border-radius: 5px 0  0 5px;" class="btn btn-outline-danger" for="radiohc">HC</label>
																		
																		<input type="radio" class="btn-check" name="intensionvoto" id="radiofr" value="FR" autocomplete="off">
																		<label style="width: 100px;" class="btn btn-outline-secondary" for="radiofr">FR</label>

																		<input type="radio" class="btn-check" name="intensionvoto" id="radiodudoso" value="DUDOSO" autocomplete="off">
																		<label style="width: 100px;" class="btn btn-outline-secondary" for="radiodudoso">DUDOSO</label>														
																	 </div>
																<?php } ?>	

																  
														</div>
														<input type="hidden" name="numero_ced" id="numero_ced" >

                  </form>

              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-warning " data-bs-dismiss="modal"><i class="bi bi-arrow-left-square-fill"></i> Cancelar </button>
				<?php if(isset($_SESSION['usuario'])){  ?>
                 	 <button type="button" style="display: none;" id="btnActualiza" class="btn btn-success " onclick="formRegistro('formRegisto', 'actualizarPersona', true, buscarRegistro)"><i class="bi bi-check-square-fill"></i> Actualizar </button>
				<?php } ?>	               

              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->	


<!-- fin de modal---------------------- -->



<script>


	var modalRegistro ;
	function buscarRegistro(){
		<?php if(!isset($_SESSION['usuario'])){  ?>
		  if($("#formBuscar #documento").val().length == 0 ){
			  return;
		  }
		<?php }  ?>
		  enviarPeticiones($("#formBuscar").serialize()+"&accion=listarPersonas", resultadosTabla);
	}

	function resultadosTabla(resultado){
	  try{if(table != null){table.destroy();}}catch(err){console.log(err)}
	  
	  if($("#formBuscar #documento").val().length > 0 ){
		  $('.table-responsive, #btndescargar').hide();
		  $('#tablaVertical').show();
	  }else{
		  $('.table-responsive, #btndescargar').show();
		  $('#tablaVertical').hide();		  
	  }
	  
	  $('#file-datatable tbody').html(resultado)
	  paratablas(true); 
	  modalRegistro.hide();
	  modalEliminar.hide();
	  
		var  x = $("#file-datatable").find("th,td");
		var i = $("#file-datatable").find("tr").length;
		var j = x.length/i;
		console.log(i , j);
		$('#tablaVertical').html('');
		var newT= $("<table>").appendTo("#tablaVertical");
		for (j1=0; j1<j;j1++){
			var temp = $("<tr>").appendTo(newT);
			for(var i1=0;i1<i; i1++){	
				if(i1>0){
				temp.append('<td> : </td>');
				}
				temp.append($(x[i1*j+j1]).clone());
				
			}
			
		}	
		$('#tablaVertical table').addClass('  table-bordered text-nowrap key-buttons');
	  
	}
	
	function editarRegistro(resultado){
		$("#formRegisto")[0].reset()
		  $("#formRegisto #numero_ced, #formRegisto #documento").val(resultado.numero_ced);	   
		  $("#formRegisto #nombre").val(resultado.nombre);
		  $("#formRegisto #apellido").val(resultado.apellido);	
		  $("#formRegisto #barrio").html(resultado.selectBarrios).selectpicker('refresh');		  
		  $("#formRegisto #direccion").val(resultado.direccion);
		  $("#formRegisto #telefono1").val(resultado.telefono1);
		  $("#formRegisto #operador").val(resultado.operador);
		  $("#formRegisto #presupuesto").val(resultado.presupuesto);
		  $("#formRegisto "+resultado.intension_voto).prop("checked", true)
		  
		  $('#btnInserta').hide();
		  $('#btnActualiza').show();
		  $('#modal-registro .modal-title').text('Actualizar  Datos del Vontante')

		modalRegistro.show();
 
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
		$wsUrl = 'Padron';	
		modalRegistro = new bootstrap.Modal(document.getElementById('modal-registro'), {
			keyboard: false
		})
		
		$('#formBuscar #distritos').change(function() {
			
			enviarPeticiones($("#formBuscar #distritos").serialize() + "&accion=listarBarrios", resultadosBarrios);
			
			console.log($("#formBuscar  #distritos").serialize())
		})
 
})

 
	



<?php
if($incluir_pie) require_once('pie.php');
?>

</script>
