<?php

   /**
   * Gets country/state list
   * @param table name
   * @return dataset
   */
   function getList($tblName)
   {
      $info['table'] = $tblName;
      $info['debug'] = false;

      $record = select($info);

      if(empty($record))
         return null;

      foreach($record as $row)
      {
         $data[$row->id] = $row->name;
      }

      return $data;

   }

   /**
   * Checks Username
   * @param username, userid
   * @return boolean
   */
   function checkusername($userName, $userID)
   {
      if ($userID)
      {
         $filterClause = ' and uid <> ' . q($userID);
      }

      $info['table'] =  USER_TBL;
      $info['where'] = 'username =' . q($userName) . $filterClause;
      $info['debug'] = false;

      $result = select($info);
      
      //If result is not empty
      if(empty($result))
      {
         return 0;
      }

      return 1;
   }

   /**
   * Checks Email
   * @param primary email, userid
   * @return boolean
   */
   function checkUserEmail($primaryEmail, $userID)
   {
      if ($userID)
      {
         $filterClause = ' and uid <> ' . q($userID);
      }

      $info['table'] =  USER_TBL;
      $info['where'] = 'email = ' . q($primaryEmail) . $filterClause;
      $info['debug'] = false;

      $result = select($info);
      
      //If result is empty
      if(empty($result))
      {
         return 0;
      }

      return 1;
   }
   
    /**
   * Gets group name list  
   * @return grouplist
   */
   function getGroupNameList($tblName)
   {
      $info['table'] = $tblName;
      $info['debug'] = false;
      $info['where'] = '1 order by name asc';

      $record = select($info);

      if(empty($record))
         return null;

      foreach($record as $row)
      {
         $data[0][] = $row->group_id; 
         $data[1][] = $row->name;
      }

      return $data;

   }
   
    /**
   * Gets sub group name list  
   * @return grouplist
   */
   function getSubGroupNameList($tblName)
   {
      $info['table'] = $tblName;
      $info['debug'] = false;
      $info['where'] = '1 order by name asc';

      $record = select($info);

      if(empty($record))
         return null;

      foreach($record as $row)
      {
         $data[0][] = $row->sub_group_id; 
         $data[1][] = $row->name;
      }

      return $data;

   }
?>