<?php

/**
  *file: report_manager.conf.php
  *purpose: configuration file for the report manager application
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
   define('REPORT_MANAGER_TEMPLATE', TEMPLATE_DIR . '/report_manager.html');
   
   define('NM_TX_ESCALATEION_GROUP', " escalationby='m_atiqur'  or escalationby='dipta'     or escalationby='samia.islam'   or escalationby='enamulhaque'   or 
     					                         escalationby='krayhan'   or escalationby='masrur'    or escalationby='sajal'         or escalationby='shariful'      or
     					                         escalationby='shekhar'   or escalationby='smonzur'   or escalationby='mofidul'       or escalationby='tanjil'        or
     					                         escalationby='taslima'   or escalationby='t_momin'   or escalationby='shariar.kabir' or escalationby='shams.shariar' or
     					                         escalationby='j_ferdous' or escalationby='gazi.asif'");
?>