<?php

session_start();

include "Operaciones.php";

$_SESSION['dbhost'];
$_SESSION['dbuser'];
$_SESSION['dbpassword'];
$_SESSION['dbname'];

if (isset($_POST['iniciar'])){
	
	//Conexion
	$db = mysql_connect($_SESSION['dbhost'], $_SESSION['dbuser'], $_SESSION['dbpassword']) or die("Connection Error: " . mysql_error());
	mysql_select_db($_SESSION['dbname']) or die("Error al conectar a la base de datos.");

	//Obtener todas las tablas de la Base
	$Sql ="SHOW TABLES";
	$result = mysql_query( $Sql ) or die("No se puede ejecutar la consulta: ".mysql_error());
	
	//Obtener los nombres de las tablas
	while($Rs = mysql_fetch_array($result)){
		$tablas[] = $Rs[0];
	}
	
	for($i=0; $i<count($tablas); $i++){
		
		$tabla=$tablas[$i];	
		
		$obj = new Operaciones($tabla);
		
		$obj->getOldKeys();
		
		$obj->newDump();
		
		$obj->getNewKeys();
		
		$obj->buscaEliminados();
		
		$obj->buscaAgregados();
		
		$obj->buscaCambios();
		

	}
	
	//$obj->removeDirectory('tmp');
	session_destroy();
	
}else{
	?>

	<h1>Realiza los cambios y al terminar presiona el boton.</h1>
	<form method="POST" action="paso2.php">
	<input type="submit" name="iniciar" value="Bucar Cambios"/>
	</form>

	<?php } ?>