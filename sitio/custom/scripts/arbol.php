<?php header('Content-Type: text/html; charset=UTF-8'); ?>
<?php
$arbol_id	= $_REQUEST['id'];
if ((is_numeric($arbol_id)) && ($arbol_id > 0)) {
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

$query = "
	SELECT r.calle, r.calle_altura, r.altura, r.espacio_verde, e.nombre_cientifico, e.nombre_comun, e.follaje_tipo, e.origen, e.region_pampeana, e.region_nea, e.region_noa, e.region_cuyana, e.region_patagonica, e.procedencia_exotica, f.nombre, f.descripcion, f.url, f.facebook, f.twitter, COUNT(*) as registros
	FROM t_registros r
	LEFT JOIN t_especies e ON r.especie_id = e.id
	LEFT JOIN t_fuentes f ON r.fuente_id = f.id
	WHERE r.arbol_id = $arbol_id
	GROUP BY r.arbol_id

";

//echo($query);
$results			= GetRS($query);
$row				= mysql_fetch_array($results);

$nombre_cientifico	= $row['nombre_cientifico'];
$nombre_comun		= $row['nombre_comun'];
$follaje_tipo		= $row['follaje_tipo'];
$origen				= $row['origen'];

$region_pampeana	= $row['region_pampeana'];
$region_nea			= $row['region_nea'];
$region_noa			= $row['region_noa'];
$region_cuyana		= $row['region_cuyana'];
$region_patagonica	= $row['region_patagonica'];

$procedencia_exotica = $row['procedencia_exotica'];

$barrio				= $row['barrio_nombre'];
$altura				= $row['altura'];

$espacio_verde		= $row['espacio_verde'];

$fuente_autor		= $row['nombre'];
$fuente_desc		= $row['descripcion'];
$fuente_url			= $row['url'];
$fuente_fb			= $row['facebook'];
$fuente_tw			= $row['twitter'];


if ( empty($espacio_verde) ) {
	$calle_altura			= $row['calle_altura'];
	if ($calle_altura == 0) $calle_altura = "s/n" ;
	$ubicacion = $row['calle'] .' '. $calle_altura;
} else {
	$ubicacion = 'Espacio Verde: '. $row['espacio_verde'];
}

echo "
<div class=\"box\">
	<a href='#' class='cerrar'> cerrar <i class=\"fa fa-times \"></i> </a>
	<h1>$nombre_cientifico<br> <small>$nombre_comun</small></h1>
	<p>$follaje_tipoje<br>
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

if (!empty($fuente_url))
	$fuente_url = "<a href=\"$fuente_url\" target=\"_blank\"><i class=\"fa fa-lg fa-globe fa-fw\"></i></a>";

if (!empty($fuente_fb))
	$fuente_fb = "<a href=\"$fuente_fb\" target=\"_blank\"><i class=\"fa fa-lg fa-facebook-square fa-fw\"></i></a>";

if (!empty($fuente_tw))
	$fuente_tw = "<a href=\"$fuente_tw\" target=\"_blank\"><i class=\"fa fa-lg fa-twitter-square fa-fw\"></i></a>";
if (   (!empty($fuente_url)) || (!empty($fuente_fb)) || (!empty($fuente_tw))   )
	$enlaces = $fuente_url . " " . $fuente_fb . " " . $fuente_tw;

echo "
	</p>
	<p>
		<i class=\"fa fa-map-marker fa-fw\"></i> $ubicacion
	</p>
	<div class=\"autor panel panel-primary\">
		<div class=\"panel-heading\"><h4> Fuente</h4></div>
		<div class=\"panel-body\">
			<p>Dato aportado por <strong>$fuente_autor</strong></p>
			<p><small>". nl2br($fuente_desc) . " 
			</small></p>
			$enlaces
		</div>
	</div>

	<div class=\"autor panel panel-default\">
		<div class=\"panel-heading\"><h4> ¿Hay algún error?</h4></div>
		<div class=\"panel-body\">
	
		<p>
			
			El siguiente código sirve para identificar a este árbol: <kbd>$arbol_id</kbd>
			<a href='$APP_URL/$arbol_id' target='_blank'><i class='fa fa-external-link'></i></a>
		</p>

		<p>Podés usarlo para reportar datos incorrectos enviando el código con los comentarios que quieras hacer por medio de <a class='text-primary' href='https://www.facebook.com/arboladourbanomapa/' target='_blank'> <i class='fa fa-facebook-square'></i>/arboladourbanomapa</a><br> ¡Gracias!</p>
	</div>
</div>";

/*
if (
		( strstr($_SERVER['SCRIPT_FILENAME'], 'GitHub') ) ||
		( strstr($_SERVER['SCRIPT_FILENAME'], 'Dropbox') ) 
	)
{
	echo "<div>
		<a href='http://localhost/arbolado/matrix/individuosedit.php?arbol_id=$arbol_id' target='_blank'>EDITAR</a>
	</div>";
}
*/

echo "</div>"; // fin div.box

?>