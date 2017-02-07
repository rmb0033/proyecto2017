
<head>
	<title>ASTI DATALOGGER v1.4</title>
	<link rel="stylesheet" href="css/style.css">
</head>
<body>
<script>
var myVar;
var estWriteWT;
var contErr=0;
function prueba(){
    estWriteWT=0;
	contErr=0;
	manWriteWT();
    myVar = setInterval(manWriteWT, 1000);
}
function manWriteWT() {
    switch(estWriteWT){
	   case 0: //antes de pedir la grabacion
			estWriteWT=1;
			var str='writeWT';
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					var aux=xmlhttp.responseText.split("|");
					if (aux[0]=="OK"){
						estWriteWT=2;
					}
					else{
						estWriteWT=4;
						document.getElementById("txtHint").innerHTML = aux[0];
					}
				}
			}
			xmlhttp.open("GET", "ajax.php?q=" + str, true);
			xmlhttp.send();
			
	        break;
	   case 1: //esperando respuesta
			document.getElementById("txtHint").innerHTML = '0';
			if(contErr++ > 5){
				estWriteWT=4;
				document.getElementById("txtHint").innerHTML = 'error';	
			}
			break;
	   case 2: //consultando estado grabacion
			estWriteWT=3;
			contErr=0;
			var str='STwriteWT';
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					document.getElementById("txtHint").innerHTML = xmlhttp.responseText;
					var aux = new String;
					aux=xmlhttp.responseText.split("|");
					var est=aux[0];
					var num=aux[1];
					switch (est){
						case "0":
							estWriteWT=4;
							document.getElementById("txtHint").innerHTML='ok';	
							break;
						case "1","2":
							estWriteWT=2;
							document.getElementById("txtHint").innerHTML = num + " segments";
							break;
							
						default:
							estWriteWT=4;
							document.getElementById("txtHint").innerHTML = 'error';	
							break;
					}
				}
			}
			xmlhttp.open("GET", "ajax.php?q=" + str, true);
			xmlhttp.send();
			break;
		case 3:
			if(contErr++ > 5){
				estWriteWT=4;
				document.getElementById("txtHint").innerHTML = 'error';	
			}
			break;
		case 4:
			clearInterval(myVar);
	       break;
	}  
}
</script>
<?php
/*
Date:	11/03/15
Author:	KIKE
URL:	n/a

Configuracion del dispositivo
*/
//Recepcion de parametros
if ($_POST["ModificarD"]) 		$dia=$_POST["dia"];
if ($_POST["ModificarH"]) 		$hora=$_POST["hora"];
if ($_POST["Reboot"])			$reboot=1;
if ($_POST["ModificarN"]) 		$numero=$_POST["numero"];
if ($_POST["ModificarAPCL"]) 	$APCL=$_POST["APCL"];
if ($_POST["ModificarAPAP"]) 	$APAP=$_POST["APAP"];
if ($_POST["ModificarSSID"]) 	$SSID=$_POST["SSID"];
if ($_POST["ModificarCLTKEY"]) 	$CLTKEY=$_POST["CLTKEY"];
if ($_POST["ModificarCLKEY"]) 	$CLKEY=$_POST["CLKEY"];
if ($_POST["ModificarCLTIP"]) 	$CLTIP=$_POST["CLTIP"];
if ($_POST["ModificarCLIP"]) 	$CLIP=$_POST["CLIP"];
if ($_POST["ModificarCLSM"]) 	$CLSM=$_POST["CLSM"];
if ($_POST["ModificarCLGW"]) 	$CLGW=$_POST["CLGW"];
if ($_POST["ModificarDNS"]) 	$DNS=$_POST["DNS"];
if ($_POST["ReadFileWT"]) 		$readWT=1;
if ($_POST["WriteFileWT"]) 		$writeWT=1;



