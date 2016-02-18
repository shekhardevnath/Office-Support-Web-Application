<?php
   /**
    * Filename: login.php
    * Purpose : This application logs out already logged in users
    */
   
   // Require main configuration file. 
   require_once($_SERVER['DOCUMENT_ROOT'] . '/nm_tx/common/conf/main.conf.php');
   
   // Create an instance of a logout application object
   $thisApp = new LogoutApp();
   
   // Run the application
   $thisApp->run();
   
   
?>