<?php
  setlocale(LC_NUMERIC, 'en_US.utf8'); 
  $con=mysqli_connect("localhost","","","weather");
  if (mysqli_connect_errno()) 
  { 
  echo "Filed to connect to Database";
  }

	//create SQL querry
  $result = mysqli_query($con,
   'SELECT DATE_FORMAT(TIMESTAMP, "%m/%d/%Y %r CST") AS Date,
    Tmp_110S_5ft_Avg AS Temp,
    ROUND(RH_RH5_5ft_Avg) AS Humidity,
    BP_BP20_5ft_Avg AS Pressure,
    WD_200P_49m_WVT AS WindDir,
    WS_C1_10m_Prim_Avg AS WindSpeed
    FROM WindFarm
		ORDER BY TIMESTAMP DESC LIMIT 1');
		
	$data = mysqli_fetch_array($result, MYSQLI_ASSOC);

	$midnight = new DateTime('today midnight');
	$query = "SELECT SUM(`Precip_NVL_5ft_Tot`) AS Precip
	  FROM WindFarm
	  WHERE `Timestamp` >= '" . $midnight->format('Y-m-d') . "'";
	
	$result = mysqli_query($con, $query);
	  
	if($result)
	{
		$precip = mysqli_fetch_array($result, MYSQLI_NUM);
		if(strlen($precip[0]) == 0)
			$data = array_merge($data, array('Precip' => "0"));
		else
			$data = array_merge($data, $precip);
	}
	else
	{
		$data = array_merge($data, array('Precip' => "0"));
	}
	
  echo json_encode($data, JSON_PRETTY_PRINT);
  mysqli_close($con);
?>