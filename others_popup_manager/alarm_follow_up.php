<?php
  // include the main configuration file
  require_once($_SERVER['DOCUMENT_ROOT'] .'/nm_tx/common/conf/main.conf.php');
  
  //do initialization
  define(FOLLOW_UP_CATEGORY, 'Power Follow-up');  
  
  $condRealAlarm    = '';
  $condStatusChange = '';
  
  //set the connection with SQL Server
  $connSQL = odbc_connect("Alarm_DB","mk5","mk5mnm");
  
  if(!$connSQL)  
  {
  	exit("Connection Failed to Database: " . $connSQL);
  }
  
  odbc_exec($connSQL,'use iNMSExtAlarmDB');
  
  //get data from fault table
  $info['table'] = FAULT_TBL;
  $info['debug'] = false;
  $info['where'] = 'problem_category like ' . q(FOLLOW_UP_CATEGORY) . ' and status like "In Progress"';
  
  $followUpAlarm = select($info);
  
  foreach($followUpAlarm as $alarm)
  { 
  	if($condRealAlarm)
  	  $condRealAlarm .= " or otherattr1 like '%" . trim($alarm->problem_component) . "%'"; 
  	else
  	  $condRealAlarm  = " otherattr1 like '%" . trim($alarm->problem_component) . "%'";   	
  }
  
  //get data from alarm table
  $query = "select otherattr1, subcenter, specificproblem, cast(alarmeventtime as smalldatetime) alarmeventtime, datediff(mi,alarmeventtime,getdate()) as duration, region, eventtype, sptype 
            from test_con_tblalarms_v2 
            where alarmcleartime is null
                  and (specificproblem like '%batt%' or specificproblem like '%temp%')
                  and (" . $condRealAlarm . ")
            order by otherattr1";
              
  $result = odbc_exec($connSQL, $query);
  
  while($row = odbc_fetch_object($result))
  {  
    $realAlarm[] = $row;
  }
  
  //compare data for necessary action  
  foreach($followUpAlarm as $alarm)
  {
  	$find  = false;
  	
  	foreach($realAlarm as $real)
  	{
  		if(strstr($real->otherattr1, $alarm->problem_component) && strstr($real->specificproblem, $alarm->finding))
  		{
  			if($real->duration/60 >= 6)
  		    $popupData[] = array('site' => $alarm->problem_component, 'alarm' => $real->specificproblem, 'eventtime' => $real->alarmeventtime, 'duration' => $real->duration/60, 'subcenter' => $real->subcenter);
  		    
  		  $find = true;
  		}
  	}
  	
  	if(!$find)  	  
  	  $condStatusChange .= $condStatusChange ? ' or fault_id=' . $alarm->fault_id : ' fault_id=' . $alarm->fault_id;  
  }
  
  
  //close the connection SQL DB
  odbc_close($connSQL);
  
  //change the status of cleared alarm
  if($condStatusChange)
  {
    unset($info);
    $info['table'] = FAULT_TBL;
    $info['debug'] = false;
    $info['data']  = array('status' => 'Escalated');
    $info['where'] = $condStatusChange;  
  }
    
  $result = update($info);
  
  //close the window automatically if no data to be showed in popup
  if(!count($popupData))
  {
  	echo "<script>window.close();</script>";
  }
  else
  {
  	foreach($popupData as $data)
  	{
  	  $htmlString .= "<tr bgcolor='white'><td>" . $data['site'] . "
  	                  </td><td>" . $data['alarm']     . "
  	                  </td><td>" . $data['eventtime'] . "
  	                  </td><td>" . round($data['duration'], 2) . "
  	                  </td><td>" . $data['subcenter'] . "
  	                  </td></tr>";  	  	  
  	}  
  }      	
?>
<html>
	<head>		
    <title>Critical Power Alarm Follow-up</title>
    <link rel="stylesheet" href="/nm_tx/common/css/default.project.css" type="text/css">  
  </head>
  <body style="background-color:#FFFFFF">
  	<br>  	
  	<h5 align="center">Sites having critical power alarm for more than 6 hours.</h5>
  	<table width="100%" cellpadding="2" cellspacing="1" bgcolor="black">
  		<tr bgcolor="white">
  			<td><b>Site<b></td><td><b>Alarm<b></td><td><b>Event Time<b></td><td><b>Duration (Hr)<b></td><td><b>Subcenter<b></td>
  		</tr>
  		<?php echo $htmlString; ?>
  	</table>
  	<p align="center"><a href="javascript:void(0);" onclick="window.close();"><b>Close</b></a></p>
  </body>
</html>