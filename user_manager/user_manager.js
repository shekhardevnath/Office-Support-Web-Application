/*
 *   File: user_manager.js
 *
 */

RE_NAME     = new RegExp(/[^A-Z^a-z^ ^\.\^]$/);
RE_EMAIL    = new RegExp(/^[A-Za-z0-9](([_|\.|\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([_|\.|\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/);
RE_USERNAME = new RegExp(/^[a-z0-9\_]+$/);
RE_DECIMAL  = new RegExp(/^[0-9]{1,8}([\.]{1}[0-9]{1,2})?$/);
RE_NUMBER   = new RegExp(/^[0-9]+$/);
RE_PHONE    = new RegExp(/^((\d\d\d)|(\(\d\d\d\)))?\s*[\.-]?\s*(\d\d\d)\s*[\.-]?\s*(\d\d\d\d)$/);
RE_ZIP      = new RegExp(/^[0-9]{5}(([\-\ ])?[0-9]{4})?$/);

function setupForm(frm)
{
   with (frm)
   {
      setRequiredField(first_name,      'textbox',   'first_name');
      setRequiredField(last_name,       'textbox',   'last_name');
      setRequiredField(username,        'textbox',   'username');
      setRequiredField(user_type,       'dropdown',  'user_type');
      setRequiredField(email,           'textbox',   'email');
      setRequiredField(status,          'radio',     'status');

      if (uid == '')
      {
         setRequiredField(password,      'textbox',     'password');
      }

      if(frm.user_type.value == 'Employee')
      {
      	 setRequiredField(address1,  'textbox',   'address1');
      	 setRequiredField(city,      'textbox',   'city');
      	 setRequiredField(zipcode,   'textbox',   'zipcode');
      	 setRequiredField(country,   'textbox',   'country');

         if(country.value == "US" )
  	     {
            setRequiredField(state1,'dropdown','state');
  	     }
  	     else
  	     {
	          setRequiredField(state2,'textbox','state');
  	     }

      }

   }
}

function validateFields(frm)
{
   with(frm)
   {
      if (!RE_USERNAME.exec(username.value))
      {
         highlightTableColumn('username');
         alert(ERROR_USERNAME);
         return false;
      }
      else if (RE_NAME.exec(first_name.value))
      {
         highlightTableColumn('first_name');
         alert(ERROR_NAME);
         return false;
      }
      else if (RE_NAME.exec(last_name.value))
      {
         highlightTableColumn('last_name');
         alert(ERROR_NAME);
         return false;
      }
      else if (!RE_EMAIL.exec(email.value))
      {
         highlightTableColumn('email');
         alert(ERROR_EMAIL);
         return false;
      }
      else if (secondary_email.value != '' && !RE_EMAIL.exec(secondary_email.value))
      {
         highlightTableColumn('secondary_email');
         alert(ERROR_EMAIL);
         return false;
      }

      else if (country.value == 'US' && zipcode.value != '' && !RE_ZIP.exec(zipcode.value))
      {
         highlightTableColumn('zipcode');
         alert(ERROR_ZIP);
         return false;
      }
   }

   if((frm.password.value != '' || frm.conf_pass.value != '') && frm.password.value != frm.conf_pass.value)
   {
      highlightTableColumn('password');
      highlightTableColumn('conf_pass');
      alert(ERROR_PASS_MATCH);
      return false;
   }

   return true;
}

function doFormSubmit()
{
   requiredFields.length = 0;

   var errCnt = 0;
   var frm = document.userManagerForm;

   if (frm.country.value == 'US')
   {
      frm.state.value = frm.state1.value;
   }
   else
   {
      frm.state.value = frm.state2.value;
   }

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

function showAddressInfo()
{
   var frm= document.userManagerForm;

   if(frm.user_type.value=='Employee')
   {
     showDiv('addressInfo');
   }

   else
   {
     hideDiv('addressInfo');
   }
}

function checkDuplicateUser()
{
   var userName = document.userManagerForm.username.value;
   cpaintCall('SELF', 'POST', 'checkusername', userName, uid, callbackCheckDuplicateUser);
}

function callbackCheckDuplicateUser(val)
{
	if(val==1)
	{
     highlightTableColumn('username');
     alert(DUPLICATE_USERNAME);
     document.userManagerForm.username.focus();
	}
  else
     resetTableColumn('username');
}

function checkDuplicateEmail()
{
   var primaryEmail = document.userManagerForm.email.value;
   cpaintCall('SELF', 'POST', 'checkUserEmail', primaryEmail, uid, callbackCheckDuplicateEmail);
}

function callbackCheckDuplicateEmail(val)
{
	if(val == 1)
	{
     highlightTableColumn('email');
     alert(DUPLICATE_EMAIL);
     document.userManagerForm.email.focus();
	}
  else
     resetTableColumn('email');
}
