<?php

/**
  *file: search_page.conf.php
  *purpose: configuration file for the search page application
  **/

// include the user class
   require_once(USER_CLASS);
   require_once(DOCUMENT_CLASS);
   
  /**
   * Template PATH Constant
   */
   define('TEMPLATE_DIR',                  APP_CONTENTS_DIR     . '/' . CURRENT_APP_PREFIX);
   define('REL_TEMPLATE_DIR',              REL_APP_CONTENTS_DIR . '/' . CURRENT_APP_PREFIX);

   /**
   * Template Constant
   */
   
   define('SEARCH_PAGE_TEMPLATE',      TEMPLATE_DIR . '/search_page.html');  

?>