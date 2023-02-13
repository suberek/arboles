<form action="<?php echo $APP_URL; ?>/index.php#mapa" method="post" id="busca_arboles">
  <div class="row">
    <div class="col-xs-12">
      <div class="form-group">
        <h3>¿Dónde?</h3>
        <div class="radio">
          <label>
            <input type="radio" id="rdonde-ciudad" name="rdonde" value="0" <?php echo (stripos($busqueda, 'marker') == 0) ? 'checked' : '' ?>>
            en todo el mapa
          </label>
          <label>
            <input type="radio" id="rdonde-mapa" name="rdonde" value="<? echo $user_latlng_default[0].' '.$user_latlng_default[1] ?>" <?php echo (stripos($busqueda, 'marker') > 0) ? 'checked' : '' ?>>
            en una zona
          </label>
        </div>
        <input type="hidden" value="<?php echo (isset($user_lat) && isset($user_lng)) ? $user_lat.' '.$user_lng : '' ?>" name="user_latlng" id="user_latlng">
      </div>
    </div>

    <div class="col-xs-12">
      <div class="form-group">
        <h3 class="pull-left">
          ¿Qué especie?
          <a href="#" id="borrar_especie_id">
            <i class="fas fa-trash"></i>
          </a>
        </h3>
        <select class="form-control input-lg selectpicker" data-style="btn-default" name="especie_id" id="especie_id" data-live-search="true">
          <option value="0">Todas</option>
          <?php
          // Consulto especies y cantidad
          $especies_query = "SELECT
            e.id as especie_id, e.nombre_cientifico, e.nombre_comun
            FROM t_registros i, t_especies AS e
            WHERE i.especie_id = e.id
            GROUP BY e.id
            ORDER BY e.nombre_cientifico";

          $especies_results  = GetRS($especies_query);

          // Armo el array con los individuos
          while ($especies_row = mysqli_fetch_array($especies_results)) {
            if (isset($i)) {
              $i++;
            } else {
              $i = 1;
            }

              $lista_NCIE    = $especies_row['nombre_cientifico'];
              $lista_NCOM    = $especies_row['nombre_comun'];
              $lista_ID    = $especies_row['especie_id'];
              //$lista_CANT    = $especies_row['CANT'];
            $selected = '';
            if ($especie_id_busqueda===$lista_ID) {
              $selected = ' selected';

              // Me guardo la variable para cambiar la URL
              $especie_URL = sanear_string($lista_NCIE);
              $especie_URL = strtolower(str_replace(" ", "-", $especie_URL));
              $especie_URL = "./" . $especie_URL;
            }

            // ver especie ID
            if (isset($_SESSION["ver_especie_id"])) {
              $voluntario_especie_id = $lista_ID . " - ";
            }

            echo '<option value="'.$lista_ID.'" '.$selected.' data-content="
                <div>';

            if (isset($voluntario_especie_id)) {
              echo $voluntario_especie_id;
            }

            echo $lista_NCIE .
                  '<small class=\'muted text-muted\'> '
                    . $lista_NCOM.
                  '</small>
                </div>
              ">'
              . $lista_NCIE .
              '</option>
            ';
          }
          ?>
        </select>
      </div>
    </div>

    <div class="col-xs-12 <?php // echo $masFiltrosCss; ?>" id="mas-filtros">
      <div class="form-group">
        <h3>Sabores</h3>
        <label for="user_sabores">
          <input type="checkbox" name="user_sabores" id="user_sabores" value="1" <?php echo ((isset($user_sabores)) && ($user_sabores > 0)) ? 'checked' : '' ?>>
          frutales y medicinales <!-- <span class="label label-warning">beta</span> -->
        </label>
      </div>

      <div class="form-group">
        <!--<h3>Origen</h3>
        <div class="radio">
          <label>
            <input type="radio" id="rorigen-nativas" name="user_origen" value="Nativo/Autóctono" <?php echo (stripos($busqueda, 'Nativo') > 0) ? 'checked' : '' ?>>
            nativas
          </label>
          <label>
            <input type="radio" id="rorigen-exoticas" name="user_origen" value="Exótico" <?php echo (stripos($busqueda, 'Exótico') > 0) ? 'checked' : '' ?>>
            exóticas
          </label>
          <a href="#" id="borrar_origen">
            <i class="fas fa-trash"></i>
          </a>
        </div>-->

        <div class="regiones">

          <h3 class="pull-left">
            Región de origen
            <a href="#" id="origen" data-toggle="tooltip" data-placement="top" title="Por el momento el mapa permite filtrar especies para las distintas regiones de origen sólo dentro de Argentina, ¡esperamos ampliar esta sección pronto!">
              <i class="fas fa-question-circle"></i>
            </a>
          </h3>

          <label for="borigen_pampeana"> <input type="checkbox" name="borigen_pampeana" id="borigen_pampeana" value="1" <?php echo ($borigen_pampeana > 0) ? 'checked' : '' ?>>
            Pampeana
          </label>
          <label for="borigen_nea"> <input type="checkbox" name="borigen_nea" id="borigen_nea" value="1"  <?php echo ($borigen_nea > 0) ? 'checked' : '' ?>>
            NEA
          </label>
          <label for="borigen_noa"> <input type="checkbox" name="borigen_noa" id="borigen_noa" value="1"  <?php echo ($borigen_noa > 0) ? 'checked' : '' ?>>
            NOA
          </label>
          <label for="borigen_cuyana"> <input type="checkbox" name="borigen_cuyana" id="borigen_cuyana" value="1"  <?php echo ($borigen_cuyana > 0) ? 'checked' : '' ?>>
            Cuyana
          </label>
          <label for="borigen_patagonica"> <input type="checkbox" name="borigen_patagonica" id="borigen_patagonica" value="1"  <?php echo ($borigen_patagonica > 0) ? 'checked' : '' ?>>
            Patagónica
          </label>
        </div>
      </div>
    </div>
    <!--<div class="col-xs-12" id="mas-filtros-btn-container">
      <btn class="btn btn-default mas-filtros">
        <?php echo ($masFiltrosCss == 'oculto') ? "mostrar" : "ocultar" ?> filtros
      </btn>
    </div>-->
  </div>
  <input name="Buscar" type="submit" value="Buscar" class="btn btn-primary btn-lg btn-block">
</form>
