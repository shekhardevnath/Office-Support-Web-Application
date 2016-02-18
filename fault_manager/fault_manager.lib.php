<?php

function getCategoryList($fault_type)
{
	if($fault_type)
	{
	  $groupID = $_SESSION['group_id'];
	  
	  $where = 'group_id = ' . $groupID . ' and ' . $fault_type . '="Y"';
    
	  $info['table']  = CATEGORY_TBL;
	  $info['debug']  = false;
	  $info['fields'] = array('category'); 
	  $info['where']  = $where . ' order by category asc';
	  
	  $category = select($info);
	  
	  if($category)
	  {
	    foreach($category as $row)
	    {
	    	$data[] = $row->category;
	    }
    }
  }
	return $data;
}

function getNM_TXComment(&$data)
{
	$info['table'] = COMMENT_TBL;
	$info['debug'] = false;
	
	if($data['list'])
	{
	  foreach($data['list'] as $key => $value)
	  {
	    $info['where'] = 'fault_id = ' . $data['list'][$key]->fault_id . ' order by comment_id asc';
	    
	    $result = select($info);
	    
	    $commentText = '';
	    
	    if($result)
	    {
	      foreach($result as $value)
	      {
	      	$commentText .= '<b>' . $value->username . ':</b> ' . $value->comment . '<br>';
	      }
	      
	      $data['list'][$key]->nm_tx_comment = $commentText;
	    }
	  }
  }  
}

function getUserList($groupID = null)
{
	if($groupID)
	{
		$where = 'group_id = ' . $groupID;
	}	
	else
	{
		$where = '1';
	}
	
	$info['table']  = USER_TBL;	
	$info['debug']  = false;
	$info['fields'] = array('username'); 
	$info['where']  = $where . ' order by username asc';
	
	$result = select($info);
	
	if($result)
	{
	  foreach($result as $row)
	  {
	  	$data[] = $row->username;
	  }
  }
  	
	return $data;
}

function removeEnterKey($address)
{ 
  $arrString = explode(chr(13) , $address);
  $address="";
  foreach($arrString as $val)
  {
  	for($i=0;$i<(strlen($val));$i++)
  	{
  		if(ord($val[$i])!=10)
  		{
  		$address = $address . $val[$i];
  		}
  	else
  		{
  		$address = $address . " ";
  		}
  	} 
  }
  return $address;
}

function getTotalRows($filterClause = null)
{
	if(!$filterClause) $filterClause ='1';
	
	$info['table']  = FAULT_TBL;
	$info['debug']  = false;
	$info['fields'] = array('count(fault_id) as total');
	$info['where']  = $filterClause;
	
	$result = select($info);
	
	if($result)
	{
		$totalRows = $result[0]->total;
	}
	
	if($totalRows)
	{
		if($totalRows > MAXIMUM_ROWS_TO_DISPLAY)
		  return MAXIMUM_ROWS_TO_DISPLAY;
		else
		  return $totalRows;  
	}
	else
	  return 0;
}

function getSeverity($probCategory = null)
{
	if($probCategory)
	{
	  $info['table']  = CATEGORY_TBL;
	  $info['debug']  = false;
	  $info['fields'] = array('severity');
	  $info['where']  = 'category = "' . $probCategory . '"';
	  
	  $result = select($info);
	  
	  if(result)
	  {
	  	return $result[0]->severity;
	  }
  }
}
?>