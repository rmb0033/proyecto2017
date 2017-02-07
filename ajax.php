<?php
function escribir_WT(){
	$pipe_in='/tmp/tuberiaDataOUT';
	$pipe_out='/tmp/tuberiaDataIN';
	$orden='writeWT /var/www/tableW/fileW ]]]';
	$resp= shell_exec('/sbin/escribe_pipe '.$pipe_in.' '.$pipe_out.' '.$orden);
	return $resp;
}
function consultar_STWriteWT(){ //consultar el estado de la grabacion de WT
	$pipe_in='/tmp/tuberiaDataOUT';
	$pipe_out='/tmp/tuberiaDataIN';
	$orden='STwriteWT ]]]';
	$resp= shell_exec('/sbin/escribe_pipe '.$pipe_in.' '.$pipe_out.' '.$orden);
	return $resp;
}

session_start();
if ($_SESSION["permiso"]>=0){

// get the q parameter from URL
$q = $_REQUEST["q"];

//echo escribir_WT();
switch($q){
	case 'writeWT':
		echo escribir_WT();
		break;
	case 'STwriteWT':
		echo consultar_STWriteWT();
	default :
		echo 'error';
}

}
else
header("Location:404.php");
?>

