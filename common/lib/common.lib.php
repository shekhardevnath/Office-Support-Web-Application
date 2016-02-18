<?php

  /**********************************************************
   *
   * General Purpose Function Library
   *
   * Purpose: the functions found in the library are widely
   *          used througout projects.
   *
   * 06/29/2005
   *    1. Address IPAddressToNumber() a function to convert IP Address to number
   *
   * 03/15/2005
   *    1. createPage() updated to include logged in user info()
   *    2. createPage() updated to add SYSTEM_DATE, SYSTEM_TIME, SYSTEM_YEAR const
   *    3. getHomeTemplate() added
   *
   * Quick Function Reference
   * =====================================================================
   * getUserField($key)..........................get user data from $_REQUEST
   * alias($table, $alias).......................TABLE as X
   * createPage($template, [$data])..............Smarty template page
   * delete($info)...............................Perform MySQL DELETE statement
   * dumpVar($var)...............................Dump variable
   * echo_br($msg)...............................Debug print statement
   * getUserAgentLanguage()......................Internal use only
   * getFromSession($key)........................Get $_SESSION[$key] value
   * getHomeTemplate(...)........................Get user's home template
   * getObjectFromSession($key ).................Get var/object from $_SESSION
   * getUserDataSet($table)......................Get $_REQUEST data for a SQL table
   * getTableFields($table)......................Get field metrix for a SQL table
   * getTableList()..............................Get a list of all the tables in current database
   * getEnumFieldValues()........................Get enumrated field values for a table field
   * insert($data)...............................Perform MySQL INSERT statement
   * insertIntoSession($key, $value).............Insert $key=$value in $_SESSION
   * IPAddressToNumber($IPAddr)..................Converts IP Address to number
   * js_escape($str = null)......................Escape a string to be used in javascripts
   * like($str)..................................LIKE '%$str%'
   * loadDataIntoRequestObject($data)............Load $data array into $_REQUEST
   * makeDirectory($newDir, $mode)...............Creates a new directory (not recursive)
   * q($str).....................................Quote and escape for MySQL use
   * removeFromSession($key).....................$_SESSION[$key] = null
   * resetSessionKey($key).......................Set a $_SESSION key to null
   * select($info)...............................Perform MySQL SELECT query
   * setPageError(&$errors, $errCode, $errField).Set input field error info for a page
   * setUserField($key, $value)..................Store key=value in $_REQUEST superglobal
   * storeObjectInSession($var, $key)............Store serialized, b64enc var in $_SESSION
   * sendEmail($mailData)........................Send Smarty template based email msg
   * update($info)...............................Perform MySQL UPDATE statement
   *
   * >> LIST OF FUNCTIONS BY CATEGORY
   *
   * HTTP Request Functions                      Smarty Functions
   * ======================                      =================
   * getUserField                                createPage
   * setUserField
   * loadDataIntoRequestObject
   *
   * Email Functions                             Debugging Functions
   * ===============                             ===================
   * sendEmail                                   dumpVar
   *                                             echo_br
   *
   * MySQL Functions                             Session Related Functions
   * ================                            =========================
   * select                                      getFromSession
   * insert                                      insertIntoSession
   * update                                      removeFromSession
   * delete                                      getObjectFromSession
   * getUserDataSet                              storeObjectInSession
   * getTableFields                              resetSessionKey
   * getTableList
   * getEnumFieldValues
   *
   * Misc. Functions
   * =======================
   * like
   * q
   * alias
   * js_escape
   * setPageError
   * makeDirectory
   * getHomeTemplate
   *
   ***********************************************************************************/

   /**
   * Returns the value for a $_REQUEST key=value pair
   *
   * @param string $key -- the $_REQUEST[$key]
   * @return mixed -- if key is present in $_REQUEST super global
   * @return null  -- if key is not present, returns null
   */
   function getUserField($key = null)
   {
      if (isset($_REQUEST[$key]))
          return $_REQUEST[$key];
      else
          return null;
   }


   /**
   * Stores a key/value pair in $_REQUEST superglobal
   * This f() has a known performance overhead but
   * still useful to keep code managable in cases where
   * embedding $_REQUEST directly in code might make
   * code harder to port when $_GET or $_POST is desired
   * request superglobal.
   *
   * @param string $key   -- the $_REQUEST[$key]
   * @param string $value -- the value for the key
   */
   function setUserField($key = null, $value = null)
   {
       $_REQUEST[$key] = $value;
   }

   /**
   * Returns a quoted, mysql ready (i.e properly character-escaped) string
   *
   * @param string $str -- the string to quote and escape for mysql
   * @return string -- the escaped, mysql ready string
   */
   function q($str = null)
   {
      return "'" . mysql_escape_string($str) . "'";
   }


   // Store serialized, based64 encoded data into session
   // using the given key

   /**
   * Stores a variable/object in session using a named key
   * The variable is serialized and base64 encoded before
   * storing in the session.
   *
   * @param mixed $var -- the variable to be stored in session
   * @param string $key -- the key used to store $var variable in session
   * @return mixed -- the variable/object
   */
   function storeObjectInSession($var = null, $key = null )
   {
      $_SESSION[$key] = base64_encode(serialize($var));
   }

   /**
   * Returns a variable/object from session using a named key
   * The variable is returned unserialized and base64 decoded
   *
   * @param string $key -- the key used to locate variable/object in the session
   */
   function getObjectFromSession($key = null )
   {
      return unserialize(base64_decode($_SESSION[$key]));
   }

   /**
   * Set a $_SESSION key to null
   *
   * @param string $key -- the key we need to reset
   */
   function resetSessionKey($key = null)
   {
      $_SESSION[$key] = null;
   }

   /**
   * Loads an array into $_REQUEST superglobal
   *
   * @param array $data  -- the assoc. array to be loaded in $_REQUEST
   * @return bool -- true if data is loaded, false if no data inserted
   */
   function loadDataIntoRequestObject($data = null)
   {
       if (count($data) && is_array($data))
       {
           $_REQUEST = array_merge($_REQUEST, $data);
           return true;
       }
       else
           return false;
   }

   /**
   * Returns all data related to a given table from $_REQUEST super global
   *
   * @param string $table -- the table name for which data is retrieved
   * @return array $resultData  -- the key=value pairs
   */
   function getUserDataSet($table = null)
   {
       // Get the field map for the current table
       $fieldMap = getTableFields($table);

       $resultData = array();

       // Loop through the field map and find out
       // which of the field(s) have data from $_REQUEST
       foreach ($fieldMap as $field => $info)
       {
          list($type,
               $len,
               $required,
               $autoinc,
               $pk,
               $unique,
               $enum) = explode(":",$info);

          // Ignore auto inc field since it is never received from user
          if ($autoinc )
              continue;

          $value = $_REQUEST[$field];

          // If value is available we need to store it
          // in result set
          if (! empty($value))
          {
              // We trim the string to remove leading
              // and trailing spaces
              if (preg_match("/string/i", $type) ||
                  preg_match("/blob/i", $type)
                 )
              {
                 $value = trim($value);
              }
              else if ((preg_match("/int/i", $type) ||
                        preg_match("/float/i", $type) ||
                        preg_match("/decimail/i", $type) ||
                        preg_match("/double/i", $type)
                        ) && ! is_numeric($value)
                      )
              {
                 // User given value is a NOT number
                 // when we are expecting one!
                 // so we are going to ignore it
                 continue;
              }

              $resultData[$field] = $value;
          }
       }

       return $resultData;
   }



   /**
   * Returns SQL table list meta data for current database
   *
   * @param  none
   * @return array  $tables -- list of tables
   */
   function getTableList()
   {
      $tables = array();

      $result = mysql_list_tables(DB_NAME);

      if (! $result)
          return $tables;

      while($row = mysql_fetch_row($result))
      {
        $tableName = $row[0];
        $tables[$tableName] = $tableName;
      }

      return $tables;
   }


   /**
   * Returns SQL table meta data for a given table
   * Return format:
   * $hash[field] = "type:length:required:autoinc:pk:unique:enum";
   *
   * @param string $table -- the table name
   * @return array  $hash  --
   */
   function getTableFields($table = null)
   {
      $result = _mysql_query_wrapper("SELECT * FROM $table");

      $errors = mysql_error();

      // If table does not exist, return null
      if (!empty($errors))
          return null;

      // Get field names for the table
      $fields = mysql_num_fields($result);

      // Setup an array to store return info
      $hash = array();

      // For each field, find out what type, length, requirements,
      // PK, unqiue, enum, attributes etc.
      for ($i=0; $i < $fields; $i++)
      {
         $type     = mysql_field_type($result, $i);
         $name     = mysql_field_name($result, $i);
         $len      = mysql_field_len($result, $i);
         $flags    = mysql_field_flags($result, $i);
         $required = (preg_match("/not_null/i", $flags)) ? 1 : 0;
         $autoinc  = (preg_match("/auto_increment/i", $flags)) ? 1 : 0;
         $pk       = (preg_match("/primary/i", $flags)) ? 1 : 0;
         $unique   = (preg_match("/unique/i", $flags)) ? 1 : 0;
         $enum     = (preg_match("/enum/i", $flags)) ? 1 : 0;

         $hash[$name] = "$type:$len:$required:$autoinc:$pk:$unique:$enum";
      }

      // Free the result set
      mysql_free_result($result);

      // Return
      return $hash;
   }

   /**
   * Inserts data into an SQL table
   * The caller must provide $data['table'] = table name, $data['data'] = array
   * where the data array must field=value pairs
   *
   * @param array $data -- the data, table information for the INSERT statement
   * @return array  $ret -- returns the new row ID or null
   */
   function insert($data = null)
   {
     $ret = array();
     $fieldMap = getTableFields($data['table']);
     $valueList = array();
     $fieldList = array();
     $userData = $data['data'];

     foreach ($fieldMap as $field => $settings)
     {
        list($type,
             $len,
             $required,
             $autoinc,
             $pk,
             $uniq,
             $enum) = explode(':', $settings);

        $userField = strtolower($field);

        if (isset($userData[$userField]))
            $value = trim($userData[$userField]);
        else
            continue;

        $fieldList[] = $field;

        // Quote if the field type requires it
        $valueList[] = (preg_match("/string/i", $type) ||
                        preg_match("/blob/i", $type) ||
                        preg_match("/date/i", $type) ||
                        preg_match("/time/i", $type)
                        ) ? q($value) : $value;
     }

     $fieldStr = implode(',', $fieldList);
     $valueStr = implode(',', $valueList);

     $stmt     = 'INSERT INTO ' . $data['table'] . " ($fieldStr) VALUES($valueStr)";

     $result   = _mysql_query_wrapper($stmt);

     $err      = mysql_error();

     if (isset($data['debug']) && $data['debug'])
     {
          echo_br($stmt);
          echo_br("Error: " . $err);
     }

     if (! empty($err))
     {
        if (preg_match("/Duplicate/i", $err))
        {
           $errors[] = $data['dup_error'];
           $ret['newid'] = null;
           $ret['error'] = $errors;
           $ret['affected_rows'] = 0;
        }
     }
     else
     {
        $ret['newid'] = mysql_insert_id();
        $ret['affected_rows'] = mysql_affected_rows();
     }

     return $ret;
   }

   /**
   * Performs an SQL SELECT statement
   * The caller must provide $data['table'] = table name
   *
   * @param array $info -- the table information for the SELECT statement
   * @return standard object $rows -- returns the rows or null
   */
   function select($info = null)
   {

      if (isset($info['table']))
          $table = $info['table'];
      else
          return null;

      if (isset($info['fields']))
         $fields = implode(',', $info['fields']);
      else
         $fields = '*';

      if (isset($info['where']))

         $where = $info['where'];

      else
         $where = '1';

      $stmt = "SELECT $fields FROM $table WHERE $where";

      if (isset($info['debug']) && $info['debug'])
          echo_br($stmt);

      if (PROFILE_MODE)
      {
      	  $start = timeNow();
      }

      $result = _mysql_query_wrapper($stmt);

      $err    = mysql_error();

      if (isset($info['debug']) && $info['debug'])
          echo_br($err);

      if (!empty($err) || mysql_num_rows($result) < 1)
          return null;

      $data = array();

      while($row = mysql_fetch_object($result))
      {
          $data[] = $row;
      }

      return $data;
   }

   /**
   * Performs an SQL DELETE statement
   * The caller must provide $data['table'] = table name
   *
   * @param array $info -- the table information for the DELETE statement
   * @return bool -- returns true if successful else false
   */
   function delete($info = null)
   {
      if (isset($info['table']))
          $table = $info['table'];
      else
          return null;

      if (isset($info['where']))

         $where = $info['where'];

      // We won't continue unless explicit
      // where clause is given
      else
      {
         return null;
      }
      $stmt = "DELETE FROM $table WHERE $where";

      $result = _mysql_query_wrapper($stmt);
      $err    = mysql_error();

      if (isset($info['debug']) && $info['debug'])
      {
          echo_br("delete($stmt)");
          echo_br("Error: $err");
          echo_br("Affected Rows: " . mysql_affected_rows());
      }
      if (!empty($err) || mysql_affected_rows() < 1)
          return false;

      return true;
   }

   /**
   * Performs an SQL UPDATE statement
   * The caller must provide $data['table'] = table name
   *
   * @param array $info -- the table information for the UPDATE statement
   * @return bool -- returns true if successful else false
   */
   function update($info)
   {

      $table = (isset($info['table'])) ? $info['table'] : null;
      $where = (isset($info['where'])) ? $info['where'] : 1;
      $data  = (isset($info['data']))  ? $info['data']  : null;

      // If table name or data not provided return false
      if (! $table || ! $data)
         return false;

      $updateStr = array();

      // Get the table field meta data
      $fieldMap = getTableFields($info['table']);

       // Quote fields as needed
       foreach ($fieldMap as $field => $settings)
       {
          // Break down each field's meta info into attributes
          list($type,
               $len,
               $required,
               $autoinc,
               $pk,
               $uniq,
               $enum) = explode(':', $settings);

          $userField = strtolower($field);


          if (isset($data[$userField]))
              $value = trim($data[$userField]);
          else
              continue;

          // Special case: value = NULL is changed to value = ''
          if (preg_match("/^NULL$/i", $value))
             $value = '';

          // Quote strings/date/blob type data
          $value= (preg_match("/string/i", $type) ||
                   preg_match("/date/i", $type) ||
                   preg_match("/time/i", $type) ||
                   preg_match("/blob/i", $type)) ? q($value) : $value;
          $updateStr[] = "$field = $value";
       }

      $keyVal = implode(', ', $updateStr);
      $update = "UPDATE $table  SET $keyVal WHERE $where";
      $result = _mysql_query_wrapper($update);
      $err    = mysql_error();
      $affectedRows = mysql_affected_rows();

      // If debugging is turned on show helpful info
      if (isset($info['debug']) && $info['debug'])
      {
         echo_br($update);
         echo_br($err);
         echo_br("Affected rows $affectedRows" );

      }
      return (empty($err) && $affectedRows > 0 ) ? true : false;
   }

   /**
   * Returns a list of enumrated values for a given MySQL table field
   *
   * @param  string $tableName      -- name of the table
   * @return array  $enumValueList  -- list of possible enumrated values for the field
   */
   function getEnumFieldValues($tableName = null, $field = null)
   {
       // Make a DDL query
       $query = "SHOW COLUMNS FROM $tableName LIKE " . q($field);

       $result = _mysql_query_wrapper($query);
       $data   = mysql_fetch_array($result);

       if(eregi("('.*')", $data['Type'], $match))
       {
          $enumStr       = ereg_replace("'", '', $match[1]);
          $enumValueList = explode(',', $enumStr);
       }

       return $enumValueList;
   }

   /**
   * Sends an SMTP email via direct socket connection
   * This function does not use PHP mail() which is slower than
   * this function.
   *
   * @param array $mailData -- the to/from/data/mail template/content-type info
   * @return bool -- returns true if successful else false
   */
   function sendEmail($mailData = null)
   {

      $message = createPage($mailData['template'], $mailData['data']);

      // Number of bytes to receive from server as response
      $len         = 1024;

      // Set headers
      $from        = $mailData['from'];

      // If the to field is an array, good
      if (is_array($mailData['to']))
      {
        $toList      = $mailData['to'];
      }
      // OK, make it an array
      else
      {
         $toList      = array($mailData['to'] => $mailData['to']);
      }

      // Determine the desired content type (text/html or text/plain) supported
      $contentType = preg_match("/html/i", $mailData['content_type'])  ? 'text/html' : 'text/plain';

      // Determine the desired character set. Default iso-8859-1
      $charSet     = $mailData['char_set'];
      if (empty($charSet))
         $charSet     = 'iso-8859-1';

      // Set mail priority
      $priority = $mailData['priority'];

      if (empty($priority))
          $priority = DEFAULT_PRIORITY_header;

      // Determine which mail server to use to connect
      if (empty($mailData['smtp_server']))
          $server      =  ini_get('SMTP');
      else
          $server      =  $mailData['smtp_server'];

      // If no mail server is selected, return false
      if (!$server)
          return false;

      // Set from address
      ini_set(sendmail_from, $from);

      // Connect to the mail server via SMTP port (25)
      $connect = fsockopen($server,
                            ini_get('smtp_port'),
                            $errno,
                            $errstr, 30);

      // If connection failed, return false
      if (!$connect)
          return false;

      // Get data from connection
      $rcv = fgets($connect, $len);

      // Say hello and receive ack
      fputs($connect, "HELO {$_SERVER['SERVER_NAME']}\r\n");
      $rcv = fgets($connect, $len);

      // For each of the recipient, loop through to deliver
      // messages
      while (list($toName, $toEmail) = each($toList)) {

         // Give mail server the From header
         fputs($connect, "MAIL FROM:$from\r\n");
         $rcv = fgets($connect, $len);

         // Give mail server the To (RCPT) header
         fputs($connect, "RCPT TO:$toEmail\r\n");
         $rcv = fgets($connect, $len);

         // Send rest of the message headers
         fputs($connect, "DATA\r\n");
         $rcv = fgets($connect, $len);

         //fputs($connect, "Subject: $subject\r\n");
         fputs($connect, "From: $fromName <$from>\r\n");
         fputs($connect, "To: $toKey  <$toValue>\r\n");
         fputs($connect, "X-Sender: <$from>\r\n");
         fputs($connect, "Return-Path: <$from>\r\n");
         fputs($connect, "Errors-To: <$from>\r\n");
         fputs($connect, 'X-Mailer: ' . DEFAULT_XMAILER_HEADER . "\r\n");
         fputs($connect, 'X-Priority: ' . $priority ."\r\n");
         fputs($connect, "Content-Type: $contentType; charset=$charSet\r\n");

         // Insert slash stripped message body
         fputs($connect, stripslashes($message)." \r\n");

         // Close data transfer by ending with a dot
         fputs($connect, ".\r\n");
         $rcv = fgets($connect, $len);

         // Get server ready for next message
         fputs($connect, "RSET\r\n");
         $rcv = fgets($connect, $len);
     }

     // Finish connection
     fputs ($connect, "QUIT\r\n");
     $rcv = fgets ($connect, $len);
     fclose($connect);

     // Restore default from
     ini_restore(sendmail_from);

     return true;
   }

   /**
   * This function returns a smarty parsed template page
   *
   * @param string $template -- FQPN of the template file
   * @return string -- the template contents page
   */
   function createPage($template = null, $data = null)
   {
      // Create a smarty object
      $smarty               = new Smarty;

      // Setup smarty directories and options
      $smarty->template_dir   = TEMPLATE_DIR;
      $smarty->compile_dir    = SMARTY_COMPILED_DIR;
      //$smarty->config_dir   = SMARTY_CONFIG_DIR;
      $smarty->cache_dir      = SMARTY_CACHE_DIR;
      $smarty->cache          = false;

      $smarty->register_modifier("sslash","stripslashes");
      //DEPRECIATED: $smarty->force_compile = true;

      // DEPRECIATED
      //$smarty->left_delimiter = '<!--{';
      //$smarty->right_delimiter = '}-->';

      // If data (key=val) is provided
      // setup Smarty assignments
      if (count($data) > 0)
      {
         foreach ($data as $key => $value)          
            $smarty->assign($key ,$value);
            
      }

      $smarty->assign('USER_TYPE', $_SESSION['user_type']);
      $userType =  preg_replace("/[^a-z]/", '_', strtolower($_SESSION['user_type']));

      $navTemplate    = sprintf("%s/%s_navigation.html", USER_HOME_DIR, $userType);
      $headerTemplate = sprintf("%s/%s_header.html",     USER_HOME_DIR, $userType);

       if (!file_exists($navTemplate))
           $navTemplate = DEFAULT_NAVIGATION_TEMPLATE;

       if (!file_exists($headerTemplate))
           $headerTemplate = DEFAULT_HEADER_TEMPLATE;

      $smarty->assign('USER_NAVIGATION', $navTemplate);
      $smarty->assign('USER_HEADER', $headerTemplate);

      $smarty->assign('USER_ID',      $_SESSION['uid']);
      $smarty->assign('USERNAME',     $_SESSION['username']);
      $smarty->assign('USER_FIRST',   $_SESSION['first_name']);
      $smarty->assign('USER_LAST',    $_SESSION['last_name']);
      $smarty->assign('GROUP_ID',     $_SESSION['group_id']);
	  $smarty->assign('SUB_GROUP_ID', $_SESSION['sub_group_id']);
	  
      // Add system time and date
      $smarty->assign('SYSTEM_DATE', date(DATE_FORMAT));
      $smarty->assign('SYSTEM_TIME', date(TIME_FORMAT));
      $smarty->assign('SYSTEM_YEAR', date('Y'));
      $smarty->assign('cmd',         $_REQUEST['cmd']);

      // Assign system key=values
      if (PRODUCTION_MODE)
        $smarty->assign('SYSTEM_PRODUCTION_MODE', 'Yes');
      else
        $smarty->assign('SYSTEM_PRODUCTION_MODE', 'No');

      // Insert the script name
      $smarty->assign('SYSTEM_APP_PREFIX',            CURRENT_APP_PREFIX);
      $smarty->assign('SYSTEM_COMMON_TEMPLATE_DIR',   REL_COMMON_TEMPLATE_DIR);
      $smarty->assign('SYSTEM_COMMON_IMAGE_DIR',      REL_COMMON_IMAGE_DIR);
      $smarty->assign('SYSTEM_COMMON_JAVASCRIPT_DIR', REL_COMMON_JAVASCRIPT_DIR);
      $smarty->assign('SYSTEM_COMMON_CSS_DIR',        REL_COMMON_CSS_DIR);
      $smarty->assign('SYSTEM_LOCAL_TEMPLATE_DIR',    REL_LOCAL_TEMPLATE_DIR);
      $smarty->assign('SYSTEM_LOCAL_IMAGE_DIR',       REL_LOCAL_IMAGE_DIR);
      $smarty->assign('SYSTEM_LOCAL_JAVASCRIPT_DIR',  REL_LOCAL_JAVASCRIPT_DIR);
      $smarty->assign('SYSTEM_LOCAL_CSS_DIR',         REL_LOCAL_CSS_DIR);
      $smarty->assign('TEMPLATE_DIR',                 TEMPLATE_DIR);
      $smarty->assign('REL_TEMPLATE_DIR',             REL_TEMPLATE_DIR);
      $smarty->assign('REL_STANDARD_CONTENTS_DIR',    REL_STANDARD_CONTENTS_DIR);
      $smarty->assign('REL_DEVELOPMENT_CONTENTS_DIR', REL_DEVELOPMENT_CONTENTS_DIR);

      // Now return parsed template

      return $smarty->fetch($template);
   }


   /**
   * Returns a value from the $_SESSION super global (associative array)
   *
   * @param string $key -- the $_SESSION[$key]
   * @return mixed -- if key is present in $_SESSION hash
   * @return null  -- if key is not present
   */
   function getFromSession($key = null)
   {

      // If the key is set in session hash
      // return it
      if (isset($_SESSION[$key]))
          return $_SESSION[$key];

      // Else return null
      else
          return null;

   }

   /**
    * Store a key=value pair in $_SESSION super global (associative array)
    *
    * @param string $key  -- the key for the entry
    * @param mixed $value -- the data to be stored in session
    * @return bool -- true if $_SESSION[key] = $value is set, else returns false
    */
   function insertIntoSession($key = null, $value = null)
   {
       if (!empty($key) && !empty($value))
       {
           $_SESSION[$key] = $value;
           return true;
       }

       return false;
   }

   /**
   * Sets a $_SESSION[key] to null
   *
   * @param string $key -- the $_SESSION[$key]
   * @return mixed -- if key is present in $_SESSION hash
   * @return bool -- true if $_SESSION[key] = $value is set to null, else returns false
   */
   function removeFromSession($key = null)
   {
      if (isset($_SESSION[$key]))
      {
          $_SESSION[$key] = null;
          return true;
      }

      return false;
   }

   /**
   * This debugging function is used to print a line with HTML BR tag
   * DO NOT USE THIS IN PRODUCTION CODE! All echo_br() calls
   * in production code must be commented out or removed!
   *
   * @param string $msg -- message to be printed
   * @return null
   */
   function echo_br($msg = null)
   {
      echo  $msg . '</br>';
   }

   /**
   * This debugging function used to dump a data structure of any kind
   * DO NOT USE THIS IN PRODUCTION CODE! All dumpVar() calls
   * in production code must be commented out or removed!
   *
   * @param mixed $var -- variable to be dumpped
   * @return null
   */
   function dumpVar($var = null)
   {
       echo "<pre>";
       print_r($var);
       echo "</pre>";
   }


   /**
   * This utility function returns a SQL LIKE clause
   *
   * @param string $str -- the string for which we need LIKE clause
   * @return string the LIKE clause
   */
   function like($str = null)
   {
      return " LIKE '%" . $str . "%'";
   }

   /**
   * This utility function returns a "TABLE as <Alias>" string
   *
   * @param string $table -- the name of the table
   * @param string $alias -- the alias for the named table
   * @return string the alias string
   */
   function alias($table = null, $alias = null)
   {
      return $table . ' as ' . $alias;
   }

   /**
   * This utility function returns a string that javascripts
   * can use. It escapes real NEW LINE and CARRIAGE RETURNS
   * with \n character set (not real "\n" chracter) so that
   * the original string displays properly when used in
   * javascript functions such as alert();
   *
   * @param string $table -- the name of the table
   * @param string $alias -- the alias for the named table
   * @return string the alias string
   */
   function js_escape($str = null)
   {
      $str = preg_replace("[\r\n]", '\n', $str);

      return $str;
   }

   /**
   *
   * @param array  $errors   -- the error array (PASSED BY REFERENCE!)
   * @param string $errCode  -- the error code
   * @param string $errField -- the form field name for which error occured
   * @return array $errors -- with new error information
   */
   function setPageError(&$errors, $errCode, $errField)
   {
       $errCnt = count($errors);

       $thisMsg = new ApplicationMessage(array('msg_code' => $errCode));
       $errMsg  = $thisMsg->getMsgText();

       // Insert javascript ready error message
       $errors[$errCnt]['javascript_error_msg'] = js_escape($errMsg);

       // Insert standard version (html ready) error message
       $errors[$errCnt]['html_error_msg'] = $errMsg;

       // Insert error field name
       $errors[$errCnt]['error_field'] = $errField;

   }

   /**
   *
   * @param string $newDir -- the directory to be created
   * @param string $mode   -- the directory permission
   * @return bool -- true if successful else false
   */
   function makeDirectory($newDir = null, $mode = null)
   {
      if (! file_exists($newDir))
      {
          return mkdir($newDir, $mode);
      }
      else
          return false;
   }

   /**
   * Returns the fully qualified path name (FQPN) of user home template
   *
   * @param $userType - the type of user
   * @return $template - FQPN of user template file
   */
   function getHomeTemplate($userType = null)
   {
       // Lower case and trim the user type string to create filename
       $filename = trim(strtolower($userType));

       // Replace any non alpha char with underscore char
       $filename = preg_replace("/[^a-z]/", '_', $filename);

       // Create the filename
       $template = sprintf("%s/%s_home.html", USER_HOME_DIR, $filename);

       // If the chosen user template does not exists,
       // use default template
       if (!file_exists($template))
           $template = DEFAULT_HOME_TEMPLATE;

       // Return the FQPN of template
       return $template;
   }


   /**
   * Returns time in microsend (for Un*ix) and seconds for Windwos
   *
   * @param none
   * @return $now - microseconds for Un*x or seconds for windows
   */
   function timeNow()
   {
       // Since micro second support is not available
       // in Windows so we will get timestamp seconds
       if (preg_match("/WINDOWS/i", SERVER_OS))
       {
          $now = time();
       }

       // For *real* OS like Linux, FreeBSD, etc.
       // get timestamp with microseconds
       else
       {
          list($usec, $sec) = explode(" ", microtime());
          $now = (float)$usec + (float)$sec;
       }

       return $now;
   }


    /**
   * Act as a wrapper for mysql_query to allow profile data collection
   *
   * @param $stmt - query statement
   * @return $results - query result set
   */
   function _mysql_query_wrapper($stmt = null)
   {

      if (PROFILE_MODE)
      {
      	  $start = timeNow();
      }

      $result = mysql_query($stmt);

      // Do not profiling query itself!
      if (PROFILE_MODE && ! preg_match('/' . APP_PROFILE_TBL . '/' , $stmt) )
      {
      	  $end = timeNow();
      	  logProfile(CURRENT_APP_PREFIX, $start, $end, $stmt);
      }

      return $result;

   }

    /**
   * Add a application profile entry in APP_PROFILE_TBL
   *
   * @param $appName - application name
   * @param $start   - start time
   * @param $end     - end time
   * @param $code    - code being profiled
   * @param $notes   - optinal notes
   * @return bool - true if entry is added else false.
   */
   function logProfile($appName = null, $start = null, $end = null, $code = null, $notes = null)
   {
       $info = array();
       $info['table'] = APP_PROFILE_TBL;

       $data              = array();
       $data['name']      = $appName;
       $data['call']      = $_SERVER['PHP_SELF'];
       $data['start']     = $start;
       $data['end']       = $end;
       $data['total']     = $end - $start;
       $data['code']      = $code;
       $data['notes']     = $notes;
       $data['remote_ip'] = $_SERVER['REMOTE_ADDR'];
       $data['run_date']      = date('Y-m-d');

       $info['data']      = $data;
       $info['debug']     = false;
       $ret = insert($info);

       return ($ret['newid'] == null) ? true : false;
   }


   /**
   * Converts a dot-formatted IP address into a number
   *
   * @param $IPAddr - IP address in common dot format
   * @param $start   - start time
   * @return bool - true if entry is added else false.
   */
   function IPAddressToNumber($IPAddr = null)
   {
       if (empty($IPAddr)) {
           return 0;
       }

       $octate = split ("\.", $IPAddr);

       return ($octate[3] + $octate[2] * 256 +
               $octate[1] * 256 * 256 +
               $octate[0] * 256 * 256 * 256);
   }
?>
