<?php
require_once('ws/conexion.php');
require_once('ws/funciones.php');

// Validación de seguridad: solo Admin puede acceder a esta página
if(isset($_SESSION['usuario']) && $_SESSION['usuario']['nivel_acceso'] != 0){
	// Redirigir según rol (evitar loop infinito)
	if($_SESSION['usuario']['nivel_acceso'] == 1){
		header("Location: index.php"); // Editor
	} else if($_SESSION['usuario']['nivel_acceso'] == 2){
		header("Location: inicio.php"); // Veedor
	} else if($_SESSION['usuario']['nivel_acceso'] == 3){
		header("Location: invitados.php"); // Invitado
	}
	die();
}

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

foreach($niveles_acceso as $clave => $valor) {
		$selectTipos .= '<option  value="'.$clave.'"  >'.$valor.'</option>';
	  }	


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
<style>

 </style>
<div class="container-fluid px-4">
    <h1 class="mt-4">Usuarios</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="inicio">Inicio</a></li>
        <li class="breadcrumb-item active">Usuarios</li>
    </ol>
						
                      <div class="row row-sm">
                            <div class="col-lg-12">
                                <div class="card" style="margin:0px 20px 20px 20px;">
                   
                                    <div class="card-body">
                                        <div class="table-responsive" >
                                            <table id="file-datatable" class="table table-bordered text-nowrap key-buttons border-bottom" style="margin:20px 0px 0px 0px;">
                                                <thead>
                                                    <tr> 
                                                        <th class="border-bottom-">DOCUMENTO</th>
                                                        <th class="border-bottom-0">NOMBRE y APELLIDOS</th>
                                                        <th class="border-bottom-0">TELÉFONO</th>
                                                        <th class="border-bottom-0">NIVEL ACCESO</th>
                                                        <th class="border-bottom-0"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
   
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>	
</div>
						
 	<div class="modal fade" id="modal-registro" tabindex="-1">
          <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title">Agregar Usuario</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                    <form  class="row" role="form" autocomplete="off" id="formRegisto">
			
			
			
					<div class="panel panel-primary">
                                            <div class="tab-menu-heading">
                                                <div class="tabs-menu">
                                                    <!-- Tabs -->
                                                    <ul class="nav panel-tabs panel-info ">
                                                        <li class="nav-item"><a href="#tab21" class="active" data-bs-toggle="tab"><span><i class="fe fe-user me-1"></i></span> Datos Personales</a></li>
                                                        <li class="nav-item"><a href="#tab22" data-bs-toggle="tab"><span><i class="bi bi-key-fill"></i></span> Datos como Usuario</a></li>
                                                    </ul>
                                                </div>
												
                                            </div>
	 
						 
											
                                            <div class="panel-body tabs-menu-body">
                                                <div class="tab-content">
                                                    <div class="tab-pane active " id="tab21">
														<div class="row">
																 <div class="input-group campo">
																	<span class="input-group-text">#</span>
																	<div class="form-floating form-floating-group flex-grow-1">
																		<input type="number" class="form-control validar" name="documento" id="documento" placeholder="Documento">
																		<label for="documento">Documento</label>
																	</div>
																  </div>

																<div class="input-group  campo" >
																	<span class="input-group-text">N</span>
																	<div class="form-floating form-floating-group flex-grow-1">
																		<input type="text" class="form-control validar" style="height: 60px;" name="nombres" id="nombres" placeholder="Nombres">
																		<label for="nombres">Nombres</label>
																	</div>
																  </div>
																  
																  
																  <div class="input-group  campo">
																	<span class="input-group-text">A</span>
																	<div class="form-floating form-floating-group flex-grow-1">
																		<input type="text" class="form-control" style="height: 60px;" name="apellidos" id="apellidos" placeholder="Apellidos">
																		<label for="apellido_paterno">Apellidos</label>
																	</div>
																  </div>
 
		 
																  
															   <div class="input-group campo" >
																	<span class="input-group-text"><i class="fa fa-location-dot"></i></span>
																	<div class="form-floating form-floating-group flex-grow-1">
																		<input type="text" class="form-control" style="height: 60px;" name="ciudad" id="ciudad" placeholder="Ciudad">
																		<label for="ciudad">Dirección</label>
																	</div>
																 </div>	

													  
																  <div class="input-group campo" >
																	<span class="input-group-text"><i class="fa fa-circle-phone-flip"></i></span>
																	<div class="form-floating form-floating-group flex-grow-1">
																		<input type="text" class="form-control" style="height: 60px;"name="telefono" id="telefono" placeholder="Teléfono">
																		<label for="telefono">Teléfono</label>
																	</div>
																  </div>				  

														</div>




                                                    </div><!--tab21-->
                                                    <div class="tab-pane" id="tab22">
														<div class="alert alert-info" role="alert">Para ingresar el usuario, debe usar su numero de documento y su contraseña.</div>
 
 														<div class="input-group campo"  style="margin:0 0 20px 0;">
															<span class="input-group-text"><i class="fa fa-map-marker-alt"></i></span>
															<div class="form-floating form-floating-group flex-grow-1">
																<select class="selectpicker  form-control"  multiple data-selected-text-format="count"  aria-label="Distritos" id="distritos" name="distritos[]">
																	<?php echo  $selectDistritos  ?>
																</select>
																<label for="distritos">Distritos</label>
															</div>				 
														</div>
														
														
														<div class="input-group campo" style="height: 50px;margin-top:10px">
															<span class="input-group-text"><i class="fa fa-key"></i></span>
															<div class="form-floating form-floating-group flex-grow-1">
																<input type="text" class="form-control" name="contrasena" id="contrasena" placeholder="Contraseña">
																<label for="contrasena">Contraseña</label>
															</div>
														 </div>															  
 									  




														 <div class="input-group  campo"  style="height: 50px;margin-top:10px;">
															<span class="input-group-text"><i class="fa fa-level-up-alt"></i></span>
															<div class="form-floating form-floating-group flex-grow-1">
																<select class="form-select validar" style="height: 60px;" aria-label="Nivel de acceso" id="nivel_acceso" name="nivel_acceso">
																	<?php echo  $selectTipos  ?>
																</select>
																<label for="nivel_acceso">Nivel de acceso</label>
															</div>
															 
														 </div>
															


															
													</div><!--tab22-->
                                                </div>
                                            </div>
                    </div>				
			
			
					  
					
                    <div class="campo" style="padding: 15px 40px; width:50%">
                      <div class="input-group">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="estado" name="estado" checked>
                          <label class="form-check-label" for="estado">Habilitado</label>
                        </div>
                      </div>
                    </div>
                    <input type="hidden" id="id" name="id" value="" >
					<input type="hidden" id="idpersona" name="idpersona" value="" >
	 
                    <div id="imagenver"></div>
                  </form>
				  <input style="display:none" id="archivoImage" type="file" name="imagen" accept="image/*">
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-warning " data-bs-dismiss="modal"><i class="bi bi-arrow-left-square-fill"></i> Cancelar </button>
                <button type="button" style="display: none;" id="btnActualiza" class="btn btn-success " onclick="formRegistro('formRegisto', 'actualizarPersona', validarRegistro(), registroInsertado)"><i class="bi bi-check-square-fill"></i> Actualizar </button>
                <button type="button" style="display: none;" id="btnInserta" class="btn btn-success " onclick="formRegistro('formRegisto', 'agregarPersona', validarRegistro(), registroInsertado)"><i class="bi bi-check-square-fill"></i> Agregar </button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->					
				



