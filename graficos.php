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

	  $query = "

		SELECT desc_dis, SUM(novoto) novoto, SUM(sivoto)  sivoto FROM (SELECT
			desc_dis,
			count(numero_ced) novoto,
			0 sivoto
		FROM
			hc_padron
			where desc_dis is not null and (voto_registrado = 0 or voto_registrado is null)
		GROUP BY
			cod_dist, desc_dis

		union all

		SELECT
			desc_dis,
			0 novoto,
			count(numero_ced) sivoto
		FROM
			hc_padron
			where desc_dis is not null and voto_registrado = 1
		GROUP BY
			cod_dist, desc_dis
		) a
		GROUP BY
			desc_dis" ;
	  $resultadosVotos= select_sql($query);
	  $sivoto = $novoto = $distritos = array();
	  foreach($resultadosVotos["resultado"] as $resul) {
			$distritos[] = "'".$resul["desc_dis"]."'";
			$sivoto[] = $resul["sivoto"];
			$novoto[] = $resul["novoto"];
	  }

 ?>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4"> Registro de consultas </h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="inicio">Rerpotes</a></li>
                            <li class="breadcrumb-item active">Gráficos</li>
                        </ol>
						<div class="card mb-4">
                            <div class="card-body">
                                Aqui puedes ver un resumén de las cantidades de votantes .

                            </div>
                        </div>

                       <!--

                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-chart-area me-1"></i>
                                Consultas por días
                            </div>
                            <div class="card-body"><canvas id="myAreaChart" width="100%" height="30"></canvas></div>
                            <div class="card-footer small text-muted">Datos del  <?php echo date("Y-m-d H:i:s")?> </div>
                        </div>
						 -->
						<canvas id="densityChart" width="600" height="400"></canvas>

					</div>


        	<!--<script src="js/scripts.js"></script>-->
        <script src="assets/Chart.js2.8/Chart.min.js" crossorigin="anonymous"></script>
		<!--<script src="assets/Chart.js2.8/chartjs-plugin-labels.js"></script>

        <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>-->




<script>
var densityCanvas = document.getElementById("densityChart");

Chart.defaults.global.defaultFontFamily = "Lato";
Chart.defaults.global.defaultFontSize = 18;

var siVoto = {
  label: 'Si votaron',
  data: [0, <?php echo implode(',',$sivoto) ?>],
  backgroundColor: 'rgb(60, 179, 113, 0.5)',
  axis: 'y',
  fill: false,
};

var noVoto = {
  label: 'No votaron',
  data: [0, <?php echo implode(',',$novoto) ?>],
  backgroundColor: 'rgb(255, 0, 0,0.5)',
  axis: 'y',
  fill: false,
};

var planetData = {
  labels: ["", <?php echo implode(',',$distritos) ?>],
  datasets: [siVoto, noVoto]
};




var barChart = new Chart(densityCanvas, {
  type: 'horizontalBar',
  data: planetData,
  options: {
    indexAxis: 'y',
     elements: {
      bar: {
        borderWidth: 2,
      }
    },
    responsive: true,
    plugins: {
      legend: {
        position: 'right',
      }
    }
  },
});



</script>
<?php
if($incluir_pie) require_once('pie.php');
?>
