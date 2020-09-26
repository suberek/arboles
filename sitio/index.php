<?php
session_start();
require_once('_db.php');
/*
Acá van los datos de conexión
$schema = "nombre de la base de datos";
$server = "servidor";
$user   = "usuario";
$pass   = "contraseña";
*/
require_once('custom/scripts/funciones-db.php');
$descripcion = 'Buscador de árboles para todas las ciudades del mundo.';
$descripcion .= ' Datos obtenidos de los censos forestales publicados en los';
$descripcion .= ' portales de datos abiertos de las municipalidades, y de colaboradores independientes.';
$descripcion .= ' El objetivo principal de este sitio es simplificar el acceso a esta valiosa información';
$descripcion .= ' esperando que colabore con el conocimiento y el cuidado de nuestro arbolado urbano.';
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8">

    <!-- Facebook meta -->
    <meta property="og:url" content="http://www.arboladourbano.com/">
    <meta property="og:title" content="Mapa del Arbolado Urbano">
    <meta property="og:description" content="<?php echo $descripcion ?>">
    <meta property="og:image" content="https://arboladourbano.com.ar/images/logo-152x152.png">

    <title>Arbolado Urbano - árboles de todas las ciudades del mundo</title>
    <meta name="description" content="<?php echo $descripcion ?>">
    <meta name="author" content="Martín Simonyan & Francisco Ferioli Marco">

    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="shortcut icon" href="<?php echo $APP_URL; ?>/images/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="<?php echo $APP_URL; ?>/images/logo.png">
    <link rel="apple-touch-icon" sizes="152x152" href="<?php echo $APP_URL; ?>/images/logo-152x152.png">

    <!-- jQuery -->
    <script
      src="https://code.jquery.com/jquery-2.2.4.min.js"
      integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
      crossorigin="anonymous">
    </script>

    <!-- Bootstrap -->
    <script
      src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
      integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
      crossorigin="anonymous">
    </script>
    <!-- Bootstrap Plugins-->
    <script src="<?php echo $APP_URL; ?>/third-party/bootstrap-plugins/bootstrap-select.min.js"></script>

    <!-- Leaflet -->
    <link
      rel="stylesheet"
      href="https://unpkg.com/leaflet@1.0.3/dist/leaflet.css"
      integrity="sha512-07I2e+7D8p6he1SIM+1twR5TIrhUQn9+I6yjqD53JQjFiMf8EtC93ty0/5vJTZGF8aAocvHYNEDJajGdNx1IsQ=="
      crossorigin="">
    <script
      src="https://unpkg.com/leaflet@1.0.3/dist/leaflet-src.js"
      integrity="sha512-WXoSHqw/t26DszhdMhOXOkI7qCiv5QWXhH9R7CgvgZMHz1ImlkVQ3uNsiQKu5wwbbxtPzFXd1hK4tzno2VqhpA=="
      crossorigin="">
    </script>

    <!-- Leaflet Plugins: Marker Clusters -->
    <link
      href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css"
      rel="stylesheet"
      type="text/css"
      media="all">
    <link
      href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css"
      rel="stylesheet"
      type="text/css"
      media="all">
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>

    <!-- Leaflet Plugins: Otros -->
    <script src="<?php echo $APP_URL; ?>/third-party/leaflet-plugins/Geocoder/Control.Geocoder.min.js"></script>
    <script src="<?php echo $APP_URL; ?>/third-party/leaflet-plugins/CanvasOverlay/L.CanvasOverlay.js"></script>
    <script src="<?php echo $APP_URL; ?>/third-party/leaflet-plugins/EasyButton/easy-button.js"></script>

    <!-- Font Awesome -->
    <script src="https://use.fontawesome.com/10e53b9570.js"></script>

    <!-- Custom -->
    <link href="<?php echo $APP_URL; ?>/custom/css/estilos.css" rel="stylesheet" type="text/css" media="all">

    <?php require_once('custom/scripts/arboles.php'); ?>
  </head>

  <body>

    <?php
    // Parámetro para ver ID especie
    if (isset($_GET['ver_especie_id'])) {
      $_SESSION['ver_especie_id'] = $_GET['ver_especie_id'];
      if ($_GET['ver_especie_id'] == 0) {
        unset($_SESSION["ver_especie_id"]);
        session_destroy();
      }
    }

    if (isset($_SESSION['ver_especie_id'])) {
      echo "<div id='consola'><pre>";
      echo "¡Hola voluntario!<br>";
      echo "ver especie id activado";
      echo "</pre></div>";
    }

    // fin ver parametro ID especie

    // Parámetro para ver DEBUG
    if (isset($_GET['debug'])) {
      $_SESSION['debug'] = $_GET['debug'];
      if ($_GET['debug'] == 0) {
        unset($_SESSION["debug"]);
        session_destroy();
      }
    }

    if (isset($_SESSION['debug'])) {
      echo "<div id='consola' class='debug'><pre>";
      echo "MODO DEBUG<br>";
      echo $busqueda . "<br>---<br>";
      if (isset($censo_query)) {
        echo $censo_query;
      };
      echo "</pre></div>";
    }

    // fin ver parametro DEBUG
    ?>

    <nav class="navbar navbar-default navbar-fixed-bottom visible-sm visible-xs">
      <div class="container-fluid">
        <a class="btn btn-default navbar-btn scroll" href="#busca_arboles">
          <i class="fa fa-search fa-sm"></i>
          Buscador
          <i class="fa fa-caret-up fa-sm"></i>
        </a>
        <a class="btn btn-default navbar-btn scroll" href="#mapa">
          <i class="fa fa-map-marker fa-sm"></i>
          Mapa
          <i class="fa fa-caret-down fa-sm"></i>
        </a>
      </div>
    </nav>
    <div class="container-fluid full-height">
      <div class="row full-height">
        <div class="progress navbar-fixed-bottom">
          <div
            class="progress-bar progress-bar-striped active"
            role="progressbar"
            aria-valuenow="1"
            aria-valuemin="0"
            aria-valuemax="100"
            style="width: 0%">
          </div>
        </div>

        <!-- modal arbol (container) -->
        <div class="col-xs-12 col-sm-9 col-md-6" id="info-arbol">
        </div>

        <!-- left slide bar -->
        <div class="col-md-4 col-lg-3" id="menu">
          <nav>
            <a class="title" href="<?php echo $APP_URL; ?>/">
              <h1>
                Arbolado Urbano
                <small>Internacional</small>
              </h1>
              <img src="images/logo-152x152-blanco.png" alt="Arbolado Urbano">
            </a>
            <?php require_once('custom/scripts/form.php'); ?>
            <div class="row red">
              <div class="col-xs-12 col-sm-3 col-md-6">
                <a class="btn btn-default btn-small btn-block facebook" href="https://www.facebook.com/arboladomapa" target="_blank">
                  <i class="fa fw fa-facebook-official"></i>
                  Seguinos
                </a>
              </div>
              <div class="col-xs-12 col-sm-3 col-md-6">
                <button class="btn btn-default btn-small btn-block Qué-es-esto?" data-toggle="modal" data-target="#Qué-es-esto?">
                  Sobre el mapa
                </button>
              </div>
            </div>

            <div id="adsense" class="row">
              <div class="col-xs-12">
                <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                <!-- arbolado1 -->
                <ins
                  class="adsbygoogle"
                  style="display:block"
                  data-ad-client="ca-pub-7228206495347837"
                  data-ad-slot="8591261973"
                  data-ad-format="auto">
                </ins>
                <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
              </div>
            </div>

            <div class="red red2 row">
              <div class="col-xs-12">
                <p class="col-xs-12 col-sm-3 col-md-12 este-mapa">
                  Este mapa cuenta con<br>
                  la valiosa colaboración de:
                </p>
                <a class="col-xs-6 col-sm-3 col-md-6 col-lg-4 lcnrs" href="https://www.facebook.com/LaCiudadNosRegalaSabores" target="_blank">
                  <img src="<?php echo $APP_URL; ?>/images/colaborador-lcnrs.png" alt="La ciudad nos regala sabores">
                </a>
                <a class="col-xs-6 col-sm-3 col-md-6 col-lg-4 laguna-fvet" href="https://www.facebook.com/elrenacerdelalaguna/" target="_blank">
                  <img src="<?php echo $APP_URL; ?>/images/colaborador-laguna-fvet.png" alt="El Renacer de la Laguna - FVET - UBA">
                </a>
                <a class="col-xs-12 col-sm-3 col-md-12 col-lg-4 arn" href="https://www.facebook.com/AsociacionRiberaNorte/" target="_blank">
                  <img src="<?php echo $APP_URL; ?>/images/colaborador-arn.png" alt="Asociación Ribera Norte">
                </a>
              </div>
            </div>
          </nav>
        </div>

        <!-- mapa (container) -->
        <div class="col-md-8 col-lg-9 full-height" id="mapa"> </div>
      </div>
    </div>

    <?php require_once('custom/scripts/modals.php') ?>
    <script type="text/javascript" src="<?php echo $APP_URL; ?>/custom/scripts/interaccion-form-mapa.min.js"></script>
    <?php require_once('custom/scripts/pintar-mapa.php') ?>
    <?php require_once('custom/scripts/funciones-js-footer.php') ?>
  </body>
</html>
