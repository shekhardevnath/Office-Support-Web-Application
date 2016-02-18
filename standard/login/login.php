<?php
   /**
    * Filename: login.php
    * Purpose : This application performs login authentication for users    
    */
    
   require_once($_SERVER['DOCUMENT_ROOT'] . '/nm_tx/common/conf/main.conf.php');
   
   // Create a login application object
   $thisApp = new LoginApp();
   
   // Run the application
   $thisApp->run();
   
?>