//funciones
function alert($message){
	echo "<script type='text/javascript'>alert('$message');</script>";
}
function espacios($num){
	$esp='';
	for ($i=0; $i<=$num;$i++){ 
	    $esp=$esp.'&nbsp';
	}
	return $esp;
}
function pintar_status()
{

  echo '<h3> Status </h3>';
	$aux=shell_exec('/usr/sbin/iwconfig wlan0 | grep "ESSID:"');
  $a=strpos($aux,'"');
  $b=strlen($aux);
  $aux=substr($aux,$a+1,$b-$a);
  $b=strpos($aux,'"');
  $aux=substr($aux,0,$b);
  echo "<b>WLAN SSID: </b>";
  echo"$aux";
  //------------------------
  $aux=shell_exec('/sbin/ifconfig wlan0 | grep "inet addr"');
  $a=strpos($aux,":");
  $b=strlen($aux);
  $aux=substr($aux,$a+1,$b-$a);
  $b=strpos($aux," ");
  $aux=substr($aux,0,$b);
  echo"&nbsp <b>IP: </b>";
  echo"$aux";
  //------------------------
  $aux=shell_exec('/usr/sbin/iwconfig wlan0 | grep "Access Point"');
  if(strpos($aux,"Not-Associated")<0){
  $a=strpos($aux,"Access Point:");
  $b=strlen($aux);
  $aux=substr($aux,$a+strlen("Access Point:"),$b-($a+strlen("Access Point:")));
  }else{$aux="";}
  echo"&nbsp <b>Access Point: </b>";
  echo"$aux</p>";
  //------------------------
  
  $aux=shell_exec('/sbin/ifconfig eth0 | grep "inet addr"');
  $a=strpos($aux,":");
  $b=strlen($aux);
  $aux=substr($aux,$a+1,$b-$a);
  $b=strpos($aux," ");
  $aux=substr($aux,0,$b);
  echo "<b>LAN IP: </b>";
  echo"$aux</p>";
}
function pintar_num()
{
	$myfile = fopen("/etc/agv.num", "r");
	$num=fgets($myfile);
	fclose($myfile);
	echo '	<form method="post">';
	echo '		<p>AGV number';
	echo "		<input type='text' name='numero' value='$num' placeholder='$num' style='width: 50px;'>";
	echo '		<input type="submit" name="ModificarN" value="Modify"></p>';
	echo '	</form>';
}
function pintar_dia()
{
	$dia=shell_exec('date +%d-%m-%Y');
	$e=espacios(30);
	echo '<tr>';
	echo "	<td>Date</td>";
	echo "	<td><input type='text' name='dia' value='$dia' placeholder='$dia' style='width: 100px;'></td>";
	echo '	<td><input type="submit" name="ConsultarD" value="Query">';
	echo '	<input type="submit" name="ModificarD" value="Modify"></td>';
	echo '</tr>';
}
function pintar_hora()
{
	$hora=shell_exec('date +%H:%M:%S');
	$e=espacios(30);
	echo '<tr>';
	echo "	<td>Time";
	echo "	<td><input type='text' name='hora' value='$hora' size='3' placeholder='$hora' style='width: 100px;'></td>";
	echo '	<td><input type="submit" name="ConsultarH" value="Query">';
	echo '	    <input type="submit" name="ModificarH" value="Modify"></td>';
	echo '</tr>';
}
function pintar_reboot()
{
	echo '	<form method="post">';
	echo '		<input type="submit" name="Reboot" value="Reboot"></p>';
	echo '	</form>';
}
function pintar_modoWifi()
{
	$modo=shell_exec('cat /etc/mtxConfig | grep APCL: | cut -c6-');
	//----------------------
	$e=espacios(10);
	if ($modo==0){
		$sel0='selected';
		$sel1='';
	}else{
		$sel0='';
		$sel1='selected';
	}
	echo '<tr>';
	echo '	<td align="right">Mode</td>';
	echo "	<td><select name='APCL' style='width: 120px; height:30px'>";
	echo "			<option value='0' $sel0 >Access point</option>";
	echo "			<option value='1' $sel1 >Wifi client</option>";
	echo '		</select></td>';
	echo '	<td><input type="submit" name="ModificarAPCL" value="Modify"></td>';
	echo '</tr>';
	
	//----------------------
	if ($modo==1){
		pintar_SSID();
		pintar_tipoClave();
		pintar_tipoIP();
	}
	else{
		pintar_ApPermanente();
	}
}
function pintar_ApPermanente(){
	$apap=shell_exec('cat /etc/mtxConfig | grep APAP: | cut -c6-');
	if ($apap==0){
		$sel0='selected';
		$sel1='';
	}else{
		$sel0='';
		$sel1='selected';	
	}
	echo '<tr>';
	echo '	<td align="right">Permanently activated</td>';
	echo "	<td><select name='APAP' style='width: 120px; height:30px'>";
	echo "			<option value='0' $sel0 >No $e1</option>";
	echo "			<option value='1' $sel1 >Yes $e2</option>";
	echo '		</select></td>';
	echo '	<td><input type="submit" name="ModificarAPAP" value="Modify"></td>';
	echo '</tr>';
}

