<?php
    /**
    * Filename: logout.class.php
    * Purpose : Defines a UserHomeApp class which extends DefaultApplication.
    *           The derived class (defined here) is used to display
    *           appropriate user home page.
    */ 
   
   class UserHomeApp extends DefaultApplication 
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
      	 
      	 // User must be authentiacted, else must
      	 // login (again)
      	 if (!$thisUser->isAuthenticated())
      	    $thisUser->goLogin();
      	 
      	 // Load user info from session
      	 $thisUser->loadFromSession();
      	 
      	 // Get user type
      	 $userType = $thisUser->getUserType();
      	 
      	 // Get user home template
      	 $template = getHomeTemplate($userType); 
      	 
      	 // Prepare smarty key=var
      	 $data = array();
      	 
      	 $contents = null;
      	 
      	 // Display home page
      	 //echo createPage($template, $data);


         // Set the current navigation item
         $this->setNavigation('home');
      	 
      	 echo $this->displayScreen($screen);
      	 
      	 return true;
      }
      
      

   }

?>