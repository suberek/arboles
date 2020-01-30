<form action="<?php echo $APP_URL; ?>/index.php#mapa" method="post" id="busca_arboles">
	<div class="row">


		<div class="col-xs-12">
			<div class="form-group">
				<h3>¿Dónde?</h3>
				<div class="radio"> 
					<label>
						<input type="radio" id="rdonde-ciudad" name="rdonde" value="0" <?php if (stripos($busqueda,'marker') == 0) echo 'checked' ?>  />
						por localidad</label>
					<label>
						<input type="radio" id="rdonde-mapa" name="rdonde" value="<? echo $user_latlng_default[0].' '.$user_latlng_default[1] ?>"  <?php if (stripos($busqueda,'marker') > 0) echo 'checked' ?>  />
						marcar en el mapa </label>
				</div>
				<input type="hidden" value="<?php if ( ( isset($user_lat) ) && ( isset($user_lng) ) 
				 ) { echo($user_lat.' '.$user_lng); }  ?>" name="user_latlng" id="user_latlng">
			</div>
		</div>

		<div class="col-xs-12">

			<div class="form-group">
				
				<select class="form-control input-lg selectpicker" data-style="btn-default" name="localidad" id="localidad" data-live-search="true">
					
					<?php					
						//$localidad_busqueda = "CABA";
						$localidad_query = "SELECT
									DISTINCT localidad
									FROM t_registros
									ORDER BY localidad";
						
						$localidad_results	= GetRS($localidad_query);
						
						// Armo el array con las localidades
						while ($localidad_row = mysqli_fetch_array($localidad_results)) {
							if ( isset($i) ) {
								$i++;
							} else {
								$i = 1;
							}
							
								
							$lista_LOCL		= $localidad_row['localidad'];

							$selected = '';
							if ($localidad_busqueda===$lista_LOCL) {
								$selected = ' selected';

								// Me guardo la variable para cambiar la URL
								$localidad_URL = sanear_string($lista_LOCL);
								$localidad_URL = strtolower(str_replace(" ", "-", $localidad_URL));
								$localidad_URL = "./" . $localidad_URL;
							} 

							// ver localidad
							if ( isset($_SESSION["ver_localidad"]) ) {
								$voluntario_localidad = $lista_LOCL . " - ";
							}

							
							echo '<option value="'.$lista_LOCL.'" '.$selected.' data-content="
									<div>';

									if( isset($voluntario_localidad) ) {
										echo $voluntario_localidad;
									}

							echo $lista_LOCL .
										'
									</div>
								">'
								. $lista_LOCL .
								'</option>
							';


						}
						
					?>
				</select>
				
			</div>

		</div>


		<div class="col-xs-12">
			<div class="form-group">
				<h3 class="pull-left">¿Qué especie? <a href="#" id="borrar_especie_id"><i class="fa fa-trash-o"></i></a></h3>
				
				
				<select class="form-control input-lg selectpicker" data-style="btn-default" name="especie_id" id="especie_id" data-live-search="true">
					<option value="0">Todas</option>
					<?php
						// Consulto especies y cantidad
						/*$especies_query = "SELECT COUNT(i.especie_id) as CANT,
						e.id as especie_id, e.nombre_cientifico, e.nombre_comun
									FROM ($vw_arboles_actualizaciones) i, t_especies AS e
									WHERE i.especie_id = e.id
									GROUP BY e.id
									ORDER BY e.nombre_cientifico";*/

						$especies_query = "SELECT
									e.id as especie_id, e.nombre_cientifico, e.nombre_comun
									FROM t_registros i, t_especies AS e
									WHERE i.especie_id = e.id
									GROUP BY e.id
									ORDER BY e.nombre_cientifico";
						
						$especies_results	= GetRS($especies_query);
						
						// Armo el array con los individuos
						while ($especies_row = mysqli_fetch_array($especies_results)) {
							if ( isset($i) ) {
								$i++;
							} else {
								$i = 1;
							}
							
								
							$lista_NCIE		= $especies_row['nombre_cientifico'];
							$lista_NCOM		= $especies_row['nombre_comun'];
							$lista_ID		= $especies_row['especie_id'];
							//$lista_CANT		= $especies_row['CANT'];
							
							$selected = '';
							if ($especie_id_busqueda===$lista_ID) {
								$selected = ' selected';

								// Me guardo la variable para cambiar la URL
								$especie_URL = sanear_string($lista_NCIE);
								$especie_URL = strtolower(str_replace(" ", "-", $especie_URL));
								$especie_URL = "./" . $especie_URL;
							} 

							// ver especie ID
							if ( isset($_SESSION["ver_especie_id"]) ) {
								$voluntario_especie_id = $lista_ID . " - ";
							}

							/* Echo con cantidades

							echo '<option value="'.$lista_ID.'" '.$selected.' data-content="
									<div class=\'pull-left\'>'
										. $voluntario_especie_id . $lista_NCIE .
										'<small class=\'muted text-muted\'> '
											. $lista_NCOM.
										'</small>
									</div>
									<div class=\'label label-warning pull-right\'>'
										.$lista_CANT.
									'</div>
									<div class=\'clearfix\'> </div>
								">'
								. $lista_NCIE .
								'</option>
							';

							Echo sin cantidades
							*/
							echo '<option value="'.$lista_ID.'" '.$selected.' data-content="
									<div>';

									if( isset($voluntario_especie_id) ) {
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
		
			<div class="form-group">
				<h3>Sabores</h3>
				<label for="user_sabores"> <input type="checkbox" name="user_sabores" id="user_sabores" value="1"  <?php if ( (isset($user_sabores)) && ($user_sabores > 0))  echo 'checked' ?> > frutales y medicinales <!-- <span class="label label-warning">beta</span> --></label>
			</div>

			<div class="form-group">

				<h3>Región de origen</h3>

				<div class="regiones">
					
					
					<label for="borigen_pampeana"> <input type="checkbox" name="borigen_pampeana" id="borigen_pampeana" value="1"  <?php if ($borigen_pampeana > 0) echo 'checked' ?> > Pampeana </label>

					<label for="borigen_nea"> <input type="checkbox" name="borigen_nea" id="borigen_nea" value="1"  <?php if ($borigen_nea > 0) echo 'checked' ?> > Noreste argentino </label>

					<label for="borigen_noa"> <input type="checkbox" name="borigen_noa" id="borigen_noa" value="1"  <?php if ($borigen_noa > 0) echo 'checked' ?> > Noroeste argentino </label>

					<label for="borigen_cuyana"> <input type="checkbox" name="borigen_cuyana" id="borigen_cuyana" value="1"  <?php if ($borigen_cuyana > 0) echo 'checked' ?> > Cuyana </label>

					<label for="borigen_patagonica"> <input type="checkbox" name="borigen_patagonica" id="borigen_patagonica" value="1"  <?php if ($borigen_patagonica > 0) echo 'checked' ?> > Patagónica </label>
				</div>
			</div>
		</div>


		
	</div>

	<input name="Buscar" type="submit" value="Buscar" class="btn btn-primary btn-lg btn-block">
</form>
