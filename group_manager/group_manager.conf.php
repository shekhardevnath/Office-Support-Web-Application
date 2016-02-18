<?php

/**
 * File: group_manager.conf.php
 * This is the configuration file for the group manager application
 */

    // include the user class
    require_once(USER_CLASS);
    require_once(DOCUMENT_CLASS);

    /**#@+
    * PATH Constant
    */

    // defines the template and template path
    define('TEMPLATE_DIR',                 APP_CONTENTS_DIR     . '/' . CURRENT_APP_PREFIX);
    define('REL_TEMPLATE_DIR',             REL_APP_CONTENTS_DIR . '/' . CURRENT_APP_PREFIX);

    /**#@+
    * Template Constant
    */
    define('GROUP_EDITOR_TEMPLATE',        TEMPLATE_DIR . '/group_manager.html');
    define('GROUP_LIST_TEMPLATE',          TEMPLATE_DIR . '/group_list.html');

    /**#@+
    * Message Constant
    */
    //define the different messages
    define('GROUP_SAVE_SUCCESS_MSG',       1111);
    define('GROUP_UPDATE_SUCCESS_MSG',     1112);
    define('GROUP_DELETE_SUCCESS_MSG',     1113);
    define('GROUP_SAVE_ERROR_MSG',         1121);
    define('GROUP_UPDATE_ERROR_MSG',       1122);
    define('GROUP_DELETE_ERROR_MSG',       1123);
    define('DUPLICATE_GROUPNAME',          1131);
?>