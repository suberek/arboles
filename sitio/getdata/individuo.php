<?php header('Content-Type: text/html; charset=UTF-8'); ?>
<?php
$id	= $_REQUEST['id'];
if ((is_numeric($id)) && ($id > 0)) {
	// sigo...
}else{
	// redirecciono...
	header("Location: /index.php");
}

require_once('../includes/funciones.php');

$query		= "
SELECT lat, lng, calle, alt_ini, nombre_cientifico, nombre_comun, tipo_follaje, origen, barrio_nombre, ALTURA_TOT
FROM 1_individuos2 i
JOIN x_especies_provisorio e ON i.id_especie=e.id_especie
JOIN a_barrios b ON i.id_barrio=b.id_barrio
WHERE id_individuo = $id
LIMIT 1;
";
$results			= GetRS($query);
$row				= mysql_fetch_array($results);

$calle				= $row['calle'];
$alt_ini			= $row['alt_ini'];
if ($alt_ini == 0) $alt_ini = "s/n" ;
$nombre_cientifico	= $row['nombre_cientifico'];
$nombre_comun		= $row['nombre_comun'];
$tipo_follaje		= $row['tipo_follaje'];
$origen				= $row['origen'];
$barrio				= $row['barrio_nombre'];
$altura				= $row['ALTURA_TOT'];

$lat				= $row['lat'];
$lng				= $row['lng'];

echo "
	<p><b>$nombre_cientifico</b><br> <i>$nombre_comun</i></p>
	<p>$tipo_follaje</p>
	<p>$origen</p>
	<p>Altura: $altura m</p>
	<p><i class=\"fa fa-map-marker\"></i> $calle $alt_ini</p>";
?>