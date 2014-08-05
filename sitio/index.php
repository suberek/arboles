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
$radius		= "150"; // Radui de búsqueda en Metros
$user_latlng_default = array("-34.60371794474704","-58.38157095015049"); // El Obelisco

//// Veo qué vino en el form
$id_especie_busqueda	= $_REQUEST['id_especie'];
$user_latlng			= $_REQUEST['user_latlng'];

/**************************************************************** PARÁMETRO ESPECIE */
if ((is_numeric($id_especie_busqueda)) && ($id_especie_busqueda > 0)) {
	$parametro .= " AND id_especie=$id_especie_busqueda";
	
	$especie_query	= "
	SELECT NOMBRE_CIE
	FROM 2_especies
	WHERE id_especie = $id_especie_busqueda
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
<link rel="shortcut icon" href="icons/favicon.ico" type="image/x-icon" />
<link rel="apple-touch-icon" href="icons/logo.png" />
<link rel="apple-touch-icon" sizes="57x57" href="icons/logo-57x57.png" />
<link rel="apple-touch-icon" sizes="72x72" href="icons/logo-72x72.png" />
<link rel="apple-touch-icon" sizes="76x76" href="icons/logo-76x76.png" />
<link rel="apple-touch-icon" sizes="114x114" href="icons/logo-114x114.png" />
<link rel="apple-touch-icon" sizes="120x120" href="icons/logo-120x120.png" />
<link rel="apple-touch-icon" sizes="144x144" href="icons/logo-144x144.png" />
<link rel="apple-touch-icon" sizes="152x152" href="icons/logo-152x152.png" />

<!-- Leaflet 0.7.2: https://github.com/CloudMade/Leaflet-->
<link rel="stylesheet" href="css/leaflet.css" />
<script src="js/leaflet.js"></script>

<!-- Google Maps -->
<script src="http://maps.google.com/maps/api/js?v=3.2&amp;sensor=false"></script>
<script src="js/leaflet.google.js"></script>
<link rel="stylesheet" href="css/leaflet.markercluster.css" />
<script src="js/leaflet.markercluster-src.js"></script>
<script src='//api.tiles.mapbox.com/mapbox.js/plugins/leaflet-locatecontrol/v0.24.0/L.Control.Locate.js'></script>
<link href='//api.tiles.mapbox.com/mapbox.js/plugins/leaflet-locatecontrol/v0.24.0/L.Control.Locate.css' rel='stylesheet' />

<!--[if lt IE 9]>
  <link href='//api.tiles.mapbox.com/mapbox.js/plugins/leaflet-locatecontrol/v0.21.0/L.Control.Locate.ie.css' rel='stylesheet' />
<![endif]-->

<!--
GeoCoder... próximamente...
<script src="esri-leaflet-geocoder-master/dist/esri-leaflet-geocoder.js"></script>
<link rel="stylesheet" href="esri-leaflet-geocoder-master/dist/esri-leaflet-geocoder.css" />
-->

<script src="js/jquery-2.1.1.min.js"></script>
<script src="js/jquery-migrate-1.2.1.min.js"></script>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="css/bootstrap.min.css">

<!-- Font Awesome 4.1.0 -->
<link rel="stylesheet" href="css/font-awesome.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="js/bootstrap.min.js"></script>
<link rel="stylesheet" href="css/la-magia.css" media="all">

<!-- Bootstrap Select -->
<script src="js/bootstrap-select.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/bootstrap-select.min.css">
<?php

if ($busqueda !== 'vacia') {
	
	// Hago LA consulta
	$censo_query = "
		SELECT id_individuo, id_especie, X(`coordenadas`) as lat, Y(`coordenadas`) as lng
		FROM 1_individuos
		$parametro";
		
	if (stripos($busqueda,'marker') > 0) {

		// Definir el centro y buscar en el radio.
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
		FROM 1_individuos
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
	
}else{	
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


		<div class="col-sm-3" id="menu">
			<nav role="navigation">
				<div id="main"> <a class="title" href="./">
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
														FROM 1_individuos AS i, 2_especies AS e
														WHERE i.id_especie = e.id_especie
														GROUP BY e.id_especie
														ORDER BY e.NOMBRE_CIE";*/
											
											// Consulto especies sin cantidad
											$especies_query = "SELECT e.id_especie, e.NOMBRE_CIE, e.NOMBRE_COM
														FROM 2_especies AS e
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
							
							
						</div>
						
						<input name="Buscar" type="submit" value="Buscar" class="btn btn-primary btn-lg btn-block">
						
					</form>
				</div>
			</nav>
		</div>
		<div class="col-sm-9 full-height" id="mapa"> </div>
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
						
<script type="text/javascript" src="js/buscar.js"></script> 
<script type="text/javascript">

$(document).ready(function(){
	
	<?php if ($total_registros_censo === 0) { ?>
		$('#sin-resultados').modal('show');
	<? } ?>
	
	// Límite del mapa puesto a Ciudad de Buenos Aires
	var southWest	= new L.LatLng(-34.7260, -58.5605),
    northEast		= new L.LatLng(-34.5096, -58.3192),
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
	
	var stm = new L.TileLayer("http://{s}.tile.stamen.com/toner-lite/{z}/{x}/{y}.png", {
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
		});
		
	
	var ggr = new L.Google('ROADMAP');
	var ggs = new L.Google('SATELLITE');
	var ggh = new L.Google('HYBRID');
	map.addControl(new L.Control.Layers({'Google Roadmap':ggr, 'Google Satélite':ggs, 'Google Hybrid':ggh, 'Stamen':stm, 'Nokia':Nokia_normalDay}));
	map.addLayer(ggr);
	
	
	/*
	GeoCoder... próximamente...
	
	var GeoSearchOptions = {
	  'useMapBounds': true,
	  'maxResults': 3
	}

	var searchControl = new L.esri.Controls.Geosearch(GeoSearchOptions).addTo(map);
	
	searchControl.on("error", function(e){
        console.log(e);
	});
	*/ 
	 
	
	// Barra de botones
	var myButton = L.control({ position: 'topleft' });
	
	// localizame
	L.control.locate({
		locateOptions: {
			maxZoom: 20
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
	

	// COPY
	map.attributionControl.addAttribution('Proyecto: <a href="http://martinsimonyan.com.ar/arboles-de-buenos-aires/">Martín Simonyan</a> | Árboles gracias a <a href="http://data.buenosaires.gob.ar/dataset/censo-arbolado/" target="_blank">data.buenosaires.gob.ar</a>');
	
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
			 
			// Agrego el círculo, el marker y abro el popup
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
	$iconos_query	= "SELECT DISTINCT id_especie, ICONO FROM 2_especies WHERE ICONO != ''";
	$iconos_results	= GetRS($iconos_query);
	
	while ($iconos_row = mysql_fetch_array($iconos_results)) {
		$icono_id_especie	= $iconos_row['id_especie'];
		$icono_icono		= $iconos_row['ICONO'];
		echo "case ". $icono_id_especie . ":
			marker_icon = new LeafIcon({iconUrl: 'images/". $icono_icono ."'});
			break;";
	}
?>	
			default:
				marker_icon = new LeafIcon({iconUrl: 'images/marker.png'});
		}
				
		var individuo = L.marker([a[0], a[1]], {icon: marker_icon});
		// Paso al individuo la propiedad de ID para hacer búsquedas dentro del popup.
		individuo.individuoId = a[2];
		
		var popupOptions =
		{
			'maxWidth': '250'
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

</script> 
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-678413-6', 'martinsimonyan.com.ar');
  ga('send', 'pageview');

</script>

<?
//echo('MARKER LATLNG:' . stripos($busqueda,'donde marker') );
?>
</body>
</html>