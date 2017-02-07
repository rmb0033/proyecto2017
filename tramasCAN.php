<?php

/*
Date:	11/03/15
Author:	KIKE
URL:	n/a

Consulta de la lista de ficheros con las trazas can
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
		$pos=strpos($elemento,"Data");
		if($pos === false){
		}
		else{
			echo date("F d Y H:i:s",filemtime("./Datalogger/".$elemento)).espacios(2);
			$sz=filesize("./Datalogger/".$elemento);
			echo sprintf('%1$08d',$sz).espacios(2);
			echo '<a href="./Datalogger/'.$elemento.'">'.$elemento.'</a><br>';
		}
	}

	$dir=opendir("./Datalogger/REPOS");
	while($elemento = readdir($dir)){
		$pos=strpos($elemento,"Data");
		
		if($pos === false){
		}
		else{
			$store[$sum] = $elemento;
			$sum++;
			
		}
	}
	echo '<h3>Repository</h3>';
	natcasesort($store);
	$store=array_reverse($store);
	foreach ($store as $value) {
		echo date("F d Y H:i:s",filemtime("./Datalogger/REPOS/".$value)).espacios(2);
		$sz=filesize("./Datalogger/REPOS/".$elemento);
		echo sprintf('%1$08d ',$sz).espacios(2);
		echo '<a href="./Datalogger/REPOS/'.$value.'">'.$value.'</a><br>';
	}
}
else{
	header("Location:404.php");
}


?>
