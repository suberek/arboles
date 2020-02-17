<?php
function GetRS($sSql){
    global $debug, $count, $schema, $server, $user, $pass;
	
	//$link = mysql_connect($server, $user, $pass);
    $mysqli = new mysqli($server, $user, $pass, $schema);

    /* verificar la conexión */
    if (mysqli_connect_errno()) {
        //printf("Falló la conexión: %s\n", mysqli_connect_error());
        exit();
    }

	//if (!$link) { die('Could not connect: ' . mysql_error()); }
	//mysql_select_db($schema, $link) or die("No se pudo seleccionar la base de datos");
	
	//mysql_query("SET NAMES 'utf8'");

    if (!$mysqli->set_charset("utf8")) {
        //printf("Error cargando el conjunto de caracteres utf8: %s\n", $mysqli->error);
        exit();
    } else {
        //printf("Conjunto de caracteres actual: %s\n", $mysqli->character_set_name());
    }
	
    if ($debug == 1)
		echo "<div style='padding:10px; border: 1px dashed #505050;'>". $sSql . "<br /> \r\n</div>";

	//$result = mysql_query($sSql);

    if ($result = $mysqli->query($sSql)) {
        //printf("La selección devolvió %d filas.\n", $result->num_rows);

        /* liberar el conjunto de resultados */
        //$result->close();
    }
	
	if ($count == true) {
		global $total_registros;
		$total_registros = $mysqli->affected_rows;
		//die("registros: ".$total_registros);
	}
	
	if (!$result) { die('Invalid query: ' . mysqli_error($mysqli)); }
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
$vw_arboles_actualizaciones = "SELECT t_registros.*, t_act.actualizaciones
  FROM t_registros JOIN (
    SELECT max(id) AS registro_id, count(1) AS actualizaciones
    FROM t_registros GROUP BY arbol_id
    ) AS t_act ON t_registros.id = t_act.registro_id
    WHERE removido IS NULL or removido = ''";


// árboles de usuarios
$vw_colaborativo = "SELECT arbol_id, lat, lng, especie_id, e.icono
    FROM ($vw_arboles_actualizaciones) r
    LEFT JOIN t_especies e ON r.especie_id = e.id
    WHERE r.fuente_id >= 3";

?>
