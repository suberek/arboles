<script type="text/javascript">

$(document).ready(function(){
    
    <?php if (  (isset($total_registros_censo))  &&  ($total_registros_censo === 0)  ) { ?>
        $('#sin-resultados').modal('show');
    <?php } ?>
    
    // Límite del mapa puesto a Ciudad de Buenos Aires
    //var southWest    = new L.LatLng(-35.052109 , -58.72673),
    //northEast    = new L.LatLng(-34.192115 , -58.064804),
    //bounds = new L.LatLngBounds(southWest, northEast);
    
    bodyHeight = $("section[data-role='main']").height();
    $("#mapa").css("height", bodyHeight); //set with CSS also...
    
    bodyWidth = $("section[data-role='main']").width();
    $("#mapa").css("width", bodyWidth); //set with CSS also...
    
    var map = L.map('mapa',
    {
        maxZoom: 21,
        minZoom: 5
    })<?php 
        if (
                ( empty($busqueda) ) ||  (
                    (  isset($total_registros_censo)  )  &&  (  $total_registros_censo == 0  ) 
                )
            )
        {
            echo '.setView([-34.618, -58.44], 12)';
        }
    ?>;
            
        
    // MAPA
    L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
        maxZoom: 21,
        attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' +
            '<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
            'Imagery © <a href="http://mapbox.com">Mapbox</a>',
        id: 'mapbox.streets'
    }).addTo(map);


    // Agregar un árbol
    var agregarArbol = L.easyButton({
        states: [{
            stateName: 'agregar-arbol',
            icon: '<i class="fa fa-plus-circle" aria-hidden="true"></i> Agregar árbol',
            title: 'Agregar un árbol',
            onClick: function(btn, map) {
                $('#agregar-arbol').find('.modal-content').css('min-width', '550px');
                $('#agregar-arbol').modal('show');
            }
        }]
    });
    agregarArbol.addTo(map);



    // Barra de botones
    var myButton = L.control({ position: 'topleft' });
    
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
    map.attributionControl.addAttribution('Proyecto: <a href="http://martinsimonyan.com/arboles-de-buenos-aires/">Martín Simonyan</a> & <a href="https://franciscoferiolimarco.wordpress.com">Francisco Ferioli Marco</a>');
    
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
            container.html('<a href="#buscar_aca" id="buscar_aca" class="btn btn-primary btn-block"><i class="fa fa-search fa-lg fa-fw"></i> Buscar en esta zona</a><a href="#buscar_en_toda_la_ciudad" id="buscar_en_toda_la_ciudad" class="btn btn-default btn-block"><i class="fa fa-trash-o fa-lg fa-fw"></i> Borrar marcador de posición <br><small>para buscar en todo el mapa</small></a>');
            
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

    if (
            ( !empty($busqueda) )  &&  ( (isset($total_registros_censo))  &&  ($total_registros_censo >= 1) ) 
        ) 
    {

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
            maxClusterRadius: 100, // en pixeles
            disableClusteringAtZoom: <?php echo $disableClusteringAtZoom ?>,
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

        var arboles = [<?php echo $arboles_para_mapa; ?>];
    
        for (var i = 0; i < arboles.length; i++) {
            var a = arboles[i];
            var content = 'cargando...';
            
            var marker_icon = new LeafIcon({iconUrl: '<?php echo $APP_URL; ?>/uploads/'+a[3]});
            
            //var arbol = L.marker([a[0], a[1]], {icon: marker_icon})
            var arbol = L.marker([a[0], a[1]], {icon: marker_icon}).on('click', onMarkerClick);
            
            // Paso al arbol la propiedad de ID para hacer búsquedas dentro del popup.
            arbol.arbolId = a[2];

            markerList.push(arbol);
        }
    
        // Todos los markers a un layer
        markers.addLayers(markerList);
        // Agrego el layer al mapa
        map.addLayer(markers);
        // Centro el mapa.
        map.fitBounds(markers.getBounds());
        
        function onMarkerClick(e) {
            var oMarkerId = this.arbolId;

            //alert(oMarkerId);
            
            $.ajax({
                url: "<?php echo $APP_URL; ?>/custom/scripts/arbol.php?id="+oMarkerId,
                success: function(datos){
                    $('#info-arbol').html(datos);
                    $('#info-arbol').slideDown();
                    //window.location.href = '#info-arbol';
                    //oPop.setContent(datos);
                    //oPop.update();
                    $('.cerrar').click(function (e) {
                        e.preventDefault();
                        $('#info-arbol').slideUp();
                    })

                }
            });
        }
    
        <?php
        
    } // FIN búsqueda <> VACIA
    
    // Si la búsqueda incluye posición, marco el círculo con el marker de esa posición.
    
    if(  stripos($busqueda,'donde marker') > 0  ) {    
        echo "onLocationFound(this.event,'". $user_lat ."','". $user_lng ."');";
    }

    ?>

    // Creo una variable dentro de window para poder llamar a map desde funciones externas.
    window.map = map;
    

    //************ PUNTOS


    if (typeof arboles != 'undefined') {

        function drawingOnCanvas(canvasOverlay, params) {
            var ctx = params.canvas.getContext('2d');
            ctx.clearRect(0, 0, params.canvas.width, params.canvas.height);
            ctx.fillStyle   = "#4f7663";
            
            var currentZoom = map.getZoom();
            //alert(currentZoom);

            // Los siguientes IF cambian el tamaño de los puntos verdes según el zoom

            if (currentZoom <= <?php echo $disableClusteringAtZoom - 6; ?>) var ctxradius = 0.5;
            if (currentZoom == <?php echo $disableClusteringAtZoom - 5; ?>) var ctxradius = 1;
            if (currentZoom == <?php echo $disableClusteringAtZoom - 4; ?>) var ctxradius = 1.5;
            if (currentZoom == <?php echo $disableClusteringAtZoom - 3; ?>) var ctxradius = 2;
            if (currentZoom == <?php echo $disableClusteringAtZoom - 2; ?>) var ctxradius = 3;
            if (currentZoom >= <?php echo $disableClusteringAtZoom - 1; ?>) var ctxradius = 4;

            if (currentZoom <= <?php echo $disableClusteringAtZoom - 4; ?>) {
                ctx.globalAlpha = 0.3;
            } else {
                ctx.globalAlpha = 0.5;
            }

            // El siguiente IF hace que no se muestren los puntos verdes al hacer zoom

            if (  currentZoom < <?php echo $disableClusteringAtZoom; ?>  ) {
            
                for (var i = 0; i < data.length; i++) {
                    var d = data[i];
                    if (params.bounds.contains([d[0], d[1]])) {
                        dot = canvasOverlay._map.latLngToContainerPoint([d[0], d[1]]);
                        ctx.beginPath();
                        //1.5 es el radio en píxeles del punto
                        ctx.arc(dot.x, dot.y, ctxradius, 0, Math.PI * 2);
                        ctx.fill();
                        ctx.closePath();
                    }
                }
            } // end if currentZoom
        }

        var data = arboles; // data loaded from data.js
        L.canvasOverlay()
            .drawing(drawingOnCanvas)
            .addTo(map);
        
    } // end if undefined
    

    // *********** FIN PUNTOS

});

</script>
