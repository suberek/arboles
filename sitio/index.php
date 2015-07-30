<?php header('Content-Type: text/html; charset=UTF-8'); ?>
<?php

require_once('_db.php');
/*
Acá van los datos de conexión
$schema = "nombre de la base de datos";
$server = "servidor";
$user   = "usuario";
$pass   = "contraseña";
*/

require_once('tirameladata/funciones.php');


// Parámetros de búsqueda
//// Defino el default
$parametro	= "WHERE 1";
$busqueda	= "vacia";
$radius		= "1000"; // Radio de búsqueda en Metros
$user_latlng_default = array("-34.60371794474704","-58.38157095015049"); // El Obelisco

//// Veo qué vino en el form
$id_especie_busqueda	= $_REQUEST['id_especie'];
$user_latlng			= $_REQUEST['user_latlng'];
$user_sabores			= $_REQUEST['user_sabores'];

/**************************************************************** PARÁMETRO ESPECIE */
if ((is_numeric($id_especie_busqueda)) && ($id_especie_busqueda > 0)) {
	$parametro .= " AND id_especie=$id_especie_busqueda";
	
	$especie_query	= "
	SELECT e.NOMBRE_CIE
	FROM especies e
	$parametroJoin
	WHERE e.id_especie = $id_especie_busqueda
	LIMIT 1;
	";
	$especie_results	= GetRS($especie_query);
	$especie_row		= mysql_fetch_array($especie_results);
	$nombre_cientifico	= $especie_row['NOMBRE_CIE'];
	
	$busqueda	= "especie una";
	
} else {
	$id_especie_busqueda = '';
	$busqueda	= "especie todas";
}

/**************************************************************** PARÁMETRO ZONA */
if (  !empty($user_latlng) && (strlen($user_latlng) > 1 )  ) {
	
	//echo(strlen($user_latlng). "<br>");
	//echo("me está llegando esto:" . $user_latlng);
	
	// Parsear lat y lng
	$arr_user_latlng = explode(" ", $user_latlng);
	$user_lat = $arr_user_latlng[0];
	$user_lng = $arr_user_latlng[1];
	
	if (  is_numeric($user_lat) && is_numeric($user_lng)  ) {
		$busqueda	.= " donde marker";
	} else {
		$busqueda	.= " donde ciudad";
	}
	
} else {
	$busqueda	.= " donde ciudad";
}

if ($busqueda == "especie todas donde ciudad") {
	$busqueda = "vacia";
}


/**************************************************************** PARÁMETRO SABORES */
if ((is_numeric($user_sabores)) && ($user_sabores > 0)) {
	//$parametro .= " AND ( id_especie = 23 )";
	$parametroJoin = " INNER JOIN especies e ON i.id_especie=e.id_especie";
	$parametro .= " AND ( e.comestible <> '' OR e.medicinal <> '' )";


	$busqueda .= " con sabores";
}

//echo("<br>".$user_lat);
//echo("<br>".$user_lng);

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
<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />
<link rel="apple-touch-icon" href="images/logo.png" />
<link rel="apple-touch-icon" sizes="57x57" href="images/logo-57x57.png" />
<link rel="apple-touch-icon" sizes="72x72" href="images/logo-72x72.png" />
<link rel="apple-touch-icon" sizes="76x76" href="images/logo-76x76.png" />
<link rel="apple-touch-icon" sizes="114x114" href="images/logo-114x114.png" />
<link rel="apple-touch-icon" sizes="120x120" href="images/logo-120x120.png" />
<link rel="apple-touch-icon" sizes="144x144" href="images/logo-144x144.png" />
<link rel="apple-touch-icon" sizes="152x152" href="images/logo-152x152.png" />

<!-- jQuery -->
<script src="third-party/jquery/jquery-2.1.1.min.js"></script>
<!-- jQuery Plugins-->
<script src="third-party/jquery/jquery-migrate-1.2.1.min.js"></script>

<!-- Bootstrap -->
<script src="third-party/bootstrap/js/bootstrap.min.js"></script>
<!-- Bootstrap Plugins-->
<script src="third-party/bootstrap-plugins/bootstrap-select.min.js"></script>

