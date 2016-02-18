<?php
/**
* This class provides feature to access ApplicationMessage Table
*
*
* @author Humayoun Alamgir
*
* @version $Id$
*
*
* @package ApplicationMessage
*/
class ApplicationMessage extends Entity
{
  /**
  * Constructor
  * @param array - attributes of the message object
  * @return none
  */
  function ApplicationMessage($attributes = null)
  {
    $this->entity_table  =  APP_MESSAGE_TBL;
    
    $this->loaded = false;
    
    
    if($attributes['msg_code'])
    {
      $this->loadApplicationMessage($attributes['msg_code']);
    }  
  }
  
  /**
  * Gets Message Code
  * @return Message Code
  */
  function getMsgCode()
  {
      return $this->msg_code;
  }

  /**
  * Sets Message Type
  * @param MsgType
  * @return none
  */
  function setMsgType($val = null)
  {
      $this->msg_type  =  $val;
  }

  /**
  * Gets Message Type
  * @return Message Type
  */
  function getMsgType()
  {
      return $this->msg_type;
  }

  /**
  * Sets Message Text
  * @param Message Text
  * @return none
  */
  function setMsgText($val = null)
  {
      $this->msg_text  =  $val;
  }
  /**
  * Gets Message Text
  * @return Message Text
  */
  function getMsgText()
  {
      return $this->msg_text;
  }

  /**
  * Sets Message Language
  * @param Message Language
  * @return none
  */
  function setMsgLang($val = null)
  {
      $this->msg_lang  =  $val;
  }
  /**
  * Gets Message Language
  * @return Message Language
  */
  function getMsgLang()
  {
      return $this->msg_lang;
  }
  
  /**
  * Sets Message Hint
  * @param Message Hint
  * @return none
  */
  function setMsgDevHint($val = null)
  {
      $this->msg_dev_hint  =  $val;
  }
  /**
  * Gets Message Hint
  * @return Message hint
  */
  function getMsgDevHint()
  {
      return $this->msg_dev_hint;
  }

  /**
  * Loads a applicationMessage
  *
  * @param msg_code of applicationMessage
  * @return none
  *
  */
  function loadApplicationMessage($msg_code = null)
  {

    if(!$msg_code){ $this->loaded = false; return false; }
    
    //Array for DB query
    $info  =  array();
    
    $info['table']  =  $this->entity_table;
    $info['where']  =  "msg_code  =  $msg_code";
    $info['debug']  =  false;
  
    $rowObject  =  select($info);   
    
    $rowObject[0];
    
    if (count($rowObject[0]))
    {
        foreach($rowObject[0] as $fieldName  => $fieldValue)
        {
           $this->$fieldName  =  $fieldValue;
        }
    }
    $this->loaded = true;
  }

  /**
  * Adds a applicationMessage
  *
  * @param attributes array
  * @return returns false if failed, else new applicationMessage ID.
  *
  */
  function addApplicationMessage($attributes  =  null)
  {
		if(!$attributes)
			$attributes  =  $this->getAttributes();
		
		$info  =  array();
    $info['table']  =  $this->entity_table;
    $info['data']   =  $attributes;
    $info['debug']  =  false;
    
    // Do insert
    $ret  =  insert($info);

    // See if insert was a sussess
    if ($ret['affected_rows'] > 0)
    {
      return $msg_code;
    }    
    return false;
  }
  
  /**
  * Deletes an applicationMessage
  *
  * @param msg_code of applicationMessage
  * @return returns false if failed, else true
  *
  */
  function deleteApplicationMessage($msg_code = null)
  {

		if(!$msg_code)
		{
			if($this->documentLoaded)
					$msg_code  =  $this->msg_code;
			else
				return false;
		}

    $info  =  array();
    
    $info['table']  =  $this->entity_table;
    $info['where']  =  "msg_code  =  $msg_code";
    $info['debug']  =  false;
    
    //Delete record
    return delete($info);
  }

  /**
  * Modifies a applicationMessage
  *
  * @param msg_code of applicationMessage
  * @return returns false if failed, else true
  *
  */
  function modifyApplicationMessage($msg_code = null, $attributes = null)
  {
		if(!$attributes)
			$attributes  =  $this->getAttributes();

    if(!$attributes)
      $attributes  =  getUserDataSet($this->entity_table);

    $info  =  array();
    
    $info['table']  =  $this->entity_table;
    $info['data']   =  $attributes;
    $info['where']  =  "msg_code = $msg_code";
    $info['debug']  =  false;
    
    //Update and return result
    return update($info);       
  }
}
?>