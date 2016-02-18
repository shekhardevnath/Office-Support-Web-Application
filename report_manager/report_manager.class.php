<?php
  /**
  *file: report_manaer.class.php
  *purpose: report manager application class
  **/
  
  class reportManagerApp extends DefaultApplication
  { 	
  	function run()
  	{ 
  		//set the connection with oracle
	  	$this->conn = odbc_connect("fms","readonly","readonly");
	  	
        //if (!$this->conn)
  		//	{exit("Connection Failed: " . $this->conn);}
  			
  		//set the connection with oracle
	  	$this->conGPTTS = odbc_connect("NCMDB2","TNM_APP","gp$%^&");
	  	
        //if (!$this->conGPTTS)
  		//	{exit("Connection Failed: " . $this->conGPTTS);}	
  		
  		
  		//set the connection with SQL Server and select DB
	  	$this->conn_sql = odbc_connect("NETVISION","mk5","mk5mnm");
	  	
        if (!$this->conn_sql)
  			{exit("Connection Failed: " . $this->conn_sql);}
  			
  			odbc_exec($this->conn_sql,'use FM');
  			
  		
  		$cmd = getUserField('cmd');
		    		
  		switch($cmd)
  		{
  			case 'tnm'                    : $screen = $this->showReportEditor($cmd); break;
  			case 'tnm_report'             : $screen = $this->showTNMReport();        break;
  			case 'nm_tx'                  : $screen = $this->showReportEditor($cmd); break;
  			case 'nm_tx_report'           : $screen = $this->showNM_TXReport();      break;  			  			
  			case 'recurrent_fault'        : $screen = $this->showReportEditor($cmd); break;
  			case 'recurrent_fault_report' : $screen = $this->showReFaultReport();    break;
  			case 'show_TR_detail'         : $screen = $this->showTRDetail();
  			default                       : $screen = $this->showReportEditor('tnm');
  		}
  		
  		echo $this->displayScreen($screen);
  		
  		//close the connection oracle
		  odbc_close($this->conn);  	
		  
		  //close the connection oracle
		  odbc_close($this->conn_sql);  	
		  
		  //close the connection oracle
		  odbc_close($this->conGPTTS);  	
  	}
  	
  	function showReportEditor($cmd)
  	{
  		//set current navigation         
  		$this->setNavigation($cmd);
  		
  		$data['start_date'] = date("Y-m-d");
  		$data['end_date']   = date("Y-m-d");
  		$data['report']     = $cmd;
  		
  		return createPage(REPORT_MANAGER_TEMPLATE, $data);
  	}
  	
  	function showTNMReport()
  	{
  		//set current navigation         
  		$this->setNavigation('tnm');
  		
  		$data['report']     = 'tnm';
  		
  		//set end date with one day advance
  		$tempEndDate = getUserField('end_date');
  		$dateParts   = explode('-', $tempEndDate);  		
  		$endDate     = date("Y-m-d", mktime(0, 0, 0, $dateParts[1], $dateParts[2]+1, $dateParts[0]));  		
  		
  		$startDate = getUserField('start_date');
  		
  		//get total faults
  		$info['table']  = FAULT_TBL;
  		$info['debug']  = false;
  		$info['fields'] = array('count(*) as total_faults');
  		$info['where']  = ' submit_date between ' . q($startDate) . ' and ' . q($endDate);
  		
  		$data['r1'] = select($info);
  		
  		//get MTTR
  		$info['fields'] = array('AVG(((TIME_TO_SEC(TIMEDIFF(last_update_date, submit_date)))/60 )/60) AS MTTR');
  		$info['where']  = ' status ="Solved" and submit_date between ' . q($startDate) . ' and ' . q($endDate);
  		
  		$data['r2'] = select($info);
  		
  		//get MTTE
  		$info['fields'] = array('AVG(((TIME_TO_SEC(TIMEDIFF(last_update_date, submit_date)))/60 )/60) AS MTTE');
  		$info['where']  = ' status ="Escalated" and submit_date between ' . q($startDate) . ' and ' . q($endDate);
  		
  		$data['r3'] = select($info);
  		
  		//get total faults by status
  		$info['fields'] = array('status, count(*) as Total');
  		$info['where']  = ' submit_date between ' . q($startDate) . ' and ' . q($endDate) . ' group by status';
  		
  		$data['r4'] = select($info);
  		
  		//get Solved faults by severity
  		$info['fields'] = array('severity, count(*) as Total');
  		$info['where']  = ' status="Solved" and submit_date between ' . q($startDate) . ' and ' . q($endDate) . ' group by severity';
  		
  		$data['r5'] = select($info);
  		
  		//MTTR by severity
  		$info['fields'] = array('severity, AVG(((TIME_TO_SEC(TIMEDIFF(last_update_date, submit_date)))/60 )/60) AS MTTR');
  		$info['where']  = ' status="Solved" and submit_date between ' . q($startDate) . ' and ' . q($endDate) . ' group by severity';
  		
  		$data['r6'] = select($info);
  		
  		//MTTE by severity
  		$info['fields'] = array('severity, count(*) as Total');
  		$info['where']  = ' status="Escalated" and submit_date between ' . q($startDate) . ' and ' . q($endDate) . ' group by severity';
  		
  		$data['r7'] = select($info);
  		
  		//Totlal Remote Support
  		$info['table']  = REMOTE_SUPPORT_TBL;
  		$info['fields'] = array('count(*) as Total');
  		$info['where']  = 'support_date between ' . q($startDate) . ' and ' . q($endDate);
  		
  		$data['r8'] = select($info);
  		
  		$data['end_date']   = $tempEndDate;
  		$data['start_date'] = $startDate;

  		return createPage(REPORT_MANAGER_TEMPLATE, $data);
  	}
  	
  	function showNM_TXReport()
  	{
  		//set current navigation         
  		$this->setNavigation('nm_tx');
  		
  		$data['report'] = 'nm_tx';
  		
  		//set end date with one day advance
  		$tempEndDate    = getUserField('end_date');
  		$endDateParts = explode('-', $tempEndDate);  		
  		$endDate      = date("d-F-y", mktime(0, 0, 0, $endDateParts[1], $endDateParts[2]+1, $endDateParts[0]));  		
  		
  		$tempStartDate = getUserField('start_date');
  		$startDateParts = explode('-', $tempStartDate);  		
  		$startDate      = date("d-F-y", mktime(0, 0, 0, $startDateParts[1], $startDateParts[2], $startDateParts[0]));
  		
  		//get MTTR Daily
  		/*$sql = "select avg((cleartime-escalationtime)*24) as MTTR_Daily from trmanager.tr_first_level
							where trtime between " . q($startDate) . " and " . q($endDate) . " and trtype ='DAILY'
							and (" . NM_TX_ESCALATEION_GROUP . ")";*/  		
  		$sql = "select tr_detls.Ticket_Type, AVG((cast(tr_detls.clear_time as date)-cast(tr_detls.escalation_time as date))*24) as mttr
             from trmanager.gptts_tr_details tr_detls, trmanager.gptts_tr_task_data tr_tks_dta, trmanager.jbpm_taskinstance tskinstance
             where tr_detls.id=tr_tks_dta.ticket_id and tr_tks_dta.task_instance_id=tskinstance.id_
                   and tr_tks_dta.employee_sub_section='71' and tskinstance.name_='TaskPrepareTicket'
                   and tskinstance.create_ between " . q($startDate) . " and " . q($endDate) . "
                   and tr_detls.Ticket_Type='DAILY'
             GROUP BY tr_detls.Ticket_Type";
  		
  		$rs=odbc_exec($this->conGPTTS,$sql);
  		
  		while($row = odbc_fetch_object($rs))
			{
				$data['r1'][] = $row;
			}
  		
  		//get MTTR QUALITY
  		/*$sql = "select avg((cleartime-escalationtime)*24) as MTTR_Quality from trmanager.tr_first_level
							where trtime between " . q($startDate) . " and " . q($endDate) . " and trtype ='QUALITY'
							and (" . NM_TX_ESCALATEION_GROUP . ")";*/  		
  		$sql = "select tr_detls.Ticket_Type, AVG((cast(tr_detls.clear_time as date)-cast(tr_detls.escalation_time as date))*24) as mttr
             from trmanager.gptts_tr_details tr_detls, trmanager.gptts_tr_task_data tr_tks_dta, trmanager.jbpm_taskinstance tskinstance
             where tr_detls.id=tr_tks_dta.ticket_id and tr_tks_dta.task_instance_id=tskinstance.id_
                   and tr_tks_dta.employee_sub_section='71' and tskinstance.name_='TaskPrepareTicket'
                   and tskinstance.create_ between " . q($startDate) . " and " . q($endDate) . "
                   and tr_detls.Ticket_Type='QUALITY'
             GROUP BY tr_detls.Ticket_Type";
  		
  		$rs=odbc_exec($this->conGPTTS,$sql);
  		
  		 while($row = odbc_fetch_object($rs))
			{
				$data['r2'][] = $row;
			}
			
			//get MTTE & MTTR by severity
			$sql = "select decode(tr_detls.severity, 1, 'Critical', 2, 'Major', 3, 'Minor') AS severity, 
			               AVG((cast(tr_detls.escalation_time as date)-cast(tr_detls.event_time as date))*24) as mtte,
			               AVG((cast(tr_detls.clear_time as date)-cast(tr_detls.escalation_time as date))*24) as mttr
              from trmanager.gptts_tr_details tr_detls, trmanager.gptts_tr_task_data tr_tks_dta, trmanager.jbpm_taskinstance tskinstance
              where tr_detls.id=tr_tks_dta.ticket_id and tr_tks_dta.task_instance_id=tskinstance.id_
                    and tr_tks_dta.employee_sub_section='71' and tskinstance.name_='TaskPrepareTicket'
                    and tskinstance.create_ between " . q($startDate) . " and " . q($endDate) . "
              GROUP BY tr_detls.severity
              ORDER BY tr_detls.severity";
  		
  		$rs=odbc_exec($this->conGPTTS,$sql);
  		
  		 while($row = odbc_fetch_object($rs))
			{
				$data['mtte_mttr'][] = $row;
			}			
			
			//get MTTE DAILY
  		/*$sql = "select avg((escalationtime-eventtime)*24) as MTTE_Daily from trmanager.tr_first_level t 
							where trtime between " . q($startDate) . " and " . q($endDate) . " and trtype='DAILY'
							and (" . NM_TX_ESCALATEION_GROUP . ")";*/
			$sql = "select tr_detls.Ticket_Type, AVG((cast(tr_detls.escalation_time as date)-cast(tr_detls.event_time as date))*24) as mtte
             from trmanager.gptts_tr_details tr_detls, trmanager.gptts_tr_task_data tr_tks_dta, trmanager.jbpm_taskinstance tskinstance
             where tr_detls.id=tr_tks_dta.ticket_id and tr_tks_dta.task_instance_id=tskinstance.id_
                   and tr_tks_dta.employee_sub_section='71' and tskinstance.name_='TaskPrepareTicket'
                   and tskinstance.create_ between " . q($startDate) . " and " . q($endDate) . "
                   and tr_detls.Ticket_Type='DAILY'
             GROUP BY tr_detls.Ticket_Type
             ORDER BY tr_detls.Ticket_Type";
  		
  		$rs=odbc_exec($this->conGPTTS,$sql);
  		
  		 while($row = odbc_fetch_object($rs))
			{
				$data['r3'][] = $row;			
			}		
			
			//get MTTE QUALITY
  		/*$sql = "select avg((escalationtime-eventtime)*24) as MTTE_Quality from trmanager.tr_first_level t 
							where trtime between " . q($startDate) . " and " . q($endDate) . " and trtype='QUALITY'
							and (" . NM_TX_ESCALATEION_GROUP . ")";*/
  		
  		$sql = "select tr_detls.Ticket_Type, AVG((cast(tr_detls.escalation_time as date)-cast(tr_detls.event_time as date))*24) as mtte
             from trmanager.gptts_tr_details tr_detls, trmanager.gptts_tr_task_data tr_tks_dta, trmanager.jbpm_taskinstance tskinstance
             where tr_detls.id=tr_tks_dta.ticket_id and tr_tks_dta.task_instance_id=tskinstance.id_
                   and tr_tks_dta.employee_sub_section='71' and tskinstance.name_='TaskPrepareTicket'
                   and tskinstance.create_ between " . q($startDate) . " and " . q($endDate) . "
                   and tr_detls.Ticket_Type='QUALITY'
             GROUP BY tr_detls.Ticket_Type
             ORDER BY tr_detls.Ticket_Type";
             
  		$rs=odbc_exec($this->conGPTTS,$sql);
  		
  		 while($row = odbc_fetch_object($rs))
			{
				$data['r4'][] = $row;
			}
												
			//get Total TR Daily
			/*$sql = "select count(*) as total_tr_daily from trmanager.tr_first_level t 
							where trtime between " . q($startDate) . " and " . q($endDate) . " and trtype='DAILY'
							and (" . NM_TX_ESCALATEION_GROUP . ")";*/  		
  		$sql = "select count(tr_detls.id) as total
              from trmanager.gptts_tr_details tr_detls, trmanager.gptts_tr_task_data tr_tks_dta, trmanager.jbpm_taskinstance tskinstance
              where tr_detls.id=tr_tks_dta.ticket_id and tr_tks_dta.task_instance_id=tskinstance.id_
                    and tr_tks_dta.employee_sub_section='71' and tskinstance.name_='TaskPrepareTicket'
                    and tskinstance.create_ between " . q($startDate) . " and " . q($endDate) . "
                    and tr_detls.Ticket_Type='DAILY'
              GROUP BY tr_detls.Ticket_Type";
  		
  		$rs=odbc_exec($this->conGPTTS,$sql);
  		
  		 while($row = odbc_fetch_object($rs))
			{
				$data['r5'][] = $row;
			}
			
			//get Total TR Daily Cleared
			$sql = "select count(*) as total_tr_daily_cleared from trmanager.tr_first_level t 
							where trtime between " . q($startDate) . " and " . q($endDate) . " and trtype='DAILY' and trstatus='CLEARED'
							and (" . NM_TX_ESCALATEION_GROUP . ")";
  		
  		$rs=odbc_exec($this->conn,$sql);
  		
  		 while($row = odbc_fetch_object($rs))
			 {
				$data['r6'][] = $row;
			 }
			
			//get Total TR Quality
			/*$sql = "select count(*) as total_tr_quality from trmanager.tr_first_level t 
							where trtime between " . q($startDate) . " and " . q($endDate) . " and trtype='QUALITY'
							and (" . NM_TX_ESCALATEION_GROUP . ")";*/
			$sql = "select count(tr_detls.id) as total
              from trmanager.gptts_tr_details tr_detls, trmanager.gptts_tr_task_data tr_tks_dta, trmanager.jbpm_taskinstance tskinstance
              where tr_detls.id=tr_tks_dta.ticket_id and tr_tks_dta.task_instance_id=tskinstance.id_
                    and tr_tks_dta.employee_sub_section='71' and tskinstance.name_='TaskPrepareTicket'
                    and tskinstance.create_ between " . q($startDate) . " and " . q($endDate) . "
                    and tr_detls.Ticket_Type='QUALITY'
              GROUP BY tr_detls.Ticket_Type";				
  		
  		$rs=odbc_exec($this->conGPTTS,$sql);
  		
  		 while($row = odbc_fetch_object($rs))
			{
				$data['r7'][] = $row;
			}
			
			//get Total TR Quality Cleared
			$sql = "select count(*) as total_tr_quality_cleared from trmanager.tr_first_level t 
							where trtime between " . q($startDate) . " and " . q($endDate) . " and trtype='QUALITY' and trstatus='CLEARED'
							and (" . NM_TX_ESCALATEION_GROUP . ")";
  		
  		$rs=odbc_exec($this->conn,$sql);
  		
  		 while($row = odbc_fetch_object($rs))
			 {
				$data['r8'][] = $row;
			 }
			
			
			//get Outage Data
			$sql="exec sp_shekhar " . q($startDate) . "," . q($endDate);
			$rs=odbc_exec($this->conn_sql,$sql);
  		
  		while($row = odbc_fetch_object($rs))
			{
			$data['r9'][] = $row;
			}
			
			//get indivisual MTTE Daily
			$sql = "select escalationby, avg((escalationtime-eventtime)*24) as MTTE_ind_Daily from trmanager.tr_first_level t 
							where trtime between " . q($startDate) . " and " . q($endDate) . " and trtype='DAILY'
							and (" . NM_TX_ESCALATEION_GROUP . ")
     					group by escalationby
              order by escalationby";
  		
  		$rs=odbc_exec($this->conn,$sql);
  		
  		 while($row = odbc_fetch_object($rs))
			 {
				$data['r10'][] = $row;
			 }			
			
			//get indivisual MTTE Quality
			$sql = "select escalationby, avg((escalationtime-eventtime)*24) as MTTE_ind_Quality from trmanager.tr_first_level t 
							where trtime between " . q($startDate) . " and " . q($endDate) . " and trtype='QUALITY'
							and (" . NM_TX_ESCALATEION_GROUP . ")
     					group by escalationby
              order by escalationby";
  		
  		
  		$rs=odbc_exec($this->conn,$sql);
  		
  		 while($row = odbc_fetch_object($rs))
			 {
				$data['r11'][] = $row;
			 }
			
			//get 10 TRs with worst MTTE 
  		$sql = "select trno, (escalationtime-eventtime)*24 as MTTE from trmanager.tr_first_level
  		        where EVENTTIME between " . q($startDate) . " and " . q($endDate) . " and trtype='DAILY' and (" . NM_TX_ESCALATEION_GROUP . ")
  		        and (escalationtime-eventtime)*24 > 0.5 and ROWNUM<=10
  		        order by MTTE desc";
  		
  		
  		$rs=odbc_exec($this->conn,$sql);
  		
  		while($row = odbc_fetch_object($rs))
			{
			$data['r12'][] = $row;
			}
			 
  		//get 10 TRs with worst MTTR 
  		$sql = "select TRNO,(cleartime-escalationtime)*24 as MTTR from trmanager.tr_first_level
  		        where EVENTTIME between " . q($startDate) . " and " . q($endDate) . " and trtype='DAILY' and (" . NM_TX_ESCALATEION_GROUP . ")
  		        and (cleartime-escalationtime)*24 > 4 and ROWNUM<=10
  		        order by MTTR desc";
  		
  		
  		$rs=odbc_exec($this->conn,$sql);
  		
  		while($row = odbc_fetch_object($rs))
			{
			$data['r13'][] = $row;
			}
			
				
  		$data['start_date'] = $tempStartDate;
  		$data['end_date']   = $tempEndDate;
  		//dumpvar($data);
  		
  		return createPage(REPORT_MANAGER_TEMPLATE, $data);
  	}
  	function showReFaultReport()
  	{
  		//set current navigation         
  		$this->setNavigation('recurrent_fault');
  		
  		$data['report'] = 'recurrent_fault';
  		
  		//set end date with one day advance
  		$tempEndDate    = getUserField('end_date');
  		$endDateParts = explode('-', $tempEndDate);  		
  		$endDate      = date("d-F-y", mktime(0, 0, 0, $endDateParts[1], $endDateParts[2]+1, $endDateParts[0]));  		
  		
  		$tempStartDate = getUserField('start_date');
  		$startDateParts = explode('-', $tempStartDate);  		
  		$startDate      = date("d-F-y", mktime(0, 0, 0, $startDateParts[1], $startDateParts[2], $startDateParts[0]));
  		
  		$sql = "select tr_detls.problem_component, count(tr_detls.problem_component) as total
              from trmanager.gptts_tr_details tr_detls, trmanager.gptts_tr_task_data tr_tks_dta, trmanager.jbpm_taskinstance tskinstance
              where tr_detls.id=tr_tks_dta.ticket_id and tr_tks_dta.task_instance_id=tskinstance.id_
                    and tr_tks_dta.employee_sub_section='71' and tskinstance.name_='TaskPrepareTicket'
                    and tr_detls.specific_problem like '%SITE%'                    
                    and tr_detls.event_time between " . q($startDate) . " and " . q($endDate) . "
              group by tr_detls.problem_component
              having count(tr_detls.problem_component)>=2
              order by total desc";
  		/*$sql = "select tr_detls.problem_component, count(tr_detls.problem_component) as total
              from trmanager.gptts_tr_details tr_detls, trmanager.gptts_tr_task_data tr_tks_dta, trmanager.jbpm_taskinstance tskinstance
              where tr_detls.id=tr_tks_dta.ticket_id and tr_tks_dta.task_instance_id=tskinstance.id_
                    and tr_tks_dta.employee_sub_section='71' and tskinstance.name_='TaskPrepareTicket'
                    and tr_detls.Ticket_Type='DAILY'
                    and tr_detls.event_time between " . q($startDate) . " and " . q($endDate) . "
              group by tr_detls.problem_component
              having count(tr_detls.problem_component)>=2
              order by total desc";*/
              
  		/*$sql = "select PROBLEMCOMPONENT, count(PROBLEMCOMPONENT) as total from trmanager.tr_first_level
  		        where EVENTTIME between " . q($startDate) . " and " . q($endDate) . " and trtype='DAILY' and (" . NM_TX_ESCALATEION_GROUP . ")
  		        group by PROBLEMCOMPONENT 
  		        having count(PROBLEMCOMPONENT) >=2 
  		        order by total desc";*/
  		 
  		 
  		$rs=odbc_exec($this->conGPTTS,$sql);
  		
  		while($row = odbc_fetch_object($rs))
			{
			$data['rec_faults'][] = $row;
			}    
			//dumpvar($data);
  		$data['start_date'] = $tempStartDate;
  		$data['end_date']   = $tempEndDate;
  		return createPage(REPORT_MANAGER_TEMPLATE, $data);
  	}
  	
  	function showTRDetail()
  	{
  		$problemComponent = getUserField('problem_component');
  		
  		//set end date with one day advance
  		$tempEndDate    = getUserField('end_date');
  		$endDateParts = explode('-', $tempEndDate);  		
  		$endDate      = date("d-F-y", mktime(0, 0, 0, $endDateParts[1], $endDateParts[2]+1, $endDateParts[0]));  		
  		
  		$tempStartDate = getUserField('start_date');
  		$startDateParts = explode('-', $tempStartDate);  		
  		$startDate      = date("d-F-y", mktime(0, 0, 0, $startDateParts[1], $startDateParts[2], $startDateParts[0]));
  		  		
  		 $sql = "select distinct(tr_detls.id), tr_detls.problem_component,tr_detls.specific_problem,tr_detls.problem_category,to_char(tr_detls.event_time, 'DD-MM-YY HH24:MI:SS') as EVENT_TIME
              from trmanager.gptts_tr_details tr_detls, trmanager.gptts_tr_task_data tr_tks_dta, trmanager.jbpm_taskinstance tskinstance
              where tr_detls.id=tr_tks_dta.ticket_id
                    and tr_tks_dta.task_instance_id=tskinstance.id_       
                    and tr_tks_dta.employee_sub_section='71'
                    and tr_detls.specific_problem like '%SITE%'
                    and tskinstance.name_='TaskPrepareTicket'                    
                    and tr_detls.event_time between " . q($startDate) . " and " . q($endDate) . "
                    and tr_detls.problem_component=" . q($problemComponent) . "
              order by tr_detls.id desc";
               		
  		/*$sql = "select * from trmanager.tr_first_level
  		        where EVENTTIME between " . q($startDate) . " and " . q($endDate) . " and trtype='DAILY' and 
  		        PROBLEMCOMPONENT = " . q($problemComponent) . " and (" . NM_TX_ESCALATEION_GROUP . ") order by trno desc";  	*/
  		
  		$rs=odbc_exec($this->conGPTTS,$sql);
  		
  		while($row = odbc_fetch_object($rs))
			{
			  $data['tickets'][] = $row;
			}	
			
			$htmlString = '';
			
			foreach($data['tickets'] as $row)
			{	
				
				$comment = getTicketComment($this->conGPTTS, $row->ID);
				
				$htmlString .=  '<tr  bgcolor="#DCE8FA">
				                   <td align="center">' . $row->ID . '</td>
				                   <td>' . $row->SPECIFIC_PROBLEM . '</td>
				                   <td>' . $row->PROBLEM_CATEGORY . '</td>
				                   <td>' . $row->PROBLEM_COMPONENT . '</td>
				                   <td align="center">' . $row->EVENT_TIME . '</td>
				                   <td>' . $comment[0] . '</td>
				                   <td>' . $comment[1] . '</td>
				                </tr>';
			}
			
			echo '<link rel="stylesheet" href="/nm_tx/common/css/default.project.css" type="text/css">
			      <table bgcolor="#FFFFFF" cellspacing="2" cellpadding="8" align="center" width="100%">
			        <tr style="font-weight: bold; color: #FFFFFF; background: #336699;">
			          <td align="center">Ticket No.</td><td>Specific Problem</td><td>Problem Category</td><td>Problem Component</td><td align="center">Event Date</td><td>NM Comment</td><td>Engineer Comment</td>
			        </tr>
			        ' . $htmlString . '			      
			      </table>';
			
			exit;
  	}
  }
?>