<!-- Leaflet -->
<script src="third-party/leaflet/leaflet.js"></script>
<!-- Leaflet Plugins -->
<script src="http://maps.google.com/maps/api/js?v=3.2&amp;sensor=false"></script>
<script src="third-party/leaflet-plugins/Google/leaflet.google.min.js"></script>
<script src="third-party/leaflet-plugins/MarkerCluster/leaflet.markercluster-src.js"></script>
<script src="third-party/leaflet-plugins/Locate/L.Control.Locate.min.js" ></script>
<script src="third-party/leaflet-plugins/Geocoder/Control.Geocoder.min.js"></script>

<!-- Custom -->
<link rel="stylesheet" type="text/css" href="custom/css/estilos.css" media="all">

<!--[if lt IE 9]>
    <link rel="stylesheet" href="third-party/leaflet-plugins/Locate/L.Control.Locate.ie.min.css"/>
<![endif]-->

<?php

if ($busqueda !== 'vacia') {
	
	// Hago LA consulta

	/*
	Usando el campo GEOESPACIAL puedo buscar así:
	$censo_query = "
	SELECT id_individuo, id_especie, X(`coordenadas`) as lat, Y(`coordenadas`) as lng
	FROM individuos
	$parametro";
	*/

	/*
	Como PHPMaker no permite la creación de campos GEOESPACIALES,
	busco por los campos comunes lat y lng
	*/

	$censo_query = "
		SELECT i.id_individuo, i.id_especie, lat, lng
		FROM individuos i
		$parametroJoin 
		$parametro";
		
	if (stripos($busqueda,'marker') > 0) {

		// Definir el centro y buscar en el radio.
		
		/*
		Usando el campo GEOESPACIAL puedo buscar así:
		$censo_query = "
		SELECT id_individuo, id_especie, X(`coordenadas`) as lat, Y(`coordenadas`) as lng ,(
			6371 * acos (
			  cos ( radians( $user_lat ) )
			  * cos( radians( lat ) )
			  * cos( radians( lng ) - radians( $user_lng ) )
			  + sin ( radians( $user_lat ) )
			  * sin( radians( lat ) )
			)
		  ) AS distance
		FROM individuos
		$parametro
		HAVING distance < ($radius/1000);";
		*/

		/*
		Como PHPMaker no permite la creación de campos GEOESPACIALES,
		busco por los campos comunes lat y lng
		*/

		$censo_query = "
		SELECT i.id_individuo, i.id_especie, lat, lng ,(
			6371 * acos (
			  cos ( radians( $user_lat ) )
			  * cos( radians( lat ) )
			  * cos( radians( lng ) - radians( $user_lng ) )
			  + sin ( radians( $user_lat ) )
			  * sin( radians( lat ) )
			)
		  ) AS distance
		FROM individuos i
		$parametroJoin
		$parametro
		HAVING distance < ($radius/1000);";
	}
	
	$count = true;
	$censo_results	= GetRS($censo_query);
	$total_registros_censo = $total_registros;
	
	// Armo el array con los individuos
	if ($total_registros_censo >= 1) {
		echo '<script> var individuos = [';
		
		while ($censo_row = mysql_fetch_array($censo_results)) {
			$i++;
				
			$lat			= $censo_row['lat'];
			$lng			= $censo_row['lng'];
			$id_individuo	= $censo_row['id_individuo'];
			$id_especie		= $censo_row['id_especie'];
			
			if ($i > 1) echo ',';
			
			echo '[' . $lat . ',' . $lng . ',' . $id_individuo. ',' . $id_especie . ']';
		}
		
		echo ']; </script>';
	}
	
} else {	
	// sin búsqueda
}

?>
</head>

<body>

<?php
//echo("CONSULTA: <br>".$busqueda);
//echo "<div>QUERY: <br><br><pre>$censo_query</pre></div>";
?>

