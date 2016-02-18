/*
**************************************************************/
var DEFAULT_HIGHLIGHT_COLOR = '#ff0000';
var DEFAULT_RESET_COLOR     = '#557799';

// Global array required for storing
// required fields information for
// the current form
var requiredFields = new Array();


//
// Commonly Used Regular Expressions
//
RE_US_ZIPCODE = /\d{5}(-\d{4})?/;
RE_EMAIL_ADDRESS = /^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})(\]?)$/;



//
// Standar cancel button function
//
function doCancel()
{
   document.location.href = CANCEL_URL;
}

//
// Show error messages (if any)
//
function showErrors(frm, setColor, resetColor)
{

   // if highlight (set) color is not provided
   // use default
   if (! setColor)
      setColor = DEFAULT_HIGHLIGHT_COLOR;


   // if reset color is not provided
   // use default
   if (! resetColor)
      resetColor = DEFAULT_RESET_COLOR;

   with (frm)
   {

     // Is there error messages from PHP side?
     if (errorMsgList.length > 0)
     {
         var errCnt = errorMsgList.length;
         var msgBlock = "";
         for(i=0; i<errCnt; i++)
         {
            msgBlock += errorMsgList[i] + "\n";
            tdColumnID = errorMsgFieldList[i];
            highlightTableColumn(tdColumnID, setColor);

            //fields = document.getElementsByName(errorMsgFieldList[i]);
            //fields[0].focus();
         }

         // If we are supposed to show alert() popup
         // for errors, show one.
         if (alertPopup)
             alert(msgBlock);
     }
   }

   return true;
}

//
// Highlight a table column foreground color and style using ID
//
function highlightTableColumn(id, highColor)
{

   if (!highColor)
       highColor = DEFAULT_HIGHLIGHT_COLOR;

   thisElement                  = document.getElementById(id);
   thisElement.style.color      = highColor;
   thisElement.style.fontWeight = "bold";
}

//
// Reset a table column foreground color and style using ID
//
function resetTableColumn(id, resetColor)
{
   if (!resetColor)
       resetColor = DEFAULT_RESET_COLOR;

   thisElement                  = document.getElementById(id);
   thisElement.style.color      = resetColor;
   thisElement.style.fontWeight = "bold";
   
}

//
// Purpose: check if a drop-down list has at least one item
//          selected or not
//
// Return:  true if at least one item is selected, else false
function itemSelectedFromDropDownList(menu)
{
   // If nothing is selected return false
   if (menu.selectedIndex == null ||
       menu.selectedIndex == 0 )
   {
       return false;
   }

   // An item is selected, return true
   return true;
}

//
// Purpose: this function allows you to define a form field
//          as a required field. A subsequent call to
//          validateForm() allows you to ensure that the
//          form cannot be submitted without required fields
//
//
function setRequiredField(formFieldObject,
                          fieldType,
                          fieldLabel,
                          fieldValidator,
                          fieldErrorMsg)
{
   var i = requiredFields.length;

   // Store the given field's requirements
   // information in the global array
   requiredFields[i] = new Array();
   requiredFields[i]['field'] = formFieldObject;
   requiredFields[i]['type']  = fieldType;
   requiredFields[i]['label']  = fieldLabel;

   // Reset the color
   resetTableColumn(fieldLabel);

   if (fieldValidator)
       requiredFields[i]['validator']  = fieldValidator;

   // MK-TODO: current version does not use error_msg data
   if (fieldErrorMsg)
      requiredFields[i]['error_msg']  = fieldErrorMsg;
   else
      requiredFields[i]['error_msg']  = "The " + formFieldObject + " value is missing or incorrect";

   // MK TODO: need to add data type validation support

}

//
// Purpose: do required field validation for a form
//          This function relies on setRequiredField()
//          set up data in requiredFields array for
//          doing appropriate validation
//
//
function validateForm(thisForm)
{
   // Get the count of required fields
   var reqFieldCnt = requiredFields.length;
   var errCnt = 0;

   with (thisForm)
   {
      for(i=0; i<reqFieldCnt; i++)
      {
         var formFieldObject  =  requiredFields[i]['field'];
         var fieldType  =  requiredFields[i]['type'];
         var fieldLabel =  requiredFields[i]['label'];
         var fieldErr   =  requiredFields[i]['error_msg'];

         // If current field is a drop down list and
         // not a single item is selected, we have a
         // required field violation!
         if (fieldType == 'dropdown' && !itemSelectedFromDropDownList(formFieldObject))
         {
             if (fieldLabel)
                highlightTableColumn(fieldLabel);

             formFieldObject.focus();
             errCnt++;
         }
         // If current field is a text box and it is empty
         // we have a required field violation
         else if (fieldType == 'textbox' && formFieldObject.value == "")
         {
             //alert("No value selected for " + fieldLabel);

             if (fieldLabel)
                highlightTableColumn(fieldLabel);

             //formFieldObject.focus();
             errCnt++;
         }
         // If current field is a check box and it is not selected
         // we have a required field violation
         else if (fieldType == 'checkbox' && ! isCheckBoxSelected(formFieldObject))
         {
             if (fieldLabel)
                highlightTableColumn(fieldLabel);

             errCnt++;
         }
         // If current field is a radio box and it is not selected
         // we have a required field violation
         else if (fieldType == 'radio' && ! isRadioSelected(formFieldObject))
         {
             if (fieldLabel)
                highlightTableColumn(fieldLabel);

             errCnt++;
         }

      }
   }

   return errCnt;
}


