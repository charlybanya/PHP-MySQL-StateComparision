===================================
	PHP-MySQL-StateComparision
===================================
========================
		License
========================

Copyright © 2012 Rubén Schaffer Levine and Cristopher Carlos Mendoza Rojas.

“PHP-MySQL-StateComparision” is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation version 3 of the License.
“PHP-MySQL-StateComparision” is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details, see <http://www.gnu.org/licenses/>.


==============================
		Introducción
==============================

PHP-MySQL-StateComparision surge de la necesidad de identificar los cambios ocurridos
en una base de datos relacional en MySQL de 120 tablas, para esto se planteo la idea de
realizar una herramienta que facilitara el proceso.

===============================
		Funcionamiento
===============================

PHP-MySQL-StateComparision consta de 3 archivos:

	+ index.php
	+ Operaciones.php
	+ paso2.php
	
En 'index.php', se encuentra la rutina inicial, en la cual si la variable '$_POST' no esta definida, muestra
un formulario en el que el navegador solicita al usuario los datos de la Base de Datos, 
direccion del host, usuario y contraseña para iniciar sesion en el servidor y el nombre de la base de datos y
los envia en una solicitud POST al mismo archivo.

Si la Variable '$_POST' ya esta definida, obtiene los datos almacenados en el arreglo '$_POST[]', los almacena en variables
de sesion ('') para su posterior uso y crea una conexion a la base de datos.

Si la conexion es correcta, solicita los nombres de todas las tablas y almacena en un arreglo. En caso contrario
muestra un mensaje de error.

Con los datos del Arreglo y por medio de un ciclo obtiene las llaves principales y se almacenan en un archivo de texto
para su posterior uso, de igual manera se hace con todos los datos de las tablas.

Al terminar el ciclo (si ya no hay mas tablas que dumpear), se redirecciona el navegador a 'paso2.php'.

En 'paso2.php' si la variable '$_POST' no esta definida, muestra el mensaje "Realiza los cambios y al terminar presiona el boton."
y un boton que al presionarlo redirecciona el navegador al mismo archivo.

Si la variable '$_POST' ya esta definida, se crea una conexion a la base de datos, si la conexion es correcta, solicita los nombres 
de todas las tablas y almacena en un arreglo. En caso contrario muestra un mensaje de error.

Con los datos del arreglo y por medio de un ciclo que se repite tantas veces como datos tiene el arreglo, se crean objetos de la clase
'Operaciones' y se llama a los metodos correspondientes para obtener un nuevo dump de la base de datos (llaves y registros), y encontrar
los cambios realizados en la misma, al terminar destruye todos los archivos temporales creados y las variables de sesion.





