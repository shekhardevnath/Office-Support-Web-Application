<?php
    /**
    * Filename: user_home.conf.php
    * Purpose : configuration file for user_home application
   */
   
   // Load user home application specific classes
   require_once(USER_CLASS);
   
   /**#@+
   * PATH Constant
   */   
   define('TEMPLATE_DIR',                 STANDARD_CONTENTS_DIR       . '/' . CURRENT_APP_PREFIX); 
   define('REL_TEMPLATE_DIR',             REL_STANDARD_CONTENTS_DIR   . '/' . CURRENT_APP_PREFIX); 
   
   /**#@+
   * Template Constant
   */   
   define('DEFAULT_HOME_TEMPLATE',        TEMPLATE_DIR                . '/default_home.html');
   define('DEFAULT_HEADER_TEMPLATE',      TEMPLATE_DIR                . '/default_header.html');
   define('DEFAULT_NAVIGATION_TEMPLATE',  TEMPLATE_DIR                . '/default_navigation.html');
?>