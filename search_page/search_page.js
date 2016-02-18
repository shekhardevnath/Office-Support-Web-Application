function showFaultDetailsPopUP(faultID)
{
	var myWindow = window.open ("/nm_tx/search_page/search_page.php?cmd=show_details&fault_id=" + faultID, "Fault_" + faultID, "width=600, height=312, left=150, top=150, scrollbars=1");	
}