<?php
setlocale(LC_ALL, 'en_US.utf8');

//select default database
$tableno = 0;
//lookup table for table names
$tables = array("WindFarm", "Davis");

if(!empty($_GET['t']))
  $tableno = intval($_GET['t']);

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
  
//$colNames will be a 1 column many row result (1x#col)
//otherwise $row contains duplicate column info (2x#col)
while ($row = $ColResults->fetch_array()) {
  $colNames[] = $row[0];
}

//get array of column descriptions (contained in internal INFORMATION_SCHEMA database)
$ColResults = mysqli_query($con,
  "SELECT `COLUMN_COMMENT` FROM `INFORMATION_SCHEMA`.`COLUMNS`
  WHERE `TABLE_SCHEMA`='weather'
  AND `TABLE_NAME`='" . $tables[$tableno] . "'");

//$colComments will be a 1 column many row result (1x#col)
while ($row = $ColResults->fetch_array()) {
  $colComments[] = $row[0];
}
 
mysqli_close($con);

//Combine colNames and colComments into a single associated array
$out_array = array('names' => $colNames, 'desc' => $colComments);

echo json_encode($out_array);

?>