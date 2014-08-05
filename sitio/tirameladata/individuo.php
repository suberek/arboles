<?php header('Content-Type: text/html; charset=UTF-8'); ?>
<?php
$id	= $_REQUEST['id'];
if ((is_numeric($id)) && ($id > 0)) {
	// sigo...
}else{
	// redirecciono...
	header("Location: /index.php");
}

require_once('../_db.php');
/*
Acá van los datos de conexión
$schema = "nombre de la base de datos";
$server = "servidor";
$user   = "usuario";
$pass   = "contraseña";
*/

require_once('funciones.php');

//die($query);

$query		= "
SELECT i.ALTURA_TOT, i.lat, i.lng, i.calle, i.alt_ini, i.espacio_verde, i.donde, e.NOMBRE_CIE, e.NOMBRE_COM, e.TIPO_FOLLA, e.ORIGEN, f.autor, f.descripcion, f.url
FROM 1_individuos i
INNER JOIN 2_especies e ON i.id_especie=e.id_especie
INNER JOIN 3_fuentes f ON f.id=i.id_fuente
WHERE id_individuo = $id
LIMIT 1;
";

$results			= GetRS($query);
$row				= mysql_fetch_array($results);

$nombre_cientifico	= $row['NOMBRE_CIE'];
$nombre_comun		= $row['NOMBRE_COM'];
$tipo_follaje		= $row['TIPO_FOLLA'];
$origen				= $row['ORIGEN'];
$barrio				= $row['barrio_nombre'];
$altura				= $row['ALTURA_TOT'];

$lat				= $row['lat'];
$lng				= $row['lng'];

$donde				= $row['donde'];

$fuente_autor		= $row['autor'];
$fuente_desc		= $row['descripcion'];
$fuente_url			= $row['url'];

if ($donde == 0 ) {
	$alt_ini			= $row['alt_ini'];
	if ($alt_ini == 0) $alt_ini = "s/n" ;
	$ubicacion = $row['calle'] .' '. $alt_ini;
} else {
	$ubicacion = 'Espacio Verde: '. $row['espacio_verde'];
}

echo "
	<h3><small>$nombre_cientifico<br> <i>$nombre_comun</i></a></small></h3>
	<p>$tipo_follaje</p>
	<p>$origen</p>
	<p>Altura: $altura m</p>
	<p><i class=\"fa fa-map-marker fa-fw\"></i> $ubicacion</p>
	<p><small> Autor: $fuente_autor. <br>
	$fuente_desc <br>
	<a href=\"$fuente_url\" target=\"_blank\">Ir a la fuente</a></small></p>";

?>