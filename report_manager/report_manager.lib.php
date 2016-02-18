<?php

  function getTRComment($conn, $trNo, $commentBy = 0)
  {
  	if($conn)
  	{
  		$sql = "select * from trmanager.tr_comments where trno=$trNo and commentedby=$commentBy";
  		
  		$rs=odbc_exec($conn,$sql);
  		
  		while($row = odbc_fetch_object($rs))
			{
			  $data['comment'][] = $row;
			}
			
			$returnString = '';
			
			if($data['comment'])
			{
			  foreach($data['comment'] as $row)
			  {
			  	$returnString .= $row->USR . ':' . $row->COMMENTS . '<br>';
			  }
		  }
			
			return $returnString;
  	}  	
  }
  
  function getTicketComment($conGPTTS, $TicketNo)
  {     
  	if($conGPTTS && $TicketNo)
  	{  		
  		$sql = "select ttd.employee_sub_section,comm.actorid_,comm.message_
             from  trmanager.gptts_tr_task_data ttd, trmanager.jbpm_comment comm
             where ttd.task_instance_id=comm.taskinstance_
                   and ttd.ticket_id=" . $TicketNo . "
             order by ttd.task_instance_id";
      
      $rs = odbc_exec($conGPTTS, $sql);
      
      while($row = odbc_fetch_object($rs))
      {
      	$data['comment'][] = $row;
      }
      
      $comment = array();
      
      if($data['comment'])
      {
      	foreach($data['comment'] as $row)
      	{
      		if($row->EMPLOYEE_SUB_SECTION == 71)
      		{
      			$comment[0] .= $row->ACTORID_ . ': ' . $row->MESSAGE_ . '<br>'; 
      		}
      		else
      		{
      			$comment[1] .= $row->ACTORID_ . ': ' . $row->MESSAGE_ . '<br>'; 
      		}
      	}
      }       
  	}
  	
  	return $comment;
  }

?>