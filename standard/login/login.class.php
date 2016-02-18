<?php
/**
 * File: login.class.php
 * This application is used to authenticate users
 *
 */

class LoginApp extends DefaultApplication
{
   /**
   * This is the "main" function which is called to run the application
   *
   * @param none
   * @return true if successful, else returns false
   */
   function run()
   {
      $credentials = array();


      // Get the user supplied credentials
      $credentials[LOGIN_ID_FIELD]  = getUserField('loginid');
      $credentials['password']      = getUserField('password');
      
      $rememberMe = getUserField(remember_me);

      // Create a new user object with the credentials
      $thisUser = new User($credentials);

      // Authenticate the user
      $ok = $thisUser->authenticate();

      // If successful (i.e. user supplied valid credentials)
      // show user home
      if ($ok)
      {
      	  //set user supplied values to cookie
      	  if($rememberMe)
      	  {
      	  	setcookie('usr_name', $credentials[LOGIN_ID_FIELD], time() + 259200, '/nm_tx/');
      	  	setcookie('usr_pass', $credentials['password'],     time() + 259200, '/nm_tx/');
      	  }
      	  else
      	  {
      	  	setcookie('usr_name', '');
      	  	setcookie('usr_pass', '');
      	  }
      	  
      	  
      	  //save value to session that will help to set the pop up logic
      	  $_SESSION['type'] = $_SESSION['type'] ? $_SESSION['type'] : getUserField('type');
      	  
          $thisUser->goHome();
      }
      // User supplied invalid credentials so show login form
      // again
      else
      { 
      	  $data = array();
      	  
      	  $data['loginid']  = $_COOKIE['usr_name'];
      	  $data['password'] = $_COOKIE['usr_pass'];
      	  
      	  if(!$data['loginid'] && !$data['password'])
      	  {
      	  	$data = array_merge($data, $_REQUEST);
      	  }
      	        	  
          echo createPage(LOGIN_TEMPLATE, $data);
      }

      return true;
   }
} // End class

?>