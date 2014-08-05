/********************************************************************************* FUNCIONES GLOBALES */

// Mostrar todas las especies
/*function muestraTodasLasEspecies() {
	$('input#id_especie').val(null);
	$('input#muestra-especie').val(null);
	
	$("ul#results").fadeOut();
	$('h6#results-text').fadeOut();
	
	$('input#respecies-todas').prop('checked', true);
}*/

function muestraTodaLaCiudad() {
	$('input#user_latlng').val(null);
	$('input#rdonde-ciudad').prop('checked', true);
	
	if (window.new_user_marker) {
		window.map.removeLayer(window.new_user_marker);
		window.map.removeLayer(window.new_user_circle);
		window.new_user_marker = undefined;
		window.new_user_circle = undefined;	
	}

}

function muestraPorAca(lat,lng,map) {
	$('input#rdonde-mapa').prop('checked', true);
	
	if (lat) {
		// cambiar el valor del lat y lng en el form al hacer click en un punto
		$('input#user_latlng').val(lat+' '+lng);
	}
	
	if (map) {
		// centrar el mapa al click
		map.panTo(new L.LatLng(lat, lng));
	}
	
}

function scrollAnimado(anchorHash){
	// animate
	$('html, body').animate({
		scrollTop: $(anchorHash).offset().top
		}, 300, function(){
	
		// when done, add hash to url
		// (default click behaviour)
		window.location.hash = anchorHash;
	});
}

function validarBusqueda(){

	/********************************************************************* Constantes */
	var hacerSubmit = false;
	
	/********************************************************************* Relevamiento de variables */
	//var especieUnaCheck	= $('input#respecies-una').prop('checked');
	//var especieTodasCheck	= $('input#respecies-todas').prop('checked');
	
	var especieUnaCheck;
	var especieTodasCheck;
	
	var especieId			= $('select#id_especie').val();
	
	if (especieId > 0) {
		especieUnaCheck = true;
		especieTodasCheck = false;
	} else {
		especieUnaCheck = false;
		especieTodasCheck = true;
	}
	
	var dondeMarkerCheck	= $('input#rdonde-mapa').prop('checked');
	var dondeCiudadCheck	= $('input#rdonde-ciudad').prop('checked');
	var dondeLatLng			= $('input#user_latlng').val();
	
	//alert(especieCheck + ' / ' + especieId + ' / ' + dondeCheck + ' / ' + dondeLatLng);
	
	/********************************************************************* Análisis */
	if(especieUnaCheck == true) {
		if (especieId > 0) {
			var especieUnaCheckOK = true;
		}
	}
	
	if(dondeMarkerCheck == true) {
		if (dondeLatLng.length > 1) {			
			var dondeMarkerCheckOK = true;
		}
	}
	
	if( (especieTodasCheck == false) || (dondeCiudadCheck == false) ) {
		var especieOdondeCheckOK = true;
	}
	
	/********************************************************************* Diagnóstico */
	
	if (especieOdondeCheckOK == true) {
		
		if (especieUnaCheck == true) {
			if (especieUnaCheckOK == true) {
				hacerSubmit = true;
			} else {
				// check en una especie pero no hay id
				$('#respecies-una-modal').modal('show');
				hacerSubmit = false;
			}
		}
		
		if (dondeMarkerCheck == true) {
			if (dondeMarkerCheckOK == true) {
				hacerSubmit = true;
			} else {
				// ckeck en marker pero no hay latlng
				$('#rdonde-ciudad-modal').modal('show');
				hacerSubmit = false;
			}
		}
		 
	} else {
		// no pueden ser todas las especies y toda la ciudad
		$('#respecies-todas-modal').modal('show');
		hacerSubmit = false;
	}
	
	/********************************************************************* Propuesta */
	
	if(hacerSubmit == true) {		

		// Levanto modal para los ansiosos como yo.
		$('#empieza-busqueda').modal('show');
		
		// Pasó todas las validaciones, envío el form.
		$('form#busca_arboles').submit();
	}
	
}

function muestraBorrarIdEspecie(){
	if( $('#id_especie').val() == 0 ) {
		$('#borrar_id_especie').addClass('hidden');
	}else{
		$('#borrar_id_especie').removeClass('hidden');
	}
}




// Start Ready
$(document).ready(function() {  

	/********************************************************************************* FUNCIONES */
	
	// Live Search. Resultados de especies mientras tipeo
	function muestraEspecie() {
		var query_value = $('input#muestra-especie').val();
		$('b#muestra-especie-string').html(query_value);
		if(query_value !== ''){
			$.ajax({
				type: "POST",
				url: "tirameladata/buscar.php",
				data: { query: query_value },
				cache: false,
				success: function(html){
					$("ul#results").html(html);
				}
			});
		}return false;    
	}

	
	/********************************************************************************* INTERACCIONES */

	
	// radio button de todas las especies
	$("input#respecies-todas").click(function(e) {
		muestraTodasLasEspecies();
	});
	
	// radio button de toda la ciudad
	$("a#vaciar-posicion").click(function(e) {
		e.preventDefault();
		muestraTodaLaCiudad();
	});
	
	
	$('input[type="radio"]').click(function(){
		
		if ($(this).attr("name")=="respecie") {
			if( $(this).attr("value")==0 ){
				$("#especies-lista").removeClass().addClass('hide');
			} else {
				$("#especies-lista").removeClass();
			}
		}
		
		if( $(this).attr("name")=="rdonde" ){
			if( $(this).attr("value") == 0 ) {
				muestraTodaLaCiudad();
			} else {
				scrollAnimado('#mapa');
				muestraPorAca();
			}
		}
	
	});
	
	$('form#busca_arboles').submit(function(e) {
		validarBusqueda();
		e.preventDefault();
	});
	
	$("nav a.scroll").on('click', function(e) {
		e.preventDefault();
		scrollAnimado(this.hash);
	});
	
	
	// Lista de Nombre científico
	$('.selectpicker').selectpicker({
		noneSelectedText: 'No hay selección',
		noneResultsText: 'No hay resultados'
	});
	
	muestraBorrarIdEspecie();
	
	$( "#id_especie" ).change(function() {
		muestraBorrarIdEspecie();
	});
	
	/*if( /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent) ) {
		$('.selectpicker').selectpicker('mobile');
	}*/
	
	$('#borrar_id_especie').click(function(e){
		e.preventDefault();
		$('#id_especie').selectpicker('val', 0);
	});

});