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

$id_barrio	= $_REQUEST['id_barrio'];
$id_especie	= $_REQUEST['id_especie'];

$parametro	= "WHERE 1";
$busqueda	= "vacia";

if ((is_numeric($id_barrio)) && ($id_barrio > 0)) {
	$parametro .= " AND id_barrio=$id_barrio";
	$busqueda	= "barrio";
}

if ((is_numeric($id_especie)) && ($id_especie > 0)) {
	$parametro .= " AND id_especie=$id_especie";
	
	if ($busqueda == "barrio") {
		$busqueda	= "barrio y especie";
	}else{
		$busqueda	= "especie";
	}
	
	$especie_query	= "
	SELECT nombre_cientifico
	FROM x_especies_provisorio
	WHERE id_especie = $id_especie
	LIMIT 1;
	";
	$especie_results			= GetRS($especie_query);
	$especie_row				= mysql_fetch_array($especie_results);
	
	$nombre_cientifico	= $especie_row['nombre_cientifico'];
	
} else {
	$id_especie = 0;
}

?>
<!DOCTYPE html>
<!-- /ht Paul Irish - http://front.ie/j5OMXi -->
<!--[if lt IE 7 ]> <html class="no-js ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]>    <html class="no-js ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]>    <html class="no-js ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html class="no-js" lang="es">
<!--<![endif]-->
<head>
<meta charset="utf-8">
<title>Árboles de la Ciudad Autónoma de Buenos Aires</title>
<meta name="description" content="">
<meta name="author" content="">

<!-- /ht Andy Clarke - http://front.ie/lkCwyf -->
<meta http-equiv="cleartype" content="on">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<link rel="shortcut icon" href="icons/favicon.ico" type="image/x-icon" />
<link rel="apple-touch-icon" href="icons/apple-touch-icon.png" />
<link rel="apple-touch-icon" sizes="57x57" href="icons/apple-touch-icon-57x57.png" />
<link rel="apple-touch-icon" sizes="72x72" href="icons/apple-touch-icon-72x72.png" />
<link rel="apple-touch-icon" sizes="76x76" href="icons/apple-touch-icon-76x76.png" />
<link rel="apple-touch-icon" sizes="114x114" href="icons/apple-touch-icon-114x114.png" />
<link rel="apple-touch-icon" sizes="120x120" href="icons/apple-touch-icon-120x120.png" />
<link rel="apple-touch-icon" sizes="144x144" href="icons/apple-touch-icon-144x144.png" />
<link rel="apple-touch-icon" sizes="152x152" href="icons/apple-touch-icon-152x152.png" />

<!-- /ht Jeremy Keith - http://front.ie/mLXiaS -->
<link rel="stylesheet" href="css/global.css" media="all">
<link rel="stylesheet" href="css/layout.css" media="all and (min-width: 33.236em)">
<!-- 30em + (1.618em * 2) = 33.236em / Eliminates potential of horizontal scrolling in most cases -->

<!--[if (lt IE 9) & (!IEMobile)]>
<link rel="stylesheet" href="css/layout.css" media="all">
<![endif]-->

<script src="js/modernizr-1.7.min.js"></script>

<!-- Leaflet 0.7.2: https://github.com/CloudMade/Leaflet-->
<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7.2/leaflet.css" />
<script src="http://cdn.leafletjs.com/leaflet-0.7.2/leaflet.js"></script>

<!-- Google Maps -->
<script src="http://maps.google.com/maps/api/js?v=3.2&sensor=false"></script>
<script src="js/leaflet.google.js"></script>

<link rel="stylesheet" href="js/MarkerCluster.css" />
<script src="js/leaflet.markercluster-src.js"></script>

<script src='//api.tiles.mapbox.com/mapbox.js/plugins/leaflet-locatecontrol/v0.24.0/L.Control.Locate.js'></script>
<link href='//api.tiles.mapbox.com/mapbox.js/plugins/leaflet-locatecontrol/v0.24.0/L.Control.Locate.css' rel='stylesheet' />

<link rel="stylesheet" href="leaflet.fullscreen-master/Control.FullScreen.css" />
<script src="leaflet.fullscreen-master/Control.FullScreen.js"></script>


<!--[if lt IE 9]>
  <link href='//api.tiles.mapbox.com/mapbox.js/plugins/leaflet-locatecontrol/v0.21.0/L.Control.Locate.ie.css' rel='stylesheet' />
<![endif]-->

<!--
GeoCoder... próximamente...
<script src="esri-leaflet-geocoder-master/dist/esri-leaflet-geocoder.js"></script>
<link rel="stylesheet" href="esri-leaflet-geocoder-master/dist/esri-leaflet-geocoder.css" />
-->

<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">

<script src="js/jquery.min.js"></script>

<script type="text/javascript" src="js/buscar.js"></script>

