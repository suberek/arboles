<?php header('Content-Type: text/html; charset=UTF-8'); ?>
<?php
require_once('../_db.php');
/*
Acá van los datos de conexión
$schema = "nombre de la base de datos";
$server = "servidor";
$user   = "usuario";
$pass   = "contraseña";
*/
require_once('../includes/funciones.php');

$dbhost = $server;
$dbname = $schema;
$dbuser = $user;
$dbpass = $pass;

//	Connection
global $tutorial_db;

$tutorial_db = new mysqli();
$tutorial_db->connect($dbhost, $dbuser, $dbpass, $dbname);
$tutorial_db->set_charset("utf8");

//	Check Connection
if ($tutorial_db->connect_errno) {
    printf("Connect failed: %s\n", $tutorial_db->connect_error);
    exit();
}

/************************************************
	Search Functionality
************************************************/

// Get Search
$search_string = $_POST['query'];
$search_string = $tutorial_db->real_escape_string($search_string);
$search_string = sanear_string($search_string);

// Check Length More Than One Character
if (strlen($search_string) >= 1 && $search_string !== ' ' ) {
		
	// Define Output HTML Formating
	$html = '';
	$html .= '<li class="result">';
	$html .= '<a href="urlString">';
	$html .= '<h3>nombre científico</h3>';
	$html .= '<h4>nombre común</h4>';
	$html .= '</a>';
	$html .= '</li>';

	// Build Query
	$query = 'SELECT nombre_cientifico, nombre_comun, id_especie FROM x_especies_provisorio WHERE nombre_cientifico LIKE "%'.$search_string.'%" OR nombre_comun LIKE "%'.$search_string.'%"';

	// Do Search
	$result = $tutorial_db->query($query);
	while($results = $result->fetch_array()) {
		$result_array[] = $results;
	}

	// Check If We Have Results
	if (isset($result_array)) {
		foreach ($result_array as $result) {

			// Format Output Strings And Hightlight Matches
			$display_cient = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result['nombre_cientifico']);
			$display_comun = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result['nombre_comun']);
			$display_url = 'index.php?id_especie='.urlencode($result['id_especie']);

			// Insert Name
			$output = str_replace('nombre científico', $display_cient, $html);

			// Insert Function
			$output = str_replace('nombre común', $display_comun, $output);

			// Insert URL
			$output = str_replace('urlString', $display_url, $output);

			// Output
			echo($output);
		}
	}else{

		// Format No Results Output
		$output = str_replace('urlString', 'javascript:void(0);', $html);
		$output = str_replace('nombre científico', '<b>Nada... </b>', $output);
		$output = str_replace('nombre común', '¿otro nombre?', $output);

		// Output
		echo($output);
	}
	
}
?>