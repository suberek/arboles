<?php

//// Defino el default
$busqueda	= "";
$radius		= "300"; // Radio de búsqueda en Metros
$disableClusteringAtZoom = 20;
$user_latlng_default = array("-34.60371794474704","-58.38157095015049"); // El Obelisco

// Armo la consulta

if ( isset($_GET['colaborativo'])  ) {
	
	$busqueda = "SQL custom";

} else  {

	// Parámetros de búsqueda
	$parametro	= "WHERE 1";


	//// Veo qué vino en el form o en la URL

	if (  isset($_GET['especie_url'])  ) {

		$especie_url = $_GET['especie_url'];

		$url_query	= "
		SELECT id
		FROM t_especies
		WHERE url = '$especie_url'
		LIMIT 1;
		";
		$url_results			= GetRS($url_query);
		$url_row				= mysqli_fetch_array($url_results);
		$especie_id_busqueda	= $url_row['id'];

	} elseif
		(
			( isset($_GET['arbol_id']) ) && 
			( is_numeric($_GET['arbol_id']) ) &&
			( $_GET['arbol_id'] > 0 )
		)
	{
		$arbol_id = $_GET['arbol_id'];

	} else {

		if (  isset($_POST['especie_id'])  ) {
			$especie_id_busqueda	= $_POST['especie_id'];
		} else {
			$especie_id_busqueda	= $_GET['especie_id'];
		}

	}

	if (  isset($_POST['user_latlng'])  ) {
		$user_latlng			= $_POST['user_latlng']; // "lat lng"
	} else {
		$user_latlng			= $_GET['user_latlng'];
	}

	if (  isset($_POST['user_sabores'])  ) {
		$user_sabores		= $_POST['user_sabores'];
	} else {
		$user_sabores		= $_GET['user_sabores'];
	}

	if (  isset($_POST['user_origen'])  ) {
		$user_origen		= $_POST['user_origen'];
	} else {
		$user_origen		= $_GET['user_origen'];
	}

	if (empty($user_origen)) {
		$user_origen = 'Todas';
	}

	if (  isset($_POST['borigen_pampeana'])  ) {
		$borigen_pampeana	= $_POST['borigen_pampeana'];
	} else {
		$borigen_pampeana	= $_GET['borigen_pampeana'];
	}

	if (empty($borigen_pampeana)) {
		$borigen_pampeana = 0;
	}

	if (  isset($_POST['borigen_nea'])  ) {
		$borigen_nea	= $_POST['borigen_nea'];
	} else {
		$borigen_nea	= $_GET['borigen_nea'];
	}

	if (empty($borigen_nea)) {
		$borigen_nea = 0;
	}

	if (  isset($_POST['borigen_noa'])  ) {
		$borigen_noa	= $_POST['borigen_noa'];
	} else {
		$borigen_noa	= $_GET['borigen_noa'];
	}

	if (empty($borigen_noa)) {
		$borigen_noa = 0;
	}

	if (  isset($_POST['borigen_cuyana'])  ) {
		$borigen_cuyana	= $_POST['borigen_cuyana'];
	} else {
		$borigen_cuyana	= $_GET['borigen_cuyana'];
	}

	if (empty($borigen_cuyana)) {
		$borigen_cuyana = 0;
	}

	if (  isset($_POST['borigen_patagonica'])  ) {
		$borigen_patagonica	= $_POST['borigen_patagonica'];
	} else {
		$borigen_patagonica	= $_GET['borigen_patagonica'];
	}

	if (empty($borigen_patagonica)) {
		$borigen_patagonica = 0;
	}

	/**************************************************************** PARÁMETRO ESPECIE */
	if ((is_numeric($especie_id_busqueda)) && ($especie_id_busqueda > 0)) {
		$parametro .= " AND r.especie_id=$especie_id_busqueda";

		$busqueda	.= "especie una /";
		
	} else {
		$especie_id_busqueda = '';
		$busqueda	.= "especie todas /";
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
			$busqueda	.= " donde marker /";
		} else {
			$busqueda	.= " donde ciudad /";
		}
		
	} else {
		$busqueda	.= " donde ciudad /";
	}

	if ($busqueda == "especie todas / donde ciudad /") {
		$busqueda = "";
	}

	/**************************************************************** JOIN con especies */
	if (
			(  (is_numeric($user_sabores)) && ($user_sabores > 0)  ) 
			||
			( $user_origen !== 'Todas' )
			||
			( $borigen_pampeana > 0 )
			||
			( $borigen_nea > 0 )
			||
			( $borigen_noa > 0 )
			||
			( $borigen_cuyana > 0 )
			||
			( $borigen_patagonica > 0 )

	   )
	{
		$masFiltrosCss = "visible";

	} else {

		$masFiltrosCss = "oculto";

	}

	/**************************************************************** PARÁMETRO SABORES */
	if ((is_numeric($user_sabores)) && ($user_sabores > 0)) {
		//$parametro .= " AND ( especie_id = 23 )";
		$parametro .= " AND ( e.comestible <> '' OR e.medicinal <> '' )";

		$busqueda .= " con sabores /";
	}


	/**************************************************************** PARÁMETRO ORIGEN */
	if ( $user_origen !== 'Todas' ) {
		$parametro .= " AND ( e.origen = '".$user_origen."'  )";

		$busqueda .= " con origen ".$user_origen." /";
	}

	/**************************************************************** PARÁMETRO R Pampeana */
	if ( $borigen_pampeana > 0 ) {
		$parametro .= " AND ( e.region_pampeana = ".$borigen_pampeana."  )";

		$busqueda .= " con pampeana ".$borigen_pampeana." /";
	}

	/**************************************************************** PARÁMETRO R Pampeana */
	if ( $borigen_nea > 0 ) {
		$parametro .= " AND ( e.region_nea = ".$borigen_nea."  )";

		$busqueda .= " con nea ".$borigen_nea." /";
	}

	/**************************************************************** PARÁMETRO R Pampeana */
	if ( $borigen_noa > 0 ) {
		$parametro .= " AND ( e.region_noa = ".$borigen_noa."  )";

		$busqueda .= " con noa ".$borigen_noa." /";
	}

	/**************************************************************** PARÁMETRO R Pampeana */
	if ( $borigen_cuyana > 0 ) {
		$parametro .= " AND ( e.region_cuyana = ".$borigen_cuyana."  )";

		$busqueda .= " con cuyana ".$borigen_cuyana." /";
	}

	/**************************************************************** PARÁMETRO R Pampeana */
	if ( $borigen_patagonica > 0 ) {
		$parametro .= " AND ( e.region_patagonica = ".$borigen_patagonica."  )";

		$busqueda .= " con patagonica ".$borigen_patagonica." /";
	}

	/**************************************************************** PARÁMETRO INDIVIDUO */
	if ( $arbol_id > 0 ) {
		$parametro .= " AND ( r.arbol_id = " . $arbol_id . "  )";

		$busqueda = " un arbol";
	}
}




