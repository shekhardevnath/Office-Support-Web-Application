<?php
   /**
   * File: group_manager.lib.php
   * Library File for Group Management
   /**
   * Gets List of UserTypes
   * @param groupid
   * @return array list of UserTypes
   */
   function getUserTypeList($groupID = null)
   {
      $userType = array('0' => 'All');
      $userType = array_merge($userType, getEnumFieldValues(USER_TBL, 'user_type'));

      foreach($userType as $key => $value)
      {
         $userTypeList[$key] = getUserList('', $value);
      }

      return $userTypeList;
   }

   /**
   * Gets List of Users
   * @param groupid, user type
   * @return array userid=>username
   */
   function getUserList($groupID = null, $userType = null)
   {
      $filterClause = ' status = ' . q(ACTIVE);

      $info['table'] = USER_TBL;
      $info['debug'] = false;

      if ($userType != 'All')
         $groupClause = " and user_type = '$userType' ";

      if ($groupID)
      {
         $info['where'] = $filterClause . $groupClause . " and uid Not In (SELECT user_id FROM ". USER_GROUP_TBL ." WHERE group_id = '$groupID') ";
   	}
   	  else
      {
         $info['where'] = $filterClause . $groupClause;
      }

      if($result = select($info))
      {
         foreach($result as $key => $value)
         {
            $list[$value->uid] = $value->username;
         }

         asort($list);
         reset($list);
      }

      return $list;
   }

   /**
   * Gets List of Team Members
   * @param groupID, &string
   * @return array list of team members
   */
   function getTeamMemberList($groupID = null, &$string)
   {
      $info['table'] = USER_TBL . ', ' . USER_GROUP_TBL;
      $info['where'] = "group_id = '$groupID' and user_id = uid";
      $info['debug'] = false;

      if($result = select($info))
      {
         foreach($result as $key => $value)
         {
            $list[$value->uid] = $value->username;
            $idList[] = $value->uid;
         }

         $string = implode(',', $idList);
         asort($list);
         reset($list);
      }

      return $list;
   }

   /**
   * Checks group name
   * @param groupName, groupID
   * @return boolean
   */
   function checkGroupName($groupName = null, $groupID = null)
   {
      if ($groupID)
         $filterClause = ' and group_id <> ' . q($groupID);

      $info['table'] =  GROUP_TBL;
      $info['where'] = 'name =' . q($groupName) . $filterClause;
      $info['debug'] = false;

      $result = select($info);

      if(empty($result))
      {
         return 0;
      }

      return 1;
   }

   /**
   * Checks group email
   * @param groupEmail, groupID
   * @param boolean
   */
   function checkGroupEmail($groupEmail = null, $groupID = null)
   {
      if ($groupID)
      {
         $filterClause = ' and group_id <> ' . q($groupID);
      }

      $info['table'] =  GROUP_TBL;
      $info['where'] = 'group_email =' . q($groupEmail) . $filterClause;
      $info['debug'] = false;

      $result = select($info);

      if(empty($result))
      {
         return 0;
      }

      return 1;
   }

?>