<script>

	$wsUrl = <?php echo '"'.$wsUrl.'"' ?>;
	var modalRegistro ;
	function buscarRegistro(){
		  console.log('buscarRegistro called with $wsUrl=' + $wsUrl);
		  enviarPeticiones({'accion':'listarPersonas' }, resultadosTabla);
	}

	function resultadosTabla(resultado){
	  $('#file-datatable tbody').html(resultado)
	  paratablas(); 
	  modalRegistro.hide();
	  modalEliminar.hide();
	}	
	function editarRegistro(resultado){
		   $("#formRegisto")[0].reset()
		   //$("#formRegisto select[multiple='multiple']").bsMultiSelect('UpdateData');
		   
		   $("#formRegisto #documento").val(resultado.documento);
		   $("#formRegisto #documento").attr("disabled", "true")
		   
		  $("#formRegisto #nombres").val(resultado.nombre);
		  $("#formRegisto #apellidos").val(resultado.apellidos);						  


		  $("#formRegisto #ciudad").val(resultado.ciudad);
		  
		  $("#formRegisto #telefono").val(resultado.telefono);
		 
		 $("#formRegisto #nivel_acceso").val(resultado.nivel_acceso);
	   
		  $("#formRegisto #idpersona").val(resultado.idpersona);
		  
		  $("#formRegisto #distritos").val(resultado.distritos).selectpicker('refresh');
 
		  
		  $("#formRegisto #id").val(resultado.id);
		  

		  if(resultado.estado == '1'){
			$("#formRegisto #estado").attr('checked','checked')
		  }else{
			$("#formRegisto #estado").removeAttr('checked')
		  }
		  

		  
		  $('#btnInserta').hide();
		  $('#btnActualiza').show();
		  $('#modal-registro .modal-title').text('Actualizar Usuario')

		modalRegistro.show();
 
	}		
 
$( document ).ready(function() {
			$.fn.dataTable.ext.buttons.alert = {
				//className: 'btn-success',
				action: function ( e, dt, node, config ) {
					$("#formRegisto")[0].reset()
					$("#formRegisto #documento").removeAttr("disabled")
					$("#modal-registro .modal-title").html('Agregar Usuario')
					modalRegistro.show();
					$("#btnInserta").show();
					$('#btnActualiza').hide();
					
				}
			}



		modalRegistro = new bootstrap.Modal(document.getElementById('modal-registro'), {
			keyboard: false
		})
		
		
		buscarRegistro();
		
	

	$('.validar').blur(function() {
			$(this).removeClass('is-invalid');
	});
	
	
	 //$("#formRegisto #distritos").val([13]);
	// $("#formRegisto #distritos").selectpicker('refresh');
})

	function validarRegistro(){
		  //tiempo precio descripcion Ancho Largo Nombre
		  $validado = true;
		  $("#formRegisto .is-invalid").removeClass('is-invalid');
		  $('#formRegisto .validar').each(function() {
			if($(this).val().length == 0){
			  $(this).addClass('is-invalid');
			  $(this).focus();
			  $validado = false;
			  return false;
			  }

		  });
		  //return false;
		  return $validado;
	}





</script>
<?php
if($incluir_pie) require_once('pie.php');
?>