<?php

/**
 * File: user_manager.class.php
 
/**
 * The userManager application class
 */

class userManagerApp extends DefaultApplication
{
   /**
   * Constructor
   * @return true
   */

   function run()
   {
      $cmd = getUserField('cmd');

      switch ($cmd)
      {
           case 'edit'   : $screen = $this->showEditor($msg);  break;
           case 'add'    : $screen = $this->saveRecord();      break;
           case 'delete' : $screen = $this->deleteRecord();    break;
           case 'list'   : $screen = $this->showList();        break;
           default       : $screen = $this->showEditor($msg);
      }

      // Set the current navigation item
      $this->setNavigation('user');

      if ($cmd == 'list')
      {
         echo $screen;
      }
      else
      {
         echo $this->displayScreen($screen);
      }

      return true;

   }

   /**
   * Shows User Editor
   * @param message
   * @return user editor template
   */
   function showEditor($msg)
   {
      $uid = getUserField('id');

      if (!empty($uid))
      {
         
         $thisUser          = new User(array('uid' => $uid));                           
         $data              = array_merge(array(), (array)$thisUser);         
      }

      $data['message']             = $msg;
      $data['user_type_list']      = getEnumFieldValues(USER_TBL, 'user_type');
      $data['group_name_list']     = getGroupNameList(GROUP_TBL);
      $data['sub_group_name_list'] = getSubGroupNameList(SUB_GROUP_TBL);
      
      return createPage(USER_EDITOR_TEMPLATE, $data);
   }

   /**
   * Saves User information
   * @return message
   */
   function saveRecord()
   {
      $userID = getUserField('id');

      if($userID)
      {
         $thisUser = new User();

         if($thisUser->modifyUser($userID))
         {
            $msg = $this->getMessage(USER_UPDATE_SUCCESS_MSG);
         }
         else
         {
            $msg = $this->getMessage(USER_UPDATE_ERROR_MSG);
         }
      }
      else
      {
         $thisUser = new User();

         if($thisUser->addUser($photoID))
         {
            $msg = $this->getMessage(USER_SAVE_SUCCESS_MSG);
         }
         else
         {
            $msg = $this->getMessage(USER_SAVE_ERROR_MSG);
         }

         setUserField('cmd', '');
      }

      return $this->showEditor($msg);
   }

   /**
   * deletes user info
   * @return message
   */
   function deleteRecord()
   {
      $userID   = getUserField('id');
      $thisUser = new User();

      $rows  = $thisUser->deleteUser($userID);

      if($rows)
      {
         $msg = $this->getMessage(USER_DELETE_SUCCESS_MSG);
      }
      else
      {
         $msg = $this->getMessage(USER_DELETE_ERROR_MSG);
      }

      setUserField('id',  '');
      setUserField('cmd', '');

      return $this->showEditor($msg);
   }

   /**
   * Shows user list
   * @return user list template
   */
   function showList()
   {
   	  $type         = getUserField('user_type');
   	  $groupID      = getUserField('group_id'); 	  
      $filterClause = '1';
      $fields       = array('u.uid',
                            'u.name',
                            'u.group_id',
                            'u.username',
                            'u.password',
                            'u.email',
                            'u.create_date',
                            'u.last_updated',
                            'u.user_type',
                            'g.name as group_name' 
                            );
      
      if ($type)
         $filterClause .= " and u.user_type = '$type' ";
      if ($groupID)
         $filterClause .= " and u.group_id = '$groupID' ";   

      $info['table']  = USER_TBL . ' as u inner join ' . GROUP_TBL . ' as g using(group_id) ';
      $info['debug']  = false;
      $info['fields'] = $fields; 
      $info['where']  = $filterClause . ' Order By username ASC';

      $data['list'] = select($info);
      
      $data['user_type_list'] = getEnumFieldValues(USER_TBL, 'user_type');      
      $data['user_type']      = $type;
      
      $data['group_name_list'] = getGroupNameList(GROUP_TBL);
      $data['group_id']        = $groupID;

      echo createPage(USER_LIST_TEMPLATE, $data);
   }
}
?>