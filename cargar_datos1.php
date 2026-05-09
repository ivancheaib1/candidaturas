<?php
if(!isset($GLOBALS['wsUrl_definido'])){
	echo '<script>var $wsUrl = "Padron";</script>';
	$GLOBALS['wsUrl_definido'] = true;
}

if($_GET['status']== 'ok'){
	$mensaje = 'ATENCIÓN! Se ha actualizado el voto de la mesa '.$_GET['mesa'].' orden '.$_GET['orden'];

	}

if($_GET['status']== 'no'){
	$mensaje = 'ERROR! No se encuentran datos del numero de mesa y orden suministrados' ;
	}
/*
echo $valor = $_SERVER['SCRIPT_NAME'];
$buscar = 'carga_datos.php';
$pos2 = 0;
$pos2 = strpos($valor, $buscar);
if($pos2 > 0 ){
	echo 'aca';
echo $usuario = $_SESSION['usuario'];
if( $usuario == ''){ $usuario = $_SESSION['usuario']; echo 'dos';} 
}else{
*/	

if(empty($_SESSION['documento_aux'])){
	$valor = $_SERVER['SCRIPT_NAME'];
	$buscar = 'index.php';
	$pos = 0;
	$pos = strpos($valor, $buscar);
	
	if($pos > 0 ){	
		 $usuario = $_SESSION['usuario']['documento'];
		 $_SESSION['documento_aux'] =  $usuario ;
	}else{
	
		$usuario = $_SESSION['usuario'];
		$_SESSION['documento_aux'] =  $usuario ;
	}
}


 $usuario = $_SESSION['documento_aux'];
 $query = "SELECT DISTINCT	cod_dpto,
				s.desc_dep,
				s.cod_dist,
				s.desc_dis 
			FROM ".$prefijo_tabla."secciones s
	INNER JOIN ".$prefijo_tabla."usuarios u 
	ON s.cod_dist = u.distritos
	inner join ".$prefijo_tabla."personas p 
	ON u.id_persona = p.id
	where p.documento = $usuario" ;
	$resultados = select_sql($query);
	$result_json = $resultados["resultado"][0];
//	print_r($result_json);
 ?>	


<div class="card mb-4" style="margin:20px;">
	<div class="card-body">
		<h5>ACTUALIZACION DE VOTOS DEL PADRON ELECTORAL</h5>
        <h6>DEPARTAMENTO: <?php echo $result_json['desc_dep']; ?> </h6>
         <h6>DISTRITO: <?php echo $result_json['desc_dis']; ?> </h6>
		<form  class="row" method="POST" action="actualizavoto.php">
		
		<?php if(isset($_SESSION['usuario'])){  ?>	

			<div class="input-group col-sm-3" style="margin-bottom:15px !important";>
				<span class="input-group-text"><i class="fa-duotone fa-table-picnic"></i></span>
				<div class="form-floating form-floating-group flex-grow-1">
					<input type="number" class="form-control"  name="mesa" id="mesa" placeholder="Mesa" style=" padding: 35px 5px;" required>
					<label for="documento">Mesa</label>
				</div>
			 </div>
					<div class="input-group col-sm-3" style="margin-bottom:15px !important";>
				<span class="input-group-text"><i class="fa-duotone fa-table-picnic"></i></span>
				<div class="form-floating form-floating-group flex-grow-1">
					<input type="number" class="form-control"  name="orden" id="mesa" placeholder="Orden" style=" padding: 35px 5px;" required>
                    <input type="hidden" name="distrito" id="distrito" value= <?php echo $result_json['cod_dist']; ?>>
					<label for="documento">Orden</label>
				</div>
			 </div>
		 <?php } ?>	

			<!-- <input type="hidden" id="accion" name="accion" value="listarPersonas" >-->	
			<div style="margin-top:10px;">
			<!-- <input type="hidden" id="accion" name="accion" value="listarPersonas" >-->	
			<button type="submit" class="btn btn-danger col-sm-2  col-sx-4" onclick="buscarRegistro()" style="margin-right:15px;"><i class="fa fa-refresh"></i> ENVIAR </button>            
                                         
			</div>	
		</form>
   		<?php  if(isset($_GET['status']) and $_GET['status']== 'no'){ ?>
        <div class="text-danger" id="mensajesession">
        <br>
		<?php 
        if(isset($mensaje) and !empty($mensaje)){
                echo $mensaje;
                }
		?></div>
        <?php   }?>
        
           <?php  if(isset($_GET['status']) and $_GET['status']== 'ok'){ ?>
        <div class="text-success" id="mensajesession">
        <br>
		<?php 
        if(isset($mensaje) and !empty($mensaje)){
                echo $mensaje;
                }
		?></div>
        <?php   }?>
	</div>
</div>
      