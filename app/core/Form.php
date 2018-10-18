<?php

/**
 * The form class.
 * Assists the management of forms.
 *
 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */

class Form
{
    public static $values = array();    //Holds submitted form field values
    public static $errors = array();    //Holds submitted form error messages
    public static $num_errors;          //The number of errors in submitted form

    /* Class constructor */
    private function __construct(){}

    public static function init()
    {
        if(isset($_SESSION['value_array']) && isset($_SESSION['error_array']))
        {
            self::$values = Session::getAndDestroy('value_array');
            self::$errors = Session::getAndDestroy('error_array');
            self::$num_errors = count(self::$errors);
        }
        else
        {
            self::$num_errors = 0;
        }
    }

   /**
    * setValue - Records the value typed into the given form field by the user.
    * @field: string    (field attribute name)
    * @value: mixed     (field value)
    * @returns null
    */
   public static function setValue($field, $value)
   {
      self::$values[$field] = $value;
   }

   /**
    * setError - Records new form error given the form field name and the error message attached to it.
    * @field: string    (field attribute name)
    * @errmsg: string   (message to attach)
    * @returns null
    */
   public static function setError($field, $errmsg)
   {
      self::$errors[$field] = $errmsg;
      self::$num_errors = count(self::$errors);
   }

   /**
    * value - Returns the value attached to the given field.
    * @field: string   (field attribute name)
    * @returns string
    */
   public static function value($field)
   {
      if(array_key_exists($field,self::$values))
      {
      	if(is_array(self::$values[$field]))
        {
            return self::$values[$field];
        }
         return htmlspecialchars(stripslashes(self::$values[$field]));
      }
      else
      {
         return "";
      }
   }

   /**
    * error - Returns the error message attached to the given field.
    * @field: string   (field attribute name)
    * @returns string
    */
   public static function error($field)
   {
      if(array_key_exists($field,self::$errors))
      {
         return self::$errors[$field];
      }
      else
      {
         return "";
      }
   }

   /* getErrorArray - Returns the array of error messages */
   public static function getErrorArray()
   {
      return self::$errors;
   }

   public static function displayError($field)
   {
    	if(!empty(self::error($field)))
    	{
            return "<p class=\"text-danger\"><em>".self::error($field)."</em></p>";;
    	}
    	else
    	{
    		return '';
    	}
   }
}
?>