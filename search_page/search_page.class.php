<?php
  /**
  *file: search_page.class.php
  *purpose: search page application class
  **/
  
  class searchPageApp extends DefaultApplication
  { 	
  	function run()
  	{ 
  		$cmd = getUserField('cmd');
		    		
  		switch($cmd)
  		{
  			case 'search'       : $screen = $this->showSearchResult(); break;
  			case 'show_details' : $screen = $this->showFaultDetails(); break;
  			case 'category_list': $screen = $this->showCategoryList(); break;
  			default             : $screen = $this->showSearchPage(); 
  		}
  		
  		//set current navigation item
  		$this->setNavigation('fault_search');
  		
  		if($cmd == 'category_list')
  		{
  			echo $screen;
  		}
  		else
  		{
  		  echo $this->displayScreen($screen);
  		}
  	}
  	
  	function showSearchPage()
  	{
  		$data['start_date'] = date("Y-m-d");
  		$data['end_date']   = date("Y-m-d");
  		
  		$data['status_list']   = getEnumFieldValues(FAULT_TBL, 'status');
  		
  		return createPage(SEARCH_PAGE_TEMPLATE, $data);
  	}
  	
  	function showSearchResult()
  	{
  		//set end date with one day advance
  		$tempEndDate = getUserField('end_date');
  		$dateParts   = explode('-', $tempEndDate);  		
  		$endDate     = date("Y-m-d", mktime(0, 0, 0, $dateParts[1], $dateParts[2]+1, $dateParts[0]));
  		
  		$startDate = getUserField('start_date');  		
  		$nodeA     = getUserField('node_a');
  		$nodeB     = getUserField('node_b');
  		$userName  = getUserField('user_name');
  		$component = getUserField('problem_component');
  		$faultId   = getUserField('fault_id');
  		$category  = getUserField('category_name'); 
  		$status    = getUserField('status');
  		
  		$filterCaluse = "1";
  		
  		if($_SESSION['user_type'] != NM_TX_USER)
  		{
  			$groupId = $_SESSION['group_id'];
  			
  			if($groupId == NM_SN_GROUP_ID || $groupId == NM_CORE_GROUP_ID)
  		    $filterCaluse .= ' and (group_id = ' . NM_CORE_GROUP_ID . ' or group_id = ' . NM_SN_GROUP_ID . ')';
  		  else
  		    $filterCaluse .= ' and group_id = ' . $groupId;  
  		}
  		
  		if($nodeA)                 $filterCaluse .= ' and node_a like "%'            . $nodeA     . '%"';
  		if($nodeB)                 $filterCaluse .= ' and node_b like "%'            . $nodeB     . '%"';
  		if($component)             $filterCaluse .= ' and problem_component like "%' . $component . '%"';
  		if($userName)              $filterCaluse .= ' and submit_by = "'             . $userName  . '"';
  		if($faultId)               $filterCaluse .= ' and fault_id = "'              . $faultId   . '"';
  		if($category)              $filterCaluse .= ' and problem_category like "%'  . $category  . '%"';
  		if($status)                $filterCaluse .= ' and status = "'                . $status    . '"';
  		if($startDate && $endDate) $filterCaluse .= ' and submit_date between "'     . $startDate . '" and "' . $endDate . '"';
  		
  		$data['start_date']        = $startDate;
  		$data['end_date']          = $tempEndDate;
  		$data['node_a']            = $nodeA;
  		$data['node_b']            = $nodeB;
  		$data['user_name']         = $userName;
  		$data['problem_component'] = $component;
  		$data['fault_id']          = $faultId;  		
  		$data['category_name']     = $category;
  		$data['status_list']       = getEnumFieldValues(FAULT_TBL, 'status');
  		$data['status']            = $status;
  		
  		$info['table'] = FAULT_TBL;
  		$info['debug'] = false;
  		$info['where'] = $filterCaluse . ' order by fault_id desc limit 0, 100';
  		
  		$data['list']  = select($info);
  		
  	  return createPage(SEARCH_PAGE_TEMPLATE, $data);
  	}
  	
  	function showCategoryList()
  	{
  		$category = getUserField('category_name');
  		
  		$info['table'] = CATEGORY_TBL;
  		$info['fields']= array('distinct(category)'); 
  		$info['debug'] = false;
  		$info['where'] = "category like '%$category%'";
  		
  		$data = select($info);
  		
  		$list = '<ul>';
  		
  		foreach($data as $item)
  		{
  		  $list .= "<li>$item->category</li>";
  		}
  		
  		$list .= '</ul>';
  		
  		return $list;
  	}
  	
  	function showFaultDetails()
  	{
  		$faultId = getUserField('fault_id');
  		
  		$data['show_details'] = 'show_details';
  		$data['fault_id']     = $faultId;
  		
  		$info['table'] = FAULT_TBL;
  		$info['debug'] = false;
  		$info['where'] = 'fault_id = ' . $faultId;   		
  		
  		$list = select($info);
  		
  		getNM_TXComment($list);
  		
  		echo '<link rel="stylesheet" href="/nm_tx/common/css/default.project.css" type="text/css">
  		      <table bgcolor="#FFFFFF" cellspacing="2" cellpadding="8" align="center" width="100%">
  	  		    <tr bgcolor="#336699" align="center">
  	  		    	<td colspan="2"><b><font color="white">Details of Fault No. ' . $faultId .'</font></b></td>
  	  		    </tr>
  	  		    <tr>
  	  		    	<td bgcolor="#83A2D1" nowrap>Problem Type</td>
  	  		    	<td bgcolor="#DCE8FA">' . stripslashes($list[0]->fault_type) . '</td>
  	  		    </tr>
  	  		    <tr>
  	  		    	<td bgcolor="#83A2D1" nowrap>Problem Category</td>
  	  		    	<td bgcolor="#DCE8FA">' . stripslashes($list[0]->problem_category) . '</td>
  	  		    </tr>
  	  		    <tr>
  	  		    	<td bgcolor="#83A2D1" nowrap>Problem Component</td>
  	  		    	<td bgcolor="#DCE8FA">' . stripslashes($list[0]->problem_component) . '</td>
  	  		    </tr>
  	  		    <tr>
  	  		    	<td bgcolor="#83A2D1">Impact</td>
  	  		    	<td bgcolor="#DCE8FA">' . stripslashes($list[0]->impact) . '</td>
  	  		    </tr>
  	  		    <tr>
  	  		    	<td bgcolor="#83A2D1">Findings</td>
  	  		    	<td bgcolor="#DCE8FA">' . stripslashes($list[0]->finding) . '</td>
  	  		    </tr>
  	  		    <tr>
  	  		    	<td bgcolor="#83A2D1" nowrap>BO Comments</td>
  	  		    	<td bgcolor="#DCE8FA">' . stripslashes($list[0]->nm_tx_comment) . '</td>
  	  		    </tr>
  	  		    <tr>
  	  		    	<td bgcolor="#83A2D1">Status</td>
  	  		    	<td bgcolor="#DCE8FA"><b>' . $list[0]->status . '</b></td>
  	  		    </tr>
  	  		    <tr>
  	  		    	<td bgcolor="#83A2D1" nowrap>Submit By</td>
  	  		    	<td bgcolor="#DCE8FA">' . $list[0]->submit_by . '</td>
  	  		    </tr>
  	  		    <tr>
  	  		    	<td bgcolor="#83A2D1" nowrap>Submit Date</td>
  	  		    	<td bgcolor="#DCE8FA">' . $list[0]->submit_date . '</td>
  	  		    </tr>
  	  		    <tr>
  	  		    	<td bgcolor="#83A2D1" nowrap>Last Updated By</td>
  	  		    	<td bgcolor="#DCE8FA">' . $list[0]->last_updated_by . '</td>
  	  		    </tr>
  	  		    <tr>
  	  		    	<td bgcolor="#83A2D1" nowrap>Last Update Date</td>
  	  		    	<td bgcolor="#DCE8FA">' . $list[0]->last_update_date .'</td>
  	  		    </tr>
  	  	    </table>';
  	  	    exit;
  	}
  }
?>