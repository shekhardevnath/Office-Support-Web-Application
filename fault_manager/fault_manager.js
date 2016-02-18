var rowNo = 2;

function addRow()
{
	td1 = document.createElement('td');
	td1.innerHTML = '<input type="text" value="" size="20" maxlength="20" name="problem_component' + rowNo + '">';	
	
	td2 = document.createElement('td');
	td2.innerHTML = '<input type="text" value="" size="12" maxlength="10" name="node_a' + rowNo + '">';	
	
	td3 = document.createElement('td');
	td3.innerHTML = '<input type="text" value="" size="12" maxlength="10" name="node_b' + rowNo + '">';	
	
	td4 = document.createElement('td');
	td4.innerHTML = '<textarea rows="2" cols="28" name="impact' + rowNo + '"></textarea>';	
	
	td5 = document.createElement('td');
	td5.innerHTML = '<textarea rows="2" cols="28" name="finding' + rowNo + '"></textarea>';	
	
	td6 = document.createElement('td');	
	td6.innerHTML = '<input type="file" size="25" name="attachment' + rowNo + '">';	
	
	td7 = document.createElement('td');
	td7.setAttribute('align', 'center');
	td7.innerHTML = '<td align="center"><img src="/nm_tx/common/image/table/close.gif" onClick="removeMe(this.parentNode.parentNode);"></td>';	
	
	tr = document.createElement('tr');
	
	tr.appendChild(td1);
	tr.appendChild(td2);
	tr.appendChild(td3);
	tr.appendChild(td4);
	tr.appendChild(td5);
	tr.appendChild(td6);
	tr.appendChild(td7);
	
	document.getElementById('add_fault_table').appendChild(tr);
	
	document.getElementById('no_of_fault').value = rowNo;
	rowNo++;
}

function removeMe(trObj)
{
	trObj.parentNode.removeChild(trObj);
}

function doSubmitFault(msg)
{
	if(!document.getElementById('fault_type').value)
	{	
		alert('You forgot to select a "Problem Type"!');
  	return false;
  }
  else if (!document.getElementById('problem_category').value)
  {
  	alert('You forgot to select a "Problem Category"!');
  	return false;
  }
  else
  {  		
	  return doConfirm(msg);
  }
}

function doUpdateFault(faultStatus)
{
	if(document.getElementById('comment').value || (document.getElementById('status').value == faultStatus))
	{		
	  return doConfirm('You are going to Update Fault. Continue?');
  }
  else
  {
  	alert("You cannot go without comment.\nSo, please put some comment...");
  	return false;
  }
}
