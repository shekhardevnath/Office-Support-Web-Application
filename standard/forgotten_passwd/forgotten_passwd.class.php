<?php

/**
 * File: forgotten_password.class.php
 * This application is used to get user email address,
 * send password reset link via email and reset password
 * when user clicks on the reset link and enters new password with
 * confirmation information.
 */

class ForgottonPasswordApp extends DefaultApplication
{

   /**
   * This is the "main" function which is called to run the application
   *
   * @param none
   * @return true if successful, else returns false
   */
   function run()
   {
      $thisUser = new User();

      // If user is logged in
      if ($thisUser->isLoggedIn())
      {
          $thisUser->goHome();
      }

      // Get command
      //$cmd = $this->getCommand();
      $cmd = getUserField('cmdd');

      // Take action based on command

      switch ($cmd)
      {
        case 'reset_pwd'   : $screen = $this->resetPassword(); break;
        case 'change_pwd'  : $screen = $this->showPasswordResetForm(); break;
        case 'send'        : $screen = $this->sendPasswordResetLink(); break;
        default            : $screen = $this->showEmailForm(); break;
      }

      echo $screen;

      return true;
   }

   /**
   * Shows the email form template
   *
   * @param none
   * @return the email form screen
   */
   function showEmailForm()
   {
      $data          = array();
      $data['cmdd']   = 'send';
      $data['email'] = getUserField('email');
      //dumpvar($_REQUEST);

      return createPage(EMAIL_FORM_TEMPLATE, $data);
   }

   /**
   * Sends the password reset link via email
   *
   * @param none
   * @return a status screen stating if email is sent or not
   */
   function sendPasswordResetLink()
   {
      $anyUser = new User();
      $data    = array();
      $email   = getUserField('email');


      // If no email address has been provided (js disabled?)
      if (empty($email))
          return showEmailForm();


      $info['fields'] = array("primary_email");
      $info['table']  = USER_TBL;
      $info['where']  = "primary_email = '$email'";
      $info['debug']  = false;

      $userList = select($info);

      if (count($userList) < 1)
      {
         $status = $this->getMessage(ERROR_NO_EMAIL_FOUND);
         $data['status'] = $status;
          return createPage(STATUS_TEMPLATE, $data);
       }

      $newPassword    = $this->generatePassword();

      $info['fields'] = array("username","first_name","last_name");
      $info['table']  = USER_TBL;
      $info['where']  = "primary_email ='$email'";
      $info['debug']  = false;
      $receiver       = select($info);

      //dumpvar($receiver);

      $data['email']       = $email;
      $data['first_name']  = $receiver[0]->first_name;
      $data['senderName']  = FROM_NAME;
      $data['senderEmail'] = FROM_ADDRESS;

      $data['subject']     = EMAIL_SUBJECT;
      $data['body']        = file_get_contents(MAIL_TEMPLATE);

      $data['body']        = str_replace('{$first_name}', $receiver[0]->first_name, $data['body']);
      $data['body']        = str_replace('{$last_name}' , $receiver[0]->last_name,  $data['body']);
      $data['body']        = str_replace('{$username}'  , $receiver[0]->username,   $data['body']);
      $data['body']        = str_replace('{$newPass}'   , $newPassword,             $data['body']);

      //dumpvar($data);


      // Change this to use phpmailer
      $ok = $this->sendMail($data);

      if ($ok)
      {
          $this->updatePassword($newPassword, $receiver[0]->username,$email);
          $status = $this->getMessage(SUCCESS_EMAIL_SENT);
      }
      else
      {
            $status = $this->getMessage(ERROR_EMAIL_NOT_SENT);
      }

      $data['status'] = $status;
      return createPage(STATUS_TEMPLATE, $data);

   }

   /**
   * Returns user ID (uid) from password reset key
   *
   * @param $encodedKey -- encoded password reset string
   * @return $uid -- user ID
   */
   function getUidFromPasswordResetKey($encodedKey = null)
   {
       $decodedKey       = base64_decode($encodedKey);
       list($uid, $junk) = explode(";", $decodedKey);

       return $uid;
   }

   /**
   * Shows password reset form
   *
   * @param none
   * @return password reset form page
   */
   function showPasswordResetForm()
   {
      $resetKey       = getUserField('r');
      $uid            = $this->getUidFromPasswordResetKey($resetKey);
      $thisUser       = new User(array('uid' => $uid));
      $storedResetKey = $thisUser->getResetKey();

      // If user provided reset key and reset key stored in the databsae
      // do not match than this is a unsafe reset as spoofing
      // of information may have occured, abort

      if (strcmp($storedResetKey, $resetKey))
      {
          $data['status'] = $this->getMessage(ERROR_INVALID_RESET_REQUEST);
          return createPage(STATUS_TEMPLATE, $data);
      }

      // OK store the UID in the session so
      // that we can reset the user's password
      // when she enters it in form.
      insertIntoSession('reset_uid', $uid);
      $data['cmd'] = 'reset_pwd';

      return createPage(PASSWORD_RESET_TEMPLATE, $data);
   }

   /**
   * Resets the user password
   *
   * @param none
   * @return status page
   */
   function resetPassword()
   {
      $data        = array();
      $uid         = getFromSession('reset_uid');
      $thisUser    = new User(array('uid' => $uid));
      $newPassword = getUserField('password');

      $info['password']  = $thisUser->encryptPassword($newPassword);
      $info['reset_key'] = 'NULL';

      $thisUser->modifyUser($info);

      $data['status'] = $this->getMessage(SUCCESS_PASSWORD_CHANGED);

      return createPage(STATUS_TEMPLATE, $data);
   }

   /**
   * Generates password
   *
   * @return password
   */
   function generatePassword()
   {

      $pwd = null;

      $passLen = mt_rand(4,12);
      $i=1;

      while($i<=$passLen)
      {
         $ithChr= mt_rand(48,122);

         if(($ithChr>57 && $ithChr<65)||($ithChr>90 && $ithChr<97))
           continue;
         $pwd .= chr($ithChr);
         $i++;
      }
      return $pwd;
   }

  /**
   * Sends mail
   * @param mail data
   * @return boolean
   */
   function sendMail($data)
  {

  	 $firstName             = $data['first_name'];
     $email                 = $data['email'];
     $currentDate           = strtotime(date("Y-m-d"));
     
     //Instantiate the phpMailer class
     $mail                  = new phpmailer();

     $mail->Host            = LOCALHOST;
     $mail->Mailer         = "smtp";
     $mail->SMTPAuth        = false;


     $mail->FromName        = $data['senderName'];
     $mail->From            = $data['senderEmail'];
     $mail->Subject         = $data['subject'];
     $body = $data['body'];

     $mail->Body            = nl2br(html_entity_decode($body));
     $mail->IsHTML(true);


     $mail->AddAddress($email,$firstName);

     $mailSent = $mail->Send();

     return $mailSent;

  }
  /**
   * Updates password
   * @param new password, username, email
   * @return none
   */
  function updatePassword($newPassword = null,$userName = null, $email = null)
  {
      $newPassword = md5($newPassword);

      $data['password'] = $newPassword;
      $info['data'] = $data;
      $info['table']= USER_TBL;
      $info['where']= "username = '$userName' AND primary_email = '$email'";
      $info['debug']= false;

      update($info);

  }

} // End of Class
?>