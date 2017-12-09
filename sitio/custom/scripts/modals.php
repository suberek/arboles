<!-- Modal: ¿Qué es esto? -->
<div class="modal fade" id="que-es-esto" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4>¿Qué es esto?</h4>
			 </div>
			<div class="modal-body">
				<p>Este mapa surge gracias a la publicación de una información increíble: el <a href="http://data.buenosaires.gob.ar/dataset/censo-arbolado" target="_blank">censo del arbolado de la Ciudad de Buenos Aires.</a></p>
				<p>El objetivo principal de este sitio es simplificar el acceso a esta valiosa información esperando que colabore con el conocimiento y el cuidado de nuestro arbolado urbano.</p>
					
				<a href="http://martinsimonyan.com.ar/arboles-de-buenos-aires/" class="btn btn-default" target="_blank">Más información <i class="fa fa-caret-right fa-sm"></i></a>
			<hr>
				<h5>¿Con qué seguir?</h5>

				<p>Podés dejar tu opinión y tus idas para seguir mejorando esta herramienta. Estas son algunas de las ideas que están en curso:</p>
					<ul>
						<li>permitir seleccionar el radio de búsqueda</li>
						<li>buscar por barrio o comuna</li>
						<li>incorporar otras fuentes y no tan sólo las del censo del gobierno</li>
						<li>incorporar a otras ciudades</li>
						<li>permitir interacción con usuarios para que carguen ejemplares, fotografías y reporten errores</li>
					</ul>
				
				<hr>
				<p class="text-center"><a href="https://github.com/suberek/arboles" target="_blank"><i class="fa fw fa-github"></i> Ver en GitHub</a></p>
				<p class="text-center"><a href="https://www.facebook.com/arboladomapa" target="_blank"><i class="fa fw fa-facebook"></i> Seguir en facebook</a></p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Gracias por leer</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal: seleccionar una especie -->
<div class="modal fade" id="respecies-una-modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-2"><i class="fa fa-exclamation-triangle fa-3x"></i></div>
					<div class="col-sm-10">
						<p>Escribí algunas letras en el cuadro de búsqueda y aparecerá un listado con las posibles especies.</p>
						<p><small>Podés buscar todas las especies mezcladas, pero antes debes limitar la zona marcando un punto en el mapa.</small></p>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal: marcar un punto -->
<div class="modal fade" id="respecies-todas-modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-2"><i class="fa fa-lightbulb-o fa-3x"></i></div>
					<div class="col-sm-10">
						<p>Opa, ¡esos son muchos árboles! Para buscar, empezá marcando un punto en el mapa.</p>
						<p><small>Consejo piola: Podés buscar en toda la ciudad si seleccionás alguna especie.</small></p>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal: o todas las especies o toda la ciudad -->
<div class="modal fade" id="rdonde-ciudad-modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-2"><i class="fa fa-map-marker fa-3x"></i></div>
					<div class="col-sm-10">Marcá un punto en el mapa para limitar la búsqueda <br>
						<small>Podés buscar en toda la ciudad seleccionado una especie.</small></div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal: ¡Buscando! -->
<div class="modal fade" id="empieza-busqueda" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-2"><i class="fa fa-search fa-3x"></i></div>
					<div class="col-sm-10">Empieza la búsqueda.<br>
					<small>(redoblantes: prrrrrrr... )</small></div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal: Formulario -->

<div class="modal fade" id="agregar-arbol" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm">
	<div class="modal-content">
	  <div class="modal-body">
		<div class="row">
			<iframe src="https://docs.google.com/forms/d/e/1FAIpQLSdT1-TNDDN7Gau_798r4EIMCqKLR58VHfsPpP6LrHUg7SEIXw/viewform?embedded=true" width="100%" height="500" frameborder="0" marginheight="0" marginwidth="0">Cargando...</iframe>
		</div>
		 
	   </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
	  </div>
	</div>
  </div>
</div>

<?php
if ($total_registros_censo === 0) { ?>
<!-- Modal: sin resultados -->
<div class="modal fade" id="sin-resultados" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm">
	<div class="modal-content">
	  <div class="modal-body">
		<div class="row">
			<div class="col-sm-2"><i class="fa fa-search fa-3x"></i></div>
			<div class="col-sm-10"><p>Tu búsqueda no arrojó resultados.</p>
				<p><small>Probá buscando la especie que te interesa en toda la ciudad, o cambiando la zona de búsqueda marcando otro lugar en el mapa.</small></p></div>
		</div>
		 
	   </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
	  </div>
	</div>
  </div>
</div>
<?php } ?>