if ($busqueda !== '') {

	/********************************************************  Hago LA consulta que trae el resultado de la búsqueda */
	
	if (stripos($busqueda,'marker') > 0) {

		// Definir el centro y buscar en el radio.

		$censo_query = "
			SELECT arbol_id, lat, lng, especie_id, e.icono, (
					6371 * acos (
						cos ( radians( $user_lat ) )
						* cos( radians( lat ) )
						* cos( radians( lng ) - radians( $user_lng ) )
						+ sin ( radians( $user_lat ) )
						* sin( radians( lat ) )
					)
				) AS distance
			FROM ($vw_arboles_actualizaciones) r
			LEFT JOIN t_especies e ON r.especie_id = e.id
			$parametro
			HAVING distance < ($radius/1000);
		";
	} else if ($busqueda == "SQL custom") {

		$censo_query = $vw_colaborativo;

	} else {

		$censo_query = "
		SELECT arbol_id, lat, lng, especie_id, e.icono
		FROM ($vw_arboles_actualizaciones) r
		INNER JOIN t_especies e ON r.especie_id=e.id
		$parametro";
	}


	//echo $busqueda . " ---- ";
	//echo $censo_query;
	//die();


	$count = true;
	$censo_results	= GetRS($censo_query);
	$total_registros_censo = $total_registros;
	
	// Armo el array con los árboles
	if ($total_registros_censo >= 1) {
		
		while ($censo_row = mysqli_fetch_array($censo_results)) {
			$i++;
				
			$arbol_id		= $censo_row['arbol_id'];
			$lat			= $censo_row['lat'];
			$lng			= $censo_row['lng'];
			$icono			= $censo_row['icono'];
			if (empty($icono)) $icono = "marker.png";
			
			if ($i > 1) $arboles_para_mapa .= ',';
			
			$arboles_para_mapa .= '[' . $lat . ',' . $lng . ',' . $arbol_id. ',"' . $icono . '"]';
		}
	}
	
} else {	
	// sin búsqueda
}

?>