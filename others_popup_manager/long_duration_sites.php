<?php
//set the connection with SQL Server and select DB
$conn = odbc_connect("NETVISION","mk5","mk5mnm");
//change database
odbc_exec($conn,'use FM');


//get Outage Data
$sql="select cell, eventtime, cast(getdate()-eventtime as float)*24 as outageDuration 
      from tbloutager 
      where status='notcleared' and cast(getdate()-eventtime as float)*24>3 and subcenter!='****New Site*****'
      order by eventtime,cell";
$rs=odbc_exec($conn,$sql);

$string = '';

while($row = odbc_fetch_object($rs))
{
$data[] = $row;
$string .= "<tr bgcolor='#D6E1F2'><td>" . findCriticalSites($conn,$row->cell) . "</td><td>$row->eventtime</td><td align='right'><b>" . round($row->outageDuration, 2) . "</b></td></tr>";
}

$htmlString =  "<html>
                 <body bgcolor='#EDF2F9' onLoad='self.focus();'>
                   <h2 align='center'> Site list with down time more than 3 hours. </h2>
                   <table bgcolor='black' cellspacing='2' cellpadding='5' align='center'>            
                     <tr bgcolor='#679FAF'>
                       <td><font color='white'><b>Site</b></font></td>
                       <td><font color='white'><b>Event Time</b></font></td>
                       <td><font color='white'><b>Duration (Hr.)</b></font></td></tr>            
                    $string
                   </table>
                   <p align='center'><b><a href='' onclick='window.close();'>||Close||</a></b></p>
                 </body>
               </html>";

echo $htmlString;

//close the connection SQL DB
odbc_close($conn);  	

function findCriticalSites($conn, $site)
{
	$query = "select sitename 
	          from tblcriticalsites
	          where sitename like '%$site%'
	          order by region";	
	$rs = odbc_exec($conn,$query);	
	
	if (odbc_num_rows($rs))	
	  $prsSites = $site . ' (Critical Site)';
	else
	  $prsSites = $site;  
	
	return $prsSites;
}
?>