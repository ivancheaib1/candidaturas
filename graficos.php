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

    // ========== QUERY 1: KPI - Total de Empadronados ==========
    $query_total = "SELECT COUNT(*) as total FROM ".$prefijo_tabla."padron";
    $resultado_total = select_sql($query_total);
    $total_empadronados = $resultado_total["resultado"][0]["total"] ?? 0;

    // ========== QUERY 2: KPI - Participación % ==========
    $query_participacion = "
        SELECT
            COUNT(*) as total,
            SUM(CASE WHEN voto_registrado = 1 THEN 1 ELSE 0 END) as votaron,
            ROUND((SUM(CASE WHEN voto_registrado = 1 THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as pct_participacion
        FROM ".$prefijo_tabla."padron
    ";
    $resultado_participacion = select_sql($query_participacion);
    $pct_participacion = $resultado_participacion["resultado"][0]["pct_participacion"] ?? 0;
    $votaron = $resultado_participacion["resultado"][0]["votaron"] ?? 0;

    // ========== QUERY 3: Gráfico 1 - Votos por Distrito ==========
    $query = "
        SELECT desc_dis, SUM(novoto) novoto, SUM(sivoto) sivoto FROM (
            SELECT
                desc_dis,
                count(numero_ced) novoto,
                0 sivoto
            FROM ".$prefijo_tabla."padron
            WHERE desc_dis IS NOT NULL AND (voto_registrado = 0 OR voto_registrado IS NULL)
            GROUP BY cod_dist, desc_dis

            UNION ALL

            SELECT
                desc_dis,
                0 novoto,
                count(numero_ced) sivoto
            FROM ".$prefijo_tabla."padron
            WHERE desc_dis IS NOT NULL AND voto_registrado = 1
            GROUP BY cod_dist, desc_dis
        ) a
        GROUP BY desc_dis
        ORDER BY desc_dis
    ";
    $resultadosVotos = select_sql($query);
    $sivoto = $novoto = $distritos = array();
    foreach($resultadosVotos["resultado"] as $resul) {
        $distritos[] = $resul["desc_dis"];
        $sivoto[] = (int)$resul["sivoto"];
        $novoto[] = (int)$resul["novoto"];
    }

    // ========== QUERY 4: Gráfico 2 - Votos por Sección ==========
    $query_seccion = "
        SELECT desc_sec, COUNT(*) as cantidad
        FROM ".$prefijo_tabla."padron
        WHERE desc_sec IS NOT NULL
        GROUP BY codigo_sec, desc_sec
        ORDER BY cantidad DESC
    ";
    $resultados_seccion = select_sql($query_seccion);
    $secciones = array();
    $cantidades_seccion = array();
    foreach($resultados_seccion["resultado"] as $sec) {
        $secciones[] = $sec["desc_sec"];
        $cantidades_seccion[] = (int)$sec["cantidad"];
    }

    // ========== QUERY 5: Gráfico 3 - Top 10 Barrios ==========
    $query_barrios = "
        SELECT barrio, COUNT(*) as cantidad
        FROM ".$prefijo_tabla."padron
        WHERE barrio IS NOT NULL AND barrio != ''
        GROUP BY barrio
        ORDER BY cantidad DESC
        LIMIT 10
    ";
    $resultados_barrios = select_sql($query_barrios);
    $barrios = array();
    $cantidades_barrios = array();
    foreach($resultados_barrios["resultado"] as $bar) {
        $barrios[] = $bar["barrio"];
        $cantidades_barrios[] = (int)$bar["cantidad"];
    }

    // ========== QUERY 6: Gráfico 4 - Intención de Voto por Distrito ==========
    $query_intencion = "
        SELECT
            desc_dis,
            COUNT(CASE WHEN voto1 IS NOT NULL AND voto1 != '' THEN 1 END) as voto1_count,
            COUNT(CASE WHEN voto2 IS NOT NULL AND voto2 != '' THEN 1 END) as voto2_count,
            COUNT(CASE WHEN voto3 IS NOT NULL AND voto3 != '' THEN 1 END) as voto3_count,
            COUNT(CASE WHEN voto4 IS NOT NULL AND voto4 != '' THEN 1 END) as voto4_count,
            COUNT(CASE WHEN voto5 IS NOT NULL AND voto5 != '' THEN 1 END) as voto5_count
        FROM ".$prefijo_tabla."padron
        WHERE desc_dis IS NOT NULL
        GROUP BY cod_dist, desc_dis
        ORDER BY desc_dis
    ";
    $resultados_intencion = select_sql($query_intencion);
    $distritos_intencion = array();
    $votos_intencion = array(
        'voto1' => array(),
        'voto2' => array(),
        'voto3' => array(),
        'voto4' => array(),
        'voto5' => array()
    );
    foreach($resultados_intencion["resultado"] as $int) {
        $distritos_intencion[] = $int["desc_dis"];
        $votos_intencion['voto1'][] = (int)$int["voto1_count"];
        $votos_intencion['voto2'][] = (int)$int["voto2_count"];
        $votos_intencion['voto3'][] = (int)$int["voto3_count"];
        $votos_intencion['voto4'][] = (int)$int["voto4_count"];
        $votos_intencion['voto5'][] = (int)$int["voto5_count"];
    }

 ?>
                    <div class="container-fluid">
                        <h1 class="mt-4">📊 Dashboard de Votación</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="inicio">Reportes</a></li>
                            <li class="breadcrumb-item active">Gráficos</li>
                        </ol>

                        <div class="card mb-4">
                            <div class="card-body">
                                <p><strong>Dashboard responsivo</strong> con visualización de datos de votación en tiempo real. Los gráficos se adaptan automáticamente a cualquier dispositivo.</p>
                            </div>
                        </div>

                        <!-- Dashboard Grid -->
                        <div class="dashboard-container">

                            <!-- Row 1: KPI Cards -->
                            <div class="dashboard-grid">
                                <!-- KPI 1: Total de Empadronados -->
                                <div class="kpi-card primary">
                                    <div class="kpi-number" id="totalEmpadronados">0</div>
                                    <div class="kpi-label">Total de Empadronados</div>
                                    <div class="kpi-subtitle">Votantes registrados</div>
                                </div>

                                <!-- KPI 2: Participación % -->
                                <div class="kpi-card success">
                                    <div class="kpi-number"><span id="participacionPct">0</span>%</div>
                                    <div class="kpi-label">Tasa de Participación</div>
                                    <div class="kpi-subtitle">Actualizado en tiempo real</div>
                                </div>
                            </div>

                            <!-- Row 2: Gráficos 1 y 2 -->
                            <div class="dashboard-grid">
                                <!-- Gráfico 1: Votos por Distrito -->
                                <div class="chart-card">
                                    <div class="chart-title">Votos por Distrito</div>
                                    <div class="chart-wrapper">
                                        <canvas id="chartDistrito"></canvas>
                                    </div>
                                </div>

                                <!-- Gráfico 2: Distribución por Sección -->
                                <div class="chart-card">
                                    <div class="chart-title">Distribución por Sección</div>
                                    <div class="chart-wrapper">
                                        <canvas id="chartSeccion"></canvas>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 3: Gráficos 3 y 4 -->
                            <div class="dashboard-grid">
                                <!-- Gráfico 3: Top 10 Barrios -->
                                <div class="chart-card">
                                    <div class="chart-title">Top 10 Barrios con Mayor Participación</div>
                                    <div class="chart-wrapper">
                                        <canvas id="chartBarrios"></canvas>
                                    </div>
                                </div>

                                <!-- Gráfico 4: Intención de Voto -->
                                <div class="chart-card">
                                    <div class="chart-title">Intención de Voto por Distrito</div>
                                    <div class="chart-wrapper">
                                        <canvas id="chartIntencion"></canvas>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

        <!-- CSS Dashboard -->
        <link rel="stylesheet" href="assets/css/graficos-dashboard.css">

        <!-- Chart.js 4.4.1 -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" crossorigin="anonymous"></script>

        <!-- Dashboard JS Helpers -->
        <script src="assets/js/dashboard-charts.js"></script>

<script>
// ========== KPI CARDS ==========
document.addEventListener('DOMContentLoaded', function() {
  // Actualizar KPI 1: Total de Empadronados
  const totalElement = document.getElementById('totalEmpadronados');
  if (totalElement) {
    totalElement.textContent = <?php echo $total_empadronados; ?>.toLocaleString('es-PY');
  }

  // Actualizar KPI 2: Participación %
  const pctElement = document.getElementById('participacionPct');
  if (pctElement) {
    pctElement.textContent = <?php echo $pct_participacion; ?>;
  }
});

// ========== GRÁFICO 1: Votos por Distrito ==========
(function() {
  const ctx1 = document.getElementById('chartDistrito');
  if (ctx1) {
    new Chart(ctx1.getContext('2d'), {
      type: 'bar',
      data: {
        labels: <?php echo json_encode($distritos); ?>,
        datasets: [
          {
            label: 'Sí votaron',
            data: <?php echo json_encode($sivoto); ?>,
            backgroundColor: 'rgba(40, 167, 69, 0.7)',
            borderColor: 'rgb(40, 167, 69)',
            borderWidth: 1
          },
          {
            label: 'No votaron',
            data: <?php echo json_encode($novoto); ?>,
            backgroundColor: 'rgba(220, 53, 69, 0.7)',
            borderColor: 'rgb(220, 53, 69)',
            borderWidth: 1
          }
        ]
      },
      options: {
        ...responsiveChartOptions,
        indexAxis: 'y',
        scales: {
          x: {
            beginAtZero: true,
            stacked: false,
            ticks: {
              stepSize: 100
            }
          }
        },
        plugins: {
          ...responsiveChartOptions.plugins,
          tooltip: {
            mode: 'index',
            intersect: false
          }
        }
      }
    });
  }
})();

// ========== GRÁFICO 2: Distribución por Sección ==========
(function() {
  const ctx2 = document.getElementById('chartSeccion');
  if (ctx2) {
    new Chart(ctx2.getContext('2d'), {
      type: 'doughnut',
      data: {
        labels: <?php echo json_encode($secciones); ?>,
        datasets: [{
          data: <?php echo json_encode($cantidades_seccion); ?>,
          backgroundColor: [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
            '#FF9F40', '#FF6384', '#C9CBCF', '#FF6384', '#36A2EB'
          ],
          borderColor: '#fff',
          borderWidth: 2
        }]
      },
      options: {
        ...responsiveChartOptions,
        plugins: {
          ...responsiveChartOptions.plugins,
          tooltip: {
            callbacks: {
              label: function(context) {
                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                const percentage = ((context.parsed / total) * 100).toFixed(1);
                return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
              }
            }
          }
        }
      }
    });
  }
})();

// ========== GRÁFICO 3: Top 10 Barrios ==========
(function() {
  const ctx3 = document.getElementById('chartBarrios');
  if (ctx3) {
    // Crear gradiente de colores (verde a rojo)
    const colors = <?php echo json_encode($cantidades_barrios); ?>.map((val, idx) => {
      const max = Math.max(...<?php echo json_encode($cantidades_barrios); ?>);
      const ratio = val / max;
      // Gradiente: verde (alto) a rojo (bajo)
      const red = Math.round(255 * (1 - ratio));
      const green = Math.round(40 + (127 * ratio));
      return `rgba(${red}, ${green}, 69, 0.7)`;
    });

    new Chart(ctx3.getContext('2d'), {
      type: 'bar',
      data: {
        labels: <?php echo json_encode($barrios); ?>,
        datasets: [{
          label: 'Cantidad de votantes',
          data: <?php echo json_encode($cantidades_barrios); ?>,
          backgroundColor: colors,
          borderColor: colors.map(c => c.replace('0.7', '1')),
          borderWidth: 1
        }]
      },
      options: {
        ...responsiveChartOptions,
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              stepSize: 50
            }
          }
        },
        plugins: {
          ...responsiveChartOptions.plugins,
          legend: {
            display: false
          }
        }
      }
    });
  }
})();

