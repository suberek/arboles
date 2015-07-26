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
SELECT i.ALTURA_TOT, i.calle, i.alt_ini, i.espacio_verde, e.NOMBRE_CIE, e.NOMBRE_COM, e.TIPO_FOLLA, e.ORIGEN, u.nombre_completo, u.descripcion, u.url
FROM individuos i
INNER JOIN especies e ON i.id_especie=e.id_especie
INNER JOIN usuarios u ON u.id=i.id_usuario
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

$espacio_verde		= $row['espacio_verde'];

$usuario_autor		= $row['nombre_completo'];
$usuario_desc		= $row['descripcion'];
$usuario_url		= $row['url'];

if ( empty($espacio_verde) ) {
	$alt_ini			= $row['alt_ini'];
	if ($alt_ini == 0) $alt_ini = "s/n" ;
	$ubicacion = $row['calle'] .' '. $alt_ini;
} else {
	$ubicacion = 'Espacio Verde: '. $row['espacio_verde'];
}

echo "
	<h1>$nombre_cientifico<br> <small>$nombre_comun</small></h1>
	<p>$tipo_follaje<br>
	$origen</p>";

if (!empty($altura))
	echo "<p>Altura: $altura m</p>";

echo "
	<p><i class=\"fa fa-map-marker fa-fw\"></i> $ubicacion</p>
	<p><small> Autor: $usuario_autor. </small></p>
	<small>
		$usuario_desc
		<p><a href=\"$usuario_url\" target=\"_blank\">Ir a la fuente</a></p>
	</small>";
?>