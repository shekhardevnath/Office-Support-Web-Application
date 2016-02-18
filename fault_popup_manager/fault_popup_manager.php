<?php

  // include the main configuration file
   require_once($_SERVER['DOCUMENT_ROOT'] .'/nm_tx/common/conf/main.conf.php');
   require_once(LOCAL_CONFIG_DIR          .'/dp.conf.php');
   require_once(LOCAL_LIB_DIR             .'/dp.lib.php');   

   
   $cmd = getUserField('cmd');
   
   if($cmd == 'getNewFault')
   {
     getNewFault();
   }  
   else if($cmd == 'showPopUp')
   {
   	 $faultID = getUserField('fault_id');
   	 showPopUp($faultID);
   }
   else if($cmd == 'update')
   {
   	  updateFault();
   }
      
   function getNewFault()
   {
   	 $type = $_SESSION['type'];
   	 
   	 $info['table']  = FAULT_TBL;
   	 $info['fields'] = array('fault_id');
   	 $info['debug']  = false;
   	 
   	 if($type)   	 
   	   $info['where']  = 'status = "New" and fault_type= ' . q($type);
   	 else
   	   $info['where']  = 'status = "New"';
   	 
   	 $result = select($info);
   	 
   	 if($result)
   	 {   	 
   	   foreach($result as $row)
   	   {
   	   	 $data[] = $row->fault_id;   	 	 
       }
       
       $dataString = implode($data, '*$#');
     }   
     
     echo $dataString;     
   }
   
   function showPopUp($faultID)
   {
   	 $type = $_SESSION['type'];
   	 
   	 $info['table']  = FAULT_TBL;
   	 $info['debug']  = false;
   	 $info['fields'] = array('problem_category', 'problem_component', 'impact', 'finding', 'submit_by');
   	 $info['where']  = 'fault_id = ' . $faultID;
   	 
   	 $result = select($info);   	 
   	 $data   = $result[0]; 
   	 
   	 $HTMLText = '<html>
   	               <header><title>New Fault ' . $faultID . '</title></header>
   	               <body onLoad="self.focus();" background="/nm_tx/common/image/back1.png">
   	               <h3 align="center">New fault entry <font color="#D67F00"><i>('. $type .')</i></font>. Please acknowledge it.</h3>   	               
   	               <table border="0" align="center" cellpadding="5" cellspacing="2" bgcolor="black" width="100%">
   	                 <tr bgcolor="white">
   	                   <td colspan="2"><b>Fault Submitted by: <font color="#D67F00"><i>"' . $data->submit_by . '"</i></font></b></td>
   	                 </tr>
   	                 <tr bgcolor="white">
   	                   <td><b>Category</b></td>
   	                   <td>' . $data->problem_category . '</td>
   	                 </tr>
   	                 <tr bgcolor="white">
   	                   <td><b>Component</b></td>
   	                   <td>' . $data->problem_component . '</td>
   	                 </tr>
   	                 <tr bgcolor="white">
   	                   <td><b>Impact</b></td>
   	                   <td>' . $data->impact . '</td>
   	                 </tr>
   	                 <tr bgcolor="white">
   	                   <td><b>Findings</b></td>
   	                   <td>' . $data->finding . '</td>
   	                 </tr>
   	               </table>
   	               <p align="center">
   	                 <form name="acknowledgeForm" action="/nm_tx/fault_popup_manager/fault_popup_manager.php?cmd=update" method="POST">
   	                   <input type="hidden" value="' . $faultID . '" name="fault_id">
   	                   <input type="hidden" value="' . $data->problem_category . '" name="problem_category">
   	                   <input type="submit" value="Acknowledge">
   	                 </form>
   	               </p>
   	               </body>
   	              </html>';
   	              
   	 echo $HTMLText;
   }
   
   function updateFault()
   {
   	  $faultID = getUserField('fault_id');
   	  $problemCategory = getUserField('problem_category');
   	
   	  if($problemCategory == 'Power Alarm')
   	     $data['status'] = 'Escalated';
   	  else
   	     $data['status'] = 'In Progress';
   	        
   	  $data['last_updated_by']  = $_SESSION['username'];
  		$data['last_update_date'] = date("Y-m-d H:i:s");  
  		$data['acknowledged_by']  = $_SESSION['username'];
  		$data['acknowledge_date'] = date("Y-m-d H:i:s");
  		
   	  $info['table'] = FAULT_TBL;
   	  $info['debug'] = true;
   	  $info['data']  = $data;
   	  $info['where'] = 'fault_id = ' . $faultID . ' and status = "New"';
   	  
   	  $result = update($info);   	  
   	  echo '<script>window.close();</script>';
   }
?>