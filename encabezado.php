<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		<meta name="description" content="<?php echo $empresa["nombre"] ?>">
		<meta name="author" content="Luis Fretes | Whatsapp 595976730804">
		<meta name="keywords" content="<?php echo $empresa["nombre"] ?>">
		<link rel="icon" type="image/png" href="assets/img/<?php echo $empresa["icono"]?>" />
        <title><?php echo $empresa["nombre"] ?></title>
		<base href="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER["HTTP_HOST"] . '/'; ?>">
        <link href="css/styles.css?v9" rel="stylesheet" />
		<link href="css/estilo.css?v9" rel="stylesheet" />
		<link href="assets/font-awesome/css/all.min.css" rel="stylesheet" />
		
		 
		<link href="assets/bootstrap-multiselect/css/BsMultiSelect.min.css" rel="stylesheet"/>
        <link href="assets/bootstrap-datepicker/jquery.datetimepicker.min.css" rel="stylesheet"/>
        
		<link href="assets/bootstrap-select/bootstrap-select.min.css" rel="stylesheet"/>
		  
		<script src="js/jquery.min.js"></script>

		

		
    </head>
    <body>
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-danger">
            <!-- Navbar Brand-->
            <a class="navbar-brand ps-3" href="inicio"><img src="assets/img/<?php echo $empresa["icono"]?>" style="height: 40px;margin-right: 10px;"  alt="logo"><?php echo $empresa["nombre"] ?></a>
            <!-- Sidebar Toggle-->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fa fa-bars"></i></button>
			<form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0"></form>
            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-user fa-fw"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        
                        <!-- <li><hr class="dropdown-divider" /></li>-->
						<?php 	if(isset($_SESSION['usuario'])){ ?>
							<li><a class="dropdown-item" href="logout.php">Salir</a></li>
						<?php }else{ ?>
							<li><a class="dropdown-item" href="login.php">Mi Cuenta</a></li>
						<?php } ?>
                    </ul>
                </li>
            </ul>
        </nav>
        <div id="layoutSidenav" >
            <div id="layoutSidenav_nav" >
                <nav class="sb-sidenav accordion sb-sidenav-dark  " id="sidenavAccordion" >
                    <div class="sb-sidenav-menu">
						
                        <div class="nav">
							<?php 	if(isset($_SESSION['usuario']) and $_SESSION['usuario']['nivel_acceso'] == 0){ ?>	
							<div class="sb-sidenav-menu-heading">Inicio</div>
                            <a class="nav-link" href="index">
                                <div class="sb-nav-link-icon"><i class="fa fa-pie-chart"></i></div>
                                Inicio
                            </a>
                            
                            <div class="sb-sidenav-menu-heading">Reportes</div>
	                        <a class="nav-link" href="graficos">
                                <div class="sb-nav-link-icon"><i class="fa fa-pie-chart"></i></div>
                                Gráficos
                            </a>	
                            
							<a class="nav-link" href="presupuestos">
                                <div class="sb-nav-link-icon"><i class="fa fa-money-check-dollar-pen "></i> </div> 
                                Presupuestos 
                            </a> 
							
							 <div class="sb-sidenav-menu-heading">Páginas</div>

 
							
                            <a class="nav-link" href="inicio">
                                <div class="sb-nav-link-icon"><i class="fa fa-folder-search "></i> </div> 
                                Padrón 
                            </a> 
							<!--
							<a class="nav-link" href="simuladorvoto">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-person-booth"></i></div>
                                Simulador
                            </a>                            
 							-->
                            <a class="nav-link" href="barrios">
                                <div class="sb-nav-link-icon"><i class="fa-regular fa-earth-americas"></i> </div> 
                                Barrios 
                            </a> 


      
							
                            <div class="sb-sidenav-menu-heading">Personas</div>
                            <a class="nav-link" href="usuarios">
                                <div class="sb-nav-link-icon"><i class="fa fa-user-circle "></i></div>
                                Usuarios
                            </a>
 					
							<?php }else{ ?>	
							
                            <a class="nav-link" href="login.php">
                                <div class="sb-nav-link-icon"><i class="fa fa-user-circle "></i></div>
                                Mi Cuenta
                            </a>
							
							<?php } ?>	
	                        
							<a class="nav-link" href="assets/hc_misiones.apk">
                                <div class="sb-nav-link-icon"><i class="fa-brands fa-android"></i></div>
                                APP Android
                            </a>						
							
                        </div>
						 
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">.:HC:. </div>
                        <?php echo $empresa["nombre"] ?>
                    </div>
                </nav>
            </div>
            <div id="layoutSidenav_content">
                <main>