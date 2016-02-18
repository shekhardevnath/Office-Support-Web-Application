<?php
   /*******************************************************
    *  File name: database.conf.php
    *
    *  Purpose: this file is used to store database
    *           table name constants and it also starts
    *           the database connection
    *
    *  CVS ID: $Id$
    *
    ********************************************************/

   // If main configuration file which defines VERSION constant
   // is not loaded, die!
   if (! defined('VERSION'))
   {
      echo "You cannot access this file directly!";
      die();
   }

  // Please note:
  // in production mode, the database authentication information
  // may vary.
  if (PRODUCTION_MODE)
  {
     define('DB_USER', 'username');
     define('DB_PASS', 'password');
     define('DB_NAME', 'database');
     define('DB_HOST', 'localhost');
  }
  else
  {
    define('DB_USER', 'root');
    define('DB_PASS', '');

    define('DB_NAME', 'nm_tx');
    define('DB_HOST', '127.0.0.1');
  }

  /**
  * Common Table Constant
  */
  // Common Tables
  define('APP_INFO_TBL',                      DB_NAME . '.app_info');
  define('APP_LANGUAGE_TBL',                  DB_NAME . '.app_language');
  define('APP_MESSAGE_TBL',                   DB_NAME . '.app_message');
  define('APP_META_TBL',                      DB_NAME . '.app_meta');
  define('APP_PROFILE_TBL',                   DB_NAME . '.app_profile');
                                              
  define('COUNTRY_LOOKUP_TBL',                DB_NAME . '.country_lookup');
  define('US_STATE_TBL',                      DB_NAME . '.us_states');
                                              
  define('DOCUMENT_TBL',                      DB_NAME . '.document');
                                              
  define('GROUP_TBL',                         DB_NAME . '.group');
  define('USER_TBL',                          DB_NAME . '.user');
  define('ADDRESS_TBL',                       DB_NAME . '.user_address');
  define('USER_GROUP_TBL',                    DB_NAME . '.user_group');
                                              
  define('FAULT_TBL',                         DB_NAME . '.fault');
  define('COMMENT_TBL',                       DB_NAME . '.comment');
  define('CATEGORY_TBL',                      DB_NAME . '.problem_category');
  define('REMOTE_SUPPORT_TBL',                DB_NAME . '.remote_support');
  define('SUPPORT_COMMENT_TBL',               DB_NAME . '.support_comment');
  define('ALARM_HANDLING_PROCESS_TBL',        DB_NAME . '.alarm_handling_process');
  define('NMS_AVAILABILITY_TBL',              DB_NAME . '.nms_availability');
  define('NMS_MONITORING_TBL',                DB_NAME . '.nms_monitoring');
  define('MEETING_TRAINING_INFO_TBL',         DB_NAME . '.meeting_training_info');
  define('MEETING_TRAINING_PARTICIPANTS_TBL', DB_NAME . '.meeting_training_participants');
  define('TBASED_FAULT_LOG_TBL',              DB_NAME . '.tbased_fault_log');
  define('SUB_GROUP_TBL',                     DB_NAME . '.sub_group');
  define('SUB_GROUP_TBL',                     DB_NAME . '.sub_group');
  define('ACTIVITY_MANAGER_TBL',              DB_NAME . '.activity_manager');
  define('ACTIVITY_LOG_TBL',                  DB_NAME . '.activity_log');
  define('ACTIVITY_CATEGORY_TBL',             DB_NAME . '.activity_category');
  define('HC_PORTAL_TBL',                     DB_NAME . '.hc_portal');
  define('ONCALL_TBL',                        DB_NAME . '.oncall');
  define('TEAM_LEADER_TBL',                   DB_NAME . '.team_leader');
  define('SUBLEASED_TBL',                     DB_NAME . '.subleased');
  define('MPBN_LINK_TBL',                     DB_NAME . '.mpbn_link');
  define('MPBN_LINK_DETAILS_TBL',             DB_NAME . '.mpbn_link_details');
  define('GP_NOTICE_TBL',                     DB_NAME . '.gp_notice');
  define('GP_NOTICE_LOG_TBL',                 DB_NAME . '.gp_notice_log');
  define('GP_EMPLOYEE_DB_TBL',                DB_NAME . '.gp_employee_db');
  define('GP_SMS_GROUP_TBL',                  DB_NAME . '.gp_sms_group');
  define('MNI_TBL',                  	      DB_NAME . '.mni');
  define('MNI_AP_TBL',                        DB_NAME . '.mni_ap');

  if (AUTO_CONNECT_TO_DATABASE)
  {
      $dbcon = mysql_connect(DB_HOST, DB_USER, DB_PASS) or die("Could not connect: " . mysql_error());
      mysql_select_db(DB_NAME, $dbcon) or die("Could not find: " . mysql_error());
  }

?>
