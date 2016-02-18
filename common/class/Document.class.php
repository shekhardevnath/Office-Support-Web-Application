<?php
/**
* This class provides feature to access Document Table
* @package DocumentEntity
*/
class DocumentEntity extends Entity
{
  /**
  * Constructor
  * @param ID of Document to load
  * @return none
  */
  function DocumentEntity($doc_id = null , $doc_dir = null)
  {
    $this->entity_table  =  DOCUMENT_TBL;

    //if (defined('DOCUMENT_REPOSITORY'))
       // $this->doc_dir = DOCUMENT_REPOSITORY;
    //else
    //dumpVar($doc_dir);
    if($doc_dir)
    {
       $this->doc_dir              = DOCUMENT_ROOT .'/' . $doc_dir; 
       $this->is_user_defined_path = true;   
    }
    else
    {
       $this->doc_dir = DOCUMENT_ROOT .'/documents';       
    }
        
        
    $this->loaded = false;

    if($doc_id)
      $this->loadDocument($doc_id);
  }

  /**
  * Checks to see if file exists
  * @return true if file found, else false
  */
  function fileExists()
  {
    if($this->loaded)
    {
    	$fileName = explode('.', $this->remote_filename);
      $fileExt  = array_pop($fileName);
      return file_exists($this->doc_dir . '/' .
                         $this->remote_filename[0] . '/' .
                         $this->remote_filename[1].'/'.$this->id.'.'.$fileExt);
    }
    else
      return false;
  }

  /**
  * Gets ID of current document
  * @return ID
  */
  function getId()
  {
      return $this->id;
  }

  /**
  * Sets Title of current document
  * @param title
  * @return none
  */
  function setTitle($val = null)
  {
      $this->title  =  $val;
  }

  /**
  * Gets title of current document
  * @return title
  */
  function getTitle()
  {
      return $this->title;
  }

  /**
  * Sets Mime Type of current document
  * @param mime type
  * @return none
  */
  function setMimeType($val = null)
  {
      $this->mime_type  =  $val;
  }
  /**
  * Gets Mime Type of current document
  * @return mime type
  */
  function getMimeType()
  {
      return $this->mime_type;
  }

  /**
  * Sets Local file name of current document
  * @param local file name
  * @return none
  */
  function setLocalFileName($val = null)
  {
      $this->local_filename  =  $val;
  }
  /**
  * Gets Local file name of current document
  * @return local file name
  */
  function getLocalFileName()
  {
      return $this->local_filename;
  }

  /**
  * Sets remote file name
  * @param remote file name
  * @return none
  */
  function setRemoteFileName($val = null)
  {
      $this->remote_filename  =  $val;
  }
  /**
  * Gets remote file name of current document
  * @return remote file name
  */
  function getRemoteFileName()
  {
      return $this->remote_filename;
  }

  /**
  * Sets key words of current document
  * @param keywords
  * @return none
  */
  function setKeyWords($val = null)
  {
      $this->keywords  =  $val;
  }
  /**
  * Gets key words of current document
  * @return keywords
  */
  function getKeyWords()
  {
      return $this->keywords;
  }

  /**
  * Sets file size of current document
  * @param size
  * @return none
  */
  function setSize($val = null)
  {
      $this->size  =  $val;
  }
  /**
  * Gets file size of current document
  * @return size
  */
  function getSize()
  {
      return $this->size;
  }

  /**
  * Sets access type of current document
  * @param access type
  * @return none
  */
  function setAccessType($val = null)
  {
      $this->access_type  =  $val;
  }
  /**
  * Gets access type of current document
  * @return access type
  */
  function getAccessType()
  {
      return $this->access_type;
  }
  
  /**
  * Get absolute of he document
  * @return Absolute path
  */
  function getAbsolutePath()
  {
      return $this->doc_dir;
  }

