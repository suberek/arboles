/********************************************************************************* FUNCIONES GLOBALES */

// Mostrar todas las especies
function muestraTodasLasEspecies() {
	$('input#id_especie').val(null);
	$('input#muestra-especie').val(null);
	
	$("ul#results").fadeOut();
	$('h6#results-text').fadeOut();
	
	$('input#respecies-todas').prop('checked', true);
}

function muestraTodaLaCiudad() {
	$('input#user_latlng').val(null);
	$('input#rdonde-ciudad').prop('checked', true);
}

function muestraPorAca(lat,lng) {
	if (lat) {
		$('input#user_latlng').val(lat+' '+lng);
	} else {
		//alert('no hay lat');
		//var latlng_default = $('input#rdonde-mapa').val();
		//$('input#user_latlng').val(latlng_default);
	}
	$('input#rdonde-mapa').prop('checked', true);
}

function validarBusqueda(){

	/********************************************************************* Constantes */
	var hacerSubmit = false;
	
	/********************************************************************* Relevamiento de variables */
	var especieUnaCheck		= $('input#respecies-una').prop('checked');
	var especieTodasCheck	= $('input#respecies-todas').prop('checked');
	var especieId			= $('input#id_especie').val();
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
		// Pasó todas las validaciones, envío el form.
		$('form#busca_arboles').submit();
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
				url: "getdata/buscar.php",
				data: { query: query_value },
				cache: false,
				success: function(html){
					$("ul#results").html(html);
				}
			});
		}return false;    
	}

	
	/********************************************************************************* INTERACCIONES */

	
	$("input#muestra-especie").live("keyup click", function(e) {
		// Set Timeout
		clearTimeout($.data(this, 'timer'));

		// Set Search String
		var search_string = $(this).val();

		// buscar
		if (search_string == '') {
			// si está vacío vacío todo
			muestraTodasLasEspecies();
		}else{
			$("ul#results").fadeIn();
			$('h6#results-text').fadeIn();
			$(this).data('timer', setTimeout(muestraEspecie, 100));
			
			$('input#respecies-una').prop('checked', true);
		};
	});
	
	// radio button de todas las especies
	$("input#respecies-todas").click(function(e) {
		muestraTodasLasEspecies();
	});
	
	// radio button de toda la ciudad
	$("a#vaciar-posicion").click(function(e) {
		e.preventDefault();
		muestraTodaLaCiudad();
	});
	
	// click en un resultado de la búsqueda de especies
	$("ul#results").on("click","a", function(e){
		e.preventDefault();

		var link = $(this).attr('href');
		var equalPosition = link.indexOf('='); //Get the position of '='
		var number = link.substring(equalPosition + 1); //Split the string
		
		$('input#id_especie').val(number);
		
		$("ul#results").fadeOut();
		$('h6#results-text').fadeOut();
		
		var texto = $(this).find('h4').text();
		
		$('input#muestra-especie').val(texto);
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
				muestraPorAca();
			}
		}
	
	});
	
	$('form#busca_arboles').submit(function(e) {
		validarBusqueda();
		e.preventDefault();
	});
	
});