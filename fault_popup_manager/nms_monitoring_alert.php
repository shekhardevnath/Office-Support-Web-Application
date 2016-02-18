<?php
  //include the main configuration file
  require_once($_SERVER['DOCUMENT_ROOT'] .'/nm_tx/common/conf/main.conf.php');
  require_once(LOCAL_CONFIG_DIR          .'/dp.conf.php');
  require_once(LOCAL_LIB_DIR             .'/dp.lib.php');
  
  $cmd  = getUserField('cmd');
  $type = getUserField('type');
  
  $user = $_SESSION['username'] ? $_SESSION['username'] : $_COOKIE['usr_name'];  
  
  $submitTime = date("Y-m-d H:i:s");
    
  if($cmd == 'save' && $type == 'hourly')
  {    
    $data['submit_date']  = $submitTime;
    $data['nms']          = 'EMOS & CACTI';
    $data['monitored_by'] = $user;
    $data['monitored']    = 1;
    $data['shift']        = 0;
    
    $info['table'] =NMS_MONITORING_TBL;
    $info['debug'] = false;
    $info['data']  = $data; 
    $resutl = insert($info);
    
    echo '<script>window.close();</script>';
  }
  
  if($cmd == 'save' && $type == 'shift')
  {    
    $data['submit_date']  = $submitTime;
    $data['nms']          = 'All NMS';
    $data['monitored_by'] = $user;
    $data['monitored']    = 1;
    $data['shift']        = 1;
    
    $info['table'] =NMS_MONITORING_TBL;
    $info['debug'] = false;
    $info['data']  = $data; 
    $resutl = insert($info);
    
    echo '<script>window.close();</script>';
  }
  
  if ($type == 'hourly')
  { 
    echo ('<html>
         	<title>NMS Monitoring Reminder</title>
         	<body bgcolor="679FAF">	
         	  <table border="0" align="center" cellpadding="2" cellspacing="5">
         	  	<tr>
         	  		<td align="center"><b><font size="5">This is a reminder & for your acknowledgment!</font></b></td>
         	  	</tr>
         	  	<tr>
         	  		<td><br></td>
         	  	</tr>
         	  	<tr>
         	  		<td><b><font size="4">This popup reminds you to check EMOS & CACTI for alarms.</font></b></td>
         	  	</tr>
         	  	<tr>
         	  		<td><b><font size="4">Please do check and </font></b> <b><font size="4" color="#FFFF9B">don\'t forget to acknowledge.</font></b></td>
         	  	</tr>
         	  	<tr>
         	  		<td><br><br></td>
         	  	</tr>
         	  	<tr>
         	  		<td align="center">
         	  		  <form name="acknowledgeForm" action="/nm_tx/fault_popup_manager/nms_monitoring_alert.php" method="POST">
         	  		    <input type="hidden" value="save" name="cmd">
         	  		    <input type="hidden" value="hourly" name="type">
         	  		    <input type="submit" value="Acknowledge">
         	  		  </form>
         	  		</td>
         	  	</tr>
         	  </table>	
           </body>
         </html>');
  }
  
  if ($type == 'shift')
  {
  	echo ('<html>
         	<title>NMS Monitoring Reminder</title>
         	<body bgcolor="679FAF">	
         	  <table border="0" align="center" cellpadding="2" cellspacing="5">
         	  	<tr>
         	  		<td align="center"><b><font size="5">This is a reminder & for your acknowledgment!</font></b></td>
         	  	</tr>
         	  	<tr>
         	  		<td><br></td>
         	  	</tr>
         	  	<tr>
         	  		<td><b><font size="4">This popup reminds you that you are going to hand over the shift very soon. Please check for any unacknowledged alarms on all the NMS (TNMS, ONMS, TimeScan, Critical Sites, U2000, EMOS).</font></b></td>
         	  	</tr>
         	  	<tr>
         	  		<td><b><font size="4">Please do check and </font></b> <b><font size="4" color="#FFFF9B">don\'t forget to acknowledge.</font></b></td>
         	  	</tr>
         	  	<tr>
         	  		<td><br><br></td>
         	  	</tr>
         	  	<tr>
         	  		<td align="center">
         	  		  <form name="acknowledgeForm" action="/nm_tx/fault_popup_manager/nms_monitoring_alert.php" method="POST">
         	  		    <input type="hidden" value="save" name="cmd">
         	  		    <input type="hidden" value="shift" name="type">
         	  		    <input type="submit" value="Acknowledge">
         	  		  </form>
         	  		</td>
         	  	</tr>
         	  </table>	
           </body>
         </html>');
  }
?>