<nav class="navbar navbar-default navbar-fixed-bottom visible-xs" role="navigation">
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

		<div class="col-sm-3 full-height" id="menu">
			<nav role="navigation">
				<a class="title" href="./">
				<h1>Arbolado<br>
					Urbano
					<small>Buenos Aires</small></h1>
				</a>
				<form action="index.php#mapa" method="post" id="busca_arboles" role="form">
					<div class="row">
						<div class="col-xs-12">
							<div class="form-group">
								<h3>¿Dónde?</h3>
								<div class="radio"> 
									<label>
										<input type="radio" id="rdonde-ciudad" name="rdonde" value="0"  <?php if (stripos($busqueda,'marker') == 0) echo 'checked' ?>  />
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
								<h3>¿Qué especie? <a href="#" id="borrar_id_especie"><i class="fa fa-trash-o"></i></a></h3>
								
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
											if ($id_especie_busqueda===$lista_ID) $selected = ' selected';
											echo '
												<option value="'.$lista_ID.'" '.$selected.' data-subtext="'. $lista_NCOM.'">' . $lista_NCIE . ' </option>
											';
										}
										
										
									?>
								</select>
								
							</div>
					
						</div>

						<div class="col-xs-12">
							<div class="form-group">
								<h3>Frutales y medicinales</h3>
								<label for="user_sabores"> <input type="checkbox" name="user_sabores" id="user_sabores" value="1"  <?php if ($user_sabores > 0) echo 'checked' ?> > Probar este filtro <span class="label label-warning">beta</span></label>
							</div>
						</div>
						
						
					</div>
					
					<input name="Buscar" type="submit" value="Buscar" class="btn btn-primary btn-lg btn-block">
					
				</form>
		
				<button class="btn btn-default btn-small btn-block que-es-esto" data-toggle="modal" data-target="#que-es-esto">¿Qué es esto?</button>

				<a class="lcnrs" href="https://www.facebook.com/LaCiudadNosRegalaSabores" target="_blank"><img src="images/complot-lcnrs.png" alt="La ciudad nos regala sabores"></a>
			
			</nav>
		</div>
	
		<div class="col-sm-9 full-height" id="mapa"> </div>
	</div>
</div>

<!-- Modal: ¿Qué es esto? -->
<div class="modal fade" id="que-es-esto" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4>¿Qué es esto?</h4>
			 </div>
			<div class="modal-body">
				<p>Este mapa surge gracias a la publicación de una información increíble: el <a href="http://data.buenosaires.gob.ar/dataset/censo-arbolado" target="_blank">censo del arbolado de la Ciudad de Buenos Aires.</a></p>
				<p>El objetivo principal de este sitio es simplificar el acceso a esta valiosa información esperando que colabore con el conocimiento y el cuidado de nuestro arbolado urbano.</p>
					
				<a href="http://martinsimonyan.com.ar/arboles-de-buenos-aires/" class="btn btn-default" target="_blank">Más información <i class="fa fa-caret-right fa-sm"></i></a>
			<hr>
				<h5>¿Con qué seguir?</h5>

				<p>Podés dejar tu opinión y tus idas para seguir mejorando esta herramienta haciendo click en la lamparita que está arriba a la derecha.</p>
				<p>Por ejemplo:
					<ul>
						<li>permitir seleccionar el radio de búsqueda</li>
						<li>buscar por barrio o comuna</li>
						<li>incorporar otras fuentes y no tan sólo las del censo del gobierno</li>
						<li>incorporar a otras ciudades</li>
						<li>permitir interacción con usuarios para que carguen ejemplares, fotografías y reporten errores</li>
						<li>etc, etc.</li>
					</ul>
				</p>
				
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal: seleccionar una especie -->
<div class="modal fade" id="respecies-una-modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-2"><i class="fa fa-exclamation-triangle fa-3x"></i></div>
					<div class="col-sm-10">
						<p>Escribí algunas letras en el cuadro de búsqueda y aparecerá un listado con las posibles especies.</p>
						<p><small>Podés buscar todas las especies mezcladas, pero antes debes limitar la zona marcando un punto en el mapa.</small></p>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal: marcar un punto -->
<div class="modal fade" id="respecies-todas-modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-2"><i class="fa fa-map-marker fa-3x"></i></div>
					<div class="col-sm-10">
						<p>Para buscar todas las especies tenés que seleccionar alguna zona marcando un punto en el mapa.</p>
						<p><small>O podés buscar en toda la ciudad seleccionado una especie.</small></p>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal: o todas las especies o toda la ciudad -->
<div class="modal fade" id="rdonde-ciudad-modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-2"><i class="fa fa-map-marker fa-3x"></i></div>
					<div class="col-sm-10">Marcá un punto en el mapa para limitar la búsqueda <br>
						<small>Podés buscar en toda la ciudad seleccionado una especie.</small></div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal: ¡Buscando! -->
