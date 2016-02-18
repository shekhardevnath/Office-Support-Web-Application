<?php
   /**
    * Filename: login.conf.php
    * Purpose : configuration file for login application
    */
   
   // Load login application specific classes
   require_once(USER_CLASS);
   
   /**#@+
   * Constant
   */
   
   // Define which user table field (email|username) is used as login ID
   define('LOGIN_ID_FIELD', 'username');
   
   // Status of users who can login
   define('ACTIVE_USER_STATUS', 'Active');
   
   define('TEMPLATE_DIR',      STANDARD_CONTENTS_DIR . '/' . CURRENT_APP_PREFIX); 
   define('REL_TEMPLATE_DIR',  REL_STANDARD_CONTENTS_DIR . '/' . CURRENT_APP_PREFIX); 
   define('LOGIN_TEMPLATE',    TEMPLATE_DIR . '/login.html');
   

?>