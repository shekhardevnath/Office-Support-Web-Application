/************************************************************
 * File: cpaint.inc.js
 * CPAINT (Cross-Platform Asynchronous INterface Toolkit)
 *
 * @package AdminApp
 * @author  Boolean Systems, Inc. - http://cpaint.sourceforge.net
 *          Copyright (c) 2005
 *
 * Version 1.01
 **************************************************************/

var cpaintSharedHttpObj;

/***************************************************************
* This function is used to instantiate the connection
* object for asynchronous data communication.
*
* @author Boolean Systems, Inc. - - http://cpaint.sourceforge.net
* @param  none
* @return XMLHttpConnection object
*
*****************************************************************/

function cpaintGetConnectionObject()
{
   try
   {
      cpaintHttpObjTemp = new ActiveXObject('Msxml2.XMLHTTP');
   }
   catch (events)
   {
      try
      {
         cpaintHttpObjTemp = new ActiveXObject('Microsoft.XMLHTTP');
      }
      catch (connObjEvent)
      {
         cpaintHttpObjTemp = null;
      }
   }

   if (!cpaintHttpObjTemp && typeof XMLHttpRequest != 'undefined')
   {
       cpaintHttpObjTemp = new XMLHttpRequest();
   }

   if (!cpaintHttpObjTemp)
   {
      alert('[CPAINT Error] Could not create connection object');
   }

   return cpaintHttpObjTemp;
}

/***************************************************************
* This function is used to communicate between the frontend
* javascript and the backend php. It is called by javascript
* with parameters for processing the info by the backend php
* and return the processed data to the designated callback
* function of the frontend javascript.
*
* @author Boolean Systems, Inc. - - http://cpaint.sourceforge.net
* @param  {url}, {method}, {backend function name},
*         {argument1} ... {argumentN}, {JS callback function},
*         OPTIONAL = TEXT | XML (default is 'TEXT')
* @return none
*
*****************************************************************/

function cpaintCall()
{

   var cpaintArgs           = cpaintCall.arguments,
       cpaintUrl            = '',
       cpaintQueryString    = '',
       cpaintArgCount,
       cpaintHttpObj;

   var cpaintCallBackFn     = '',
       cpaintLastArg        = 0,
       cpaintReturntype     = '';

   if ((cpaintArgs[cpaintArgs.length - 1] == 'TEXT') ||
       (cpaintArgs[cpaintArgs.length - 1] == 'XML'))
   {
      cpaintCallBackFn   = cpaintArgs[cpaintArgs.length - 2];
      cpaintLastArg      = cpaintArgs.length - 2;
      cpaintReturntype   = cpaintArgs[cpaintArgs.length - 1];
   }
   else
   {
      cpaintCallBackFn   = cpaintArgs[cpaintArgs.length - 1];
      cpaintLastArg      = cpaintArgs.length - 1;
      cpaintReturntype   = 'TEXT';
   }

   if (typeof(cpaintUseMultipleConn) == 'undefined')
   {
      cpaintUseMultipleConn = false;
   }

   if (typeof(cpaintDebug) == 'undefined')
   {
      cpaintDebug = false;
   }

   if (cpaintArgs[0] == 'SELF')
   {
      cpaintUrl = document.location.href;
   }
   else
   {
      cpaintUrl = cpaintArgs[0];
   }

   for (cpaintArgCount = 3; cpaintArgCount < cpaintLastArg; cpaintArgCount++)
   {
      cpaintQueryString = cpaintQueryString + '&cpaintArgument[]=' +
                          encodeURIComponent(cpaintArgs[cpaintArgCount]);

   }

   if (cpaintArgs[1] == 'GET')
   {
      cpaintUrl = cpaintUrl + '?cpaintFunction=' + cpaintArgs[2] + cpaintQueryString;

   }
   else
   {
      cpaintQueryString = 'cpaintFunction=' + cpaintArgs[2] + cpaintQueryString;

   }

   if (cpaintReturntype == 'XML')
   {
      cpaintQueryString = cpaintQueryString + '&cpaintReturnXML=true';
   }

   if (cpaintUseMultipleConn == true)
   {
      if (cpaintDebug == true)
      {
         alert('[CPAINT Debug] Using new connection object');
         cpaintHttpObj = cpaintGetConnectionObject();
      }
   }
   else
   {
     if (cpaintDebug == true)
     {
        alert('[CPAINT Debug] Using shared connection object.');
     }

     if (typeof(cpaintSharedHttpObj) == 'undefined')
     {
        if (cpaintDebug == true)
        {
           alert('[CPAINT Debug] Getting new shared connection object.');
        }

        cpaintSharedHttpObj = cpaintGetConnectionObject();

     }

     cpaintHttpObj = cpaintSharedHttpObj;
   }

   if (cpaintHttpObj.readyState != 4)
   {
      cpaintHttpObj.abort();
   }

   cpaintHttpObj.open(cpaintArgs[1], cpaintUrl, true);

   if (cpaintArgs[1] == "POST")
   {
      try
      {
         cpaintHttpObj.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      }
      catch(cpaintError)
      {
       alert('[CPAINT Error] POST cannot be completed due to incompatible browser. Use GET as your request method.');
      }
   }


   cpaintHttpObj.onreadystatechange = function()
                                      {
                                         if (cpaintHttpObj.readyState != 4)
                                         {
                                            return;
                                         }

                                         if (cpaintDebug == true)
                                         {
                                            alert('[CPAINT Debug] ' + cpaintHttpObj.responseText);
                                         }

                                         if (cpaintReturntype == 'XML')
                                         {
                                            cpaintCallBackFn(cpaintHttpObj.responseXML);
                                         }
                                         else
                                         {

                                          cpaintCallBackFn(cpaintHttpObj.responseText);
                                         }
                                      }
   if (cpaintArgs[1] == 'GET')
   {
      cpaintHttpObj.send(null);
   }
   else
   {
     cpaintHttpObj.send(cpaintQueryString);
   }
}

