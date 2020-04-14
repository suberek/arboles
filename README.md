# Arbolado Urbano

🔗 http://www.arboladourbano.com/

# Descripción

Mapa/buscador de árboles. 🗺️ 🔍 🌲

# Utilidad

Por favor leé las secciones "[Objetivos](https://github.com/suberek/arboles/wiki/Espa%C3%B1ol#objetivos)" y "[Fundamentación](https://github.com/suberek/arboles/wiki/Espa%C3%B1ol#objetivos)".

# Cómo participar en el proyecto

Por favor leé las secciones "[Voluntariado](https://github.com/arboladourbano/arboles/wiki/Espa%C3%B1ol#voluntariado)" y "[Colaboradores](https://github.com/arboladourbano/arboles/wiki/Espa%C3%B1ol#colaboradores)".

# 🆘 Ayuda

Por favor leé la sección "[Contacto](https://github.com/arboladourbano/arboles/wiki/Espa%C3%B1ol#contacto)".

# 👨‍💻 🛠️ Mantenedores

* Martín Simonyan.
* Francisco Ferioli Marco.

# MIGRAR LA SIGUIENTE INFORMACIÓN A LA WIKI

# Antecedentes (sección "Antecedentes" o "Bibliografía")

**Internet**

http://mapa.buenosaires.gob.ar/ <br>
http://www.buenosaires.gob.ar/areas/med_ambiente/Arbolado/index.php?menu_id=20834&tipo=car <br>
http://arbolesciudad.com.ar/ <br>
http://unahormiga.com/2013/06/mapaverde-como-lo-hice/

# Cómo se desarrolló la base de datos (sección "Banco de datos")

> **NOTA:** la base de datos es de código cerrado, no está publicada y tampoco tenemos pensado publicarla a corto plazo. Sin embargo, podés descargar los datasets del directorio sitio/datos-abiertos o de nuestra cuenta de Google Drive.

1. Descargar un dataset, conservar la copia original y trabajar con un duplicado.

2. Notepad++:
- Codificación.
- Convertir a ANSI (no es necesario pero permite posteriormente la correcta visualización en CSVed).

3. CSVed:
- Filtrar por barrio y por calle.
- Eliminar columnas irrelevantes: nombre_familia, nombre_genero, nombre_cientifico, nombre_comun, tipo_follaje, origen (todas esas columnas estarán asociadas a id_especia).
- No se usarán estos datos: código de manzana, geometry.
- Referencias espaciales: se obtienen de las columnas latitud y longitud: coord_x, coord_y.

4. Agregar columna arbol_id con un valor 0 e incremento de 1.

5. Notepad++:
- Reemplazar los nombres de los barrios por IDs. El primer número del ID corresponde a la comuna y el segundo a un barrio diferente ordenado alfabéticamente. Por ejemplo:
  - AGRONOMIA	es 151: Comuna 15, barrio 1.
  - ALMAGRO		es 051: Comuna  5, barrio 1.
  - BOEDO	  	es 052: Comuna  5, barrio 2.
- UPDATE '1_individuos2' SET id_barrio=51 WHERE barrio LIKE 'ALMAGRO'

6. Renombrar columnas latitud por lat y longitud por lng, ya que así las toma el plugin "simple-csv-master".

7. Recordar volver a convertir a UTF-8 sin BOM.

8. CSV Splitter:
- Particionar en archivos de 20000 registros.
- **¡¡¡MEJOR!!! por medio de la consola mySQL escribir:** LOAD DATA LOCAL INFILE 'c:/arb.csv' INTO TABLE db_arbolado.1_individuos2 CHARACTER SET utf8 FIELDS TERMINATED BY ',' ENCLOSED BY '\"' IGNORE 1 LINES;

9. Crear una base de datos MySQL e importar todos los fragmentos.

10. Crear una tabla individuos, especies, generos, familias para luego relacionarlas mediante id.

11. Con Microsoft Excel obtener especies únicas eliminando duplicados.

12. Creación de campo coordenadas (mysql poit):
- Crear campo vacío.
- Actualizar la tabla para que vuelque los valores que están en campos separados: UPDATE 1_individuos SET `coordenadas`=  GeomFromText(CONCAT('POINT(',lat, ' ',lng,')'));

# Bibliografía (sección "Bibliografía")

**Internet**

http://data.buenosaires.gob.ar/dataset/censo-arbolado <br>
http://leafletjs.com/ <br>
http://mappinggis.com/2013/10/como-incluir-las-capas-de-google-en-leaflet/ <br>
http://matchingnotes.com/using-google-map-tiles-with-leaflet <br>
http://leaflet-extras.github.io/leaflet-providers/preview/index.html <br>
http://www.disenowebeficaz.es/blog/solucionar-error-al-importar-una-base-de-datos-mysql-con-utf8/ <br>
Custom Google Map<br>
http://czaplewski.wordpress.com/2012/05/14/custom-google-maps-in-leaflet/ <br>
http://gmaps-samples-v3.googlecode.com/svn/trunk/styledmaps/wizard/index.html <br>
http://howto-use-mysql-spatial-ext.blogspot.com.ar/ <br>
http://blog.habrador.com/2013/01/how-to-import-large-csv-file-into-mysql.html <br>
