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

require_once('includes/funciones.php');


// Parámetros de búsqueda
//// Defino el default
$parametro	= "WHERE 1";
$busqueda	= "vacia";
$radius		= "0.0019"; // Valor por default
$user_latlng_default = array("-34.60371794474704","-58.38157095015049"); // El Obelisco

//// Veo qué vino en el form
//$id_barrio			= $_REQUEST['id_barrio'];
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
		// Definir el centro
		$centerLat = $user_lat;
		$centerLng = $user_lng;
	
		// Definir límites de un cuadrado
		$centerLatMenos = $centerLat - $radius;
		$centerLatMas   = $centerLat + $radius;
		$centerLngMenos = $centerLng - $radius;
		$centerLngMas   = $centerLng + $radius;
	
		$bbox = "CONCAT('POLYGON((', 
		$centerLatMenos, ' ', $centerLngMenos, ',', 
		$centerLatMas, ' ', $centerLngMenos, ',', 
		$centerLatMas, ' ', $centerLngMas, ',', 
		$centerLatMenos, ' ', $centerLngMas, ',', 
		$centerLatMenos, ' ', $centerLngMenos, '))' 
		)";
	
		$parametro .= " AND Intersects( `coordenadas`, GeomFromText(".$bbox.") )
			AND SQRT(POW( ABS( X(`coordenadas`) - ". $centerLat ."), 2) + POW( ABS(Y(`coordenadas`) - ". $centerLng ."), 2 )) < ". $radius .""; // esta última línea es para hacer un círculo
		
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

//echo("<br>".$busqueda);


?>
<!DOCTYPE html>
<html lang="es">
<meta name="viewport" content="width=device-width, initial-scale=1">
<head>
<meta charset="utf-8">
<title>Árboles de la Ciudad Autónoma de Buenos Aires</title>
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
<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7.2/leaflet.css" />
<script src="http://cdn.leafletjs.com/leaflet-0.7.2/leaflet.js"></script>

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

<!-- Font Awesome -->
<link href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.css" rel="stylesheet">

