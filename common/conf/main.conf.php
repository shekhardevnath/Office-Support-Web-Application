<?php
   /***********************************************************
    *
    *  Main Configuration File
    *
    *  Purpose: this file is used to load all configuration
    *           related to current project
    *
    *  CVS ID: $Id$
    ***********************************************************/

   // Default language
   define('DEFAULT_LANGUAGE', 'en-us');

   // Default date/time format
   define('DATE_FORMAT', 'm/d/Y');
   define('TIME_FORMAT', 'h:i:s T');

   // Version of this software
   define('VERSION', '0.0.1');

   // Set this if this is released software
   define('PRODUCTION_MODE', false);

   // Test mode -- used to load test data
   define('TEST_MODE', false);

   // Profile mode
   define('PROFILE_MODE', false);

   // Set true if in debug mode
   define('DEBUG_MODE', false);

   // Automatically connect to database
   define('AUTO_CONNECT_TO_DATABASE', true);

  /*
   *  In production mode we do not show error messages
   *  on the Web browser since it can lead to security leaks
   *  Each application should record errors using PEAR Log()
   *  facility instead
   */
   define('E_NONE', 0);

   if (PRODUCTION_MODE)
      error_reporting(E_NONE);
   else
      error_reporting(E_ALL ^ E_NOTICE);

   /**
   * Setup server information constants
   */

   // Server name
   define('SERVER_NAME', $_SERVER['SERVER_NAME']);

   // Set server OS to Unix or Windows
   if (empty($_SERVER['WINDIR']))
       define('SERVER_OS', 'Unix');
   else
       define('SERVER_OS', 'Windows');

   // Server protocol and port
   if (preg_match("/HTTP\//i", $_SERVER['SERVER_PROTOCOL']))
       define('SERVER_PROTOCOL', 'http');
   else
       define('SERVER_PROTOCOL', 'https');

   define('SERVER_PORT', $_SERVER['SERVER_PORT']);

   // Script name
   define('PHP_SELF', $_SERVER['PHP_SELF']);

   // Site URL
   define('SITE_URL', SERVER_PROTOCOL . '://' . SERVER_NAME);

   // Start session
   session_start();

   define('DOCUMENT_ROOT',             $_SERVER['DOCUMENT_ROOT']);
   define('APP_DIR',                   DOCUMENT_ROOT . '/nm_tx');
   define('APP_CONTENTS_DIR',          DOCUMENT_ROOT . '/nm_tx');
   define('EXT_DIR',                   DOCUMENT_ROOT . '/nm_tx/ext');
   define('TEMP_DIR',                  DOCUMENT_ROOT . '/nm_tx/temp');
   define('DOCUMENTS_DIR',             DOCUMENT_ROOT . '/nm_tx/documents');
   define('PDF_DIR',                   DOCUMENT_ROOT . '/nm_tx/pdf');

   // Relative directory path
   define('REL_APP_DIR',               '/' . basename(APP_DIR));
   define('REL_APP_CONTENTS_DIR',      '/' . basename(APP_CONTENTS_DIR));
   define('REL_DOCUMENTS_DIR',         '/' . basename(DOCUMENTS_DIR));
   define('REL_COMMON_CONTENTS_DIR',   REL_APP_CONTENTS_DIR    . '/common');
   define('REL_COMMON_JAVASCRIPT_DIR', REL_COMMON_CONTENTS_DIR . '/js');
   define('REL_COMMON_TEMPLATE_DIR',   REL_COMMON_CONTENTS_DIR . '/template');
   define('REL_COMMON_IMAGE_DIR',      REL_COMMON_CONTENTS_DIR . '/image');
   define('REL_COMMON_CSS_DIR',        REL_COMMON_CONTENTS_DIR . '/css');
   define('REL_LOCAL_CONTENTS_DIR',    REL_APP_CONTENTS_DIR    . '/local');
   define('REL_LOCAL_JAVASCRIPT_DIR',  REL_LOCAL_CONTENTS_DIR  . '/js');
   define('REL_LOCAL_TEMPLATE_DIR',    REL_LOCAL_CONTENTS_DIR  . '/template');
   define('REL_LOCAL_IMAGE_DIR',       REL_LOCAL_CONTENTS_DIR  . '/image');
   define('REL_LOCAL_CSS_DIR',         REL_LOCAL_CONTENTS_DIR  . '/css');

   // Smarty configuration
   define('SMARTY_DIR',                EXT_DIR . '/smarty/libs/'); // trailing / required
   define('SMARTY_COMPILED_DIR',       TEMP_DIR);
   define('SMARTY_CACHE_DIR',          TEMP_DIR);
   define('SMARTY_CACHE_DIR',          TEMP_DIR);
   require_once(SMARTY_DIR .           'Smarty.class.php');

   // phpMailer confoguration
   define('PHPMAILER_DIR',             EXT_DIR . '/phpmailer');
   //require_once(PHPMAILER_DIR .        '/class.phpmailer.php');

   // Chart configuration
   //define('CHART_FILE',                EXT_DIR.'/charts/charts.php');

   // PDF configuration
   define('FPDF_DIR',                  EXT_DIR.'/fpdf/');
   //require_once(FPDF_DIR .             'class.fpdf.php');

   define('HTML2PDF_DIR',              EXT_DIR.'/html2fpdf/');
   //require_once(HTML2PDF_DIR .         'class.html2fpdf.php');

   define('HTMLTOOLKIT_DIR',           EXT_DIR.'/htmltoolkit/');
   //require_once(HTMLTOOLKIT_DIR .      'class.htmltoolkit.php');

   define('AJAX_DIR',                  EXT_DIR.'/ajax/');
   //define('AJAX_INC',                  AJAX_DIR . 'cpaint.inc.php');

   // Common directories
   define('COMMON_DIR',                    APP_DIR     . '/common');
   define('COMMON_CONFIG_DIR',             COMMON_DIR  . '/conf');
   define('COMMON_CLASS_DIR',              COMMON_DIR  . '/class');
   define('COMMON_LIB_DIR',                COMMON_DIR  . '/lib');
   define('RELATIVE_STANDARD_APP_DIR',     '/nm_tx/standard');

   // Local directories
   define('LOCAL_DIR',                     APP_DIR   . '/local');
   define('LOCAL_CONFIG_DIR',              LOCAL_DIR . '/conf');
   define('LOCAL_CLASS_DIR',               LOCAL_DIR . '/class');
   define('LOCAL_LIB_DIR',                 LOCAL_DIR . '/lib');

   // Content directories
   define('STANDARD_CONTENTS_DIR',         APP_CONTENTS_DIR . '/standard');
   define('REL_STANDARD_CONTENTS_DIR',     REL_APP_CONTENTS_DIR . '/' . basename(STANDARD_CONTENTS_DIR));
   define('REL_DEVELOPMENT_CONTENTS_DIR',  REL_APP_CONTENTS_DIR . '/' . basename(DEVELOPMENT_CONTENTS_DIR));

   // File upload directory
   define('DOCUMENT_REPOSITORY',         DOCUMENT_ROOT . '/documents');

   // Font directory
   define('FPDF_FONTPATH',               DOCUMENT_ROOT . '/font/');

   //Set OS based on client's request header
   define('CLIENT_OS', strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'win') ? 'Windows' : 'Other');

   // Load required files
   require_once(COMMON_CONFIG_DIR. '/database.conf.php');
   require_once(COMMON_LIB_DIR .   '/common.lib.php');

   // Common class names
   define('ENTITY_CLASS',              COMMON_CLASS_DIR . '/Entity.class.php');
   define('APPLICATION_CLASS',         COMMON_CLASS_DIR . '/DefaultApplication.class.php');
   define('APPLICATION_MESSAGE_CLASS', COMMON_CLASS_DIR . '/applicationMessage.class.php');
   define('USER_CLASS',                COMMON_CLASS_DIR . '/User.class.php');
   define('DOCUMENT_CLASS',            COMMON_CLASS_DIR . '/Document.class.php');

   // Load required classes
   require_once(ENTITY_CLASS);
   require_once(APPLICATION_CLASS);
   require_once(APPLICATION_MESSAGE_CLASS);

   //
   // Application Specific Configuration Loader
   //

   // Get the name of the script used to load your application
   define('CURRENT_APP_NAME', basename($_SERVER['PHP_SELF']));

   // Get application directory
   define('CURRENT_APP_DIR', dirname($_SERVER['SCRIPT_FILENAME']));

   // Get the application prefix (i.e. login.php has 'login' prefix)
   list($appPrefix, $appExt) = explode('.', CURRENT_APP_NAME);

   define('CURRENT_APP_PREFIX', $appPrefix);
   define('CURRENT_APP_CONFIG_FILE', CURRENT_APP_DIR . '/' . CURRENT_APP_PREFIX . '.conf.php');
   define('CURRENT_APP_LIB_FILE',    CURRENT_APP_DIR . '/' . CURRENT_APP_PREFIX . '.lib.php');
   define('CURRENT_APP_CLASS_FILE',  CURRENT_APP_DIR . '/' . CURRENT_APP_PREFIX . '.class.php');


   // Common URLs
   define('LOGIN_URL',         RELATIVE_STANDARD_APP_DIR . '/login/login.php');
   define('LOGOUT_URL',        RELATIVE_STANDARD_APP_DIR . '/logout/logout.php');
   define('USER_HOME_URL',     RELATIVE_STANDARD_APP_DIR . '/user_home/user_home.php');
   define('FAULT_MANAGER_URL', REL_APP_DIR               . '/fault_manager/fault_manager.php');

   // Load the application configuration file (if any)
   if (file_exists(CURRENT_APP_CONFIG_FILE))
       require_once(CURRENT_APP_CONFIG_FILE);

   // Load the application library file (if any)
   if (file_exists(CURRENT_APP_LIB_FILE))
       require_once(CURRENT_APP_LIB_FILE);

   // Load the application class file (if any)
   if (file_exists(CURRENT_APP_CLASS_FILE))
       require_once(CURRENT_APP_CLASS_FILE);

   // standard defines
   define('NULL_STRING', 'NULL');

   // Error messages
   define('ERROR_UNAUTHORIZED_ACCESS', 666);
   define('ERR_APP_MESSAGE_NOT_FOUND', 100);

   // User related configuration
   define('USER_HOME_DIR',                STANDARD_CONTENTS_DIR . '/user_home');
   define('NO_PERMISSION_TEMPLATE',       USER_HOME_DIR . '/permission_denied.html');

   define('HEADER',                       USER_HOME_DIR . '/header.html');
   define('FOOTER',                       USER_HOME_DIR . '/footer.html');

   define('DEFAULT_HOME_TEMPLATE',        USER_HOME_DIR . '/default_home.html');
   define('DEFAULT_HEADER_TEMPLATE',      USER_HOME_DIR . '/default_header.html');
   define('DEFAULT_NAVIGATION_TEMPLATE',  USER_HOME_DIR . '/default_navigation.html');
   
   //Some constants
   define('ADMIN_USER',       'Admin');
   define('NM_TX_USER',       'BO');
   define('OTHER_USER',       'Other');
   define('NM_TX_GROUP_ID',   '18');
   define('NM_SN_GROUP_ID',   '8');
   define('NM_CORE_GROUP_ID', '7');
   
   
   //set the time zone
  putenv('TZ=Asia/Dhaka');
?>