  /**
  * Sets owner of current document
  * @param owner
  * @return none
  */
  function setOwner($val = null)
  {
      $this->owner_id  =  $val;
  }
  /**
  * Gets owner of current document
  * @return owner
  */
  function getOwner()
  {
      return $this->owner_id;
  }

  /**
  * Sets permission group of current document
  * @param permission group id
  * @return none
  */
  function setPermissionGroupId($val = null)
  {
      $this->permission_group_id  =  $val;
  }
  /**
  * Gets permission group of current document
  * @return permission group id
  */
  function getPermissionGroupId()
  {
      return $this->permission_group_id;
  }

  /**
  * Sets group access rights of current document
  * @param group access rights
  * @return none
  */
  function setGroupAccessRights($val = null)
  {
      $this->group_access_rights  =  $val;
  }
  /**
  * Gets group access rights
  * @return group access rights
  */
  function getGroupAccessRights()
  {
      return $this->group_access_rights;
  }

  /**
  * Sets world access rights of current document
  * @param world access rights
  * @return none
  */
  function setWorldAccessRights($val = null)
  {
      $this->world_access_rights  =  $val;
  }
  /**
  * Gets world access rights of current document
  * @return world access rights
  */
  function getWorldAccessRights()
  {
      return $this->world_access_rights;
  }

  /**
  * Loads a document
  *
  * @param id of document
  * @return none
  *
  */
  function loadDocument($doc_id = null)
  {

    if(!$doc_id){ $this->loaded = false; return false; }

    $info  =  array();

    $info['table']  =  $this->entity_table;
    $info['where']  =  "id =".q($doc_id);
    $info['debug']  =  false;

    $selected  =  select($info);
    $rowObject = $selected[0];
    
    //If data found
    if (count($rowObject))
    {
        foreach($rowObject as $fieldName   =>  $fieldValue)
        {
           $this->$fieldName  =  $fieldValue;
        }
    }
    $this->loaded = true;
  }

 /**
  * Adds a document
  *
  * @param attributes array
  * @return returns false if failed, else new document ID.
  *
  */
  function addDocument($attributes  =  null)
  {
    if(!$attributes)
      $attributes  =  $this->getAttributes();

    
    //uncomment these lines to have class get user input direct from browser
    //if(!count($attributes))
    //  $attributes  =  getUserDataSet($this->entity_table);

    $info  =  array();

    $info['table']  =  $this->entity_table;
    $info['data']   =  $attributes;
    $info['debug']  =  false;

    // Do insert
    $ret  =  insert($info);
    // See if insert was a sussess
    if ($ret['affected_rows'] > 0)
    {

      $doc_id  =  $ret['newid'];
      
      // Now upload the document file to disk
      $uploaded  =  $this->uploadDocument($doc_id);

      if($uploaded)
      {
        $info['data']   =  $uploaded;
        $info['where']  =  "id =".q($doc_id);
        $updated  = update($info);
      }

      if(!$updated || !$uploaded)
      {
        // delete previously inserted new record
        $this->deletePhysicalDocument($doc_id);
        $this->deleteDocument($doc_id);
        return false;
      }
      return $doc_id;

    }
    return false;
  }

  /**
  * Deletes a document
  *
  * @param id of document
  * @return returns false if failed, else true
  *
  */
  function deleteDocument($doc_id = null)
  {

    if(!$doc_id)
    {
      if($this->loaded)
          $doc_id  =  $this->id;
      else
        return false;
    }

    //$this->deletePhysicalDocument($doc_id);

    $info  =  array();
    $data['status'] = 'Deleted';
    $info['table']  = $this->entity_table;
    $info['where']  = "id=".q($doc_id);
    $info['data']   = $data;
    $info['debug']  = false;
    
    //Update and return boolean
    return update($info);
  }