<?php if ($busqueda !== 'vacia') {

	// Armo el array con los individuos

	$censo_query = "
		SELECT id_individuo, lat, lng, id_especie
		FROM 1_individuos
		$parametro";
	
	$count = true;
	$censo_results	= GetRS($censo_query);
	$total_registros_censo = $total_registros;
	$count = false;
	
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
	
} ?>

</head>

<body <?php if ($busqueda == 'vacia') echo 'class="active-nav"' ?>>

<div class="contenedor-que-oculta">

<nav id="menu" role="navigation">
	<div id="progress"><div id="progress-bar"></div></div>
	
	<div id="main">
		
		<a class="title" href="./"><h1>Árboles de<br> Buenos Aires</h1></a>
		
		<form action="index.php" method="post">
		
			<div>
				<label for="search"><h2 class="title">especie</h2></label>
				<input type="text" id="search" autocomplete="off" class="search-box" value="<?php echo $nombre_cientifico; ?>"  onClick="this.setSelectionRange(0, this.value.length)">
			</div>
			<h4 id="results-text" class="results-text">Mostrando resultados para: <b id="search-string">Array</b></h4>
			<ul id="results" class="results"></ul>
			
			<input type="hidden" value="<?php echo $id_especie ?>" name="id_especie" id="id_especie">
			
			<div>
				<label for="id_barrio"><h2 class="title">barrio</h2></label>
				<select name="id_barrio" id="id_barrio">
					<option>Todos los barrios</option>
					<?php
					// Barrios
					$barrios_query = 'SELECT barrio_nombre, id_barrio FROM a_barrios ORDER BY barrio_nombre';
					$barrios_results	= GetRS($barrios_query);
					while ($barrios_row = mysql_fetch_array($barrios_results)) {
						$barrio_id		=  $barrios_row['id_barrio'];
						$barrio_nombre	=  $barrios_row['barrio_nombre'];
						if ($id_barrio == $barrio_id) { $selected = 'selected="selected"'; }
						echo '<option value="'.$barrio_id.'" '.$selected.'>'.$barrio_nombre.'</option>';
						$selected = "";
					}
					?>
				</select>	
			</div>
			
			<input name="Buscar" type="submit" value="Buscar" class="btn">
		
		</form>
		
	</div>										
</nav>

<section role="main">	
	<div id="map"></div>
</section>

</div>


<script type="text/javascript">

var showMenu = function() {
	$('body').removeClass("active-sidebar").toggleClass("active-nav");
	$('.menu-button').toggleClass("active-button");	
}

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
		maxBounds: bounds,
		fullscreenControl: true,
		fullscreenControlOptions: { // optional
			title:"Show me the fullscreen !"
		}
	})<?php  if ( ($busqueda == 'vacia') || ($total_registros_censo == 0) ) echo '.setView([-34.618, -58.44], 12)' ?>;
	
	// detect fullscreen toggling
	map.on('enterFullscreen', function(){
		if(window.console) window.console.log('enterFullscreen');
	});
	map.on('exitFullscreen', function(){
		if(window.console) window.console.log('exitFullscreen');
	});
		
		
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
			title: "¿Dónde estoy?",  // title of the locate control
			popup: "Estás a unos {distance} {unit} de este punto",  // text to appear if user clicks on circle
			outsideMapBoundsMsg: "You seem located outside the boundaries of the map" // default message for onLocationOutsideMapBounds
		}
	}).addTo(map);
	
	// buscar
	myButton.onAdd = function (map) {
		this._div = L.DomUtil.create('div', 'leaflet-bar');
		this._div.innerHTML = '<a href="#menu" title="Buscar" class="menu-button"><i class="fa fa-search"></i></a>';
		$(this._div).click(function(e) {
			setInterval(function(){
				map.invalidateSize() // GRACIAS!
			}, 5);
			
			e.preventDefault();
			showMenu();
		});	
		return this._div;
	};
	
	// Agrega botones
	myButton.addTo(map);	
	
	// COPY
	map.attributionControl.addAttribution('Proyecto: <a href="http://martinsimonyan.com.ar/arboles-de-buenos-aires/">Martín Simonyan</a> | Árboles gracias a <a href="http://data.buenosaires.gob.ar/dataset/censo-arbolado/" target="_blank">data.buenosaires.gob.ar</a>');
	
	
	
	
	<?php if (($busqueda !== 'vacia') || ($total_registros_censo >= 1) ) {  ?>
	
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
	
	
	
	/*
	Colores para markers circle
	var marker_color;
	var marker_color_default = '#5cba9d';
	var marker_color_violeta = '#8779d0';
	var marker_color_amarillo = '#f3f153';
	var marker_color_rosa = '#ffa4a4';
	var marker_color_naranja = '#fe861c';
	*/
	
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
		
		// Markers default
		//var individuo = L.marker(L.latLng(a[0], a[1]));
		
		//Markers colores
		/*var individuo = L.circleMarker(L.latLng(a[0], a[1]), 
			circleOptions = {
				 color: '#fff',
				 fillColor: marker_color, 
				 fillOpacity: 0.7,
				 radius: 12
			 }
		);*/
		
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