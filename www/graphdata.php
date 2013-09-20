<?php
setlocale(LC_NUMERIC, 'en_US.utf8');

if(empty($_GET['start']) || empty($_GET['end'])){
	$start = new DateTime('yesterday');
	$end = new DateTime('tomorrow midnight');
}
else {
	$start = new DateTime($_GET['start']);
	$end = new DateTime($_GET['end']);
}

$con=mysqli_connect("localhost","","","weather");
if ($con->connect_errno) 
{ 
	echo "Filed to connect to Database: " . $con->connect_error;
	exit();
}

//create SQL querry
$result = mysqli_query($con,
 "SELECT Tmp_110S_5ft_Avg AS Temp,
	ROUND(RH_RH5_5ft_Avg) AS Humidity,
	BP_BP20_5ft_Avg AS Pressure,
	WS_C1_10m_Prim_Avg AS WindSpeed
	FROM WindFarm
	WHERE `TIMESTAMP` BETWEEN '" . $start->format('Y-m-d') . "' AND '" . $end->format('Y-m-d') . 
	"' ORDER BY TIMESTAMP");

if(!$result) {
	echo "[]";
	exit();
}

while($row = $result->fetch_assoc()){
	$temps[] = round(floatval($row['Temp'])*9.0/5.0+32, 2);
	$hum[] = round(floatval($row['Humidity']), 2);
	$pres[] = round(floatval($row['Pressure'])*0.295299801, 2);
	$wind[] = round(floatval($row['WindSpeed'])*2.2369, 2);
}

if(empty($temps)){
	echo "[]";
	exit();
}

//Get the start time DATE_FORMAT(`TIMESTAMP`, '%c/%d/%Y %r CST')
$result = mysqli_query($con,
 "SELECT UNIX_TIMESTAMP(`TIMESTAMP`) AS TIMESTAMP FROM WindFarm
  WHERE `TIMESTAMP` BETWEEN '" . $start->format('Y-m-d') . "' AND '" . $end->format('Y-m-d') .
	"' ORDER BY TIMESTAMP LIMIT 1");
	
$firstRow = $result->fetch_assoc();

$data = array('Start' => intval($firstRow['TIMESTAMP'])*1000-18000000,
	'Temp' => $temps, 'Humidity' => $hum,
	'Pressure' => $pres, 'WindSpeed' => $wind);

echo json_encode($data);

mysqli_close($con);

?>