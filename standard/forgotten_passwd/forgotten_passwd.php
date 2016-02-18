<?php
  /*
   * Filename: forgeotten_password.php
   * Purpose : This application performs retrieval of forgotten password
   */
   
   require_once($_SERVER['DOCUMENT_ROOT'] . '/nm_tx/common/conf/main.conf.php');
   require_once(USER_CLASS);
   
   //Instantiate the  ForgottonPasswordApp Class
   $thisApp = new ForgottonPasswordApp();
   
     
   $thisApp->run();
   
?>