function pintar_SSID(){
	$ssid=shell_exec('cat /etc/mtxConfig | grep CLSSID: | cut -c8-');
	echo '<tr>';
	echo '	<td align="right">SSID</td>';
	echo "	<td><input type='text' name='SSID' value='$ssid' placeholder='$ssid' style='width: 120px;'></td>";
	echo '	<td><input type="submit" name="ModificarSSID" value="Modify"></td>';
	echo '</tr>';
}
function pintar_tipoClave(){
	$cltkey=shell_exec('cat /etc/mtxConfig | grep CLTKEY: | cut -c8-');
	if ($cltkey==0){
		$sel0='selected';
		$sel1='';
	}else{
		$sel0='';
		$sel1='selected';	
	}
	echo '<tr>';
	echo '	<td align="right">Key type</td>';
	echo "	<td><select name='CLTKEY' style='width: 120px; height:30px'>";
	echo "			<option value='0' $sel0 >No key $e1</option>";
	echo "			<option value='1' $sel1 >WPA $e2</option>";
	echo '		</select></td>';
	echo '	<td><input type="submit" name="ModificarCLTKEY" value="Modify"></td>';
	echo '</tr>';
	if($cltkey==1){
		pintar_clave();
	}
}
function pintar_clave(){
	$CLKEY=shell_exec('cat /etc/mtxConfig | grep CLKEY: | cut -c7-');
	echo '<tr>';
	echo '	<td align="right">Password</td>';
	echo "	<td><input type='text' name='CLKEY' value='$CLKEY' placeholder='$CLKEY' style='width: 120px;'></td>";
	echo '	<td><input type="submit" name="ModificarCLKEY" value="Modify"></td>';
	echo '</tr>';
}
function pintar_tipoIP(){
	$cltip=shell_exec('cat /etc/mtxConfig | grep CLTIP: | cut -c7-');
	if ($cltip==0){
		$sel0='selected';
		$sel1='';
	}else{
		$sel0='';
		$sel1='selected';	
	}
	echo '<tr>';
	echo '	<td align="right">Type of IP</td>';
	echo "	<td><select name='CLTIP' style='width: 120px; height:30px'>";
	echo "			<option value='0' $sel0 >Static</option>";
	echo "			<option value='1' $sel1 >Dynamic</option>";
	echo '		</select></td>';
	echo '	<td><input type="submit" name="ModificarCLTIP" value="Modify"></td>';
	echo '</tr>';
	
	if($cltip==0){
		pintar_IP();
		pintar_Mascara();
		pintar_Gateway();
	}
	
}

