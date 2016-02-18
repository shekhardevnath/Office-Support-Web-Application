
var timeLimit = 60000;

setTimeout("searchNewFault();", timeLimit);       

function searchNewFault()
{
		var url = '/nm_tx/fault_popup_manager/fault_popup_manager.php';
		var pars = 'cmd=getNewFault';
		
		var myAjax = new Ajax.Request(
			url, 
			{
				method: 'get', 
				parameters: pars, 
				onComplete: showResponse
			});
	  
	  //showNMmonitoringAlert();
	  
		setTimeout("searchNewFault();", timeLimit);       
} 

function showResponse(returnResponse)
{
	rspText = returnResponse.responseText;
	
	var dataArray = rspText.split('*$#');
	
	if(rspText)
	{
	  for(i=0;i<dataArray.length;i++)
	  {
	  	showPopUp(dataArray[i]);
	  }
  }
}

function showPopUp(faultID)
{
	window.open ("/nm_tx/fault_popup_manager/fault_popup_manager.php?cmd=showPopUp&fault_id=" + faultID, "NewFault" + faultID, "width=500, height=280, scrollbars=1");
}

function showNMmonitoringAlert()
{
	var curDate    = new Date();
	var curHours   = curDate.getHours();
	var curMinutes = curDate.getMinutes();
	
	if(curHours == 6 || curHours == 14 || curHours == 22)
	{
		if(curMinutes == 0 || curMinutes == 1)
		{
			window.open ("/nm_tx/fault_popup_manager/nms_monitoring_alert.html", "Reminder", "width=500, height=300, scrollbars=0");
		}
	}
}