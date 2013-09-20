<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <link rel='stylesheet' type='text/css' href='base.css'>
	<link rel='stylesheet' type='text/css' href='last3days.css'>
  <title>Weather Station</title>
  <script src="js/jquery-1.10.2.min.js"></script>
  <script src="js/highcharts.js"></script>
	<script src="js/last3days.js"></script>
	<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
</head>
<body>
<script src="js/modules/exporting.js"></script>
<script src="js/themes/grid.js"></script>

<div id='menu'>
  <ul>
    <li><a class='nav' href='index.php'>Now</a></li>
    <li><span class='active-nav'>Last 3 Days</span></li>
    <li><a class='nav' href='cams.php'>Live Web Cams</a></li>
    <li><a class='nav' href='stations.php'>Weather Stations</a></li>
  </ul>
</div>

<div id='content' style="height: 1070px">
	<div id='range'>
		Range: <input type="text" id='start-date' style="width:6em">
		-
		<input type="text" id='end-date' style="width:6em">
		<button type="button" onclick='eventRange()'>Update</button>
	</div>
	<div id='tempgraph' style="width: 580px; height: 250px; margin: 0 4px 3px"></div>
	<div id='humgraph' style="width: 580px; height: 250px; margin: 0 4px 3px"></div>
	<div id='presgraph' style="width: 580px; height: 250px; margin: 0 4px 3px"></div>
	<div id='windgraph' style="width: 580px; height: 250px; margin: 0 4px 3px"></div>
</div>

</body>
</html>