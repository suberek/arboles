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

require_once('../includes/funciones.php');

//die($query);

$query		= "
SELECT i.ALTURA_TOT, i.lat, i.lng, i.calle, i.alt_ini, i.espacio_verde, i.donde, e.NOMBRE_CIE, e.NOMBRE_COM, e.TIPO_FOLLA, e.ORIGEN
FROM 1_individuos i
JOIN 2_especies e ON i.id_especie=e.id_especie
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

if ($donde == 0 ) {
	$alt_ini			= $row['alt_ini'];
	if ($alt_ini == 0) $alt_ini = "s/n" ;
	$ubicacion = $row['calle'] .' '. $alt_ini;
} else {
	$ubicacion = 'Espacio Verde: '. $row['espacio_verde'];
}

echo "
	<p><b>$nombre_cientifico</b><br> <i>$nombre_comun</i></p>
	<p>$tipo_follaje</p>
	<p>$origen</p>
	<p>Altura: $altura m</p>
	<p><i class=\"fa fa-map-marker\"></i> $ubicacion</p>";
?>