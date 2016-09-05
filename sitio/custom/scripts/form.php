<form action="<?php echo $APP_URL; ?>/index.php#mapa" method="post" id="busca_arboles">
	<div class="row">
		<div class="col-xs-12">
			<div class="form-group">
				<h3>¿Dónde?</h3>
				<div class="radio"> 
					<label>
						<input type="radio" id="rdonde-ciudad" name="rdonde" value="0" <?php if (stripos($busqueda,'marker') == 0) echo 'checked' ?>  />
						en toda la ciudad </label>
					<label>
						<input type="radio" id="rdonde-mapa" name="rdonde" value="<? echo $user_latlng_default[0].' '.$user_latlng_default[1] ?>"  <?php if (stripos($busqueda,'marker') > 0) echo 'checked' ?>  />
						marcar en el mapa </label>
				</div>
				<input type="hidden" value="<?php echo($user_lat.' '.$user_lng); ?>" name="user_latlng" id="user_latlng">
			</div>
		</div>

		<div class="col-xs-12">

			<div class="form-group">
				<h3 class="pull-left">¿Qué especie?</h3>
				<a href="#" id="borrar_id_especie"><i class="fa fa-trash-o"></i></a>
				
				<select class="form-control input-lg selectpicker" data-style="btn-default" name="id_especie" id="id_especie" data-live-search="true">
					<option value="0">Todas</option>
					<?php
						// Consulto especies y cantidad
						$especies_query = "SELECT count(i.id_especie) as CANT,
						e.id_especie, e.NOMBRE_CIE, e.NOMBRE_COM
									FROM individuos AS i, especies AS e
									WHERE i.id_especie = e.id_especie
									GROUP BY e.id_especie
									ORDER BY e.NOMBRE_CIE";
						
						// Consulto especies sin cantidad
						/*$especies_query = "SELECT 
						e.id_especie, e.NOMBRE_CIE, e.NOMBRE_COM
									FROM especies AS e
									ORDER BY e.NOMBRE_CIE";*/
						
						$especies_results	= GetRS($especies_query);
						
						// Armo el array con los individuos
						while ($especies_row = mysql_fetch_array($especies_results)) {
							$i++;
								
							$lista_NCIE		= $especies_row['NOMBRE_CIE'];
							$lista_NCOM		= $especies_row['NOMBRE_COM'];
							$lista_ID		= $especies_row['id_especie'];
							$lista_CANT		= $especies_row['CANT'];
							
							$selected = '';
							if ($id_especie_busqueda===$lista_ID) {
								$selected = ' selected';

								// Me guardo la variable para cambiar la URL
								$especie_URL = sanear_string($lista_NCIE);
								$especie_URL = strtolower(str_replace(" ", "-", $especie_URL));
								$especie_URL = "./" . $especie_URL;
							} 
							echo '<option value="'.$lista_ID.'" '.$selected.' data-content="
									<div class=\'pull-left\'>'
										. $lista_NCIE .
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
						}
						
					?>
				</select>
				
			</div>

		</div>
							

		<div class="col-xs-12 <?php echo $masFiltrosCss; ?>" id="mas-filtros">
			<div class="form-group">
				<h3>Sabores</h3>
				<label for="user_sabores"> <input type="checkbox" name="user_sabores" id="user_sabores" value="1"  <?php if ($user_sabores > 0) echo 'checked' ?> > frutales y medicinales <!-- <span class="label label-warning">beta</span> --></label>
			</div>

			<div class="form-group">
				<h3>Origen</h3>
				<div class="radio"> 
					<label>
						<input type="radio" id="rorigen-nativas" name="user_origen" value="Nativo/Autóctono" <?php if (stripos($busqueda,'Nativo') > 0) echo 'checked' ?>  />
						nativas </label>
					<label>
						<input type="radio" id="rorigen-exoticas" name="user_origen" value="Exótico" <?php if (stripos($busqueda,'Exótico') > 0) echo 'checked' ?>  />
						exóticas </label>
					<a href="#" id="borrar_origen"><i class="fa fa-trash-o"></i></a>
				</div>

				<div class="regiones">
					
					<h3>Región de origen</h3>
					<label for="borigen_pampeana"> <input type="checkbox" name="borigen_pampeana" id="borigen_pampeana" value="1"  <?php if ($borigen_pampeana > 0) echo 'checked' ?> > Pampeana </label>

					<label for="borigen_nea"> <input type="checkbox" name="borigen_nea" id="borigen_nea" value="1"  <?php if ($borigen_nea > 0) echo 'checked' ?> > NEA </label>

					<label for="borigen_noa"> <input type="checkbox" name="borigen_noa" id="borigen_noa" value="1"  <?php if ($borigen_noa > 0) echo 'checked' ?> > NOA </label>

					<label for="borigen_cuyana"> <input type="checkbox" name="borigen_cuyana" id="borigen_cuyana" value="1"  <?php if ($borigen_cuyana > 0) echo 'checked' ?> > Cuyana </label>

					<label for="borigen_patagonica"> <input type="checkbox" name="borigen_patagonica" id="borigen_patagonica" value="1"  <?php if ($borigen_patagonica > 0) echo 'checked' ?> > Patagónica </label>
				</div>
			</div>
		</div>


		<div class="col-xs-12" id="mas-filtros-btn-container">
			<a href="#" class="btn btn-default mas-filtros"><?php if ($masFiltrosCss == 'oculto') { echo "más"; }else{ echo "menos";} ?> filtros</a>
		</div>
	</div>

	<input name="Buscar" type="submit" value="Buscar" class="btn btn-primary btn-lg btn-block">
</form>