<?php
  // connect to database
  $dbcon = mysql_connect('127.0.0.1', 'root', '') or die("Could not connect: " . mysql_error());
  mysql_select_db('nm_tx', $dbcon) or die("Could not find: " . mysql_error());
  
  //set memory limit
  ini_set('memory_limit','32M');
  
  //default checked for radio button
  $checked['csn'] = 'checked';
  
  //default header
  $gpttsHeadingText	  = "Escalated Tickets by Severity";
  
  if($_REQUEST['cmd'])
  {
  	//set end date with one day advance
    $tempEndDate   = $_REQUEST['end_date'];
    $endDateParts  = explode('-', $tempEndDate);  		
    $endDateMysql  = date("Y-m-d", mktime(0, 0, 0, $endDateParts[1], $endDateParts[2]+1, $endDateParts[0]));
    $endDateOracle = date("d-F-y", mktime(0, 0, 0, $endDateParts[1], $endDateParts[2]+1, $endDateParts[0]));  		
    
    $tempStartDate   = $_REQUEST['start_date'];
    $startDateParts  = explode('-', $tempStartDate);  		
    $startDateMysql  = date("Y-m-d", mktime(0, 0, 0, $startDateParts[1], $startDateParts[2], $startDateParts[0]));
    $startDateOracle = date("d-F-y", mktime(0, 0, 0, $startDateParts[1], $startDateParts[2], $startDateParts[0]));
    
    //get group name condition & others     
    if($_REQUEST['group']=='csn')
    {
    	$condGroupName  = 'and (group_name="core" or group_name="service")';
    	$condGroupId    = 'and am.sub_group_id=1';
    	$checked['csn'] = 'checked'; 
    }
    else if($_REQUEST['group']=='radio')
    {
    	$condGroupName    = ' and group_name="' . $_REQUEST['group'] .'"';
    	$condGroupId      = 'and am.sub_group_id=2';
    	$checked['radio'] = 'checked';
    }
    else if($_REQUEST['group']=='transport')
    {
    	$condGroupName        = ' and group_name="' . $_REQUEST['group'] .'"';
    	$condGroupId          = 'and am.sub_group_id=3';
    	$checked['transport'] = 'checked';
    }    
    
    //get fault 
    $sql = "SELECT submit_by, severity, COUNT(*) AS total FROM tbased_fault_log 
           WHERE event_time BETWEEN '" . $startDateMysql . "' and '" . $endDateMysql . "'
           and solved_by='BO'
           " . $condGroupName . " 
           GROUP BY submit_by, severity
           order by submit_by, severity";        
    
    $rs = mysql_query($sql,$dbcon);  
    
    while($row = mysql_fetch_object($rs))
	  {		  	
	    $fault[$row->submit_by][$row->severity] = $row->total;
	  }
	  
	  //get activity
	  $sql = "SELECT am.holding_by, activity_type, COUNT( * ) AS total
            FROM activity_manager am, activity_log al
            WHERE am.activity_id = al.activity_id
            " . $condGroupId . "
            AND al.assigned_to =  ''
            AND al.assigned_date BETWEEN '". $startDateMysql ."' AND '" . $endDateMysql . "'
            GROUP BY am.holding_by, activity_type
            ORDER BY am.holding_by, activity_type";        
    
    $rs = mysql_query($sql,$dbcon);  
    
    while($row = mysql_fetch_object($rs))
	  {		  	
	    $activity[$row->holding_by][] = array('type' => $row->activity_type, 'total' => $row->total);
	  }
	  
	  //get GPTTS	  
	  if($_REQUEST['group'] == 'csn')
	  {
	  	$sql = "select decode(tr_detls.severity,1,'Critical',2,'Major',3,'Minor') as severity, count(tr_detls.id) as total 
              from trmanager.gptts_tr_details tr_detls, trmanager.gptts_tr_task_data tr_tks_dta, trmanager.jbpm_taskinstance tskinstance
              where tr_detls.id=tr_tks_dta.ticket_id and tr_tks_dta.task_instance_id=tskinstance.id_
                    and (tr_tks_dta.employee_sub_section='66' or tr_tks_dta.employee_sub_section='70') 
                    and tskinstance.name_='TaskPrepareTicket'      
                    and tskinstance.create_ between '$startDateOracle' and '$endDateOracle'
              group by tr_detls.severity
              order by tr_detls.severity";      
              
      $gpttsHeadingText = "Escalated Tickets by Severity:";
	  }
	  else if($_REQUEST['group'] == 'radio')
	  {
	  	$sql = "select decode(tr_tks_dta.employee_sub_section,82,'RAN') as groupname,
                     decode(tr_detls.severity,1,'Critical',2,'Major',3,'Minor') as severity, count(unique(tr_detls.id)) as total
              from trmanager.gptts_tr_details tr_detls, trmanager.gptts_tr_task_data tr_tks_dta, trmanager.jbpm_taskinstance tskinstance
              where tr_detls.id=tr_tks_dta.ticket_id and tr_tks_dta.task_instance_id=tskinstance.id_
                    and (tr_tks_dta.employee_sub_section='82')
                    and tskinstance.name_='TaskTicketOperation'      
                    and tr_detls.id in (select tr_detls.id
                                        from trmanager.gptts_tr_details tr_detls, trmanager.gptts_tr_task_data tr_tks_dta, trmanager.jbpm_taskinstance tskinstance
                                        where tr_detls.id=tr_tks_dta.ticket_id and tr_tks_dta.task_instance_id=tskinstance.id_
                                              and tr_tks_dta.employee_sub_section='68' and tskinstance.name_='TaskPrepareTicket'      
                                              and tr_detls.event_time between '$startDateOracle' and '$endDateOracle'
                                       )
                    and tr_detls.event_time between '$startDateOracle' and '$endDateOracle'
              group by tr_tks_dta.employee_sub_section, tr_detls.severity
              order by tr_tks_dta.employee_sub_section, tr_detls.severity";
              
      $gpttsHeadingText = "Escalated (RAN) Tickets by Severity:";        
	  }	       
	  else if($_REQUEST['group'] == 'transport')  
	  {        
	  	$sql = "select decode(tr_tks_dta.employee_sub_section,285,'IPTS',84,'MTS',284,'OTS') as groupname,
                     decode(tr_detls.severity,1,'Critical',2,'Major',3,'Minor') as severity, count(unique(tr_detls.id)) as total
              from trmanager.gptts_tr_details tr_detls, trmanager.gptts_tr_task_data tr_tks_dta, trmanager.jbpm_taskinstance tskinstance
              where tr_detls.id=tr_tks_dta.ticket_id and tr_tks_dta.task_instance_id=tskinstance.id_
                    and (tr_tks_dta.employee_sub_section='84' or tr_tks_dta.employee_sub_section='284' or tr_tks_dta.employee_sub_section='285')
                    and tskinstance.name_='TaskTicketOperation'      
                    and tr_detls.id in (select tr_detls.id
                                        from trmanager.gptts_tr_details tr_detls, trmanager.gptts_tr_task_data tr_tks_dta, trmanager.jbpm_taskinstance tskinstance
                                        where tr_detls.id=tr_tks_dta.ticket_id and tr_tks_dta.task_instance_id=tskinstance.id_
                                              and tr_tks_dta.employee_sub_section='71' and tskinstance.name_='TaskPrepareTicket'      
                                              and tr_detls.event_time between '$startDateOracle' and '$endDateOracle'
                                       )
                    and tr_detls.event_time between '$startDateOracle' and '$endDateOracle'
              group by tr_tks_dta.employee_sub_section, tr_detls.severity
              order by tr_tks_dta.employee_sub_section, tr_detls.severity";
              
      $gpttsHeadingText = "Escalated (IPTS, MTS & OTS) Tickets by Severity:";              	 
	  }        
	  
	  //set the connection with oracle
	  $conGPTTS = odbc_connect("NCMDB2","TNM_APP","gp$%^&");
	  	  
	  $rs = odbc_exec($conGPTTS,$sql);
	  
	  if($_REQUEST['group'] == 'csn')
	  {
	    while($row = odbc_fetch_object($rs))
	    {
	      $gptts[] = $row;
	    }
	  }
	  else
	  {
	  	while($row = odbc_fetch_object($rs))
	    {
	      $gptts[$row->GROUPNAME][$row->SEVERITY] = $row->TOTAL;
	      $data[]=$row;
	    }
	  }
	  
	  
	  //echo "<pre>";
	  //print_r($data);
	  //echo "</pre>";  
	  
	  //close the connection oracle
	  odbc_close($conGPTTS);	 
	}          
