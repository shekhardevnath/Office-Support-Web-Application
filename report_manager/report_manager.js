function showTRDetailsPopUP(problemComponent, startDate, endDate)
{
	var myWindow = window.open ("/nm_tx/report_manager/report_manager.php?cmd=show_TR_detail&problem_component=" + problemComponent + "&start_date=" + startDate + "&end_date=" + endDate, "tr_details", "width=900, height=400, left=150, top=150, scrollbars=1");	
}