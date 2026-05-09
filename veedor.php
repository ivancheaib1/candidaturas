<?php 
require_once('ws/conexion.php');
require_once('ws/funciones.php');
$_SESSION['usuario'] = NULL; unset($_SESSION['usuario']);  
$query = "SELECT id,nombre,icono,logo imagen,correo,mapa,estado FROM ".$prefijo_tabla."sitio WHERE estado = 1 AND id = 1";
$empresa = $_SESSION['empresa'] = select_sql($query)["resultado"][0];
if($empresa["imagen"] == "" or  !file_exists("assets/img/".$empresa["imagen"])){
    $empresa["imagen"] = "banner.png";
} 

if($empresa["icono"] == "" or !file_exists("assets/img/".$empresa["icono"])){
    $empresa["icono"] = "ico.png";
}
InsertarPeticion();

mysqli_close($mysqli_web);

if($_GET['error']== 'si'){
	$mensaje = 'Por favor verifique su contraseña';
	}
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		<meta name="description" content="<?php echo $empresa["nombre"] ?>">
		<meta name="author" content="Luis Fretes | Whatsapp 595976730804">
		<meta name="keywords" content="<?php echo $empresa["nombre"] ?>">
        <meta name="author" content="" />
		<link rel="icon" type="image/png" href="assets/img/<?php echo $empresa["icono"]?>" />
        <title><?php echo $empresa["nombre"] ?></title>
        <link href="css/styles.css" rel="stylesheet" />
		<link href="css/estilo.css" rel="stylesheet"/>
        <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="bg-danger">
        <div id="layoutAuthentication">
            <div id="layoutAuthentication_content">
                <main>
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-5">
								<div class="text-center">
								<img src="assets/img/<?php echo $empresa["imagen"] ?>" style="height: 100px;margin: 50px;"  alt="logo">
								</div>
                                <div class="card shadow-lg border-0 rounded-lg ">
									
                                    <div class="card-header"><h3 class="text-center font-weight-light my-4">  .:Acceso al Sistema:.</h3></div>
                                    <div class="card-body">
                                        <form  class="row" method="POST" action="validausu.php">

                                        <div class="input-group  campo" >
											<span class="input-group-text"><i class="fa fa-user"></i></span>
											<div class="form-floating form-floating-group flex-grow-1">
                                            <input class="form-control" id="usuario" type="text" placeholder="Usuario"  name="usuario"  />
                                                <label for="usuario">Usuario</label>
											</div>
										</div>

                                        <div class="input-group  campo" >
											<span class="input-group-text"><i class="fa fa-key"></i></span>
											<div class="form-floating form-floating-group flex-grow-1">
                                            <input class="form-control" id="inputPassword" type="password" placeholder="Password" name="contrasena" />
                                                <label for="inputPassword">Contraseña</label>
											</div>
										</div>											
											<div class="text-danger" id="mensajesession">
											<?php 
											if(isset($mensaje) and !empty($mensaje)){
													echo $mensaje;
													}
												?></div>
                                            <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <input class="btn btn-danger" type="submit" value="Ingresar" />

                                               
                                            </div>
                                        </form>
                                    </div>
 
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
			
			
            <div id="layoutAuthentication_footer" style="height:20px;" >
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                              <div class="text-muted">Copyright &copy; <?php echo $empresa["nombre"].' ('.date('Y').')' ?></div>
                            <div>
								 <a href="">Soporte</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
		
		
	<div id="preloader">
		<div id="preloader-inner"></div>
	</div>
	
	      <div class="modal fade" id="modal-mensaje" tabindex="-1" style="z-index: 9999 !important">
          <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title">Default Modal</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <p>One fine body&hellip;</p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary  pull-left" data-bs-dismiss="modal">Cerrar</button>
              <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
			  
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
		<script src="js/jquery.min.js"></script>
        <script src="js/scripts.js"></script>
		<script src="js/funciones.js?v7"></script>
		<script>
/*	$wsUrl =  'Usuario.php';
	function validarLogin(){
	  $validado = true;
	  $(".is-invalid").removeClass('is-invalid');
	  $(".validar").each(function() {
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
	function iniciar(){
		formRegistro('formIniciar', 'formIniciar', validarLogin(), logininiciar, 'Usuario.php')
	}
	
	$(document).keydown(function (e) {
	   if (e.which == 13) {
			 iniciar()
	   }           
	});

	$('.validar').blur(function() {
			$(this).removeClass('is-invalid');
	});
	
	
	function logininiciar(resultado) {
	  if(resultado.error){
		$('#mensajesession').html(resultado.mensaje);
	  }else{
		//window.location.reload();
		irUrl('inicio')
	  }
	 // modalIniciar.hide();
	}*/
</script>

    </body>
</html>
