<?php
error_reporting(1);
if(!isset($_SESSION)){@session_start();}
date_default_timezone_set('America/Asuncion');

// AUTO-DETECCION DE ENTORNO
$en_docker = (getenv('DB_HOST') !== false) || (php_sapi_name() === 'cli' && file_exists('/.dockerenv'));

$es_produccion = (
    strpos($_SERVER['HTTP_HOST'] ?? '', 'votacion.com.py') !== false ||
    strpos($_SERVER['HTTP_HOST'] ?? '', 'votacion.quantex.com.py') !== false ||
    strpos($_SERVER['SERVER_NAME'] ?? '', 'votacion.com.py') !== false
);

if ($en_docker || (!$es_produccion && php_sapi_name() !== 'cli')) {
    // DESARROLLO LOCAL (DOCKER)
    $hostname_web = "host.docker.internal";
    $database_web = "hcmisiones";
    $username_web = "hcmisiones";
    $password_web = "123hcmisiones123//";
    $puerto_web = 3310;
    $ambiente = "DOCKER";
} else {
    // PRODUCCION
    $hostname_web = "localhost";
    $database_web = "votmilttnhb_hcmisiones";
    $username_web = "votmilttnhb_hcmisiones";
    $password_web = "123hcmisiones123//*";
    $puerto_web = 3306;
    $ambiente = "PRODUCCION";
}

$prefijo_tabla = "hc_";

// CONECTAR A BD
if (isset($puerto_web) && $puerto_web != '3306') {
    $mysqli_web = mysqli_connect($hostname_web, $username_web, $password_web, $database_web, $puerto_web);
} else {
    $mysqli_web = mysqli_connect($hostname_web, $username_web, $password_web, $database_web);
}

// VERIFICAR CONEXION
if ($mysqli_web->connect_errno) {
    $respuesta = "Error conectando a MySQL<br>";
    $respuesta .= "Ambiente: " . $ambiente . "<br>";
    $respuesta .= "Host: " . $hostname_web . ":" . $puerto_web . "<br>";
    $respuesta .= "BD: " . $database_web . "<br>";
    $respuesta .= "Usuario: " . $username_web . "<br>";
    $respuesta .= "Error: (" . $mysqli_web->connect_errno . ") " . $mysqli_web->connect_error . "<br>";
    echo $respuesta;
    exit();
}

// ZONA HORARIA MYSQL
$horaMysql = " DATE_ADD(now(), INTERVAL -6 HOUR) ";

// CHARSET
mysqli_set_charset($mysqli_web, "utf8");

// DEBUG
$mostrarLogSQL = ($ambiente === "DOCKER") ? true : false;

// FUNCIONES SQL
function select_sql($sql_SELECT)
{
    $_SESSION['sql'][] = $sql_SELECT;
    $error = true;
    $consulta = $GLOBALS['mysqli_web']->query($sql_SELECT);
    $result_array = array();

    if ($consulta) {
        $result_array = $consulta->fetch_all(MYSQLI_ASSOC);
        $error = false;
    } else {
        $error = true;
        $result_array = $GLOBALS['mysqli_web']->error;
    }
    return array("error" => $error, "resultado" => $result_array);
}

function ejecutar_sql($sql_ejecutar)
{
    $_SESSION['sql'][] = $sql_ejecutar;
    $error = true;
    $result = null;

    if ($GLOBALS['mysqli_web']->query($sql_ejecutar)) {
        $result = $GLOBALS['mysqli_web']->insert_id;
        $error = false;
    } else {
        $result = $GLOBALS['mysqli_web']->error;
        $error = true;
    }

    return array("error" => $error, "resultado" => $result);
}
?>
