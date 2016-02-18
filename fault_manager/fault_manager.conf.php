<?php

/**
  *file: fault_manager.conf.php
  *purpose: configuration file for the fault manager application
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
   
   define('FAULT_EDITOR_TEMPLATE',          TEMPLATE_DIR . '/fault_manager.html');
   define('OTHER_FAULT_LIST_TEMPLATE',      TEMPLATE_DIR . '/fault_list_other.html');   
   define('NM_TX_FAULT_LIST_TEMPLATE',      TEMPLATE_DIR . '/fault_list_nm_tx.html');   
   
   //Pagination Constants
   define('ROWS_PER_PAGE',           20);
   define('MAXIMUM_ROWS_TO_DISPLAY', 500);

?>