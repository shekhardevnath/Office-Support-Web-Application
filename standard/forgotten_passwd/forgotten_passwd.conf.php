<?php
/**
 * File: forgotten_password.conf.php
 * Configuration file forgotten_password application
 */

   /**#@+
   * PATH Constant
   */
   define('TEMPLATE_DIR',  STANDARD_CONTENTS_DIR . '/' . CURRENT_APP_PREFIX);
   define('REL_TEMPLATE_DIR',  REL_STANDARD_CONTENTS_DIR . '/' . CURRENT_APP_PREFIX);

   /**#@+
   * Template Constant
   */
   define('EMAIL_FORM_TEMPLATE',          TEMPLATE_DIR . '/ask_email.html');
   define('STATUS_TEMPLATE',              TEMPLATE_DIR . '/status.html');
   define('PASSWORD_RESET_TEMPLATE',      TEMPLATE_DIR . '/reset.html');

   /**#@+
   * Mail Configuration Constant
   */
   define('FROM_NAME',                    'Administrator');
   define('FROM_ADDRESS',                 'admin@admin.com');
   define('MAIL_TEMPLATE',                TEMPLATE_DIR . '/mail.txt');
   define('EMAIL_SUBJECT',                "Your password reset request is successfull");
   define('LOCALHOST',                    'localhost');

   /**#@+
   * Password Configuration Constant
   */
   define('MIN_PASSWORD_SIZE', 6);
   define('MAX_PASSWORD_SIZE', 12);
   define('ERROR_NO_EMAIL_FOUND',           130);
   define('ERROR_EMAIL_NOT_SENT',           140);
   define('SUCCESS_EMAIL_SENT',             150);
   define('SUCCESS_PASSWORD_CHANGED',       160);
   define('ERROR_INVALID_RESET_REQUEST',    170);

?>