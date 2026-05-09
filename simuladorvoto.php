<style>
	.cantidatos{
		-webkit-box-shadow: 0px 0px 3px 0px rgba(0,0,0,0.75);
		-moz-box-shadow: 0px 0px 3px 0px rgba(0,0,0,0.75);
		box-shadow: 0px 0px 3px 0px rgba(0,0,0,0.75);

		border-radius: 3px 3px 3px 3px;
		-moz-border-radius: 3px 3px 3px 3px;
		-webkit-border-radius: 3px 3px 3px 3px;
		border: 0px solid #000000;
		
		margin:5px;
		height:250px;
		cursor:pointer;
		
	}
</style>

<div class="container-fluid px-4">
    <h1 class="mt-4">Simulador de Votos	</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="inicio">Sistema HC</a></li>
        <li class="breadcrumb-item active">Simulador</li>
    </ol>
                      <div class="row row-sm">
                            <div class="col-lg-12">
                                <div class="card">
                   
                                    <div class="card-body text-center" style=" background-color: #e6e5e4; ">
										<div id="paso1">
											Presentá tu <b>cédula de identidad</b> civil a los miembros de la mesa receptora de votos, </br> quienes te entregarán el <b>boletín</b> firmado por los dos vocales.
											</br>
											<img   src="assets/img/boleta-troquel.png" >
											</br>
											<button class="btn btn-lg btn-danger" onClick="$('#paso1').hide(500);$('#paso2').show(500);">Continuar</button>
										</div>
										
										<div id="paso2" style="display:none">
											Colocá el <b>boletín</b> en la ranura como lo indica la flecha.
											</br>
											<img   src="assets/img/maquina2_p6.png" >
											</br>
											<button class="btn btn-lg btn-warning" onClick="$('#paso2').hide(500);$('#paso1').show(500);">Volver</button>
											<button class="btn btn-lg btn-danger" onClick="simular(1, null)">Simular Votación</button>
										</div>
										
										
										<div id="paso3" class="row" style="display:none">
										
										</div>
                                    </div>
 
                                </div>
                            </div>
                        </div>	
</div>




<script>
	function simular($paso, $partido){
		  enviarPeticiones({'accion':'listarCandidatos', 'paso' : $paso , 'partido' : $partido  }, resultadosTabla);
	}
	
	function resultadosTabla(resultado){		
		$('#paso3').html(resultado);
		$('#paso2').hide(500);$('#paso3').show(500);
	}
	
	function votar($paso, $vacanciaId, $cantidatoId){
		enviarPeticiones({'accion':'agregarVoto', 'paso' : $paso , 'vacanciaId' : $vacanciaId, 'cantidatoId' : $cantidatoId }, console.log);
		simular($paso+1);
	}
$( document ).ready(function() {
	$wsUrl = <?php echo '"'.ucwords(basename($_SERVER['PHP_SELF'], '.php')).'"' ?> ;
})
	
</script>