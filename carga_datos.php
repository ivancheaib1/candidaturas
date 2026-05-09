<?php 
require_once('ws/conexion.php');
require_once('ws/funciones.php');
$url = (explode("/", trim($_SERVER["REQUEST_URI"], '/')));
$posicion = 1;
$pagina = (!isset($url[$posicion]) or $url[$posicion] == "" or substr($url[$posicion],0,5) == "carga_") ? "cargar_datos1.php" : $url[$posicion].'.php';

//echo $pagina; exit();
//var_dump($_SESSION);

if(!isset($_SESSION['usuario'])){
	header("Location: http://".$_SERVER["SERVER_NAME"]."/".$carpeta_sistema."veedor.php");die();
}


$query = "SELECT id,nombre,icono,logo imagen,correo,mapa,estado,facebook,whatsapp FROM ".$prefijo_tabla."sitio WHERE estado = 1 AND id = 1";
$empresa = $_SESSION['empresa'] = select_sql($query)["resultado"][0];
if($empresa["imagen"] == "" or  !file_exists("assets/img/".$empresa["imagen"])){
    $empresa["imagen"] = "logo.png";
} 

if($empresa["icono"] == "" or !file_exists("assets/img/".$empresa["icono"])){
    $empresa["icono"] = "ico.png";
} 
InsertarPeticion();

$pagina = 'cargar_datos1.php';

if(!file_exists($pagina)){http_response_code(404);$pagina = "404.php";} 

require_once('encabezado.php');
?>
<script>
var $wsUrl = 'Padron';

function buscarRegistro(){
	// Simplemente envía el formulario directamente
	document.querySelector('form[action="actualizavoto.php"]').submit();
}
</script>
<?php
///require_once('menu.php');

include($pagina);

require_once('pie.php');



mysqli_close($mysqli_web);

?> 