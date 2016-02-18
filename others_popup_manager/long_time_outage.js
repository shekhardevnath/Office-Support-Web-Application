var timeLimit = 30000;

setTimeout("getSiteList();", timeLimit);       

function getSiteList()
{	
	var curDate    = new Date();
	var curHours   = curDate.getHours();
	var curMinutes = curDate.getMinutes();
	
	if(curMinutes==00 && (curHours==09 || curHours==12 || curHours==15 || curHours==18 || curHours==21))
	{
	  window.open ("/nm_tx/others_popup_manager/long_time_outage.php", "reminder", "width=500, height=350, scrollbars=0");
    }

    setTimeout("getSiteList();", timeLimit);       
}