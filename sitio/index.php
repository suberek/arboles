<?php
require_once('_db.php');
/*
Acá van los datos de conexión
$schema = "nombre de la base de datos";
$server = "servidor";
$user   = "usuario";
$pass   = "contraseña";
*/

require_once('custom/scripts/funciones-db.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="utf-8">

<!-- meta pal' face -->
<meta property="og:url" content="http://arboladourbano.com.ar/" />
<meta property="og:title" content="Mapa del Arbolado Urbano" />
<meta property="og:description" content="Buscador de árboles para la Ciudad de Buenos Aires. Datos recolectados en el censo de arbolado realizado por el GCBA entre el año 2011 y 2014. El objetivo principal de este sitio es simplificar el acceso a esta valiosa información esperando que colabore con el conocimiento y el cuidado de nuestro arbolado urbano." />
<meta property="og:image" content="http://arboladourbano.com.ar/images/logo-152x152.png" />

<title>Arbolado Urbano - árboles de la Ciudad Autónoma de Buenos Aires</title>
<meta name="description" content="Buscador de árboles para la Ciudad de Buenos Aires. Datos recolectados en el censo de arbolado realizado por el GCBA entre el año 2011 y 2014.">
<meta name="author" content="Martín Simonyan">

<!-- /ht Andy Clarke - http://front.ie/lkCwyf -->
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<link rel="shortcut icon" href="<?php echo $APP_URL; ?>/images/favicon.ico" type="image/x-icon" />
<link rel="apple-touch-icon" href="<?php echo $APP_URL; ?>/images/logo.png" />
<link rel="apple-touch-icon" sizes="152x152" href="<?php echo $APP_URL; ?>/images/logo-152x152.png" />

<!-- jQuery -->
<script src="<?php echo $APP_URL; ?>/third-party/jquery/jquery-2.1.1.min.js"></script>
<!-- jQuery Plugins-->
<script src="<?php echo $APP_URL; ?>/third-party/jquery/jquery-migrate-1.2.1.min.js"></script>

<!-- Bootstrap -->
<script src="<?php echo $APP_URL; ?>/third-party/bootstrap/js/bootstrap.min.js"></script>
<!-- Bootstrap Plugins-->
<script src="<?php echo $APP_URL; ?>/third-party/bootstrap-plugins/bootstrap-select.min.js"></script>

<!-- Leaflet -->
<script src="<?php echo $APP_URL; ?>/third-party/leaflet/leaflet.js"></script>
<!-- Leaflet Plugins -->
<script src="http://maps.google.com/maps/api/js?v=3.23&amp;key=AIzaSyBdRozBWsT2EOXOdF-6BQapQC_2GGz0qZQ"></script>
<script src="<?php echo $APP_URL; ?>/third-party/leaflet-plugins/Google/leaflet.google.min.js"></script>
<script src="<?php echo $APP_URL; ?>/third-party/leaflet-plugins/MarkerCluster/leaflet.markercluster.js"></script>
<script src="<?php echo $APP_URL; ?>/third-party/leaflet-plugins/Locate/L.Control.Locate.min.js" ></script>
<script src="<?php echo $APP_URL; ?>/third-party/leaflet-plugins/Geocoder/Control.Geocoder.min.js"></script>
<script src="<?php echo $APP_URL; ?>/third-party/leaflet-plugins/CanvasOverlay/L.CanvasOverlay.js"></script>

<!-- Custom -->
<link href="<?php echo $APP_URL; ?>/custom/css/estilos.css" rel="stylesheet" type="text/css" media="all">

<!--[if lt IE 9]>
	<link rel="stylesheet" href="third-party/leaflet-plugins/Locate/L.Control.Locate.ie.min.css"/>
<![endif]-->

<?php require_once('custom/scripts/individuos.php'); ?>
</head>

<body>

<?php
/*
echo("CONSULTA: <br>" . $busqueda);
echo "<div>QUERY: <br><br><pre>$censo_query</pre></div>";

echo "<br><br><div>$_POST: <br><br><pre>". print_r($_POST) ."</pre></div>";
*/
?>

<nav class="navbar navbar-default navbar-fixed-bottom visible-sm visible-xs">
	<div class="container-fluid">
		<a class="btn btn-default navbar-btn scroll" href="#busca_arboles"><i class="fa fa-search fa-sm"></i> Buscador <i class="fa fa-caret-up fa-sm"></i> </a>
		<a class="btn btn-default navbar-btn scroll" href="#mapa"><i class="fa fa-map-marker fa-sm"></i> Mapa <i class="fa fa-caret-down fa-sm"></i></a>
	</div>
</nav>
<div class="container-fluid full-height">
	<div class="row full-height">
		<div class="progress navbar-fixed-bottom">
			<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="1" aria-valuemin="0" aria-valuemax="100" style="width: 0%"> </div>
		</div>
		
		<!-- modal individuo (container) -->
		<div class="col-xs-12 col-sm-9 col-md-6" id="info-individuo"> </div>

		<!-- left slide bar -->
		<div class="col-md-4 col-lg-3" id="menu">
			<nav>
				<a class="title" href="<?php echo $APP_URL; ?>/">
				<h1>Arbolado Urbano
					<small>Buenos Aires</small></h1>
				</a>
				
				<? require_once 'custom/scripts/form.php'; ?>

				<div class="red">
					<div class="row">

						hola

						
						<a class="col-xs-12 col-sm-3 col-md-12 facebook" href="https://www.facebook.com/arboladourbanomapa" target="_blank"><i class="fa fw fa-facebook-official"></i> Seguinos en facebook</a>

						<p class="col-xs-12 col-sm-3 col-md-12 este-mapa">Este mapa cuenta con<br> la valiosa colaboración de:</p>

						<a class="col-xs-6 col-sm-3 col-md-6 lcnrs" href="https://www.facebook.com/LaCiudadNosRegalaSabores" target="_blank"><img src="<?php echo $APP_URL; ?>/images/colaborador-lcnrs.png" alt="La ciudad nos regala sabores"></a>

						<a class="col-xs-6 col-sm-3 col-md-6 arn" href="https://www.facebook.com/AsociacionRiberaNorte/" target="_blank"><img src="<?php echo $APP_URL; ?>/images/colaborador-arn.png" alt="Asociación Ribera Norte"></a>

						
					</div>
				</div>

				<button class="btn btn-default btn-small btn-block que-es-esto" data-toggle="modal" data-target="#que-es-esto">¿Qué es esto?</button>

				<div id="adsense">
					<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
					<script>
						(adsbygoogle = window.adsbygoogle || []).push({
							google_ad_client: "ca-pub-7228206495347837",enable_page_level_ads: true
						});
					</script>
				</div>
			
			</nav>
		</div>
		
		<!-- mapa (container) -->
		<div class="col-md-8 col-lg-9 full-height" id="mapa"> </div>
	</div>
</div>

<?php require_once('custom/scripts/modals.php') ?>
<script type="text/javascript" src="<?php echo $APP_URL; ?>/custom/scripts/interaccion-form-mapa.min.js"></script>
<?php require_once('custom/scripts/pintar-mapa.php') ?>
<?php require_once('custom/scripts/funciones-js-footer.php') ?>
</body>
</html>