function pintar_IP(){
	$CLIP=shell_exec('cat /etc/mtxConfig | grep CLIP: | cut -c6-');
	$e=espacios(10);
	echo '<tr>';
	echo '	<td align="right">IP</td>';
	echo "	<td><input type='text' name='CLIP' value='$CLIP' placeholder='$CLIP' style='width: 120px;'></td>";
	echo '	<td><input type="submit" name="ModificarCLIP" value="Modify"></td>';
	echo '</tr>';
}
function pintar_Mascara(){
	$CLSM=shell_exec('cat /etc/mtxConfig | grep CLSM: | cut -c6-');
	$e=espacios(10);
	echo '<tr>';
	echo '	<td align="right">Subnet Mask</td>';
	echo "	<td><input type='text' name='CLSM' value='$CLSM' placeholder='$CLSM' style='width: 120px;'></td>";
	echo '	<td><input type="submit" name="ModificarCLSM" value="Modify"></td>';
	echo '</tr>';
}
function pintar_Gateway(){
	$CLGW=shell_exec('cat /etc/mtxConfig | grep CLGW: | cut -c6-');
	$e=espacios(10);
	echo '<tr>';
	echo '	<td align="right">Gateway</td>';
	echo "	<td><input type='text' name='CLGW' value='$CLGW' placeholder='$CLGW' style='width: 120px;'></td>";
	echo '	<td><input type="submit" name="ModificarCLGW" value="Modify"></td>';
	echo '</tr>';
}

function pintar_DNS()
{
	$myfile = fopen("/etc/resolv.conf", "r");
	$dns=fgets($myfile);
	fclose($myfile);
	echo '	<form method="post">';
	echo '		<p>DNS server';
	echo "		<input type='text' name='DNS' value='$dns' placeholder='$dns' style='width: 130px;'>";
	echo '		<input type="submit" name="ModificarDNS" value="Modify"></p>';
	echo '	</form>';
}
function pintar_file_readWT()
{
	if (file_exists('./tableR/fileR')) $line='<a href="./tableR/fileR">fileR</a>';
	else $line='none                  ';
	echo '	<form method="post">';
	echo '		<p>Last read file:';
	echo "		$line";
	echo '		<input type="submit" name="ReadFileWT" value="Read"></p>';
	echo '	</form>';
}
function pintar_file_writeWT()
{
	if (file_exists('./tableW/fileW')) $line='<a href="./tableW/fileW" onclick="prueba()">fileW</a>';
	else $line='none                  ';
	echo '<table>';
	echo '	<td>File to write:<td>';
	echo "	<td>$line<td>";
	//echo '		<input type="submit" name="WriteFileWT" value="Write" onclick="prueba()"></p>';
	echo '	<td><p><button name="WriteFileWT" onclick="prueba()"> Write</button>';
	echo '      <span id="txtHint"></span></p></td>';
	echo '	</table>';
	echo '<form></form>';
	
}