?>           
<html>       
	<form name="reportForm" action="group_report.php" method="POST" enctype="multipart/form-data">
	<input type="hidden" name="cmd" value="show_report">
	<table align="center" bgcolor="black" cellpadding="2" cellspacing="1">
		<tr bgcolor="white">
			<td>   
	      <table align="center">	      	
	      	<tr>
	      		<td>Start Date: </td><td><input type="text" name="start_date" value="<?php echo $_REQUEST['start_date']?$_REQUEST['start_date']:date('Y-m-d');?>"> (YYYY-MM-DD)</td>
	      	</tr>
	      	<tr>
	      		<td>End Date: </td><td><input type="text" name="end_date" value="<?php echo $_REQUEST['end_date']?$_REQUEST['end_date']:date('Y-m-d');?>"> (YYYY-MM-DD)</td>
	      	</tr>
	      	<tr>
	      		<td>Group: </td>
	      		<td>
	      			<input <?php echo $checked['csn']?> type="radio" name="group" value="csn"> CSN
	      			<input <?php echo $checked['radio']?> type="radio" name="group" value="radio"> Radio
	      			<input <?php echo $checked['transport']?> type="radio" name="group" value="transport"> Transport
	      		</td>
	      	</tr>
	      	<tr>
	      		<td><input type="submit" class="submit" name="show" value="Show Report"></td><td><input type="reset" class="reset" name="reset" value="Reset"></td>
	      	</tr>
	      </table>
	    </td>  
	  </tr>
  </table>
  </form>
  <table border="0" cellpadding="5" cellspacing="10" bgcolor="white">
  	<tr bgcolor="white" valign="top">
  		<td>
  			<h3><u>Fault Status by Severity:</u></h3>
  			<table border="0" cellspacing="1" cellpadding="2" bgcolor="blue">
  				<tr bgcolor="white">
  					<td><b>Username</b></td><td><b>Critical</b></td><td><b>Major</b></td><td><b>Minor</b></td>
  				</tr>
  				<?php
  				  if($fault)
  				  {
  				    foreach($fault as $key => $value)
  				    {
  				    	echo "<tr bgcolor='white'><td>$key</td><td>$value[Critical]</td><td>$value[Major]</td><td>$value[Minor]</td></tr>";
  				    }
  				  }				  
  				?>
  			</table>
  		</td>
  		<td>
  			<h3><u>Activity Status by Activity Type:</u></h3>
  			<table border="0" cellspacing="1" cellpadding="2" bgcolor="blue">
  				<tr bgcolor="white">
  					<td><b>Username</b></td><td><b>Activity Type</b></td><td><b>Total</b></td>
  				</tr>
  				<?php
  			    if($activity)
  			    {
  			    	foreach($activity as $key => $value)
  				    {
  				    	$counter  = 0;
  				    	$totalRow = count($value);
  				    	
  				    	foreach($value as $data)
  				    	{
  				    		if($counter == 0)
  				    		  echo "<tr bgcolor='white'><td rowspan=$totalRow>$key</td><td>$data[type]</td><td>$data[total]</td></tr>";
  				    		else
  				    	    echo "<tr bgcolor='white'><td>$data[type]</td><td>$data[total]</td></tr>";
  				    	  $counter++;
  				      }
  				    }
  			    }
  			  ?>
  			</table>  			
  		</td>
  		<td>
  			<h3><u><?php echo $gpttsHeadingText;?></u></h3>
  			<table border="0" cellpadding="2" cellspacing="1" bgcolor="blue">
  				<?php
  				  if($gptts)
  				  {
  				    if($_REQUEST['group'] == 'csn')
  				    {
  				    	echo "<tr bgcolor='white'><td><b>Severity</b></td><td><b>Total</b></td></tr>";
  				    	foreach($gptts as $value)
  				    	{
  				    		echo "<tr bgcolor='white'><td>$value->SEVERITY</td><td>$value->TOTAL</td></tr>";
  				    	}
  				    }
  				    else
  				    {
  				    	echo "<tr bgcolor='white'><td><b>Username</b></td><td><b>Critical</b></td><td><b>Major</b></td><td><b>Minor</b></td></tr>";
  				    	foreach($gptts as $key => $value)
  				    	{
  				    		echo "<tr bgcolor='white'><td>$key</td><td>$value[Critical]</td><td>$value[Major]</td><td>$value[Minor]</td></tr>";
  				    	}
  				    }
  				  }
  				?>
  			</table>  			
  		</td>
  	</tr>
  </table>	
</html>