<html>
<head>
<script type="text/javascript">

//busca caracteres que no sean espacio en blanco en una cadena

function vacio(q) {
	for ( i = 0; i < q.length; i++ ) {
		if ( q.charAt(i) != " " ) {
			return true
		}
	}
	return false
}
//Inicio de la Validacion
function Valida(F) {
	if( vacio(F.host.value) == false ) {
		alert("Introduzca nombre o direccion del Host")
		return false
	} 
	if( vacio(F.user.value) == false ) {
		alert("Introduzca nombre de usuario")
		return false
	}
	else
	if( vacio(F.pass.value) == false ) {
		alert("Introduzca la contraseña")
		return false
	}
	else
	if( vacio(F.dbname.value) == false ) {
		alert("Introduzca nombre de la Base de datos")
		return false
	}
	else			 
	{
		return true;

	}
	
}
</script>

</head>
<?php
session_start();
//Variables de conexion
if (isset($_POST['dumpear'])){

	mkdir('tmp');

	$_SESSION['dbhost'] = $_POST['host'];
	$_SESSION['dbuser'] = $_POST['user'];
	$_SESSION['dbpassword'] = $_POST['pass'];
	$_SESSION['dbname'] = $_POST['dbname'];

	//Conexion
	$db = mysql_connect($_SESSION['dbhost'], $_SESSION['dbuser'], $_SESSION['dbpassword']) or die("Connection Error: " . mysql_error());
	mysql_select_db($_SESSION['dbname']) or die("Error al conectar a la base de datos.");

	//Obtener todas las tablas de la Base
	$Sql ="SHOW TABLES";
	$result = mysql_query( $Sql ) or die("No se puede ejecutar la consulta: ".mysql_error());

	$i=0;
	//Obtener los nombres de las tablas
	while($Rs = mysql_fetch_array($result)){
		$tablas[] = $Rs[0];
	}
	
	$tam = count($tablas);
	$saltos = array("\r\n", "\n", "\r");
	while($i<$tam){
		
		$Sql3 = "SELECT k.column_name 
		FROM information_schema.table_constraints t 
		JOIN information_schema.key_column_usage k 
		USING(constraint_name,table_schema,table_name) 
		WHERE t.constraint_type='PRIMARY KEY' AND t.table_schema='".$_SESSION['dbname']."' AND t.table_name='".$tablas[$i]."'";
		$llave = mysql_query( $Sql3 ) or die ("No se puede ejecutar la tercer consulta: ".mysql_error());
		
		
		$ids = array();
		
		while($id=mysql_fetch_row($llave)){
			$ids[] = $id[0];
		}
		$columnas = implode(", ", $ids);
		
		
		
		$archivo= "tmp/".$tablas[$i].".old.txt";
		$fp=fopen($archivo,"a");
		if(isset($id[0])){
			$Sql2 = "select * from $tablas[$i] order by ".$id[0];			
		}else{
			$Sql2 = "select * from $tablas[$i]";
		}
		$filas = mysql_query( $Sql2 ) or die("INo se puede ejecutar la segunda consulta: ".mysql_error()."<br /> Tabla:".$tablas[$i]);
		while($Rs2 = mysql_fetch_row($filas)){
			for($j=0; $j<count($Rs2); $j++){
				if($j==(count($Rs2)-1)){
					fwrite($fp, str_replace($saltos, " ", $Rs2[$j])."\n", 1024);	
				}else{
					fwrite($fp, str_replace($saltos, " ", $Rs2[$j])."\t", 1024);
				}
			}
		}
		fclose($fp);
		
		
		
		$archivo2 ="tmp/".$tablas[$i].".keys.old.txt";
		$fp2=fopen($archivo2,"a");
		
		if($columnas<>""){
			$Sql4 = "SELECT $columnas from $tablas[$i]";
			$query = mysql_query( $Sql4 ) or die ("1No se puede ejecutar la cuarta consulta: ".mysql_error());
			
			while($llaves = mysql_fetch_row($query)){
				fwrite($fp2, trim(implode("\t", $llaves))."\n", 1024);	
			}	
		}
		
		else{
			$Sql4 = "SELECT * from $tablas[$i]";
			$query = mysql_query( $Sql4 ) or die ("2No se puede ejecutar la cuarta consulta: ".mysql_error());
			while($llaves = mysql_fetch_row($query)){
				$sin  = $llaves[0];
				fwrite($fp2, trim($sin)."\n", 1024);
			}
		}
		

		fclose($fp2);
		
		$i++;
	}

	header('Location: paso2.php');

}else{

	?>

	<form method="POST" action="index.php" onSubmit="return Valida(this)">
	Nombre o Direcci&oacute;n del Host<input type="text" name="host" /><br />
	Usuario<input type="text" name="user" /><br />
	Contrase&ntilde;a<input type="password" name="pass" /><br />
	Base de Datos<input type="text" name="dbname" /><br />
	<input type="submit" name="dumpear" value="Dumpear!!"/>

	</form>
	<?php } ?>