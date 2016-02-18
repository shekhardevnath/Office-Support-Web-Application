<?php

   class Entity
   {

       /**
       * Default Constructor
       * @return none
       */
       function Entity()
       {
           // Default Constructor
       }

       /**
       * Prints object
       * @return none
       */
       function dump()
       {
           dumpVar($this);
       }

       /**
       * Gets attributes
       * @param attributes, table
       * @return attributes
       */
       function getAttributes($attributes = null, $tableName = null)
       {
          if ($attributes)
               return $attributes;

          if (empty($tableName))
              $tableName = $this->entity_table;

          $attr = array();
          
          //Gets table fields specified by $tableName 
          $tableFields = getTableFields($tableName);

          foreach($tableFields as $thisField => $thisFieldConfig)
          {
               if (!empty($this->$thisField))
                   $attr[$thisField] = $this->$thisField;
          }

          return $attr;
       }

       /**
       * Search attributes
       * @param conditions, separator, table
       * @return dataset
       */
       function search($conditions = null, $separator = null, $tbl = null)
       {
           $result = array();
           if (empty($conditions))
              return $result;

           if (!is_array($conditions))
           {
               $where = $conditions;
           }
           else
           {
               if (empty($separator))
                   $separator = "OR";

               foreach($conditions as $fieldname=>$val)
               {
                   if (is_numeric($fieldname))
                   {
                       $ret[] = $val;
                   }
                   else if (!is_array($val))
                   {
                       $ret[] = "$fieldname = ".q($val);
                   }
                   else
                   {
                       list($val, $op) = $val;
                       $ret[] = "$fieldname $op ".q($val);
                   }
               }
               $where = implode(" $separator ", $ret);
           }

           if (empty($tbl))
              $tbl = $this->entity_table;

           $select = array(
                           'table'  => $tbl,
                           'fields' => array('*'),
                           'where'  => $where,
                           'debug'  => false
                          );

           $selected = select($select);

           return $selected;
       }
   }

?>