  /**
  * Modifies a document
  *
  * @param id of document
  * @return returns false if failed, else true
  *
  */
  function modifyDocument($doc_id = null, $attributes = null)
  {
    if(!$attributes)
      $attributes  =  $this->getAttributes();

    if(!$attributes)
      $attributes  =  getUserDataSet($this->entity_table);

    if($_FILES)
    {
      $this->deletePhysicalDocument($doc_id);
      $uploaded  =  $this->uploadDocument($doc_id);
      if($uploaded)
        $attributes  =  array_merge($attributes, $uploaded);
    }
    
    
    $info  =  array();

    $info['table']  =  $this->entity_table;
    $info['data']   =  $attributes;
    $info['where']  =  "id =".q($doc_id);
    $info['debug']  =  false;
    
    //Update and return boolean
    return update($info);
  }

 /**
  * Uploads a document to %<doc_dir>/<$remote_filename[0]>/<$remote_filename[1]>/<$docId.$fileExt>
  *
  * @param document id
  * @return returns array of uploaded file attributes or false if upload fails
  *
  */
  function uploadDocument($doc_id = null)
  {
  	
  	$returnValue['remote_filename']  =  $this->convertFilename($_FILES['document']['name']);
  	$remote_filename = $returnValue['remote_filename'];
  	
    if ($_FILES['document']['error']  == 0)
    {
      if($this->is_user_defined_path)
      {
      	exec('mkdir -p ' . $this->doc_dir);
      }
      else
      {    
         if(!file_exists($this->doc_dir . '/' . $remote_filename[0]))
            mkdir($this->doc_dir . '/' . $remote_filename[0]);
           
         if(!file_exists($this->doc_dir . '/' . $remote_filename[0] . '/' . $remote_filename[1]))
            mkdir($this->doc_dir . '/' . $remote_filename[0] . '/' . $remote_filename[1]);
      }
         
      $returnValue['local_filename']   =  $_FILES['document']['name'];  

      $returnValue['mime_type']        =  $_FILES['document']['type'];
      
      $returnValue['size']             =  $_FILES['document']['size'];
      
      $fileName = explode('.', $remote_filename);
      $fileExt  = array_pop($fileName);
       
       
      if($this->is_user_defined_path)
      {
         $save_as  =  $this->doc_dir . '/' . $remote_filename;
      }
      else
      {    
         $save_as  =  $this->doc_dir . '/' . $remote_filename[0].'/'.$remote_filename[1]. '/' .$doc_id.'.'.$fileExt;
      }    
      
      $returnValue['doc_dir'] = $save_as;
      
      //dumpVar($save_as);
      $uploaded  =  copy($_FILES['document']['tmp_name'], $save_as);

      if($uploaded)
      {
         return $returnValue;
      }   
    }
              
    return false;
  }

/**
  * Deletes a physical file and the folder it is in
  *
  * @param document id
  * @return returns ture if success or false if fails
  *
  */
  function deletePhysicalDocument($doc_id = null)
  {
  	$remote_filename  =  $this->getRemoteFileName();
  	
  	$fileName = explode('.', $remote_filename);
    $fileExt  = array_pop($fileName);
  	
    if(!file_exists($this->doc_dir . '/' . $remote_filename[0].'/'.$remote_filename[1].'/'.$doc_id.'.'.$fileExt)) return false;

    if($handle  =  opendir($this->doc_dir.'/'.$remote_filename[0].'/'.$remote_filename[1]))
    {
       while (false !==  ($file  =  readdir($handle)))
       {
         if ($file !=  '.' && $file !=  '..')
         {
            unlink($this->doc_dir . '/' .$remote_filename[0].'/'.$remote_filename[1].'/'. $file);
         }
       }
       closedir($handle);
    }
    return rmdir($this->doc_dir . '/'. $remote_filename[0].'/'.$remote_filename[1]);
  }


/**
  * Removes all char except for A-Za-z0-9.- with underscore for given string
  *
  * @param sting
  * @return returns underscored string
  *
  */
  function convertFilename($filename = null)
  {
    $pattern  =  "/[^A-Za-z0-9\/.-]/";

    $replace  =  "_";

    return strtolower(preg_replace($pattern, $replace, $filename));
  }
}
?>