/***************************************************************
* This function is used to get remote file using XMLHttp
* connection object and for this purpose it uses a proxy file
* named cpaint_proxy.php
*
* @author Boolean Systems, Inc. - - http://cpaint.sourceforge.net
* @param  {proxy_file}, {remoteURL}, {method}, {returnType},
*         {JS callback function}, {param1_name},
*         {param1_value} ... {paramN_name}, {paramN_value}
* @return type = TEXT | XML
*
*****************************************************************/

function cpaintGetRemoteFile()
{
   var cpaintArgs = cpaintGetRemoteFile.arguments;
   var cpaintUrl = cpaintArgs[0];
   var cpaintQueryString = '', cpaintArgCount = 5, cpaintHttpObj;

   if (typeof(cpaintUseMultipleConn) == 'undefined')
   {
      cpaintUseMultipleConn = false;
   }

   if (typeof(cpaintDebug) == 'undefined')
   {
      cpaintDebug = false;
   }

   while (cpaintArgCount <= cpaintArgs.length - 1)
   {
     cpaintQueryString = cpaintQueryString + escape(cpaintArgs[cpaintArgCount]
                         + '=' + cpaintArgs[cpaintArgCount + 1] + '&');
     cpaintArgCount = cpaintArgCount + 2;
   }

   if (cpaintArgs[2] == 'GET')
   {
     cpaintUrl = cpaintUrl + '?cpaintRemoteUrl=' + escape(cpaintArgs[1]) +
                 '&cpaintRemoteMethod=' + cpaintArgs[2] + '&cpaintRemoteReturntype=' +
                 cpaintArgs[3] + '&cpaintRemoteQuery=' + cpaintQueryString;
   }
   else
   {
     cpaintQueryString = 'cpaintRemoteUrl=' + escape(cpaintArgs[1]) +
                         '&cpaintRemoteMethod=' + cpaintArgs[2] + '&cpaintRemoteReturntype=' +
                          cpaintArgs[3] + '&cpaintRemoteQuery=' + cpaintQueryString;
   }

   if (cpaintUseMultipleConn == true)
   {
     if (cpaintDebug == true)
     {
        alert('[CPAINT Debug] Using new connection object');
     }
     cpaintHttpObj = cpaintGetConnectionObject();
   }
   else
   {
     if (cpaintDebug == true)
     {
        alert('[CPAINT Debug] Using shared connection object.');
     }
     if (typeof(cpaintSharedHttpObj) == 'undefined')
     {
       if (cpaintDebug == true)
       {
          alert('[CPAINT Debug] Getting new shared connection object.');
       }
       cpaintSharedHttpObj = cpaintGetConnectionObject();
     }
     cpaintHttpObj = cpaintSharedHttpObj;
   }

   if (cpaintHttpObj.readyState != 4)
   {
      cpaintHttpObj.abort();
   }

   cpaintHttpObj.open(cpaintArgs[2], cpaintUrl, true);

   if (cpaintArgs[2] == "POST")
   {
     try
     {
       cpaintHttpObj.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
     }
     catch(cpaintError)
     {
       alert('[CPAINT Error] POST cannot be completed due to incompatible browser.  Use GET as your request method.');
     }
   }
   cpaintHttpObj.onreadystatechange = function()
                                      {
                                         if (cpaintHttpObj.readyState != 4) return;

                                         if (cpaintDebug == true)
                                         {
                                            alert('[CPAINT Debug] ' + cpaintHttpObj.responseText);
                                         }

                                         if (cpaintArgs[3] == 'TEXT')
                                         {
                                            cpaintArgs[4](cpaintHttpObj.responseText);
                                         }
                                         else
                                         {
                                            cpaintArgs[4](cpaintHttpObj.responseXML);
                                         }
                                      }
   if (cpaintArgs[2] == 'GET')
   {
      cpaintHttpObj.send(null);
   }
   else
   {
      cpaintHttpObj.send(cpaintQueryString);
   }
}