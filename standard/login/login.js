
//
//  Purpose: set up the required fields for the current form
//           
//
function setuploginForm(mode)
{
   var frm = document.loginForm;
   
   with (frm)
   {                   
      // username field is required
      setRequiredField(loginid, 'textbox', 'loginid');
      
      // password field is required
      setRequiredField(password, 'textbox',  'password');
   }
}

function dologinFormSubmit(mode)
{
   var errCnt = 0;
   var frm = document.loginForm;
   
   // Setup required fields
   setuploginForm(mode);
   
   // Validate form for required fields
   errCnt = validateForm(frm);
     
   with (frm)
   {  
      
      // If no errors, submit the form
      if (!errCnt)
      {
         submit();
         return true;
      }
      else
      {
        alert(INPUT_ERROR_ALERT_MSG);
        return false;
      }
   }
}