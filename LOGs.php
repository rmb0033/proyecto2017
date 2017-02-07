<?php

/*
Date:	11/03/15
Author:	KIKE
URL:	n/a

Consulta de la lista de ficheros con eventos
*/
function espacios($num){
	$esp='';
	for ($i=0; $i<=$num;$i++){ 
	    $esp=$esp.'&nbsp';
	}
	return $esp;
}
session_start();
if ($_SESSION["permiso"]>=0){
	echo '<h3>Workspace</h3>';
	$dir=opendir("./Datalogger");
	while($elemento = readdir($dir)){
		$pos=strpos($elemento,"LOG");
		
		if($pos === false){
		}
		else{
			$store[$sum] = $elemento;
			$sum++;
			
		}
	}
	natcasesort($store);
	$store=array_reverse($store);
	foreach ($store as $value) {
		echo date("F d Y H:i:s",filemtime("./Datalogger/".$value)).espacios(2);
		$sz=filesize("./Datalogger/".$value);
		echo sprintf('%1$08d',$sz).espacios(2);
		echo '<a href="./Datalogger/'.$value.'">'.$value.'</a><br>';
	}
}
else{
	header("Location:404.php");
}

?>
