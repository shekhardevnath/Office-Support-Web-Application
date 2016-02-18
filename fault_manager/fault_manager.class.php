<?php
  /**
  *file: fault_manager.class.php
  *purpose: fault manager application class
  **/
  
  class faultManagerApp extends DefaultApplication
  {
  	function run()
  	{
  		$cmd = getUserField('cmd');
  		
  		switch($cmd)
  		{
  			case 'edit'         : $screen = $this->showEditor();       break;
  			case 'edit_fault'   : $screen = $this->showFaultEditor();  break;
  			case 'add'          : $screen = $this->addFault();         break;
  			case 'update_fault' : $screen = $this->updateFault();      break;
  			case 'show_details' : $screen = $this->showFaultDetails(); break;
  			default             : $screen = $this->showFaultList();
  		}
  		
  		
  		echo $this->displayScreen($screen);
  	}
  	
  	function showEditor()
  	{
  		//set current navigation item
  		$this->setNavigation('fault_entry');
  		
  		$fault_type = getUserField('fault_type');
  		
  		$data['fault_type']      = $fault_type;
  		$data['severity_list']   = getEnumFieldValues(FAULT_TBL, 'severity');
  		$data['fault_type_list'] = getEnumFieldValues(FAULT_TBL, 'fault_type');
  		$data['category_list']   = getCategoryList($fault_type);  				  		
  		  		
  		return createPage(FAULT_EDITOR_TEMPLATE, $data);
  	}
  	
  	function showFaultEditor()
  	{
  		$faultId = getUserField('fault_id');
  		
  		$info['table']  = FAULT_TBL;
  		$info['debug']  = false;
  		$info['fields'] = array('status', 'severity');
  		$info['where']  = 'fault_id = ' . $faultId;
  		
  		$result = select($info);  		
  		
  		$data['fault_id']      = $faultId;  		
  		$data['status_list']   = getEnumFieldValues(FAULT_TBL, 'status');
  		$data['severity_list'] = getEnumFieldValues(FAULT_TBL, 'severity');
  		$data['status']        = $result[0]->status;
  		$data['severity']      = $result[0]->severity;
  		
  		return createPage(FAULT_EDITOR_TEMPLATE, $data);
  	}
  	
  	function addFault()
  	{
  		$noOfFault = getUserField('no_of_fault');
  		
  		$data['problem_category'] = getUserField('problem_category');
  		$data['fault_type']       = getUserField('fault_type');
  		$data['severity']         = getSeverity($data['problem_category']);
  		
  		$data['submit_by']   = $_SESSION['username'];
  		$data['submit_date'] = date("Y-m-d H:i:s");
  		$data['group_id']    = $_SESSION['group_id'];
  		
  		$info['table'] = FAULT_TBL;  		
  		$info['debug'] = false;
  		
  		for($i=1; $i<=$noOfFault; $i++)
  		{
  			$problemComponent = strtoupper(getUserField('problem_component'                . $i));
  			$nodeA            = strtoupper(getUserField('node_a'                           . $i));
  			$nodeB            = strtoupper(getUserField('node_b'                           . $i));
  			$impact           = strtoupper(removeEnterKey(getUserField('impact'            . $i)));
  			$finding          = strtoupper(removeEnterKey(getUserField('finding'           . $i)));
  			
  			if($problemComponent)
  			{
  		    $data['problem_component'] = $problemComponent;  		    
  		    $data['node_a']            = $nodeA;
  		    $data['node_b']            = $nodeB;
  		    $data['impact']            = $impact;
  		    $data['finding']           = $finding;  		    
  		    
  		    $info['data'] = $data;
  		    
  		    //save fuult
  		    $fault = insert($info);
  		    
  		    //save if any attachment
  		    if($fault)
  		    {
			    	 $currentYear         = date('Y');
			    	 $currentMonth        = date('F');
			    	 $attachmentYearPath  = DOCUMENTS_DIR . '/' . $currentYear;
			    	 $attachmentMonthPath = $attachmentYearPath . '/' . $currentMonth;
          
			    	 if(!file_exists($attachmentYearPath))
			    	 {
			    			mkdir($attachmentYearPath);
			    	 }
			    	 if(!file_exists($attachmentMonthPath))
			    	 {
			    	 		mkdir($attachmentMonthPath);
			    	 }
			    
			    	 $destPath  = $attachmentMonthPath . '/' . $fault['newid'] . '_' . $_FILES['attachment' . $i]['name'];
			    	 $fileSaved = move_uploaded_file($_FILES['attachment' . $i]['tmp_name'], $destPath);
			    }
  		  
  		    //save the attachment path into database
  		    if($fileSaved)
  		    { 
  		    	$relAttPath = REL_APP_DIR . REL_DOCUMENTS_DIR . '/' . $currentYear . '/' . $currentMonth . '/' . $fault['newid'] . '_' . $_FILES['attachment' . $i]['name'];
  		    	
  		    	$query = 'update ' . FAULT_TBL . ' set attachment = "' . $relAttPath . '" where fault_id = ' . $fault['newid'];
  		    	mysql_query($query);
  		    }
  		  }
  		}
  		
  		header('Location: ' . FAULT_MANAGER_URL);
  		exit;
  	}
  	
  	function updateFault()
  	{
  		$faultId   = getUserField('fault_id');
  		$preStatus = getUserField('pre_status');
  		$comment   = strtoupper(getUserField('comment'));
  		$status    = getUserField('status');
  		
  		//update fault if status has been changed or commnet has been given
  		if($comment || ($preStatus != $status))
  		{
  		  $data['last_updated_by']  = $_SESSION['username'];
  		  $data['last_update_date'] = date("Y-m-d H:i:s");
  		  $data['status']           = $status;
  	  }
  	  $data['severity'] = getUserField('severity');
  		
  		$info['table']  = FAULT_TBL;
  		$info['debug']  = false;
  		$info['data'] = $data; 
  		$info['where']  = 'fault_id = ' . $faultId;
  		
  		$result = update($info);
  		
  		//Save NM_TX comments
  		if($comment || ($preStatus != $status))
  		{
  		  $data = array();
  		  $info = array();
  		  
  		  $data['fault_id']     = $faultId;
  		  $data['username']	    = $_SESSION['username'];
  		  $data['comment']      = $comment;
  		  $data['comment_date'] = date("Y-m-d H:i:s");
  		  
  		  $info['table'] = COMMENT_TBL;
  		  $info['debug'] = false;
  		  $info['data']  = $data;
  		  
  		  insert($info);
  	  }
  		
  		header('Location: ' . FAULT_MANAGER_URL);
  		exit;  		
  	}
  	
  	function showFaultDetails()
  	{
  		$faultId = getUserField('fault_id');
  		
  		$data['show_details'] = 'show_details';
  		$data['fault_id']     = $faultId;
  		
  		$info['table'] = FAULT_TBL;
  		$info['debug'] = false;
  		$info['where'] = 'fault_id = ' . $faultId;   		
  		
  		$data['list'] = select($info);
  		
  		//get NM_TX comment
  		getNM_TXComment($data); 
  		
  		return createPage(FAULT_EDITOR_TEMPLATE, $data);
  	}
  	
  	function showFaultList()
  	{
  		//set current navigation item
  		$this->setNavigation('fault_view');
  		
  		$userType  = $_SESSION['user_type'];
  		$status    = getUserField('status');
  		$user      = getUserField('user');
		$faultType = getUserField('fault_type');
  		
  		if($userType == NM_TX_USER)
  		{
  			$filterClause = '1';  			
  			if($user)      $filterClause .= ' and last_updated_by = "' . $user      . '"';
  			if($status)    $filterClause .= ' and status          = "' . $status    . '"';
			if($faultType) $filterClause .= ' and fault_type      = "' . $faultType . '"';
  	  }
  	  else
  	  {
  	  	$groupId = $_SESSION['group_id'];
  	  	
  	  	if($groupId == NM_SN_GROUP_ID || $groupId == NM_CORE_GROUP_ID)
  	  	{
  	  		$filterClause = '(group_id = ' . NM_SN_GROUP_ID . ' or group_id = ' . NM_CORE_GROUP_ID . ')';
  	  	}
  	  	else
  	  	{
  	  		$filterClause = 'group_id = ' . $groupId;
  	  	}
  	  	  	  	
  	  	if($user)   $filterClause .= ' and submit_by = "' . $user   . '"';
  			if($status) $filterClause .= ' and status    = "' . $status . '"';
  	  }
  		
  		//Codes for Pagination starts here
  		$start = getUserField('start');   
      $start ? $start = $start : $start = 0;
      
      if($user)   $data['user']   = $user;
      if($status) $data['status'] = $status;
      
      $data['num_of_pages']   = ceil(getTotalRows($filterClause)/ROWS_PER_PAGE);
      $data['displayed_page'] = ($start/ROWS_PER_PAGE) + 1;
      $data['rows_per_page']  = ROWS_PER_PAGE;      
  		//Codes for Pagination ends here
  		
  		$info['table'] = FAULT_TBL;
  		$info['debug'] = false;
  		$info['where'] = $filterClause . ' order by fault_id desc limit ' . $start . ' , ' . ROWS_PER_PAGE ;
  		
  		$data['list']            = select($info);
  		$data['status_list']     = getEnumFieldValues(FAULT_TBL, 'status');
		$data['fault_type_list'] = getEnumFieldValues(FAULT_TBL, 'fault_type');
  		$data['user']            = $user;
  		$data['status']          = $status;
		$data['fault_type']      = $faultType;  		
  		$data['user_list']       = getUserList($_SESSION['group_id']);  	  
  	
  		if($userType == NM_TX_USER)
  		{
  			return createPage(NM_TX_FAULT_LIST_TEMPLATE, $data);
  		}  
  	  else
  	  {
  	    return createPage(OTHER_FAULT_LIST_TEMPLATE, $data);
  	  }  
  	}
  }
?>