function cambiar_num($num)
{
	if (is_numeric($num)){
	if (($num<1000) &&($num>0)){
	    $num_s=sprintf("%'.03d\n", $num);
		$myfile = fopen("/etc/agv.num", "w");
		fwrite($myfile, $num_s);
		fclose($myfile);
	}
	else{
		alert('numero de AGV debe pertenecer al rango [1-999]');
	}
   }
   else{
		alert('numero de AGV debe pertenecer al rango [1-999]');
	}
}
function cambiar_dia($dia)
{
	if(strlen($dia)>10){
		alert("formato de fecha incorrecta");
		return;
	}
	$auxD=split('-',$dia);
	if(count($auxD)<>3){
		alert("formato de fecha incorrecta");
		return;
	}
	$d=$auxD[0];
	$m=$auxD[1];
	$a=$auxD[2];
	if (strlen($d)<>2 or strlen($m)<>2 or strlen($a)<>4){
		alert("formato de fecha incorrecta");
		return;
	}	
	if(!is_numeric($d) or !is_numeric($m) or !is_numeric($a)){
		alert("formato de fecha incorrecta");
		return;
	}
	if ($d<1 or $d>31 or $m <1 or $m > 12 or $a < 1){
		alert("formato de fecha incorrecta");
		return;
	}
	$cmd='date '.$a.'.'.$m.'.'.$d.'-'.shell_exec('date +%H:%M:%S');
	shell_exec($cmd);
	shell_exec('/sbin/hwclock -w'); //grabamos de forma definitiva la fecha
}
function cambiar_hora($hora)
{
	if(strlen($hora)>8){
		alert("formato de hora incorrecto1");
		return;
	}
	$auxH=split(':',$hora);
	if(count($auxH)<>3){
		alert("formato de hora incorrecto2");
		return;
	}
	$h=$auxH[0];
	$m=$auxH[1];
	$s=$auxH[2];
	if (strlen($h)<>2 or strlen($m)<>2 or strlen($s)<>2){
		alert("formato de hora incorrecto3");
		return;
	}	
	if(!is_numeric($h) or !is_numeric($m) or !is_numeric($s)){
		alert("formato de hora incorrecto4");
		return;
	}
	if ($h<0 or $h>23 or $m <0 or $m > 59 or $s < 0 or $s > 59){
		alert("formato de hora incorrecto5");
		return;
	}
	$fecha=shell_exec('date +%Y.%m.%d');
	if (strlen($fecha)>10){
		$fecha=substr($fecha,0,10);
	}
	$cmd ='date '.$fecha.'-'.$h.':'.$m.':'.$s;
	shell_exec($cmd);
	shell_exec('/sbin/hwclock -w'); //grabamos de forma definitiva la fecha
}
function cambiar_modoWifi($APCL){
	mod_mtxConfig('APCL:','APCL:'.$APCL);
}
function cambiar_apPermanente($APAP){
	mod_mtxConfig('APAP:','APAP:'.$APAP);
	if ($APAP==1)
		shell_exec('sh /scripts/conv2AP.sh');
	else 
		shell_exec('/usr/sbin/iwpriv wlan0 AP_BSS_STOP');
}
function cambiar_SSID($SSID){
	mod_mtxConfig('CLSSID:','CLSSID:'.$SSID);
	mod_lineaFichero('ssid=',"'    ssid=".'"'.$SSID.'"'."'",'/etc/wpa_supplicant.conf');
}
function cambiar_tipoClave($CLTKEY){
	mod_mtxConfig('CLTKEY:','CLTKEY:'.$CLTKEY);
}
function cambiar_Clave($CLKEY){
	$ssid=shell_exec('cat /etc/mtxConfig | grep CLSSID: | cut -c8-');
	$ret= shell_exec('echo '.$CLKEY.' | /sbin/wpa_passphrase '.$ssid.' | cut -c 1-3' );
	$x=strpos($ret,'#psk=');
	if ($x>0) $x1=strpos($ret,'psk=',$x+4);
	else $x1=strpos($ret,'psk=');
	$x2=strpos($ret,'}',$x1);
	
	$psk= substr($ret,$x1+4,$x2-($x1+4)-1);
	if ($psk <> ''){
		mod_mtxConfig('CLKEY:','CLKEY:'.$CLKEY);
		mod_lineaFichero('psk=','"    psk='.$psk.'"','/etc/wpa_supplicant.conf');
	}
}

