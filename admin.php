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

if(!isset($_SESSION['usuario']) || $_SESSION['usuario']['nivel_acceso'] != 1){
    header("Location: login.php");
    die();
}
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Panel Administrativo</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>

    <div class="row">
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-users me-1"></i>
                    Gestión de Usuarios
                </div>
                <div class="card-body">
                    <p>Administrar usuarios del sistema</p>
                    <a href="usuarios" class="btn btn-primary">Ir a Usuarios</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
if($incluir_pie) require_once('pie.php');
?>
