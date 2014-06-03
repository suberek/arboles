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

/*$query		= "
SELECT i.lat, i.lng, i.calle, i.alt_ini, i.ALTURA_TOT, e.nombre_cientifico, e.nombre_comun, e.tipo_follaje, e.origen, b.barrio_nombre
FROM 1_individuos i
JOIN x_especies_provisorio e ON i.id_especie=e.id_especie
JOIN a_barrios b ON i.id_barrio=b.id_barrio
WHERE id_individuo = $id
LIMIT 1;
";*/

//die($query);

$query		= "
SELECT i.ALTURA_TOT, i.lat, i.lng, i.calle, i.alt_ini, i.espacio_verde, i.donde, e.nombre_cientifico, e.nombre_comun, e.tipo_follaje, e.origen
FROM 1_individuos i
JOIN x_especies_provisorio e ON i.id_especie=e.id_especie
WHERE id_individuo = $id
LIMIT 1;
";

$results			= GetRS($query);
$row				= mysql_fetch_array($results);

$nombre_cientifico	= $row['nombre_cientifico'];
$nombre_comun		= $row['nombre_comun'];
$tipo_follaje		= $row['tipo_follaje'];
$origen				= $row['origen'];
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