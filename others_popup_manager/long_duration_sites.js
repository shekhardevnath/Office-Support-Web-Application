var timeLimit = 30000;

setTimeout("getSiteList();", timeLimit);       

function getSiteList()
{	
	var curDate    = new Date();
	var curHours   = curDate.getHours();
	var curMinutes = curDate.getMinutes();
	
	if (curMinutes==00)
	{
	  window.open ("/nm_tx/others_popup_manager/long_duration_sites.php", "SiteList", "width=500, height=350, scrollbars=1");
  }
  
	setTimeout("getSiteList();", timeLimit);       
}