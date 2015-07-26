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
require_once('funciones.php');

$dbhost = $server;
$dbname = $schema;
$dbuser = $user;
$dbpass = $pass;

//	Connection
global $tutorial_db;

$tutorial_db = new mysqli();
$tutorial_db->connect($dbhost, $dbuser, $dbpass, $dbname);
$tutorial_db->set_charset("utf8");

	// Build Query
	
	$query = "SELECT * FROM familias order by familia";

	// Do Search
	$result = $tutorial_db->query($query);
	while($results = $result->fetch_array()) {
		$result_array[] = $results;
	}

	// Check If We Have Results
	if (isset($result_array)) {
		foreach ($result_array as $result) {
			$SQL = "UPDATE 2_especies SET id_familia = "  .$result['id'].  " WHERE NOMBRE_FAM LIKE '"  .$result['familia'].  "';";
			echo($SQL . "<br>");
		}
	}
?>