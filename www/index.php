<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <link rel='stylesheet' type='text/css' href='base.css'>
  <link rel='stylesheet' type='text/css' href='index.css'>
  <title>Weather Station</title>
  <script src="js/jquery-1.10.2.min.js"></script>
  <script src="js/index.js"></script>
</head>
<body onload="init()">
<div id='blackout'>
</div>
<div id='menu'>
  <ul>
    <li><span class='active-nav'>Now</span></li>
    <li><a class='nav' href='last3days.php'>Last 3 Days</a></li>
    <li><a class='nav' href='cams.php'>Live Web Cams</a></li>
    <li><a class='nav' href='stations.php'>Weather Stations</a></li>
  </ul>
</div>

<div id='content'>
  <div id='cams'>
  </div>
  <div id='weather-content'>
    <div id='weather-heading'>
      Hays Weather Conditions<br/>
    </div>
    <div id='date'>?</div><br/>
    <table>
      <tr><td><div id='temp'>?</div></td>
      <td>
      <div id='readings'>
        <table>
          <tr><td><div id='mlabel'>Relative Humidity:</div></td><td> <div id='Humidity_val'>?</div></td></tr>
          <tr><td><div id='mlabel'>Pressure:</div></td><td> <div id='Pressure_val'>?</div></td></tr>
          <tr><td><div id='mlabel'>Wind:</div></td><td> <div id='Wind_val'>?</div></td></tr>
          <tr><td><div id='mlabel'>Precipitation:</div></td><td> <div id='Precipitation'>?</div></td></tr>
        </table>
      </div>
      </td></tr>
    </table>
  </div>
</div>

</body>
</html>