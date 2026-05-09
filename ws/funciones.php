<?php 
$monedaSimbolo = "Gs.";
$decimal = 2;
$usuario = (isset($_SESSION['usuario']['id'])) ? $_SESSION['usuario']['id'] : 0; //$_SESSION['usuario']['id']
$horaAbiertaDesde = '07:00';
$horaAbiertaHasta = '22:30';
$intervaloTimepoReserva = 15;
$minutosReserva = 90;
$minimoReserva =  $intervaloTimepoReserva ;
$esAdmin = verificarRol('Admin');

$meses = array("", "Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
$duracion  = array(10,20,30,40,50,60,70,80,90,100,110,120);
$colorfondo = "#027c49"; //"#2c3846e3";
 

$niveles_acceso = array('Administrador', 'Editor','Veedor', 'Invitado');
$dias = array("Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado");

$cantidadLimites = 20; //Cantidad de registros a traer en select para la tabla 
$turnos = array(1 => 'Mañana', 2 => 'Tarde' , 3 => 'Noche');
$tipo_pago = array(1 => 'Efectivo', 2 => 'Tarjeta' , 3 => 'Cheque', 4 => 'Crédito');
$imprimirPDF = false;
 
$var_carpeta_pdf = " ";

$carpeta_sistema = "";

function imprimirJson($error, $resultado)
{
	
	if($GLOBALS['mostrarLogSQL']){//Si esta true $mostrarLogSQL va a imprimir el sql 
		echo json_encode(array("sql" => $_SESSION['sql'], "error" => $error, "resultado" => $resultado));	 	
	}else{
		echo json_encode(array("error" => $error, "resultado" => $resultado));
	}
	InsertarPeticion();
	exit;
}

function encabezadoJSON()
{
	header("Content-type: application/json");
	//header("charset=utf-8");
	//header("charset=utf-8");
	header("Access-Control-Allow-Origin: *");
}

function seguridadJSON()
{
 
}

function limpiarMalicioso($string)
{
  return trim($string);
}

function contrasena($password)
{
	return substr(md5($password), -5).md5($password);
}

function InsertarPeticion()
{
 $parametros = base64_encode(gzcompress(json_encode(array('GET'=>$_GET, 'POST'=>$_POST, 'SQL'=> $_SESSION['sql']))));
 $insertar = "INSERT INTO  ".$GLOBALS['prefijo_tabla']."peticiones(id_usuario, ip_usuario, navegador_usuario, url_peticion, param_peticion, fecha_peticion) 
 VALUES ( '".$_SESSION['usuario']['id']."', '".get_client_ip_server()."', '".getBrowser()."', '".$_SERVER['SCRIPT_FILENAME']."', '". $parametros."', '".date('Y-m-d H:i:s')."')";
 //ejecutar_sql($insertar);	
 $_SESSION['sql'] = array();
 unset($_SESSION['sql']);
 
}
function verificarRol($rol)
{
	//	var_dump($_SESSION["usuario"]);
	return (isset($_SESSION["usuario"]["tipo"]) and $_SESSION["usuario"]["tipo"] == 1);
	/*
	$existeRol = false;
	foreach($_SESSION["usuario"]["roles"] as $roles){
		if($roles["descripcion"] == $rol){
			$existeRol = true;
		}
	}
	return $existeRol;*/
}

function generarLetra(){
	$letras = array("a","b","c","d","e","f","0","1","2","3","4","5","6","7","8","9");
	$numero = rand(0,15); //(Math.random()*15).toFixed(0);
	return $letras[$numero];
}	

function colorHEX(){
	$coolor = "";
	for($i=0;$i<6;$i++){
		$coolor = $coolor . generarLetra() ;
	}
	return "#" + $coolor;
}

function get_client_ip_server() {
    $ipaddress = '';
    if ($_SERVER['HTTP_CLIENT_IP'])
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if($_SERVER['HTTP_X_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if($_SERVER['HTTP_X_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if($_SERVER['HTTP_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if($_SERVER['HTTP_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if($_SERVER['REMOTE_ADDR'])
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';

    return $ipaddress;
}

function construirURL($archivo, $parametros = array()) {
	$protocolo = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
	$host = $_SERVER['HTTP_HOST'];
	$carpeta = $GLOBALS['carpeta_sistema'] ?? '';

	$url = $protocolo . $host . '/' . $carpeta . $archivo;

	if (!empty($parametros)) {
		$url .= '?' . http_build_query($parametros);
	}

	return $url;
}

function getBrowser(){
$user_agent = $_SERVER['HTTP_USER_AGENT'];
if(strpos($user_agent, 'MSIE') !== FALSE)
   return 'Internet explorer';
 elseif(strpos($user_agent, 'Edge') !== FALSE) //Microsoft Edge
   return 'Microsoft Edge';
 elseif(strpos($user_agent, 'Trident') !== FALSE) //IE 11
    return 'Internet explorer';
 elseif(strpos($user_agent, 'Opera Mini') !== FALSE)
   return "Opera Mini";
 elseif(strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR') !== FALSE)
   return "Opera";
 elseif(strpos($user_agent, 'Firefox') !== FALSE)
   return 'Mozilla Firefox';
 elseif(strpos($user_agent, 'Chrome') !== FALSE)
   return 'Google Chrome';
 elseif(strpos($user_agent, 'Safari') !== FALSE)
   return "Safari";
 else
   return 'No hemos podido detectar su navegador';

}

function descargar_archivo($dl_file)
{
	if(is_file("../csv/".$dl_file))
	{
		if(ini_get('zlib.output_compression')) {
			ini_set('zlib.output_compression', 'Off');
		}
		header('Expires: 0');
		header('Pragma: public');
		header('Cache-Control: private',false);
		header('Content-Type: application/force-download');
		header('Content-Disposition: attachment; filename="'.basename($dl_file).'"');
		header('Content-Transfer-Encoding: binary');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Last-Modified: '.gmdate ('D, d M Y H:i:s', filemtime ("../csv/".$dl_file)).' GMT');
		header('Content-Length: '.filesize("../csv/".$dl_file));
		header('Connection: close');
		readfile("../csv/".$dl_file);
		unlink("../csv/".$dl_file);
		die();
	} else {
		echo "Archivo $dl_file no encontrado";
	}
}


function ddmmyyyyTOyyyymmdd($fecha){
	
	$f = explode(" ", trim($fecha));
	if(count($f) > 0) {
		$d = explode("/", trim($f[0]));
		return $d[2]."-".$d[1]."-".$d[0].' '.$f[1];
	}else {
		$d = explode("/", trim($fecha));
		return $d[2]."-".$d[1]."-".$d[0];
	}
	
 
}
function yyyymmddTOddmmyyyy($fecha){
	$f = explode(" ", trim($fecha));
	if(count($f) > 0) {
		$d = explode("-", trim($f[0]));
		return $d[2]."/".$d[1]."/".$d[0].' '.$f[1];
	}else {
		$d = explode("-", trim($fecha));
		return $d[2]."/".$d[1]."/".$d[0];
	}
}




function array_sort($array, $on, $order=SORT_ASC)
{
	//$array = $array[0];
	//print_r($array);
	 
    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
            break;
            case SORT_DESC:
                arsort($sortable_array);
            break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }

    return $new_array;
}

function cargarPagina(){
	$url = (explode("/", trim($_SERVER["REQUEST_URI"], '/')));
	//var_dump($url);exit;//echo substr($url[1],0,5);exit;
	$pagina = (!isset($url[1]) or substr($url[1],0,5) == "index") ? "principal.php" : $url[1].'.php';
	unset($url[0]);unset($url[1]);
	$variables = array_values($url);
	if(!file_exists($pagina)){http_response_code(404);$pagina = "404.php";} 
	include($pagina);
}



function daterange($col) {
	echo '<div class="form-group '.$col.'">
		<label>Fechas:</label>
		<div class="input-group daterange-btn">
			<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
			<input type="text" class="form-control" name="fechas" id="fechas" value="'.date ( 'd/m/Y' , strtotime ( '-29 day' , strtotime ( date('Y-m-d') ) ) ) . " - ". date ( 'd/m/Y') .'">		  
		</div>
		</div>';
} 
/************************************/

?>
