<?php
/**
 * File: logout.class.php
 * This application is used to log out authenticated users.
 */
 
class LogoutApp extends DefaultApplication
{
   /**
   * This is the "main" function which is called to run the application
   *
   * @param none
   * @return true if successful, else returns false
   */
   function run()
   {
      // Create a user object
      $thisUser = new User();

      // Set default logout status to false (unsuccessful)
      $status = false;

      // If user is already logged in
      if ($thisUser->isAuthenticated())
      {
         // Unset all session variables
         session_unset();

         // Distroy session
         session_destroy();

         // Logout successful
         $status = true;
      }

      // OK now show login page
      $thisUser->goLogin();

      // Logout is successful
      return $status;
   }
}

?>