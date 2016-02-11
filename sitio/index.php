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
<meta name="viewport" content="width=device-width, initial-scale=1">
<head>
<meta charset="utf-8">
<title>Arbolado Urbano - árboles de la Ciudad Autónoma de Buenos Aires</title>
<meta name="description" content="Buscador de árboles para la Ciudad de Buenos Aires. Datos recolectados en el censo de arbolado realizado por el GCBA entre el año 2011 y 2014.">
<meta name="author" content="Martín Simonyan">

<!-- /ht Andy Clarke - http://front.ie/lkCwyf -->
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<link rel="shortcut icon" href="<?php echo $APP_URL; ?>/images/favicon.ico" type="image/x-icon" />
<link rel="apple-touch-icon" href="<?php echo $APP_URL; ?>/images/logo.png" />
<link rel="apple-touch-icon" sizes="57x57" href="<?php echo $APP_URL; ?>/images/logo-57x57.png" />
<link rel="apple-touch-icon" sizes="72x72" href="<?php echo $APP_URL; ?>/images/logo-72x72.png" />
<link rel="apple-touch-icon" sizes="76x76" href="<?php echo $APP_URL; ?>/images/logo-76x76.png" />
<link rel="apple-touch-icon" sizes="114x114" href="<?php echo $APP_URL; ?>/images/logo-114x114.png" />
<link rel="apple-touch-icon" sizes="120x120" href="<?php echo $APP_URL; ?>/images/logo-120x120.png" />
<link rel="apple-touch-icon" sizes="144x144" href="<?php echo $APP_URL; ?>/images/logo-144x144.png" />
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
<script src="http://maps.google.com/maps/api/js?v=3.2&amp;sensor=false"></script>
<script src="<?php echo $APP_URL; ?>/third-party/leaflet-plugins/Google/leaflet.google.min.js"></script>
<script src="<?php echo $APP_URL; ?>/third-party/leaflet-plugins/MarkerCluster/leaflet.markercluster.js"></script>
<script src="<?php echo $APP_URL; ?>/third-party/leaflet-plugins/Locate/L.Control.Locate.min.js" ></script>
<script src="<?php echo $APP_URL; ?>/third-party/leaflet-plugins/Geocoder/Control.Geocoder.min.js"></script>
<script src="<?php echo $APP_URL; ?>/third-party/custom-scrollbar/jquery.mCustomScrollbar.concat.min.js"></script>

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

<nav class="navbar navbar-default navbar-fixed-bottom visible-sm visible-xs" role="navigation">
	<div class="container-fluid">
		<a type="button" class="btn btn-default navbar-btn scroll" href="#busca_arboles"><i class="fa fa-search fa-sm"></i> Buscador <i class="fa fa-caret-up fa-sm"></i> </a>
		<a type="button" class="btn btn-default navbar-btn scroll" href="#mapa"><i class="fa fa-map-marker fa-sm"></i> Mapa <i class="fa fa-caret-down fa-sm"></i></a>
	</div>
