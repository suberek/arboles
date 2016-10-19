<?php
// Parámetros de búsqueda
//// Defino el default
$parametro	= "WHERE 1";
$busqueda	= "";
$radius		= "500"; // Radio de búsqueda en Metros
$user_latlng_default = array("-34.60371794474704","-58.38157095015049"); // El Obelisco


$user_latlng			= $_GET['user_latlng'];
$user_sabores			= $_GET['sabores'];
$user_origen			= $_GET['origen'];
if (empty($user_origen)) {
	$user_origen = 'Todas';
}

//// Veo qué vino en el form o en la URL

if (  isset($_GET['especie_url'])  ) {

	$especie_url = $_GET['especie_url'];

	$url_query	= "
	SELECT id_especie
	FROM especies
	WHERE url = '$especie_url'
	LIMIT 1;
	";
	$url_results			= GetRS($url_query);
	$url_row				= mysql_fetch_array($url_results);
	$id_especie_busqueda	= $url_row['id_especie'];

} elseif
	(
		( isset($_GET['id_individuo']) ) && 
		( is_numeric($_GET['id_individuo']) ) &&
		( $_GET['id_individuo'] > 0 )
	)
{
	$id_individuo = $_GET['id_individuo'];

} else {

	if (  isset($_POST['id_especie'])  ) {
		$id_especie_busqueda	= $_POST['id_especie'];
	} else {
		$id_especie_busqueda	= $_GET['id_especie'];
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
if ((is_numeric($id_especie_busqueda)) && ($id_especie_busqueda > 0)) {
	$parametro .= " AND i.id_especie=$id_especie_busqueda";
	
	$especie_query	= "
	SELECT e.NOMBRE_CIE
	FROM especies e
	$parametroJoin
	WHERE e.id_especie = $id_especie_busqueda
	LIMIT 1;
	";
	$especie_results	= GetRS($especie_query);
	$especie_row		= mysql_fetch_array($especie_results);
	$nombre_cientifico	= $especie_row['NOMBRE_CIE'];
	
	$busqueda	.= "especie una /";
	
} else {
	$id_especie_busqueda = '';
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
	$parametroJoin = " INNER JOIN especies e ON i.id_especie=e.id_especie";

	$masFiltrosCss = "visible";

} else {

	$masFiltrosCss = "oculto";

}

/**************************************************************** PARÁMETRO SABORES */
if ((is_numeric($user_sabores)) && ($user_sabores > 0)) {
	//$parametro .= " AND ( id_especie = 23 )";
	$parametro .= " AND ( e.comestible <> '' OR e.medicinal <> '' )";

	$busqueda .= " con sabores /";
}


/**************************************************************** PARÁMETRO ORIGEN */
if ( $user_origen !== 'Todas' ) {
	$parametro .= " AND ( e.ORIGEN = '".$user_origen."'  )";

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
if ( $id_individuo > 0 ) {
	$parametro .= " AND ( i.id_individuo = " . $id_individuo . "  )";

	$busqueda = " un individuo";
}

//echo("<br>".$user_lat);
//echo("<br>".$user_lng);




if ($busqueda !== '') {
	
	/********************************************************  Hago LA consulta que trae el resultado de la búsqueda */

	/*
	Usando el campo GEOESPACIAL puedo buscar así:
	$censo_query = "
	SELECT id_individuo, id_especie, X(`coordenadas`) as lat, Y(`coordenadas`) as lng
	FROM individuos
	$parametro";
	*/

	/*
	Como PHPMaker no permite la creación de campos GEOESPACIALES,
	busco por los campos comunes lat y lng
	*/

	$censo_query = "
		SELECT i.id_individuo, i.id_especie, lat, lng
		FROM individuos i
		$parametroJoin 
		$parametro";
		
	if (stripos($busqueda,'marker') > 0) {

		// Definir el centro y buscar en el radio.
		
		/*
		Usando el campo GEOESPACIAL puedo buscar así:
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
		FROM individuos
		$parametro
		HAVING distance < ($radius/1000);";
		*/

		/*
		Como PHPMaker no permite la creación de campos GEOESPACIALES,
		busco por los campos comunes lat y lng
		*/

		$censo_query = "
		SELECT i.id_individuo, i.id_especie, lat, lng ,(
			6371 * acos (
			  cos ( radians( $user_lat ) )
			  * cos( radians( lat ) )
			  * cos( radians( lng ) - radians( $user_lng ) )
			  + sin ( radians( $user_lat ) )
			  * sin( radians( lat ) )
			)
		  ) AS distance
		FROM individuos i
		$parametroJoin
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
	
} else {	
	// sin búsqueda
}

?>