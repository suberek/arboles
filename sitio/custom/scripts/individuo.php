<?php header('Content-Type: text/html; charset=UTF-8'); ?>
<?php
$id	= $_REQUEST['id'];
if ((is_numeric($id)) && ($id > 0)) {
	// sigo...
}else{
	// redirecciono...
	header("Location: ".$APP_URL."/index.php");
}

require_once('../../_db.php');
/*
Acá van los datos de conexión
$schema = "nombre de la base de datos";
$server = "servidor";
$user   = "usuario";
$pass   = "contraseña";
*/

require_once('funciones-db.php');

//die($query);

$query		= "
SELECT i.ALTURA_TOT, i.calle, i.alt_ini, i.espacio_verde, e.NOMBRE_CIE, e.NOMBRE_COM, e.TIPO_FOLLA, e.ORIGEN, e.region_pampeana, e.region_nea, e.region_noa, e.region_cuyana, e.region_patagonica, e.procedencia_exotica, u.nombre_completo, u.descripcion, u.url
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

$region_pampeana	= $row['region_pampeana'];
$region_nea			= $row['region_nea'];
$region_noa			= $row['region_noa'];
$region_cuyana		= $row['region_cuyana'];
$region_patagonica	= $row['region_patagonica'];

$procedencia_exotica = $row['procedencia_exotica'];

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
	<div class=\"box\">
	<a href='#' class='cerrar'>   cerrar <i class=\"fa fa-times \"></i> </a>
	<h1>$nombre_cientifico<br> <small>$nombre_comun</small></h1>
	<p>$tipo_follaje<br>
	Origen: $origen";
if (!empty($procedencia_exotica))
	echo "<br>Procedencia: $procedencia_exotica";


if (
	($region_pampeana > 0) ||
	($region_nea > 0) ||
	($region_noa > 0) ||
	($region_cuyana > 0) ||
	($region_patagonica > 0)
   )
{
	echo "<br>Región de origen: ";
	$region_cant = 0;

	if ($region_pampeana > 0) {
		echo "Pampeana ";
		$region_cant = 1;
	}

	if ($region_nea > 0) {
		if ($region_cant > 0) echo " / ";
		echo "NEA ";
		$region_cant = 1;
	}

	if ($region_noa > 0) {
		if ($region_cant > 0) echo " / ";
		echo "NOA ";
		$region_cant = 1;
	}

	if ($region_cuyana > 0) {
		if ($region_cant > 0) echo " / ";
		echo "Cuyana ";
		$region_cant = 1;
	}

	if ($region_patagonica > 0) {
		if ($region_cant > 0) echo " / ";
		echo "Patagónica";
	}

}

if (!empty($altura))
	echo "<br>Altura: $altura m";

echo "
	</p>
	<p>
		<i class=\"fa fa-map-marker fa-fw\"></i> $ubicacion
	</p>
	<p> <small class=\"autor\">
			Fuente: <a href=\"$usuario_url\" target=\"_blank\">$usuario_autor</a>.
	</small> </p>

	<h4 class=\"enlace-directo\"> ¿Hay algún error?</h4>
	<p>
		
		El siguiente código sirve para identificar a este árbol: <kbd>$id</kbd>
		<a href='$APP_URL/$id' target='_blank'><i class='fa fa-external-link'></i></a>
	</p>

	<p>Podés usarlo para reportar datos incorrectos enviando el código con los comentarios que quieras hacer por medio de <a class='text-primary' href='https://www.facebook.com/arboladourbanomapa/' target='_blank'> <i class='fa fa-facebook-square'></i>/arboladourbanomapa</a><br> ¡Gracias!</p>";


if (
		( strstr($_SERVER['SCRIPT_FILENAME'], 'GitHub') ) ||
		( strstr($_SERVER['SCRIPT_FILENAME'], 'Dropbox') ) 
	)
{
	echo "<div>
		<a href='http://localhost/arbolado/matrix/individuosedit.php?id_individuo=$id' target='_blank'>EDITAR</a>
	</div>";
}


echo "</div>"; // fin div.box

?>