/*
 *   File: alert_manager.js
 *
 */

RE_EMAIL     = new RegExp(/^[A-Za-z0-9](([_|\.|\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([_|\.|\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/);
RE_GROUPNAME = new RegExp(/^[A-Za-z0-9\_]+$/);

var role_id=new Array();
var role_title=new Array();
var role_string = '';
var user_role=new Array();
var nm=new Array();
var cnt=0;
var exectd=0;

function setupForm(frm)
{
   with (frm)
   {
      setRequiredField(name,         'textbox',   'name');
      setRequiredField(group_type,   'dropdown',  'group_type');
      setRequiredField(group_email,  'textbox',   'group_email');
      setRequiredField(status,       'radio',     'status');
      setRequiredField(group_leader, 'dropdown',  'group_leader');
   }
}

function validateFields(frm)
{
   with(frm)
   {
      if (!RE_GROUPNAME.exec(name.value))
      {
         highlightTableColumn('name');
         alert(ERROR_GROUPNAME);
         return false;
      }

      else if (!RE_EMAIL.exec(group_email.value))
      {
         highlightTableColumn('group_email');
         alert(ERROR_EMAIL);
         return false;
      }
   }

   return true;
}

function doFormSubmit()
{
   requiredFields.length = 0;

   var errCnt = 0;
   var frm = document.groupManagerForm;

   // Setup required fields
   setupForm(frm);

   // Validate form for required fields
   errCnt = validateForm(frm);

   if (errCnt)
   {
      alert(MISSING_REQUIRED_FIELDS);
      return false;
   }

   else
   {
      if(validateFields(frm))
      {
         return true;
      }
      else
         return false;
   }
}

function isNotMember(uid)
{
	 len = mem_id.length;
	 for (i=0; i<len; i++)
	 {
	    if (mem_id[i] == uid)
	       return false;
	 }

	 return true;
}

function fillOptionList(member, value)
{
   while(member.options.length)
      member.removeChild(member.options[0]);

   var len = eval("user_"+value+ "_name.length");

   for(x=0; x<len; x++)
   {
   	 if (isNotMember(eval("user_"+value+"_id["+x+"]")))
   	 {
       newOption = new Option (eval("user_"+value+"_name["+x+"]"), eval("user_"+value+"_id["+x+"]"));
       member.options.add(newOption);
     }
   }
}

function moveList(fbox, tbox, opts, tlbox, dir)
{
   var tl = tlbox.value;

   for(i = 0; i < fbox.options.length; ) {
      if (fbox.options[i].selected && fbox.options[i].value != '')
      {
         var newOpt = new Option(fbox.options[i].text, fbox.options[i].value);
         tbox.options[tbox.length] = newOpt;

			   if (dir == 0)
			   {
            var newOpt = new Option(fbox.options[i].text, fbox.options[i].value);
            tlbox.options[tlbox.length] = newOpt;
         }
         else
            tlbox.options[i+1] = null;

         fbox.options[i] = null;

         continue;
      }
      i++;
   }
   mysort(fbox);
   mysort(tbox);
   mysort(tlbox);

   tlbox.value = tl;

   opts.value='';
   mem_id = new Array();
   mem_name = new Array();

   if (dir == 0)
   {
      for(c = 0; c < tbox.length; c++) {
          opts.value = opts.value + tbox.options[c].value;
          if (c < tbox.length-1)
          {
          	  opts.value = opts.value + ',';
          }

          mem_id[c] = tbox.options[c].value;
          mem_name[c] = tbox.options[c].text;
      }
   }
   else
   {
      for(c = 0; c < fbox.length; c++)
      {
          opts.value = opts.value + fbox.options[c].value;
          if (c < fbox.length-1)
          {
          	 opts.value = opts.value + ',';
          }
          mem_id[c] = fbox.options[c].value;
          mem_name[c] = fbox.options[c].text;
      }
   }
}

function mysort(selBox)
{
   for(i = 0; i < selBox.options.length; i++)
   {
        for(j = i+1; j < selBox.options.length; j++)
        {
           if(selBox.options[i].text.toLowerCase()>selBox.options[j].text.toLowerCase())
           {
             var temp1 = selBox.options[i].value;
             var temp2 = selBox.options[i].text;
             selBox.options[i].value = selBox.options[j].value;
             selBox.options[i].text  = selBox.options[j].text;

             selBox.options[j].value = temp1;
             selBox.options[j].text  = temp2;

           }
        }
        //alert(selBox.options[i].value)
        //alert(selBox.options[i].text)
   }
}
