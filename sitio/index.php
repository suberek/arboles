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
$descripcion = 'Mapa colaborativo del arbolado en ciudades';
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8">

    <!-- Facebook meta -->
    <meta property="og:url" content="<?php echo $APP_URL; ?>">
    <meta property="og:title" content="Arbolado Urbano">
    <meta property="og:description" content="<?php echo $descripcion ?>">
    <meta property="og:image" content="<?php echo $APP_URL; ?>/images/logo-152x152.png">

    <title>Arbolado Urbano - <?php echo strtolower($descripcion); ?></title>
    <meta name="description" content="<?php echo $descripcion; ?>">
    <meta name="author" content="Martín Simonyan">

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

    <nav id="menu-top" class="navbar navbar-default navbar-fixed-top">
      <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>

          <a class="navbar-brand" href="<?php echo $APP_URL; ?>/">
            <img src="<?php echo $APP_URL; ?>/images/logo-arbolado-urbano-titulo.png" alt="Arbolado Urbano">
            Arbolado Urbano
          </a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">


          <ul class="nav navbar-nav navbar-right">
            <!--<li><a href="/blog"><i class="fa fa-pencil" aria-hidden="true"></i> Blog</a></li>-->
            <li><a href="#" data-toggle="modal" data-target="#que-es-esto"><i class="fa fw fa-question-circle" aria-hidden="true"></i> Sobre el mapa</a></li>
            <li><a href="https://cafecito.app/arboladomapa" target="_blank"><i class="fa fa-coffee" aria-hidden="true"></i> Donaciones</a></li>
            <li><a href="#" data-toggle="modal" data-target="#seguinos" ><i class="fa fw fa-heart" aria-hidden="true"></i> Seguinos</a></li>
            <li></li>
          </ul>
        </div><!-- /.navbar-collapse -->
      </div><!-- /.container-fluid -->
    </nav>

    <nav class="navbar navbar-default navbar-fixed-bottom visible-sm visible-xs">
      <div class="container-fluid">
        
        <a class="btn btn-default navbar-btn scroll" href="#mapa">
          <i class="fa fa-map-marker fa-sm"></i>
          Mapa
          <i class="fa fa-caret-up fa-sm"></i>
        </a>

        <a class="btn btn-default navbar-btn scroll" href="#busca_arboles">
          <i class="fa fa-search fa-sm"></i>
          Buscador
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

         <!-- mapa (container) -->
        <div class="col-xs-12 col-sm-8 col-lg-9" id="mapa"> </div>

        <!-- left slide bar -->
        <div class="col-xs-12 col-sm-4 col-lg-3" id="menu">
          <nav>
            <?php require_once('custom/scripts/form.php'); ?>
            

            <div id="adsense" class="row hidden-xs visible-sm visible-md visible-lg">
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

            <div class="red row">
              <div class="col-xs-12 hidden-xs visible-sm visible-md visible-lg">
                <p class="col-xs-12 este-mapa">
                  Este mapa cuenta con la<br>
                   inconmensurable colaboración de:
                </p>
                <a class="col-xs-6 lcnrs" href="https://www.facebook.com/LaCiudadNosRegalaSabores" target="_blank">
                  <img src="<?php echo $APP_URL; ?>/images/colaborador-lcnrs.png" alt="La ciudad nos regala sabores">
                </a>
                <a class="col-xs-6 laguna-fvet" href="https://www.facebook.com/elrenacerdelalaguna/" target="_blank">
                  <img src="<?php echo $APP_URL; ?>/images/colaborador-laguna-fvet.png" alt="El Renacer de la Laguna - FVET - UBA">
                </a>
              </div>
            </div>
          </nav>
        </div>

       
      </div>
    </div>

    <?php require_once('custom/scripts/modals.php') ?>
    <script type="text/javascript" src="<?php echo $APP_URL; ?>/custom/scripts/interaccion-form-mapa.min.js"></script>
    <?php require_once('custom/scripts/pintar-mapa.php') ?>
    <?php require_once('custom/scripts/funciones-js-footer.php') ?>
  </body>
</html>
