<?php
setlocale(LC_NUMERIC, 'en_US.utf8');

if(empty($_GET['start']) || empty($_GET['end'])){
	$start = new DateTime('yesterday');
	$end = new DateTime('tomorrow midnight');
}
else {
	$start = new DateTime($_GET['start']);
	$end = new DateTime($_GET['end']);
	//add 1 day to include the day specified at the end
	$end->add(new DateInterval('P1D'));
}

//connect to mysql database
$con=mysqli_connect("localhost","","","weather");
if ($con->connect_errno) //check for error when connecting
{
	echo "Filed to connect to Database: " . $con->connect_error;
	exit();
}

$range = $start->diff($end);

//create SQL querry
if($range->days < 4.1)
{
 $result = mysqli_query($con,
  "SELECT UNIX_TIMESTAMP(TIMESTAMP) AS TS,
	 Tmp_110S_5ft_Avg AS Temp,
	 ROUND(RH_RH5_5ft_Avg) AS Humidity,
	 BP_BP20_5ft_Avg AS Pressure,
	 WS_C1_10m_Prim_Avg AS WindSpeed
	 FROM WindFarm
	 WHERE `TIMESTAMP` BETWEEN '" . $start->format('Y-m-d') . "' AND '" . $end->format('Y-m-d') . 
	 "' ORDER BY TIMESTAMP");
}
else
{
 //interval controls the plotted interval in minutes
 if($range->days < 8)
	$interval = 60;
 else if($range->days < 16)
  $interval = 120;
 else if ($range->days < 32)
	$interval = 240;
 else
  $interval = 360;
 
 $result = mysqli_query($con,
  "SELECT (ROUND(UNIX_TIMESTAMP(TIMESTAMP) /
	  (60*". $interval .")) * 60 * ". $interval ."-60*60) AS TS,
 	 AVG(Tmp_110S_5ft_Avg) AS Temp,
	 AVG(ROUND(RH_RH5_5ft_Avg)) AS Humidity,
	 AVG(BP_BP20_5ft_Avg) AS Pressure,
	 AVG(WS_C1_10m_Prim_Avg) AS WindSpeed
	 FROM Windfarm
	 WHERE `TIMESTAMP` BETWEEN '" . $start->format('Y-m-d') . "' AND '" . $end->format('Y-m-d') . 
	 "' GROUP BY TS");
}

if(!$result) {
	echo "[]";
	exit();
}

//generate indevidual [x,y] arrays that are [timestamp, value] formatted
while($row = $result->fetch_assoc()){
	$timeoffset = intval($row['TS'])*1000-18000000;
	$temps[] = array($timeoffset, round(floatval($row['Temp'])*9.0/5.0+32, 2));
	$hum[] = array($timeoffset, round(floatval($row['Humidity']), 2));
	$pres[] = array($timeoffset, round(floatval($row['Pressure'])*0.295299801, 2));
	$wind[] = array($timeoffset, round(floatval($row['WindSpeed'])*2.2369, 2));
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

//Combine the datasets to a single array, this array will be turned into a json object
$data = array('Start' => intval($firstRow['TIMESTAMP'])*1000-18000000,
	'Temp' => $temps, 'Humidity' => $hum,
	'Pressure' => $pres, 'WindSpeed' => $wind);

echo json_encode($data);

mysqli_close($con);

?>