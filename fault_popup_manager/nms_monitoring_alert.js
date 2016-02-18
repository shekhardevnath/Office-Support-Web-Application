var AlertTimeLimit = 60000;

setTimeout("showPopupAlert();", AlertTimeLimit);       

function showPopupAlert()
{
	var curDate    = new Date();
	var curHours   = curDate.getHours();
	var curMinutes = curDate.getMinutes();
	
	if(curMinutes==00)
	{
	  window.open ("/nm_tx/fault_popup_manager/nms_monitoring_alert.php?type=hourly", "Reminder", "width=500, height=350, scrollbars=0, resizable=1");
	}
	  
	if(curHours == 06 || curHours == 13 || curHours == 22)
	{
		if(curMinutes == 30)
		{
			window.open ("/nm_tx/fault_popup_manager/nms_monitoring_alert.php?type=shift", "Reminder", "width=500, height=350, scrollbars=0, resizable=1");
		}
	}
				  
	setTimeout("showPopupAlert();", AlertTimeLimit);       
}