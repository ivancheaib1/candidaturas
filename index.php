<?php 
require_once('ws/conexion.php');
require_once('ws/funciones.php');

// Primero intenta leer del parámetro GET (cuando viene del .htaccess)
if(isset($_GET['page']) && !empty($_GET['page'])){
    $pagina_solicitada = $_GET['page'];
    $es_pagina_default = false;
} else {
    // Si no, lee de la URL
    $url = explode("/", trim($_SERVER["REQUEST_URI"], '/'));
    $pagina_solicitada = isset($url[0]) ? $url[0] : '';
    $es_pagina_default = ($pagina_solicitada == "" or substr($pagina_solicitada,0,5) == "index");
}

$pagina = $es_pagina_default ? "inicio.php" : $pagina_solicitada.'.php';

//echo $pagina; exit();

if(!isset($_SESSION['usuario']) and ($pagina != "login.php" and $pagina != "loginveedor.php")){
	header("Location: http://".$_SERVER["HTTP_HOST"]."/login.php");die();
}
 //print_r($_SESSION);
 $nivel_acceso = (string)$_SESSION['usuario']['nivel_acceso'];

// Solo redirigir si es la página por defecto
if($es_pagina_default){
	if($nivel_acceso === '1' ){
		$pagina = "usuarios.php";

	} else if($nivel_acceso === '2' ){
		$pagina = "inicio.php";

	} else if($nivel_acceso === '3' ){
		$pagina = "invitados.php";

	}
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

// Establecer $wsUrl basado en la página que se va a incluir (sin la extensión .php)
$wsUrl = ucwords(str_replace('.php', '', $pagina));

if(!file_exists($pagina)){http_response_code(404);$pagina = "404.php";}

require_once('encabezado.php');
?>
<script>
var $wsUrl = '<?php echo $wsUrl; ?>';
</script>
<?php
///require_once('menu.php');
//print_r($pagina);
//var_dump($_SESSION);
include($pagina);
require_once('pie.php');



mysqli_close($mysqli_web);

?> 