<div class="modal fade" id="empieza-busqueda" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-2"><i class="fa fa-search fa-3x"></i></div>
					<div class="col-sm-10">Empieza la búsqueda.<br>
					<small>(redoblantes: prrrrrrr... )</small></div>
				</div>
			</div>
		</div>
	</div>
</div>


<?php
if ($total_registros_censo === 0) { ?>
<!-- Modal: sin resultados -->
<div class="modal fade" id="sin-resultados" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm">
	<div class="modal-content">
	  <div class="modal-body">
		<div class="row">
			<div class="col-sm-2"><i class="fa fa-search fa-3x"></i></div>
			<div class="col-sm-10"><p>Tu búsqueda no arrojó resultados.</p>
		<p><small>Probá buscando la especie que te interesa en toda la ciudad, o cambiando la zona de búsqueda marcando otro lugar en el mapa.</small></p></div>
		</div>
		 
		</div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
	  </div>
	</div>
  </div>
</div>
<? } ?>
	
<script type="text/javascript" src="custom/js/buscar.min.js"></script>
<script type="text/javascript">

$(document).ready(function(){
	
	<?php if ($total_registros_censo === 0) { ?>
		$('#sin-resultados').modal('show');
	<? } ?>
	
	// Límite del mapa puesto a Ciudad de Buenos Aires
	var southWest	= new L.LatLng(-35.052109 , -58.72673),
	northEast	= new L.LatLng(-34.192115 , -58.064804),
	bounds = new L.LatLngBounds(southWest, northEast);
	
	bodyHeight = $("section[data-role='main']").height();
	$("#mapa").css("height", bodyHeight); //set with CSS also...
	
	bodyWidth = $("section[data-role='main']").width();
	$("#mapa").css("width", bodyWidth); //set with CSS also...
	
	var map = L.map('mapa',
	{
		maxZoom: 20,
		minZoom: 12,
		maxBounds: bounds
	})<?php  if ( ($busqueda == 'vacia') || ($total_registros_censo == 0) ) echo '.setView([-34.618, -58.44], 12)' ?>;
			
		
	// MAPAS
	
	/*var stm = new L.TileLayer("http://{s}.tile.stamen.com/toner-lite/{z}/{x}/{y}.png", {
		minZoom: 2,
		maxZoom: 20,
		subdomains: 'abcd',
		attribution: 'mapa <a href="http://stamen.com">Stamen Design</a> y <a href="http://openstreetmap.org">OpenStreetMap</a>'
		});
	var Nokia_normalDay = new L.TileLayer("http://{s}.maptile.lbs.ovi.com/maptiler/v2/maptile/newest/normal.day/{z}/{x}/{y}/256/png8?token={devID}&app_id={appID}", {
		minZoom: 2,
		maxZoom: 20,
		subdomains: '1234',
		devID: 'xyz',
		appID: 'abc',
		attribution: 'mapa <a href="http://developer.here.com">Nokia</a>'
		});*/
		
	
	var ggr = new L.Google('ROADMAP');
	//var ggs = new L.Google('SATELLITE');
	//var ggh = new L.Google('HYBRID');
	//map.addControl(new L.Control.Layers({'Google Roadmap':ggr, 'Google Satélite':ggs, 'Google Hybrid':ggh, 'Stamen':stm, 'Nokia':Nokia_normalDay}));
	map.addLayer(ggr);
	
	// Barra de botones
	var myButton = L.control({ position: 'topleft' });
	
	// localizame
	L.control.locate({
		locateOptions: {
			maxZoom: 17
		},
		keepCurrentZoomLevel: true,
		strings: {
			title: "Buscar dónde estoy",  // title of the locate control
			popup: "Creo que estás por acá...",  // text to appear if user clicks on circle
			outsideMapBoundsMsg: "Me parece que estás fuera de la ciudad. Si no es así, por favor mové el mapa manualmente." // default message for onLocationOutsideMapBounds
		}
	}).addTo(map);

	
	function onLocationError(e) {
		alert(e.message);
	}

	map.on('locationfound', onLocationFound);
	map.on('locationerror', onLocationError);


	// Buscador Geocoder
	var geocoder = L.Control.geocoder({
		position:'topleft',
		showResultIcons:true,
		collapsed: true,
		placeholder: 'Buscá un lugar en la Ciudad',
		errorMessage: 'Nada, che. Buscá otra vez.',
	}).addTo(map);

	geocoder.markGeocode = function(result) {
		var bboxcenter = result.center;
		muestraPorAca(bboxcenter.lat, bboxcenter.lng, map, true)
	};
	

	// COPY
	map.attributionControl.addAttribution('Proyecto: <a href="http://martinsimonyan.com.ar/arboles-de-buenos-aires/">Martín Simonyan</a>');
	
	map.on('click', function(e) {
		onLocationFound(e,'','');
	})
	
	// Variable que contendrá al Marker con ubicación del usuario.
	// El uso de window permite encontrar la variable para borrar y crear el marker desde otra función como la que se usa en buscar.js
	window.new_user_marker = undefined;
	window.new_user_circle = undefined;
	
	function onLocationFound(e,lat,lng) {
		// Me fijo de dónde viene la lat y lng. Si es del evento del mouse (e) o si es del form (lat, lng)
		
		lat = lat || 0;
		lng = lng || 0;
		
		if( lat == 0 ) {
			//tomo el valor por Geolocalización o click;
			var nuevoLat = e.latlng.lat;
			var nuevoLng = e.latlng.lng;
		} else {
			//tomo el valor por parámetro de función (del form);
			var nuevoLat = lat;
			var nuevoLng = lng
		}
		
		// Si el marker no existe, lo creo y si existe lo muevo.
		if(typeof(window.new_user_marker)==='undefined') {
			
			L.Icon.Default.imagePath = 'images/';
			
			
			window.new_user_marker = new L.marker([nuevoLat,nuevoLng],{
				draggable: true,
				title: "",
				alt: "",
				riseOnHover: true
			});
			
			window.new_user_circle = new L.circle([nuevoLat,nuevoLng],<?=$radius?>,{
				color: '#000',
				fillColor: '#ddd',
				fillOpacity: 0.3
			});
			
			// Creo un elemento para el contenido
			var container = $('<div />');

			// Acción asociada al link dentro del popup
			container.on('click', '#buscar_en_toda_la_ciudad', function(e) {
				e.preventDefault();
				muestraTodaLaCiudad();
			});

			// Contenido html del Popup
			container.html('<a href="#buscar_en_toda_la_ciudad" id="buscar_en_toda_la_ciudad" class="btn btn-default btn-block"><i class="fa fa-trash-o fa-lg"></i> Borrar marcador de posición</a>');
			
			window.new_user_marker.bindPopup(container[0]);
			 
			// Agrego el círculo, el marker
			window.new_user_circle.addTo(map);
			window.new_user_marker.addTo(map);

			
		} else {
			window.new_user_marker.setLatLng([nuevoLat,nuevoLng]);
			window.new_user_circle.setLatLng([nuevoLat,nuevoLng]);
		}
		
		// Disparo función que modifica el form y centra el mapa (entro otras cosas)
		muestraPorAca(nuevoLat, nuevoLng, map);
		
		// Al arrastrar el marker
		window.new_user_marker.on("dragend", function (e) {
			var chagedPos = e.target.getLatLng();
			window.new_user_circle.setLatLng(chagedPos);
			muestraPorAca(chagedPos.lat, chagedPos.lng, map);
		});
		
	}
	
	<?php if (($busqueda !== 'vacia') && ($total_registros_censo >= 1) ) { ?>
	
	var progress = $('.progress');
	var progressBar = $('.progress-bar');

	function updateProgressBar(processed, total, elapsed, layersArray) {
		if (elapsed > 1000) {
			// Si toma más de un segundo en cargar, se muestra la barra de progreso.
			$('.progress').slideDown('slow');
			porcentaje = Math.round(processed/total*100) + '%';
			$('.progress-bar').css({'width':porcentaje})
		}

		if (processed === total) {
			// Todos los markers cargados, oculto la barra.
			$('.progress').slideUp('slow');
		}
	}
	
	// Propiedades de clustering
	
	var markers = L.markerClusterGroup({
		chunkedLoading: true,
		chunkProgress: updateProgressBar,
		showCoverageOnHover: false,
		zoomToBoundsOnClick: true,
		spiderfyDistanceMultiplier: 2,
		maxClusterRadius: 50
	});
	
	var markerList = [];
	
	// Propiedades de los markers
	
	var LeafIcon  = L.Icon.extend({
		options: {
			iconSize:     [30, 34],
			iconAnchor:   [15, 31],
			popupAnchor:  [1, -20]
		}
	});
	
	// Iconos customizados por especie (algunas tienen)
	
	for (var i = 0; i < individuos.length; i++) {
		var a = individuos[i];
		var content = 'cargando...';
		var especie	= a[3];
		
		switch (especie) {
<?php
	$iconos_query	= "SELECT DISTINCT id_especie, ICONO FROM especies WHERE ICONO != ''";
	$iconos_results	= GetRS($iconos_query);
	
	while ($iconos_row = mysql_fetch_array($iconos_results)) {
		$icono_id_especie	= $iconos_row['id_especie'];
		$icono_icono		= $iconos_row['ICONO'];
		echo "case ". $icono_id_especie . ":
			marker_icon = new LeafIcon({iconUrl: 'uploads/". $icono_icono ."'});
			break;";
	}
?>	
			default:
				marker_icon = new LeafIcon({iconUrl: 'uploads/marker.png'});
		}
				
		var individuo = L.marker([a[0], a[1]], {icon: marker_icon});
		// Paso al individuo la propiedad de ID para hacer búsquedas dentro del popup.
		individuo.individuoId = a[2];
		
		var popupOptions =
		{
			'maxWidth': '400'
		}
		
		individuo.bindPopup(content, popupOptions);
		individuo.on('popupopen', onMarkerClick);
		markerList.push(individuo);
	}
	
	// Todos los markers a un layer
	markers.addLayers(markerList);
	// Agrego el layer al mapa
	map.addLayer(markers);
	// Centro el mapa.
	map.fitBounds(markers.getBounds());
	
	function onMarkerClick(e) {
		var oPop		= e.popup;
		var oMarkerId	= e.popup._source.individuoId
		
		$.ajax({
			url: "tirameladata/individuo.php?id="+oMarkerId,
			success: function(datos){
				oPop.setContent(datos);
				oPop.update();
			}
		});
	}
	
	<?php
	/*
	Cambiar el tamaño del ícono dependiendo del zoom
	map.on('zoomend', function() {
		if(map.getZoom() < 19){
			$('.leaflet-marker-icon').css({ "width": "15px", "height": "17px", "marginLeft": "-8px", "marginTop": "-18px" });
		}	 else {
			$('.leaflet-marker-icon').css({ "width": "30px", "height": "34px","marginLeft": "-15px", "marginTop": "-31px" });
		}
	});
	*/
	?>
	
	<?php }
	
	// Si la búsqueda incluye posición, marco el círculo con el marker de esa posición.
	
	if(  stripos($busqueda,'donde marker') > 0  ) {	
	?>
	onLocationFound(this.event,'<?= $user_lat ?>','<?= $user_lng ?>');	
	<?php } ?>

	// Creo una variable dentro de window para poder llamar a map desde funciones externas. Por ejemplo las que tengo definidas en buscar.js.
	window.map = map;
	
});


