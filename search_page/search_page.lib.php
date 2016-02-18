<?php
function getNM_TXComment(&$list)
{
	$info['table'] = COMMENT_TBL;
	$info['debug'] = false;
	
	if($list)
	{
	  foreach($list as $key => $value)
	  {
	    $info['where'] = 'fault_id = ' . $list[$key]->fault_id . ' order by comment_id asc';
	    
	    $result = select($info);
	    
	    $commentText = '';
	    
	    if($result)
	    {
	      foreach($result as $value)
	      {
	      	$commentText .= '<b>' . $value->username . ':</b> ' . $value->comment . '<br>';
	      }
	      
	      $list[$key]->nm_tx_comment = $commentText;
	    }
	  }
  }  
}
?>