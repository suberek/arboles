<?php
function GetRS($sSql){
    global $debug, $count, $schema, $server, $user, $pass;
	
	$link = mysql_connect($server, $user, $pass);
	if (!$link) { die('Could not connect: ' . mysql_error()); }
	mysql_select_db($schema, $link) or die("No se pudo seleccionar la base de datos");
	
	mysql_query("SET NAMES 'utf8'");
	
    if ($debug == 1)
		echo "<div style='padding:10px; border: 1px dashed #505050;'>". $sSql . "<br /> \r\n</div>";

	$result = mysql_query($sSql);
	
	if ($count == true) {
		global $total_registros;
		$total_registros = mysql_affected_rows();
		//die("registros: ".$total_registros);
	}
	
	if (!$result) { die('Invalid query: ' . mysql_error()); }
	return $result;
}


function sanear_string($cadena)
{
    $cadena = trim($cadena);
 
    $cadena = str_replace(
        array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
        array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
        $cadena
    );
 
    $cadena = str_replace(
        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
        array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
        $cadena
    );
 
    $cadena = str_replace(
        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
        array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
        $cadena
    );
 
    $cadena = str_replace(
        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
        array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
        $cadena
    );
 
    $cadena = str_replace(
        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
        array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
        $cadena
    );
 
    $cadena = str_replace(
        array('ñ', 'Ñ', 'ç', 'Ç'),
        array('n', 'N', 'c', 'C',),
        $cadena
    );
 
    //Esta parte se encarga de eliminar cualquier caracter extraño
    $cadena = str_replace(
        array("\\", "¨", "º", "-", "~",
             "#", "@", "|", "!", "\"",
             "·", "$", "%", "&", "/",
             "(", ")", "?", "'", "¡",
             "¿", "[", "^", "`", "]",
             "+", "}", "{", "¨", "´",
             ">", "<", ";", ",", ":", "."),
        '',
        $cadena
    );
 
 
    return $cadena;
}

// Consulta para obtener sólo arboles y NO registros
$vw_arboles = "SELECT arbol_id, lat, lng, especie_id, icono, COUNT(*) as registros FROM t_registros LEFT JOIN t_especies ON t_registros.especie_id = t_especies.id GROUP BY arbol_id";

// árboles de CABA
$vw_frutales_caba = "SELECT arbol_id, lat, lng, especie_id, e.icono, ( 6371 * acos ( cos ( radians( -34.613148810142455 ) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians( -58.44383239746093 ) ) + sin ( radians( -34.613148810142455 ) ) * sin( radians( lat ) ) ) ) AS distance FROM t_registros r LEFT JOIN t_especies ON r.especie_id = t_especies.id INNER JOIN t_especies e ON r.especie_id=e.id WHERE 1 AND ( e.comestible <> '' OR e.medicinal <> '' ) GROUP BY arbol_id HAVING distance < (10000/1000)";

// árboles de usuarios
$vw_colaborativo = "SELECT arbol_id, lat, lng, especie_id, e.icono
    FROM t_registros r
    LEFT JOIN t_especies e ON r.especie_id = e.id
    WHERE r.fuente_id >= 5
    GROUP BY arbol_id";

?>