<?php
/**
 * This the application file that is invoked to
 * instanciate the group manager application
 */

   // include the main configuration file
   require_once($_SERVER['DOCUMENT_ROOT'] .'/nm_tx/common/conf/main.conf.php');
   require_once(LOCAL_CONFIG_DIR          .'/dp.conf.php');
   require_once(LOCAL_LIB_DIR             .'/dp.lib.php');
   require_once(LOCAL_CLASS_DIR           .'/Group.class.php');
   require_once(AJAX_DIR                  .'cpaint.inc.php');

   // Instanciate the user manager class
   $thisApp  = new groupManagerApp();

   // Instanciate the user class
   $thisUser = new User();

   // checks the user authentication
   if($thisUser->isAuthenticated())
   {
      $thisApp->run();
   }
   else
   {
      $thisUser->goLogin();
   }

?>