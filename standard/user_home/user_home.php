<?php
   /**
    * Filename: user_home.php
    * Purpose : This application displays user home page
   */
   
   // Load main configuration file
   require_once($_SERVER['DOCUMENT_ROOT'] . '/nm_tx/common/conf/main.conf.php');
   
   // Create an instance of the UserHomeApp object
   $thisApp = new UserHomeApp();

   // Run the application
   $thisApp->run();
      
?>