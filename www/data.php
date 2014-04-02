<?php
setlocale(LC_ALL, 'en_US.utf8');

$get_lower = array_change_key_case($_GET, CASE_LOWER);

//select default database
$tableno = 0;
//lookup table for table names
$tables = array("WindFarm", "Davis");

$beforeDate = "";
$afterDate = "";

if(!empty($get_lower['dset']))
  $tableno = $get_lower['dset'];
	
if(!empty($get_lower['after']))
	$afterDate = (new DateTime($get_lower['after']))->format('Y-m-d');

if(!empty($get_lower['before']))
	$beforeDate = (new DateTime($get_lower['before']))->format('Y-m-d');

//BEWARE PHP DOES NOT SUPPORT UNSIGNED INT
//Column flags will be parsed as string of hex chars
$colhexstring = "0";
if(!empty($get_lower['cols']))
	$colhexstring = $get_lower['cols'];

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

$ColStrArr = [];
	
//if colFlags == 0 then get all table columns
if(intval($colhexstring,16) == 0) {
  $qry = "SELECT * FROM `" . $tables[$tableno] . "`" . $dateselect;
	
  $results = mysqli_query($con, $qry);
}
else {
  //only get columns specified by colhexstring flags
	//This algorithm parses the string character by character
	$len_colhexstring = strlen($colhexstring);
	//for each character starting from the right (least significant value first)
  for($strloc = $len_colhexstring; $strloc > 0; $strloc--)
	{
			$digitplace = $len_colhexstring-$strloc;
      $char = $colhexstring[$strloc-1];
			$charval = intval($char,16);
			//for each character we must check for all combinations of bit string 0x0-0xF (0-15)
			for($i = 0; $i < 16; $i++)
			{
				if((1<<$i) & $charval)
				{
					//each character accesses the next 4 flags (bits)
					$colNo = 4*$digitplace+$i;
					//make sure user didn't pass an invalid flag
					if($colNo >= count($colNames))
						break;
					if($colNo < 0) exit();
					//append the colomn name to list of columns to be downloaded
					$ColStrArr[] = $colNames[$colNo];
					$dispColNo[] = $colNo;
				}
			}
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

if(intval($colhexstring)==0)
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