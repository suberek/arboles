/* JS File */

// Start Ready
$(document).ready(function() {  

	// Live Search
	// On Search Submit and Get Results
	function search() {
		var query_value = $('input#search').val();
		$('b#search-string').html(query_value);
		if(query_value !== ''){
			$.ajax({
				type: "POST",
				url: "data/buscar.php",
				data: { query: query_value },
				cache: false,
				success: function(html){
					$("ul#results").html(html);
				}
			});
		}return false;    
	}
	
	$("input#search").live("keyup click", function(e) {
		// Set Timeout
		clearTimeout($.data(this, 'timer'));

		// Set Search String
		var search_string = $(this).val();

		// Do Search
		if (search_string == '') {
			$("ul#results").fadeOut();
			$('h4#results-text').fadeOut();
			
			$('input#id_especie').val(0);
		}else{
			$("ul#results").fadeIn();
			$('h4#results-text').fadeIn();
			$(this).data('timer', setTimeout(search, 100));
		};
	});
	
	$("ul#results").on("click","a", function(e){
		e.preventDefault();

		var link = $(this).attr('href');
		var equalPosition = link.indexOf('='); //Get the position of '='
		var number = link.substring(equalPosition + 1); //Split the string
		
		$('input#id_especie').val(number);
		
		$("ul#results").fadeOut();
		$('h4#results-text').fadeOut();
		
		var texto = $(this).find('h3').text();
		
		$('input#search').val(texto);
	});
	
	$("input#search").focusout(function() {
		if( $('input#id_especie').val() == 0 ) $(this).val('');
	})



	
});