// ========== GRÁFICO 4: Intención de Voto por Distrito ==========
(function() {
  const ctx4 = document.getElementById('chartIntencion');
  if (ctx4) {
    new Chart(ctx4.getContext('2d'), {
      type: 'line',
      data: {
        labels: <?php echo json_encode($distritos_intencion); ?>,
        datasets: [
          {
            label: 'Voto 1',
            data: <?php echo json_encode($votos_intencion['voto1']); ?>,
            borderColor: '#FF6384',
            backgroundColor: 'rgba(255, 99, 132, 0.1)',
            fill: true,
            tension: 0.3
          },
          {
            label: 'Voto 2',
            data: <?php echo json_encode($votos_intencion['voto2']); ?>,
            borderColor: '#36A2EB',
            backgroundColor: 'rgba(54, 162, 235, 0.1)',
            fill: true,
            tension: 0.3
          },
          {
            label: 'Voto 3',
            data: <?php echo json_encode($votos_intencion['voto3']); ?>,
            borderColor: '#FFCE56',
            backgroundColor: 'rgba(255, 206, 86, 0.1)',
            fill: true,
            tension: 0.3
          },
          {
            label: 'Voto 4',
            data: <?php echo json_encode($votos_intencion['voto4']); ?>,
            borderColor: '#4BC0C0',
            backgroundColor: 'rgba(75, 192, 192, 0.1)',
            fill: true,
            tension: 0.3
          },
          {
            label: 'Voto 5',
            data: <?php echo json_encode($votos_intencion['voto5']); ?>,
            borderColor: '#9966FF',
            backgroundColor: 'rgba(153, 102, 255, 0.1)',
            fill: true,
            tension: 0.3
          }
        ]
      },
      options: {
        ...responsiveChartOptions,
        interaction: {
          mode: 'index',
          intersect: false
        },
        scales: {
          y: {
            beginAtZero: true,
            stacked: false,
            ticks: {
              stepSize: 50
            }
          }
        },
        plugins: {
          ...responsiveChartOptions.plugins,
          filler: {
            propagate: true
          }
        }
      }
    });
  }
})();

</script>
<?php
if($incluir_pie) require_once('pie.php');
?>
