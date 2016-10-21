<script type="text/javascript">

$(document).ready(function(){
	
	<?php if ($total_registros_censo === 0) { ?>
		$('#sin-resultados').modal('show');
	<? } ?>
	
	// Límite del mapa puesto a Ciudad de Buenos Aires
	//var southWest	= new L.LatLng(-35.052109 , -58.72673),
	//northEast	= new L.LatLng(-34.192115 , -58.064804),
	//bounds = new L.LatLngBounds(southWest, northEast);
	
	bodyHeight = $("section[data-role='main']").height();
	$("#mapa").css("height", bodyHeight); //set with CSS also...
	
	bodyWidth = $("section[data-role='main']").width();
	$("#mapa").css("width", bodyWidth); //set with CSS also...
	
	var map = L.map('mapa',
	{
		maxZoom: 21,
		minZoom: 5
		//maxBounds: bounds
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


	// Buscador Geocoder
	var geocoder = L.Control.geocoder({
		collapsed: false,
		placeholder: 'Buscá un lugar',
		errorMessage: 'Nada, che. Buscá otra vez.',
		geocoder: new L.Control.Geocoder.Nominatim(
			{
				geocodingQueryParams: {
					countrycodes: 'ar' // limito a Argentina

				}
    		}
    	)
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
			
			L.Icon.Default.imagePath = '<?php echo $APP_URL; ?>/images/';
			
			
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
			container.on('click', '#buscar_aca', function(e) {
				e.preventDefault();
				muestraPorAca(false,false,false,true);
				//buscar();
			});

			container.on('click', '#buscar_en_toda_la_ciudad', function(e) {
				e.preventDefault();
				muestraTodaLaCiudad();
			});

			// Contenido html del Popup
			container.html('<a href="#buscar_aca" id="buscar_aca" class="btn btn-primary btn-block"><i class="fa fa-search fa-lg fa-fw"></i> Buscar acá</a><a href="#buscar_en_toda_la_ciudad" id="buscar_en_toda_la_ciudad" class="btn btn-default btn-block"><i class="fa fa-trash-o fa-lg fa-fw"></i> Borrar marcador de posición <br><small>para buscar en todo el mapa</small></a>');
			
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
	
	<?php

	if (($busqueda !== 'vacia') && ($total_registros_censo >= 1) ) {

	?>
	
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
			showCoverageOnHover: true,
			zoomToBoundsOnClick: true,
			spiderfyDistanceMultiplier: 2,
			maxClusterRadius: <?php echo $radius; ?>,
			disableClusteringAtZoom: 19,
			polygonOptions: {
		        fillColor: '#5cba9d',
		        color: '#5cba9d',
		        weight: 1,
		        opacity: 1,
		        fillOpacity: 0.1
		     }
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
						marker_icon = new LeafIcon({iconUrl: '". $APP_URL . "/uploads/". $icono_icono ."'});
						break;";
				}
				?>	
				default:
					marker_icon = new LeafIcon({iconUrl: '<?php echo $APP_URL; ?>/uploads/marker.png'});
			}
			
			//var individuo = L.marker([a[0], a[1]], {icon: marker_icon})
			var individuo = L.marker([a[0], a[1]], {icon: marker_icon}).on('click', onMarkerClick);
			
			// Paso al individuo la propiedad de ID para hacer búsquedas dentro del popup.
			individuo.individuoId = a[2];
			
			//var popupOptions = { 'maxWidth': '400' }
			//individuo.bindPopup(content, popupOptions);
			//individuo.on('popupopen', onMarkerClick);

			markerList.push(individuo);
		}
	
		// Todos los markers a un layer
		markers.addLayers(markerList);
		// Agrego el layer al mapa
		map.addLayer(markers);
		// Centro el mapa.
		map.fitBounds(markers.getBounds());
		
		function onMarkerClick(e) {
			//var oPop		= e.popup;
			//var oMarkerId	= e.popup._source.individuoId
			var oMarkerId = this.individuoId;

			//alert(oMarkerId);
			
			$.ajax({
				url: "<?php echo $APP_URL; ?>/custom/scripts/individuo.php?id="+oMarkerId,
				success: function(datos){
					$('#info-individuo').html(datos);
					$('#info-individuo').slideDown();
					//window.location.href = '#info-individuo';
					//oPop.setContent(datos);
					//oPop.update();
					$('.cerrar').click(function (e) {
						e.preventDefault();
						$('#info-individuo').slideUp();
					})

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
	} // FIN búsqueda <> VACIA
	
	// Si la búsqueda incluye posición, marco el círculo con el marker de esa posición.
	
	if(  stripos($busqueda,'donde marker') > 0  ) {	
		echo "onLocationFound(this.event,'". $user_lat ."','". $user_lng ."');";
	}

	?>

	// Creo una variable dentro de window para poder llamar a map desde funciones externas.
	// Por ejemplo las que tengo definidas en buscar.js.
	window.map = map;
	

	//************ PUNTOS

	if (typeof individuos != 'undefined') {

		function drawingOnCanvas(canvasOverlay, params) {
			var ctx = params.canvas.getContext('2d');
			ctx.clearRect(0, 0, params.canvas.width, params.canvas.height);
			ctx.fillStyle   = "#4f7663";
			// ctx.strokeStyle = "#ffffff";
			for (var i = 0; i < data.length; i++) {
				var d = data[i];
				if (params.bounds.contains([d[0], d[1]])) {
					dot = canvasOverlay._map.latLngToContainerPoint([d[0], d[1]]);
					ctx.beginPath();
					ctx.arc(dot.x, dot.y, 2.5, 0, Math.PI * 2);
					ctx.fill();
					//ctx.stroke();
					ctx.closePath();
				}
			}
		}

		var data = individuos; // data loaded from data.js
		L.canvasOverlay()
			.drawing(drawingOnCanvas)
			.addTo(map);
		
	} // end if
	

	// *********** FIN PUNTOS

});

</script>