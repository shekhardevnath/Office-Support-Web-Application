<?php
 /************************************************************
 * File: cpaint.inc.php
 * CPAINT (Cross-Platform Asynchronous INterface Toolkit)
 * This tool is used to communicate between the frontend JS
 * and the backend PHP.
 *
 * @package AdminApp
 * @author  Boolean Systems, Inc. - http://cpaint.sourceforge.net
 *          Copyright (c) 2005
 *
 * Version: 1.01
 *
 **************************************************************/

  error_reporting (E_ALL ^ E_NOTICE);

  global $cpaintXMLResult;

  header ("Expires: Fri, 14 Mar 1980 20:53:00 GMT");
  header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
  header ("Cache-Control: no-cache, must-revalidate");
  header ("Pragma: no-cache");

  // if the method is GET and it contains the XML
  // object then set header content type to text/xml
  if ($_GET['cpaintReturnXML'] == "true")
  {
     header("Content-Type:  text/xml");
  }

  // if the method is POST and contains the XML
  // object then set header content type to text/xml
  if ($_POST['cpaintReturnXML'] == "true")
  {
     header("Content-Type:  text/xml");
  }

  // if GET method is used and the callback function
  // is available, then call the callback function with
  // cpaintArgument as parameter
  if ($_GET['cpaintFunction'] != "")
  {
     print(call_user_func_array($_GET['cpaintFunction'], $_GET['cpaintArgument']));

     exit();
  }

  // if POST method is used and the callback function
  // is available, then call the callback function with
  // cpaintArgument as parameter
  elseif ($_POST['cpaintFunction'])
  {
     print(call_user_func_array($_POST['cpaintFunction'], $_POST['cpaintArgument']));

     exit();
  }

  /***************************************************************
  * This function is used to return the AJAX response to the
  * backend php.
  *
  * @access public
  * @author Boolean Systems, Inc. - - http://cpaint.sourceforge.net
  * @param  none
  * @return AJAX response to the back end
  *
  *****************************************************************/

  function cpaintXMLReturnData()
  {
     global $cpaintXMLResult;

     return "<?xml version=\"1.0\" standalone=\"yes\"?><AJAX-RESPONSE>" . $cpaintXMLResult . "</AJAX-RESPONSE>";
  }

  /***************************************************************
  * This function is used to add data to the XML result
  *
  * @access public
  * @author Boolean Systems, Inc. - - http://cpaint.sourceforge.net
  * @param  $dataName, $uniqueId, $dataValue
  * @return none
  *
  *****************************************************************/

  function cpaintXMLAddData($dataName, $uniqueId, $dataValue)
  {
     global $cpaintXMLResult;

     $cpaintXMLResult = $cpaintXMLResult . "<" . strtoupper($dataName) . " ID=\"" . $uniqueId . "\">" . $dataValue . "</" . strtoupper($dataName) . ">";
  }

  /***************************************************************
  * This function is used to open the XML result
  *
  * @access public
  * @author Boolean Systems, Inc. - - http://cpaint.sourceforge.net
  * @param  $uniqueId
  * @return none
  *
  *****************************************************************/

  function cpaintXMLOpenResult($uniqueId)
  {
     global $cpaintXMLResult;

     $cpaintXMLResult = $cpaintXMLResult . "<AJAX-RESULT ID=\"" . $uniqueId . "\">";
  }

  /***************************************************************
  * This function is used to close the XML result
  *
  * @access public
  * @author Boolean Systems, Inc. - - http://cpaint.sourceforge.net
  * @param  $uniqueId
  * @return none
  *
  *****************************************************************/

  function cpaintXMLCloseResult()
  {
     global $cpaintXMLResult;

     $cpaintXMLResult = $cpaintXMLResult . "</AJAX-RESULT>";
  }

?>