// Include the UserVoice JavaScript SDK (only needed once on a page)
UserVoice=window.UserVoice||[];
(function(){
  var uv=document.createElement('script');
  uv.type='text/javascript';
  uv.async=true;
  uv.src='//widget.uservoice.com/Eq8cib0TB3FmGnxB0NRmw.js';
  var s=document.getElementsByTagName('script')[0];
  s.parentNode.insertBefore(uv,s)
})();

UserVoice.push(['addTrigger', {
  mode: 'smartvote', // Modes: contact (default), smartvote, satisfaction
  trigger_position: 'top-right',
  trigger_color: 'white',
  trigger_background_color: '#5cba9d',
  accent_color: '#5cba9d',
  contact_enabled: false,
  trigger_style: 'icon',
  smartvote_title: '¿Con qué seguir?',
  menu_enabled : true
}]);




// GOOGLE ANALYTICS

  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
  ga('create', 'UA-678413-17', 'auto');
  ga('require', 'displayfeatures');
  ga('require', 'linkid', 'linkid.js');
  ga('send', 'pageview');
  <?php if ($busqueda !== 'vacia') {
  // Seguimiento Conversiones (búsquedas)
  ?>
  ga("send", "formulario posteado", "buscar", "arboles", <?php echo $total_registros_censo?> );
  <?php } ?>



</script>


<?
//echo('MARKER LATLNG:' . stripos($busqueda,'donde marker') );
?>
</body>
</html>