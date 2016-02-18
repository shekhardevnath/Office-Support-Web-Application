//
// forgotten_passwd.js -- used in forgotten password reset request processing
//


//
//  Purpose: set up the required fields for the current form
//
//



function setupforgotpwdForm()
{
   var frm = document.forgotpwdForm;

   with (frm)
   {
      // email field is required
      setRequiredField(email,'textbox', 'email', 'email');
   }
}


//
//  Purpose: set up the required fields for the password reset form
//
//
function setupforgotpwdChangeForm()
{
   var frm = document.forgotpwdChangeForm;

   with (frm)
   {
      // email field is required
      setRequiredField(password,'textbox', 'password', 'password');
      setRequiredField(password2,'textbox', 'password2', 'password');
   }
}


//
// The forgotten password reset request form submission function
// Purpose: this function is used to ensure
//          that required fields have data
//          and if a required field is missing
//          appropriate errors are set
//
function doforgotpwdFormSubmit(mode)
{
   var errCnt = 0;
   var frm = document.forgotpwdForm;

   // Setup required fields
   setupforgotpwdForm();

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

//
// The forgotten password change request form submission function
// Purpose: this function is used to ensure
//          that required fields have data
//          and if a required field is missing
//          appropriate errors are set
//
function doforgotpwdChangeFormSubmit(mode, minPwdLength, maxPwdLength)
{
   var errCnt = 0;
   var frm = document.forgotpwdChangeForm;

   // Setup required fields
   setupforgotpwdChangeForm();

   // Validate form for required fields
   errCnt = validateForm(frm);

   sizeError = validatePasswords(frm.password.value,
                                 frm.password2.value,
                                 minPwdLength,
                                 maxPwdLength);

   if (sizeError)
   {
      alert(PASSWORD_SIZE_MISMATCH);
      return false;
   }

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

function validatePasswords(pwd1, pwd2, minPwdLength, maxPwdLength)
{
   return( (pwd1.length < minPwdLength) ||
           (pwd1 != pwd2) ||
           (pwd1.length > maxPwdLength)
         ) ? 1 : 0;
}