<!-- Latest compiled and minified JavaScript -->
<script src="js/bootstrap.min.js"></script>
<link rel="stylesheet" href="css/la-magia.css" media="all">
<?php if ($busqueda !== 'vacia') {

	
	
	// Hago LA consulta
	$censo_query = "
		SELECT id_individuo, id_especie, X(`coordenadas`) as lat, Y(`coordenadas`) as lng
		FROM 1_individuos
		$parametro";
	
	$count = true;
	$censo_results	= GetRS($censo_query);
	$total_registros_censo = $total_registros;
	$count = false;
	
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
<nav class="navbar navbar-default navbar-fixed-top visible-xs" role="navigation">
	<div class="container-fluid">
		<a type="button" class="btn btn-default navbar-btn" href="#"><i class="fa fa-caret-up"></i> Buscar</a>
	</div>
</nav>
<div class="container-fluid full-height">
	<div class="row full-height">
		<div id="progress">
			<div id="progress-bar"></div>
		</div>
		<div class="col-sm-3">
			<nav id="menu" role="navigation">
				<div id="main"> <a class="title" href="./">
					<h1>Árboles de<br>
						Buenos Aires</h1>
					</a>
					<form action="index.php" method="post" id="busca_arboles" role="form">
						<div class="form-group">
							<h3>¿Qué especies?</h3>
							<div class="radio">
								<label for="respecies-todas">
									<input type="radio" id="respecies-todas" name="respecie" value="0" <?php if (stripos($busqueda,'una') == 0) echo 'checked' ?> />
									todas </label>
								<label for="respecies-una">
									<input type="radio" id="respecies-una" name="respecie" value="1" <?php if (stripos($busqueda,'una') > 0) echo 'checked' ?> />
									una </label>
							</div>
							<div id="especies-lista" class="hide">
								<input type="text" id="muestra-especie" autocomplete="off" class="form-control input-lg" value="<?php echo $nombre_cientifico; ?>"  onClick="this.setSelectionRange(0, this.value.length)">
								<h4 id="results-text" class="results-text">Mostrando resultados para: <b id="muestra-especie-string">Array</b></h4>
								<ul id="results" class="results list-unstyled">
								</ul>
							</div>
							<input type="hidden" value="<?php echo $id_especie_busqueda ?>" name="id_especie" id="id_especie">
						</div>
						
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
						
						<input name="Buscar" type="submit" value="Buscar" class="btn btn-primary btn-lg btn-block">
						
						<!-- Modal: seleccionar una especie -->
						<div class="modal fade" id="respecies-una-modal" tabindex="-1" role="dialog" aria-hidden="true">
						  <div class="modal-dialog modal-sm">
							<div class="modal-content">
							  <div class="modal-body">
								<div class="row">
									<div class="col-sm-2"><i class="fa fa-exclamation-triangle fa-3x"></i></div>
									<div class="col-sm-10"><p>Escribí algunas letras en el cuadro de búsqueda y aparecerá un listado con las posibles especies.</p>
								<p><small>Podés buscar todas las especies mezcladas, pero antes debes limitar la zona marcando un punto en el mapa.</small></p></div>
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
									<div class="col-sm-10"><p>Para buscar todas las especies tenés que seleccionar alguna zona marcando un punto en el mapa.</p>
								<p><small>O podés buscar en toda la ciudad seleccionado una especie.</small></p></div>
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
						
					</form>
				</div>
			</nav>
		</div>
		<div class="col-sm-9 full-height" id="map"> </div>
	</div>
</div>
<script type="text/javascript" src="js/buscar.js"></script> 
<script type="text/javascript">

$(document).ready(function(){
	
	// Límite del mapa puesto a Ciudad de Buenos Aires
	var southWest	= new L.LatLng(-34.7260, -58.5605),
    northEast		= new L.LatLng(-34.5096, -58.3192),
    bounds = new L.LatLngBounds(southWest, northEast);
	
	bodyHeight = $("section[data-role='main']").height();
	$("#map").css("height", bodyHeight); //set with CSS also...
	
	bodyWidth = $("section[data-role='main']").width();
	$("#map").css("width", bodyWidth); //set with CSS also...
	
	var map = L.map('map',
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
	

	// buscar
	/*myButton.onAdd = function (map) {
		this._div = L.DomUtil.create('div', 'leaflet-bar');
		this._div.innerHTML = '<a href="#menu" title="Buscar" class="menu-button"><i class="fa fa-search"></i></a>';
		$(this._div).click(function(e) {
			setInterval(function(){
				map.invalidateSize() // GRACIAS!
			}, 5);
			
			e.preventDefault();
			showHideMenu();
		});	
		return this._div;
	};
	
	// Agrega botones
	myButton.addTo(map);
	*/
	
	// COPY
	map.attributionControl.addAttribution('Proyecto: <a href="http://martinsimonyan.com.ar/arboles-de-buenos-aires/">Martín Simonyan</a> | Árboles gracias a <a href="http://data.buenosaires.gob.ar/dataset/censo-arbolado/" target="_blank">data.buenosaires.gob.ar</a>');
	

	
	map.on('click', function(e) {
		onLocationFound(e);
	})
	
	
	
	// Variable que contendrá al Marker con ubicación del usuario.
	var new_user_marker;
	var new_user_circle;
	
	function onLocationFound(e) {
		// Si el marker no existe, lo creo y si existe lo muevo.
		if(typeof(new_user_marker)==='undefined') {
			new_user_marker = new L.marker(e.latlng,{
				draggable: true,
				title: "",
	 	        alt: "",
				riseOnHover: true
			});
			
			new_user_circle = new L.circle(e.latlng,200,{
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
				//showMenu();
				map.removeLayer(new_user_marker);
				map.removeLayer(new_user_circle);
				new_user_marker = undefined;
				new_user_circle = undefined;
			});

			// Contenido html del Popup
			container.html('<a href="#buscar_en_toda_la_ciudad" id="buscar_en_toda_la_ciudad" class="btn btn-default btn-block"><i class="fa fa-trash-o fa-lg"></i> Borrar marcador de posición</a>');
			
			new_user_marker.bindPopup(container[0]);
			 
			// Agrego el círculo, el marker y abro el popup
			new_user_circle.addTo(map);
			new_user_marker.addTo(map);
			
			// disparo función que modifica el form
			muestraPorAca(e.latlng.lat, e.latlng.lng);
			
		 } else {
			new_user_marker.setLatLng(e.latlng);
			new_user_circle.setLatLng(e.latlng);
		}
		
		// Update marker on changing it's position
		new_user_marker.on("dragend", function (e) {
			var chagedPos = e.target.getLatLng();
				
			// Cambiar la posición del círculo (radio de búsqueda)
			new_user_circle.setLatLng(chagedPos);
			//this.openPopup();
			
			// disparo función que modifica el form
			muestraPorAca(chagedPos.lat, chagedPos.lng);
			
		});
		
		// centrar el mapa al click
		map.panTo(new L.LatLng(e.latlng.lat, e.latlng.lng));
		// cambiar el valor del lat y lng en el form al hacer click en un punto
		$('input#user_latlng').val(e.latlng.lat + ' ' +e.latlng.lng);
		
	}
	
	
	<?php if (($busqueda !== 'vacia') || ($total_registros_censo >= 1) ) { ?>
	
	var progress = document.getElementById('progress');
	var progressBar = document.getElementById('progress-bar');

	function updateProgressBar(processed, total, elapsed, layersArray) {
		if (elapsed > 1000) {
			// if it takes more than a second to load, display the progress bar:
			progress.style.display = 'block';
			progressBar.style.width = Math.round(processed/total*100) + '%';
		}

		if (processed === total) {
			// all markers processed - hide the progress bar:
			progress.style.display = 'none';
		}
	}
	
	var markers = L.markerClusterGroup({
		chunkedLoading: true,
		chunkProgress: updateProgressBar,
		showCoverageOnHover: false,
		disableClusteringAtZoom: 18,
		maxClusterRadius: 28
	});
	
	var markerList = [];
	

	var LeafIcon  = L.Icon.extend({
		options: {
			iconSize:     [29, 31],
			iconAnchor:   [15, 25],
			popupAnchor:  [0, -15]
		}
	});
	var arbolIcon			= new LeafIcon({iconUrl: 'images/marker.png'});
	var arbolIconVioleta	= new LeafIcon({iconUrl: 'images/marker-violeta.png'});
	var arbolIconAmarillo	= new LeafIcon({iconUrl: 'images/marker-amarillo.png'});
	var arbolIconRojo		= new LeafIcon({iconUrl: 'images/marker-rojo.png'});
	var arbolIconRosa		= new LeafIcon({iconUrl: 'images/marker-rosa.png'});
	var arbolIconNaranja	= new LeafIcon({iconUrl: 'images/marker-naranja.png'});
	
	for (var i = 0; i < individuos.length; i++) {
		var a = individuos[i];
		var content = 'cargando...';
		var especie	= a[3];
		if (especie === 11) { // jacarandá
			marker_color = arbolIconVioleta;
		} else if ((especie === 249) || (especie === 145) || (especie === 41) ) { //tecoma, limonero y ginkgo
			marker_color = arbolIconAmarillo;
		} else if ((especie === 25) || (especie === 340)) { // palo borracho
			marker_color = arbolIconRosa;
		} else if ((especie === 148) || (especie === 144)) { // naranjo amargo y dule
			marker_color = arbolIconNaranja;
		} else {
			marker_color = arbolIcon;
		}
				
		var individuo = L.marker([a[0], a[1]], {icon: marker_color});

		individuo.individuoId = a[2];
		
		individuo.bindPopup(content);
		individuo.on('popupopen', onMarkerClick);
		markerList.push(individuo);
	}
	
	markers.addLayers(markerList);
	map.addLayer(markers);
	map.fitBounds(markers.getBounds());
	
	function onMarkerClick(e) {
		var oPop		= e.popup;
		var oMarkerId	= e.popup._source.individuoId
		
		$.ajax({
			url: "getdata/individuo.php?id="+oMarkerId,
			success: function(datos){
				oPop.setContent(datos);
				oPop.update();
			}
		});
	}
	
	<?php } ?>
	
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
</body>
</html>