//
// Purpose: allows you to find out if a checkbox is
//          selected. If you have a checkbox group (i.e.
//          checkbox with same name, it will work too
//
function isCheckBoxSelected(chkbox)
{
   var noneChecked = true;

   if (typeof chkbox.length == 'undefined')
   {
     // there's only one checkbox in the form
     // normalize it to an array
     chkbox = new Array(chkbox);
   }

   for (var i = 0; i < chkbox.length; i++)
   {
      if (chkbox[i].checked)
      {
         noneChecked = false;
         break;
      }
    }

   if (noneChecked)
       return false;

   return true;
}

function isRadioSelected(btnName)
{

  // If only one item
  if (typeof btnName.length == 'undefined')
  {
    if (btnName.checked == true)
        return true;
    else
        return false;
  }

  // There are many radio options
  var len = btnName.length;

  var i=0;
  var noneSelected = true;

  while(noneSelected && i<len)
  {
    if (btnName[i].checked == true)
    {
      noneSelected = false;
    }
    i++;
  }

  return !noneSelected;

}

//
// Purpose: allows you to select a list of items for a
//          checkbox group
//
function selectChosenCheckBoxItems(formFieldString, chosenList)
{
   var frm = document.forms[0];

   with (frm)
   {
   if (chosenList.length > 0)
      {
          var chkbox = elements[formFieldString];

          if (typeof chkbox.length == 'undefined')
          {
            chkbox = new Array(chkbox);
          }

          for (var i = 0; i < chkbox.length; i++)
          {
             for(var j=0; j < chosenList.length; j++)
             {
                if (chkbox[i].value == chosenList[j])
                {
                  chkbox[i].checked = true;
                }
             }
          }
      }

   }
}


//
// Utility Function
//
// Purpose: strip everything nut [0-9.] set from given string
// Return : string containing only [0-9.] characters
function makeNumber(str)
{
   if (str != null && str.length > 0)
       return str.replace(/[^0-9.]/g, '');
   else
       return null;
}


//
// Toggle a div block using the div id
//
// Example div:
// <div id="A" style="display: none"> something </div>
//
// Example call:
//
// <a href="javascript:toggle('A')"><strong>Show Details of section A</strong></a>

function toggle(targetId) {

  if ("none" == document.getElementById(targetId).style.display) {
     document.getElementById(targetId).style.display = "block";
  }
  else {
     document.getElementById(targetId).style.display = "none";
  }
}

function doConfirm(msg)
{
    return confirm(msg);
}

/**
 * This function gets the
 * order to be displayed
 * as an icon in the table heading

 * @param number id--the index in the document.images collection
 * @return none
 */

function toggleSort(id)
{
    counter = getClickCount();

    for(i=0; i<=document.images.length; i++)
    {
       if (document.getElementById(i))
       {
          document.getElementById(i).style.display = 'none';
       }
    }

   if(counter % 2 == 1)
   {
       document.getElementById(id+1).style.display = 'inline';
   }
   else
   {
       document.getElementById(id+2).style.display = 'inline';
   }
}

function getClickCount()
{
   return ++counter;
}

function openAWindow( pageToLoad, winName, width, height, center)
{
   xposition=0;
   yposition=0;

   if ((parseInt(navigator.appVersion) >= 4 ) && (center))
   {
      xposition = (screen.width - width) / 2;
      yposition = (screen.height - height) / 2;
   }

   winName = "'" + winName + "'";

   args = "width=" + width + ","
   + "height=" + height + ","
   + "location=0,"
   + "menubar=0,"
   + "resizable=1,"
   + "scrollbars=1,"
   + "status=0,"
   + "titlebar=0,"
   + "toolbar=0,"
   + "hotkeys=0,"
   + "screenx=" + xposition + "," //NN Only
   + "screeny=" + yposition + "," //NN Only
   + "left=" + xposition + "," //IE Only
   + "top=" + yposition; //IE Only

   window.open(pageToLoad, 'win', args);
}

/*
 * Purpose: this function shows the Div
 */
function showDiv(divId)
{
   if (document.getElementById)
	  { // DOM3 = IE5, NS6
      document.getElementById(divId).style.display = 'inline';
   }
   else
	  {
      if (document.layers)
	     { // Netscape 4
         document.divId.style.display = 'inline';
      }
      else
	     { // IE 4
         document.all.divId.style.display = 'inline';
      }
   }
}

/*
 * Purpose: this function hides the Div
 */
function hideDiv(divId)
{
   if (document.getElementById)
   { // DOM3 = IE5, NS6

      document.getElementById(divId).style.display = 'none';
   }
   else
   {
      if (document.layers)
      { // Netscape 4
         document.divId.display = 'none';
      }
      else
      {   // IE 4
         document.all.divId.style.display = 'none';
      }
   }
}
