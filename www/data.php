<?php
setlocale(LC_ALL, 'en_US.utf8');

$get_lower = array_change_key_case($_GET, CASE_LOWER);

//select default database
$tableno = 0;
//lookup table for table names
$tables = array("WindFarm", "Davis");

$beforeDate = "";
$afterDate = "";

//select fields (0, 0, 0, 0 gets all)
//since php doesn't support 128 bit integers we will use 4 32 bit integers to store the flags
$colFlags = array(0,0,0,0);

if(!empty($get_lower['dset']))
  $tableno = $get_lower['dset'];
	
if(!empty($get_lower['after']))
	$afterDate = (new DateTime($get_lower['after']))->format('Y-m-d');

if(!empty($get_lower['before']))
	$beforeDate = (new DateTime($get_lower['before']))->format('Y-m-d');

if(!empty($get_lower['cols']))
{ //BEWARE PHP DOES NOT SUPPORT UNSIGNED INT
  $colFlags[0] = intval(substr($get_lower['cols'],0,4),16);
  $colFlags[1] = intval(substr($get_lower['cols'],4,4),16);
  $colFlags[2] = intval(substr($get_lower['cols'],8,4),16);
  $colFlags[3] = intval(substr($get_lower['cols'],12,4),16);
  $colFlags[4] = intval(substr($get_lower['cols'],16,4),16);
  $colFlags[5] = intval(substr($get_lower['cols'],20,4),16);
  $colFlags[6] = intval(substr($get_lower['cols'],24,4),16);
  $colFlags[7] = intval(substr($get_lower['cols'],28,4),16);
  //var_dump($colFlags);
}

//connect to internal mysql database to retrieve column names
$con=mysqli_connect("localhost","","", "INFORMATION_SCHEMA");
if ($con->connect_errno) //check for error when connecting
{
	echo "Filed to connect to " . $tables[$tableno] . " Database: " . $con->connect_error;
	exit();
}

//get array of column names (contained in internal INFORMATION_SCHEMA database)
$ColResults = mysqli_query($con,
  "SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS`
  WHERE `TABLE_SCHEMA`='weather'
  AND `TABLE_NAME`='" . $tables[$tableno] . "'");

//$ColResults will be a 1 column many row result (1x#col)
while ($row = $ColResults->fetch_array()) {
  $colNames[] = $row[0];
}
mysqli_close($con);

//Retrieve row values
$con=mysqli_connect("localhost","","", "weather");
if ($con->connect_errno) //check for error when connecting
{
	echo "Filed to connect to " . $tables[$tableno] . " Database: " . $con->connect_error;
	exit();
}

//handle date select part of querry
$dateselect = "";
if($afterDate != "" && $beforeDate != "")
	$dateselect = "WHERE `TIMESTAMP` < '" . $beforeDate . "' and `TIMESTAMP` > '" . $afterDate . "'";
	
elseif($afterDate != "")
	$dateselect = "WHERE `TIMESTAMP` > '" . $afterDate . "'";
	
elseif($beforeDate != "")
	$dateselect = "WHERE `TIMESTAMP` < '" . $beforeDate . "'";

//if colFlags == 0 then get all table columns
if(array_sum($colFlags) == 0) {
  $qry = "SELECT * FROM `" . $tables[$tableno] . "`" . $dateselect;
	
  $results = mysqli_query($con, $qry);
}
else {
  //only get columns specified by colFlags flags
  for($order = 0; $order < 8; $order++)
    for($shift = 0; $shift < 16; $shift++)
      //1 corresponds to column 0, 10 corresponds to column 1, 100 corresponds to column 2, ... column 128
      //Therefore 110 corresponds to columns 1 and 2
      if((1<<$shift) & $colFlags[$order])
      {
        $colNo = $shift+$order*16;
        if($colNo >= count($colNames))
          break;
        $ColStrArr[] = $colNames[$colNo];
        //build an array listing all displayed columns by number
        $dispColNo[] = $colNo;
      }
  
  //remove dangling comma from end
  $ColStr = implode(",", $ColStrArr);

  $qry = "SELECT " . $ColStr . " FROM `" . $tables[$tableno] . "`" . $dateselect;
  $results = mysqli_query($con, $qry);
}

if($results == false) {
  echo "Error encountered while retrieving the data: ";
  echo $qry;
  mysqli_close($con);
  exit();
}

//Handle filenaming for extra time parameters in the url
$details = "";
if($afterDate != "")
	$details = $details . "_After-" . $afterDate;
if($beforeDate != "")
	$details = $details . "_Before-" . $beforeDate;

$filename = $tables[$tableno] . $details . '_DownloadedOn-' . date("Y-m-d_H-i");

if(array_sum($colFlags)==0)
  $dispColNames = $colNames;
else
  foreach($dispColNo as $colNo)
    $dispColNames[] = $colNames[$colNo];

//Write to file
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=" . $filename . ".csv");
header("Pragma: no-cache");
header("Expires: 0");
$fout = fopen("php://output", "w");
fputcsv($fout, $dispColNames);
while($row = $results->fetch_assoc()) {
  $size = $results->field_count;
  fputcsv($fout, $row);
}
fclose($fout);

mysqli_close($con);
?>