</nav>
<div class="container-fluid full-height">
	<div class="row full-height">
		<div class="progress navbar-fixed-bottom">
		  <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="1" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
			
		  </div>
		</div>
		
		<div class="col-md-4 col-lg-3" id="info-individuo">
			
		</div>

		<div class="col-md-4 col-lg-3" id="menu">
			<nav role="navigation">
				<a class="title" href="<?php echo $APP_URL; ?>/">
				<h1>Arbolado<br>
					Urbano
					<small>Buenos Aires</small></h1>
				</a>
				
				<form action="<?php echo $APP_URL; ?>/index.php#mapa" method="post" id="busca_arboles" role="form">
					<div class="row">
						<div class="col-xs-12">
							<div class="form-group">
								<h3>¿Dónde?</h3>
								<div class="radio"> 
									<label>
										<input type="radio" id="rdonde-ciudad" name="rdonde" value="0" <?php if (stripos($busqueda,'marker') == 0) echo 'checked' ?>  />
										en toda la ciudad </label>
									<label>
										<input type="radio" id="rdonde-mapa" name="rdonde" value="<? echo $user_latlng_default[0].' '.$user_latlng_default[1] ?>"  <?php if (stripos($busqueda,'marker') > 0) echo 'checked' ?>  />
										marcar en el mapa </label>
								</div>
								<input type="hidden" value="<?php echo($user_lat.' '.$user_lng); ?>" name="user_latlng" id="user_latlng">
							</div>
						</div>
					
						<div class="col-xs-12">
					
							<div class="form-group">
								<h3 class="pull-left">¿Qué especie?</h3>
								<a href="#" id="borrar_id_especie"><i class="fa fa-trash-o"></i></a>
								
								<select class="form-control input-lg selectpicker" data-style="btn-default" name="id_especie" id="id_especie" data-live-search="true">
									<option value="0">Todas</option>
									<?php
										// Consulto especies y cantidad
										/*$especies_query = "SELECT count(i.id_especie) as CANT, e.id_especie, e.NOMBRE_CIE, e.NOMBRE_COM
													FROM individuos AS i, especies AS e
													WHERE i.id_especie = e.id_especie
													GROUP BY e.id_especie
													ORDER BY e.NOMBRE_CIE";*/
										
										// Consulto especies sin cantidad
										$especies_query = "SELECT e.id_especie, e.NOMBRE_CIE, e.NOMBRE_COM
													FROM especies AS e
													ORDER BY e.NOMBRE_CIE";
										
										$especies_results	= GetRS($especies_query);
										
										// Armo el array con los individuos
										while ($especies_row = mysql_fetch_array($especies_results)) {
											$i++;
												
											$lista_NCIE		= $especies_row['NOMBRE_CIE'];
											$lista_NCOM		= $especies_row['NOMBRE_COM'];
											$lista_ID		= $especies_row['id_especie'];
											//$lista_CANT		= $especies_row['CANT'];
											
											$selected = '';
											if ($id_especie_busqueda===$lista_ID) {
												$selected = ' selected';

												// Me guardo la variable para cambiar la URL
												$especie_URL = sanear_string($lista_NCIE);
												$especie_URL = strtolower(str_replace(" ", "-", $especie_URL));
												//$especie_URL = "/q/" . $id_especie_busqueda . "/" . $especie_URL;
												$especie_URL = "/q/" . $especie_URL;
											} 
											echo '<option value="'.$lista_ID.'" '.$selected.' data-subtext="'. $lista_NCOM.'">' . $lista_NCIE . ' </option>
											';

											/*$SQL = "UPDATE especies SET url = '"  .$especie_URL.  "' WHERE id_especie=".$lista_ID.";";
											echo($SQL . "<br>");*/
										}
										
										
									?>
								</select>
								
							</div>
					
						</div>
											

						<div class="col-xs-12 <?php echo $masFiltrosCss; ?>" id="mas-filtros">
							<div class="form-group">
								<h3>Sabores</h3>
								<label for="user_sabores"> <input type="checkbox" name="user_sabores" id="user_sabores" value="1"  <?php if ($user_sabores > 0) echo 'checked' ?> > frutales y medicinales <span class="label label-warning">beta</span></label>
							</div>
					
							<div class="form-group">
								<h3>Origen</h3>
								<div class="radio"> 
									<label>
										<input type="radio" id="rorigen-nativas" name="user_origen" value="Nativo/Autóctono" <?php if (stripos($busqueda,'Nativo') > 0) echo 'checked' ?>  />
										nativas </label>
									<label>
										<input type="radio" id="rorigen-exoticas" name="user_origen" value="Exótico" <?php if (stripos($busqueda,'Exótico') > 0) echo 'checked' ?>  />
										exóticas </label>
									<a href="#" id="borrar_origen"><i class="fa fa-trash-o"></i></a>
								</div>
							</div>
						</div>


						<div class="col-xs-12" id="mas-filtros-btn-container">
							<a href="#" class="btn btn-default mas-filtros"><?php if ($masFiltrosCss == 'oculto') { echo "más"; }else{ echo "menos";} ?> filtros</a>
						</div>
						
						
					</div>
					
					<input name="Buscar" type="submit" value="Buscar" class="btn btn-primary btn-lg btn-block">
				</form>

				<a class="lcnrs" href="https://www.facebook.com/LaCiudadNosRegalaSabores" target="_blank"><img src="<?php echo $APP_URL; ?>/images/complot-lcnrs.png" alt="La ciudad nos regala sabores"></a>

				<button class="btn btn-default btn-small btn-block que-es-esto" data-toggle="modal" data-target="#que-es-esto">¿Qué es esto?</button>

				<div id="adsense">
					<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
					<!-- arbolado auto -->
					<ins class="adsbygoogle"
						style="display:block"
						data-ad-client="ca-pub-7228206495347837"
						data-ad-slot="4469149174"
						data-ad-format="auto"></ins>
					<script>
						(adsbygoogle = window.adsbygoogle || []).push({});
					</script>
				</div>
			
			</nav>
		</div>
	
		<div class="col-md-8 col-lg-9 full-height" id="mapa"> </div>
	</div>
</div>


<?php require_once('custom/scripts/modals.php') ?>
<script type="text/javascript" src="<?php echo $APP_URL; ?>/custom/scripts/interaccion-form-mapa.min.js"></script>
<?php require_once('custom/scripts/pintar-mapa.php') ?>

<?php require_once('custom/scripts/funciones-js-footer.php') ?>

</body>
</html>