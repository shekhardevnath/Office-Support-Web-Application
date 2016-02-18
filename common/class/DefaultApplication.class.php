<?php
/**
* This is the DefaultApplication class which
* provides the DefaultApplication object.
* @package DefaultApplication
*/
class DefaultApplication
{
   /**
   * Default Constructor
   * @return none
   */
   function DefaultApplication($attributes = null)
   {
      //Set application name
      $this->name = CURRENT_APP_NAME;

      //Set default langauge
      $this->language = DEFAULT_LANGUAGE;

      if ($attributes)
      {
         foreach($attributes as $key => $value)
         {
            $this->$key = $value;
         }
      }


   }

   /**
   * This function runs application(need to override)
   * @return none
   */
   function run()
   {
     // must override this
   }

   /**
   * This function stops application (should override)
   * @return none
   */
   function stop()
   {
     // you should override this in your class
     // if you need to do some cleanup after running
     // your app
   }

   /**
   * This function sets debug mode
   * @return none
   */
   function setDebugMode()
   {
     $this->debug_mode = true;
   }

   /**
   * This function resets debug mode
   * @return none
   */
   function resetDebugMode()
   {
     $this->debug_mode = false;
   }

   /*
   * Enables implicit flush mode so that NPH-like behavior is enabled
   * @return none
   */
   function enableImplicitFlushMode()
   {
      ob_implicit_flush(1);
   }

   /*
   * Disables implicit flush mode so that NPH-like behavior is disabled
   * @return none
   */
   function disableImplicitFlushMode()
   {
      ob_implicit_flush(0);
   }

   /*
   * Sets specified delay
   * @param integer $microSeconds
   * @return none
   */
   function setMicroSecondDelay($microSeconds = null)
   {
       if (! preg_match("/WINDOWS/i", SERVER_OS))
       {
          usleep($microSeconds);
       }
   }

   /**
   * Starts timer
   * @return none
   */
   function startTimer()
   {
      insertIntoSession($this->name . '-start', $this->timeNow());
   }

   /**
   * Stops timer
   * @return none
   */
   function stopTimer()
   {
      insertIntoSession($this->name . '-stop', $this->timeNow());
   }
   /**
   * Gets start timer-time
   * @return integer timer-time
   */
   function getStartTime()
   {
      return getFromSession($this->name . '-start');
   }

   /**
   * Gets stop timer-time
   * @return integer timer-time
   */
   function getStopTime()
   {
      return getFromSession($this->name . '-stop');
   }

   /**
   * Get elapsed timer-time
   * @return integer timer-time
   */
   function elapsedTime()
   {
      $diff = $this->getStopTime() - $this->getStartTime();
      echo_br("Start time: " . $this->getStartTime());
      echo_br("End time: " .   $this->getStopTime());
      echo_br("Spent: $diff seconds");
   }

   /**
   * Gets current time
   * @return integer $now
   */
   function timeNow()
   {
       // Since micro second support is not available
       // in Windows so we will get timestamp seconds
       if (preg_match("/WINDOWS/i", SERVER_OS))
       {
          $now = time();
       }

       // For *real* OS like Linux, FreeBSD, etc.
       // get timestamp with microseconds
       else
       {
          list($usec, $sec) = explode(" ", microtime());
          $now = (float)$usec + (float)$sec;
       }

       return $now;
   }
   /**
   * Sets language
   * @param $newLanguage
   * @return none
   */
   function setLanguage($newLanguage = null)
   {
     $this->language = $newLanguage;
   }

   /**
   * Get current language
   * @return current language
   */
   function getLanguage()
   {
   	 return $this->language;
   }

   /**
   * Gets message
   * @param $msg
   * @return current language
   */
   function getMessage($msgCode = null,  $data = null, $msgLang = null)
   {
   	 if (empty($msgLang))
   	     $msgLang = $this->getLanguage();
      $thisMsg = new ApplicationMessage(array('msg_code' => $msgCode,
                                              'msg_lang' => $msgLang)
                                       );

      $msgText = $thisMsg->getMsgText();

      // No message found
      if (empty($msgText))
      {
         $thisMsg = new ApplicationMessage(array('msg_code' => ERR_APP_MESSAGE_NOT_FOUND,
                                              'msg_lang' => $this->getLanguage())
                                       );

         $msgText = $thisMsg->getMsgText();
      }
      // Now personalize the message with data

      if (count($data))
      {
      	  foreach ($data as $key => $value)
      	  {
      	  	 $placeHolderKey = '{$'. $key . '}'; // {$key}
      	  	 $msgText        = str_replace($placeHolderKey,  $value, $msgText);
      	  }
      }

      return $msgText;

   }

   /**
   * Sets user
   * @param user
   * @return none
   */
   function setUser($me = null)
   {
      $this->user = $me;
   }

   /**
   * Sets navigation
   * @param navigation item
   * @return none
   */
   function setNavigation($navItem = null)
   {
      $this->navigation = $navItem;
   }

   /**
   * Displays Screen
   * @param contents
   * @return createPage($template, $data)
   */
   function displayScreen($contents = null)
   {

      $userType = $_SESSION['user_type'];

      $template = getHomeTemplate($userType);

      $data = array();

      $data['contents'] = $contents;
      $data['nav_item'] = $this->navigation;
      $data['top_nav']  = $_SESSION['top_nav'];
      $data['type']     = $_SESSION['type'];
      
      return createPage($template, $data);
   }
   /**
   * Gets command (cmd) value
   * @return getUserField('cmd')
   */  
   function getCommand()
   {
     return getUserField('cmd');	
   }
   /**
   * Gets dump of session-data
   * @return none
   */
   function dumpSession()
   {
      dumpVar($_SESSION);
   }

}
?>