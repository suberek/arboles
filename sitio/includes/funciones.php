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
             ">", "<", ";", ",", ":"),
        '',
        $cadena
    );
 
 
    return $cadena;
}

?>