function cambiar_tipoIP($CLTIP){
	mod_mtxConfig('CLTIP:','CLTIP:'.$CLTIP);
}
function cambiar_IP($CLIP){
	mod_mtxConfig('CLIP:','CLIP:'.$CLIP);
}
function cambiar_Mascara($CLSM){
	mod_mtxConfig('CLSM:','CLSM:'.$CLSM);
}
function cambiar_Gateway($CLGW){
	mod_mtxConfig('CLGW:','CLGW:'.$CLGW);
}
function cambiar_DNS($dns)
{
	$myfile = fopen("/etc/resolv.conf", "w");
	fwrite($myfile, $dns);
	fclose($myfile);
}
function escribir_WT(){
	$pipe_in='/tmp/tuberiaDataOUT';
	$pipe_out='/tmp/tuberiaDataIN';
	$orden='writeWT /var/www/tableW/fileW ]]]';
	$resp= shell_exec('/sbin/escribe_pipe '.$pipe_in.' '.$pipe_out.' '.$orden);
	//$resp= shell_exec("echo '".$orden."' > ".$pipe_out);
	
	echo $resp;
}
function leer_WT(){
	$pipe_in='/tmp/tuberiaDataOUT';
	$pipe_out='/tmp/tuberiaDataIN';
	$orden='readWT /var/www/tableR/fileR';
	$resp= shell_exec('/sbin/escribe_pipe '.$pipe_in.' '.$pipe_out.' '.$orden);
	echo $resp;
}
function mod_mtxConfig($strBus,$strCam){
	mod_lineaFichero($strBus,$strCam,'/etc/mtxConfig');
}
function mod_lineaFichero($strBus,$strCam,$fichero){
	$linea=shell_exec('cat '.$fichero.' | grep '.$strBus.' -n | cut -f1 -d":" | head -n1');
	$lhead=$linea-1;
	$ltotal=shell_exec('wc '.$fichero.' -l');
	$ltail=$ltotal-$linea;
	if ($lhead<0)$lhead=0;
	if ($ltail<0)$ltail=0;
	shell_exec('head '.$fichero.' -n'.$lhead.'> /tmp/f1.txt');
	if ($linea > 0)	shell_exec('echo '.$strCam.' > /tmp/f2.txt');
	shell_exec('tail '.$fichero.' -n'.$ltail.'> /tmp/f3.txt');
	shell_exec('cat /tmp/f1.txt /tmp/f2.txt /tmp/f3.txt > '.$fichero);
	//shell_exec('rm /tmp/f1.txt /tmp/f2.txt /tmp/f3.txt');
}


session_start();
if ($_SESSION["permiso"]>=1){
	if (isset($numero)){
		cambiar_num($numero);
		unset($numero);
	}
	if (isset($hora)){
		cambiar_hora($hora);
		unset($hora);
	}
	if (isset($dia)){
		cambiar_dia($dia);
		unset($dia);
	}
	if (isset($APCL)){
		cambiar_modoWifi($APCL);
		unset($APCL);
	}
	if (isset($APAP)){
		cambiar_apPermanente($APAP);
		unset($APAP);
	}
	if (isset($SSID)){
		cambiar_SSID($SSID);
		unset($SSID);
	}
	if (isset($CLTKEY)){
		cambiar_tipoClave($CLTKEY);
		unset($CLTKEY);
	}
	if (isset($CLKEY)){
		cambiar_Clave($CLKEY);
		unset($CLKEY);
	}
	if (isset($CLTIP)){
		cambiar_tipoIP($CLTIP);
		unset($CLTIP);
	}
	if (isset($CLIP)){
		cambiar_IP($CLIP);
		unset($CLIP);
	}
	if (isset($CLSM)){
		cambiar_Mascara($CLSM);
		unset($CLSM);
	}
	if (isset($CLGW)){
		cambiar_Gateway($CLGW);
		unset($CLGW);
	}
	if (isset($DNS)){
		cambiar_DNS($DNS);
		unset($DNS);
	}
	if (isset($reboot)){
		shell_exec('/sbin/reboot');
		unset($reboot);
	}
	if (isset($readWT)){
		leer_WT();
		unset($readWT);
		
	}
	/*if (isset($writeWT)){
		escribir_WT();
		unset($writeWT);
	}
	*/
	echo '<center>';
  pintar_status();
	pintar_num();
	echo '<h3> Actual date & time</h3>';
	echo '<form method="post"><table>';
	pintar_dia();
	pintar_hora();
	echo '</table></form>';
	echo '<h3> Wifi communication </h3>';
	echo '<form method="post"><table>';
	pintar_modoWifi();
	echo '</table></form>';
	echo '<h3> DNS</h3>';
	pintar_DNS();
	echo '<h3>Work Table</h3>';
	//pintar_file_readWT();
	pintar_file_writeWT();
	pintar_reboot();
	
	echo '</center>';
}
else{
	header("Location:404.php");
}
?>
</body>
