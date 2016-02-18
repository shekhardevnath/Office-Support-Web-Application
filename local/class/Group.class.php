<?php
    /**
    * File: Group.class.php
    
    * The Group application class
    */

class Group
{
    /**
    * Constructor
    * @return
    */
    function Group($groupID = null)
    {
       $this->entity_table = GROUP_TBL;
       $this->loaded       = false;

       if ($groupID)
       {
          $this->loadGroupByID($groupID);
       }
    }

   /**
   * Gets Group Information
   * @return Group Information
   */
   function getGroupInfo()
   {
      return $this->getAttributes();
   }

   /**
   * Loads group info
   * @param group id
   * @return Group Information
   */
   function loadGroupByID($groupID = null)
   {
      return $this->loadGroupByKeyValue('group_id', $groupID);
   }

   /**
   * Loads group info
   * @param key => value
   * @return Group Information
   */
   function loadGroupByKeyValue($key = null, $value = null)
   {
      $info['table'] = $this->entity_table;
      $info['where'] = "$key = $value";
      $info['debug'] = false;
      $rows = select($info);

      if (count($rows))
      {
         foreach($rows[0] as $key => $value)
         {
            $this->$key = $value;
         }

         $this->loadGroupMember($value->group_id);

         $this->loaded = true;
      }
   }

   /**
   * Loads group member info
   * @param group id
   * @return Group Member Information
   */
   function loadGroupMember($groupID = null)
   {
      $info['table'] = USER_GROUP_TBL;
      $info['where'] = "group_id = $groupID";
      $info['debug'] = false;

      $rows = select($info);

      $this->member_list = $rows;
   }

   /**
   * Adds a group into
   * @param none
   * @return boolean
   */
   function addGroup()
   {
      $data = getUserDataSet(GROUP_TBL);

      $data['create_date'] = date('Y-m-d');

      $info['table'] = $this->entity_table;
      $info['debug'] = false;
      $info['data']  = $data;

      $add = insert($info);

      $groupID = $add['newid'];
      
      if ($groupID) return true;
      else return false;
   }

   /**
   * Adds group member
   * @param group_id, user_list
   * @return boolean
   */
   function addGroupMember($groupID = null, $userList = array())
   {
      $this->deleteGroupMember($groupID);

      $data['group_id'] = $groupID;

      $info['table'] = USER_GROUP_TBL;
      $info['debug'] = false;

      if (!empty($userList))
      {
         foreach ($userList as $key => $value)
         {
            $data['user_id'] = $value;
            $info['data']  = $data;

            $add = insert($info);
         }
      }

      return $add;
   }

   /**
   * Deletes group member
   * @param group id
   * @return boolean
   */
   function deleteGroupMember($groupID = null)
   {
      $info['table'] = USER_GROUP_TBL;
      $info['debug'] = false;
      $info['where'] = "group_id = '$groupID'";

      $del = delete($info);

      return $del;
   }

    /**
    * Modifies specfied group
    * @returns true in success, false otherwise
    */
   function modifyGroup($groupID = null)
   {
      $data = getUserDataSet(GROUP_TBL);

      $data['last_updated'] = date('Y-m-d');

      $info['table'] = $this->entity_table;
      $info['debug'] = false;
      $info['where'] = "group_id = '$groupID'";
      $info['data']  = $data;

      $update = update($info);
      
      if ($update) return true;
      else return false;
     }

    /**
    * Deletes specfied group from DB
    * @returns true in success, false otherwise
    */
    function deleteGroup($id)
    {
       $info=array();
       $info['table']=$this->entity_table;
       $info['where']="group_id='$id'";
       $info['debug']=false;
       
       return delete($info);
       
    }

    /**
    * Loads all groups
    * @return array group list
    */
    function loadAllGroup()
    {
      $info=array();
      $info['entity_table']=$this->entity_table;
      $info['fields']=array('group_id','name','group_type','created_by','group_email','status','create_date');
      $info['where']="name!=''";
      $info['debug']=false;

      $res=select($info);

      $list=array();

      if(count($res))
      {
         $i=0;
         foreach($res as $v)
         {
            $list['group_id'][$v->group_id]=$v->group_id;
            $list['group_name'][$v->group_id]=$v->name;
            $list['group_type'][$v->group_id]=$v->group_type;
            $list['group_created_by'][$v->group_id]=(!$v->created_by)?'Predefined':$this->findUser($v->created_by);
            $list['group_email'][$v->group_id]=$v->group_email;
            $list['group_status'][$v->group_id]=$v->status;
            $list['group_create_date'][$v->group_id]=$v->create_date;

         }
      }//End of if count

      return $list;
    }//End of loadAllGroup()

   /**
   * Finds username given by uid
   * @return username
   */
   function findUser($uid)
   {

      $info=array();
      $info['entity_table'] = USER_TBL;
      $info['fields']= array('username');
      $info['where'] = "uid='$uid'";
      $info['debug'] = false;

      $res=select($info);

      if(count($res))
      {
       foreach($res as $v)
       {
         $name=$v->username;
         break;
       }
      } //if count
      return $name;
   }//End of findUser()

   /**
   * Gets all groups
   * @return array group list
   */
   function getAllGroupList()
   {
      $info['entity_table'] = $this->entity_table;
      $info['debug'] = false;

      // Select groups from DB
      $result = select($info);

      return $result;
   }
}//End of Class
?>
