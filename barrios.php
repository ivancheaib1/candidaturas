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
				codigo_sec,
				desc_sec,
				desc_dis,
				cod_dist
				
			FROM
				".$prefijo_tabla."secciones" ;
	  $resultadosSecciones= select_sql($query);
	  $selectSecciones = '';
	  foreach($resultadosSecciones["resultado"] as $resul) { //'.(($selectSecciones=="") ? "selected" : "").'
	 
		$selectSecciones .= '<option  '.(($selectSecciones=="") ? "selected" : "").' data-subtext="'.$resul["desc_dis"].'" value="'.$resul["codigo_sec"].'"  >'.$resul["desc_sec"].'</option>';
	  }
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

<div class="card mb-4">
	<div class="card-body">
		<h5>Consultar Barrios</h5>
		<form  class="row" role="form" autocomplete="off" id="formBuscar" onsubmit="buscarRegistro();return false;">
			<div class="input-group col-sm-3"  >
				<span class="input-group-text"><i class="fa fa-map-marker-alt"></i></span>
				<div class="form-floating form-floating-group flex-grow-1">
					<select class="selectpicker  form-control"  data-selected-text-format="count"  aria-label="Secciones" id="codigo_sec" name="codigo_sec">
						<?php echo  $selectSecciones  ?>
					</select>
					<label for="secciones">Secciones</label>
				</div>				 
			</div>
			<button type="button" class="btn btn-danger col-sm-2  col-sx-8" onclick="buscarRegistro()"><i class="fa fa-search"></i> BUSCAR </button>	
		</form>
	</div>
</div>


<div class="container-fluid px-4">
    <h1 class="mt-4">Barrios	</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="inicio">Sistema HC</a></li>
        <li class="breadcrumb-item active">Gestionar barrios</li>
    </ol>
                      <div class="row row-sm">
                            <div class="col-lg-12">
                                <div class="card">
                   
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table id="file-datatable" class="table table-bordered text-nowrap key-buttons border-bottom">
                                                <thead>
                                                    <tr> 
                                                        <th class="border-bottom-0">NOMBRE</th>
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
                <h4 class="modal-title">Agregar Grupo</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                    <form  class="row" role="form" autocomplete="off" id="formRegisto">
					
					<div class="input-group "  >
						<span class="input-group-text"><i class="fa fa-map-marker-alt"></i></span>
						<div class="form-floating form-floating-group flex-grow-1">
							<select class="selectpicker  form-control"  data-selected-text-format="count"  aria-label="Secciones" id="codigo_sec" name="codigo_sec">
								<?php echo  $selectSecciones  ?>
							</select>
							<label for="secciones">Secciones</label>
						</div>				 
					</div>
			
					 <div class="input-group campo">
                        <span class="input-group-text">#</span>
                        <div class="form-floating form-floating-group flex-grow-1">
                            <input type="text" class="form-control validar" name="barrio" id="barrio" placeholder="Barrio">
                            <label for="barrio">Barrio</label>
                        </div>
                      </div>

					  				
					<input type="hidden" id="barrio_2" name="barrio_2" value="" >
					<input type="hidden" id="codigo_sec_2" name="codigo_sec_2" value="" >
	 
                  </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-warning " data-bs-dismiss="modal"><i class="bi bi-arrow-left-square-fill"></i> Cancelar </button>
                <button type="button" style="display: none;" id="btnActualiza" class="btn btn-success " onclick="formRegistro('formRegisto', 'actualizarBarrio', validarRegistro(), registroInsertado)"><i class="bi bi-check-square-fill"></i> Actualizar </button>
                <button type="button" style="display: none;" id="btnInserta" class="btn btn-success " onclick="formRegistro('formRegisto', 'agregarBarrio', validarRegistro(), registroInsertado)"><i class="bi bi-check-square-fill"></i> Agregar </button>
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
		  enviarPeticiones({'accion':'listarBarrios', 'codigo_sec' : $('#codigo_sec').val() }, resultadosTabla);
	}

	function resultadosTabla(resultado){
	 try{if(table != null){table.destroy();}}catch(err){console.log(err)}
	  $('#file-datatable tbody').html(resultado)
	  paratablas(); 
	  modalRegistro.hide();
	  modalEliminar.hide();
	}	
	function editarRegistro(resultado){
		 //console.log(resultado)
		  $("#formRegisto #barrio, #formRegisto #barrio_2").val(resultado.barrio);
		  $("#formRegisto #codigo_sec").val(resultado.codigo_sec).selectpicker('refresh');
		  
		  $("#formRegisto #codigo_sec_2").val(resultado.codigo_sec);	  
		  
		  $('#btnInserta').hide();
		  $('#btnActualiza').show();
		  $('#modal-registro .modal-title').text('Actualizar Barrio')

		modalRegistro.show();
 
	}		
 
$( document ).ready(function() {
			$.fn.dataTable.ext.buttons.alert = {
				//className: 'btn-success',
				action: function ( e, dt, node, config ) {
					$("#formRegisto")[0].reset()
					$("#modal-registro .modal-title").html('Agregar Grupo')
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