<?php

$_SESSION['dbhost'];
$_SESSION['dbuser'];
$_SESSION['dbpassword'];
$_SESSION['dbname'];

class Operaciones{

	private $nomtabla;
	private $keys= array();
	private $keys2= array();
	
	public function __construct($tabla){
		
		$this->nomtabla = $tabla;
		
	}
	
	//Obtener las claves principales del Dump
	public function getOldKeys (){
		$oldtabla=$this->nomtabla;
		$archivo= "tmp/".$oldtabla.".keys.old.txt";
		//Abrir Dump en solo Lectura
		$fp=fopen($archivo,"r");
		$linea=trim(fgets($fp, 1024));
		while($linea!=NULL){
			$keys[]=$linea;
			$linea=fgets($fp,1024);
		}
		fclose($fp);
		if(isset($keys)){
			$this->keys=$keys;	
		}
	}
	
	public function newDump(){
		
		$tabla = $this->nomtabla;
		$saltos = array("\r\n", "\n", "\r");
		
		$Sql3 = "SELECT k.column_name 
		FROM information_schema.table_constraints t 
		JOIN information_schema.key_column_usage k 
		USING(constraint_name,table_schema,table_name) 
		WHERE t.constraint_type='PRIMARY KEY' AND t.table_schema='".$_SESSION['dbname']."' AND t.table_name='".$tabla."'";
		$llave = mysql_query( $Sql3 ) or die ("No se puede ejecutar la tercer consulta: ".mysql_error());
		
		
		
		
		$ids = array();
		
		while($id=mysql_fetch_row($llave)){
			$ids[] = $id[0];
		}
		$columnas = implode(", ", $ids);
		
		
		
		$archivo= "tmp/".$tabla.".new.txt";
		$fp=fopen($archivo,"a");
		//Obtener los campos de la tabla y guardarlos en un archivo de texto
		if(isset($id[0])){
			$Sql2 = "select * from $tabla order by ".$id[0];			
		}else{
			$Sql2 = "select * from $tabla";
		}
		$filas = mysql_query( $Sql2 ) or die("ONo se puede ejecutar la segunda consulta: ".mysql_error());
		while($Rs2 = mysql_fetch_row($filas)){
			for($j=0; $j<count($Rs2); $j++){
				if($j==0){
					fwrite($fp, str_replace($saltos, " ", $Rs2[$j])."\t", 1024);
				}else
				if($j==(count($Rs2)-1)){
					fwrite($fp, str_replace($saltos, " ", $Rs2[$j])."\n", 1024);	
				}else{
					fwrite($fp, str_replace($saltos, " ", $Rs2[$j])."\t", 1024);
				}
			}
		}
		fclose($fp);
		
		$archivo2 ="tmp/".$tabla.".keys.new.txt";
		$fp2=fopen($archivo2,"a");
		
		if($columnas<>""){
			$Sql4 = "SELECT ".$columnas." from $tabla";
			$query = mysql_query( $Sql4 ) or die ("1No se puede ejecutar la cuarta consulta: ".mysql_error());
			
			while($llaves = mysql_fetch_row($query)){
				fwrite($fp2, trim(implode("\t", $llaves))."\n", 1024);	
			}	
		}else{
			$Sql4 = "SELECT * from $tabla";
			$query = mysql_query( $Sql4 ) or die ("2No se puede ejecutar la cuarta consulta: ".mysql_error());
			while($llaves = mysql_fetch_row($query)){
				$sin  = $llaves[0];
				fwrite($fp2, trim($sin)."\n", 1024);
			}
		}

		
		fclose($fp2);
	}
	
	public function getNewKeys (){
		$newtabla=$this->nomtabla;
		$archivo= "tmp/".$newtabla.".keys.new.txt";
		//Abrir Dump en solo Lectura
		$fp=fopen($archivo,"r");
		$linea=trim(fgets($fp, 1024));
		while($linea!=NULL){
			$keys2[]=$linea;
			$linea=fgets($fp,1024);
		}
		fclose($fp);
		if(isset($keys2)){
			$this->keys2=$keys2;	
		}
	}
	
	public function buscaEliminados(){
		$keys=$this->keys;
		$keys2=$this->keys2;
		
		for($i=0; $i<count($keys); $i++){
			if(in_array($keys[$i], $keys2, FALSE)){
				
			}else{
				echo "La Clave: ".$keys[$i]." de la tabla ".$this->nomtabla." ha sido eliminada<br /><br />";
			}
		}
		
	}

	public function buscaAgregados(){
		$keys=$this->keys;
		$keys2=$this->keys2;
		for($i=0; $i<count($keys2); $i++){
			if(in_array($keys2[$i], $keys, FALSE)){
				
			}else{
				echo "La Clave: ".$keys2[$i]." de la tabla ".$this->nomtabla." ha sido agregada<br /><br />";
			}
		}
		
	}
	
	public function buscaCambios(){
		$tabla=$this->nomtabla;
		$original = array();
		$nuevo = array();
		$keys=$this->keys;
		$keys2=$this->keys2;
		
		//Almacenar en un arreglo los registros Originales
		$archivo= "tmp/".$tabla.".old.txt";
		$fp=fopen($archivo,"r");
		$linea=trim(fgets($fp, 1024));
		while($linea!=NULL){
			$original[]=trim($linea);
			$linea=fgets($fp,1024);
		}
		fclose($fp);
		
		$archivo2= "tmp/".$tabla.".new.txt";
		$fp2=fopen($archivo2,"r");
		$linea2=trim(fgets($fp2, 1024));
		while($linea2!=NULL){
			$nuevo[]=trim($linea2);
			$linea2=fgets($fp2,1024);
		}
		fclose($fp2);
		
		for($i=0; $i<count($keys); $i++){
			$line = array_search($keys[$i],$keys2, TRUE );
			if(in_array($keys[$i], $keys2, TRUE)){
				
				if(strcmp($original[$i], $nuevo[$line])<>0){
					echo("La Clave: ".$keys[$i]." de la tabla ".$tabla." ha sido modificada<br />");
					echo("Original: ".$original[$i]."<br />");
					echo("Nuevo: ".$nuevo[$line]."<br /><br />");
					

				}
				
			}
		}
		
	}
	
	function removeDirectory($path){
		$path = rtrim( strval( $path ), '/' ) ;

		$d = dir( $path );

		if( ! $d )
		return false;

		while ( false !== ($current = $d->read()) )
		{
			if( $current === '.' || $current === '..')
			continue;

			$file = $d->path . '/' . $current;

			if( is_dir($file) )
			removeDirectory($file);

			if( is_file($file) )
			unlink($file);
		}

		rmdir( $d->path );
		$d->close();
		return true;
	}
}
?>