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

function muestraPorAca(lat,lng,map,buscar) {
	$('input#rdonde-mapa').prop('checked', true);
	
	if (lat) {
		// cambiar el valor del lat y lng en el form al hacer click en un punto
		$('input#user_latlng').val(lat+' '+lng);
	}
	
	if (map) {
		// centrar el mapa al click
		map.panTo(new L.LatLng(lat, lng));
	}

	if (buscar) {
		valida = validarBusqueda();
		if (valida) {
			document.getElementById("busca_arboles").submit();
		} else {
			e.preventDefault();	
		}
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

	var especieSaboresCheck	= $('input#user_sabores').prop('checked'); // devuelve true o false
	
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
	
	// Para seguir, se tiene que dar alguna de las 3 condiciones:
	// hay alguna especie -ó- se marcó un sitio -ó- se filtra por frutales)
	if( (especieTodasCheck == false) || (dondeCiudadCheck == false) || (especieSaboresCheck == true) ) {
		var especieOdondeOsaboresCheckOK = true;
	}
	
	/********************************************************************* Diagnóstico */
	
	if (especieOdondeOsaboresCheckOK == true) {
		
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

		if (especieSaboresCheck == true) {
			hacerSubmit = true;
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
		//$('form#busca_arboles').submit();
		return true;
	} else {
		return false;
	}
}

function muestraBorrarIdEspecie(){
	if( $('#id_especie').val() == 0 ) {
		$('#borrar_id_especie').addClass('hidden');
	}else{
		$('input#user_sabores').prop('checked', false);
		$('#borrar_id_especie').removeClass('hidden');
	}
}

function muestraBorrarOrigen(){
	if (
			($('#rorigen-nativas').prop('checked') == false)
			&&
			($('#rorigen-exoticas').prop('checked') == false)
		)
	{
		$('#borrar_origen').addClass('hidden');
	} else {
		$('#borrar_origen').removeClass('hidden');
	}
}


// Start Ready
$(document).ready(function() {  

	/********************************************************************************* FUNCIONES */
	
	
	
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
		valida = validarBusqueda();
		if (valida) {
			$('form#busca_arboles').submit();
		}else{
			e.preventDefault();	
		}
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
	
	$('#borrar_id_especie').click(function(e){
		e.preventDefault();
		$('#id_especie').selectpicker('val', 0);
	});


	$('input#user_sabores').click(function(){
		
		if ( $(this).prop('checked') == true ) {
			$('#id_especie').selectpicker('val', 0);
		}
	
	});

	muestraBorrarOrigen();
	
	$( "#rorigen-nativas, #rorigen-exoticas" ).change(function() {
		muestraBorrarOrigen();
	});


	$('#borrar_origen').click(function(e){
		e.preventDefault();
		$('#rorigen-nativas, #rorigen-exoticas').prop('checked', false);
	});


	$('.mas-filtros').click(function(){
		//alert($('#mas-filtros').css('display'));
		if  (  $('#mas-filtros').css('display') == 'none' ) {
			$('#mas-filtros').slideDown();
			$(this).html('menos filtros');
		}else{
			$('#mas-filtros').slideUp();
			$(this).html('más filtros');
		}
		
	})


});