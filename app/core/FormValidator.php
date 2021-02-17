<?php
/**
 * The formvalidator class.
 * Assists the validation of forms.
 *
 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class FormValidator{
    public static $controller;
    /* Class constructor */
    public function __construct(Controller $controller)
    {
        self::$controller = $controller;
    }

    /*******************************************************************
    ** validates addresses
    ********************************************************************/
    public static function validateAddress($address, $suburb, $state, $postcode, $country, $ignore_address_error, $prefix = "", $session_var = false)
    {
        if( !self::dataSubbed($address) )
        {
            if($session_var)
            {
                Session::set($session_var, true);
            }
            Form::setError($prefix.'address', 'An address is required');
        }
        elseif( !$ignore_address_error )
        {
            if( (!preg_match("/(?:[A-Za-z].*?\d|\d.*?[A-Za-z])/i", $address)) && (!preg_match("/(?:care of)|(c\/o)|( co )/i", $address)) )
            {
                if($session_var)
                {
                    Session::set($session_var, true);
                }
                Form::setError($prefix.'address', 'The address must include both letters and numbers');
            }
        }
        if(!self::dataSubbed($postcode))
        {
            if($session_var)
            {
                Session::set($session_var, true);
            }
            Form::setError($prefix.'postcode', "A delivery postcode is required");
        }
        if(!self::dataSubbed($country))
        {
            if($session_var)
            {
                Session::set($session_var, true);
            }
            Form::setError($prefix.'country', "A delivery country is required");
        }
        elseif(strlen($country) > 2)
        {
            if($session_var)
            {
                Session::set($session_var, true);
            }
            Form::setError($prefix.'country', "Please use the two letter ISO code");
        }
        elseif($country == "AU")
        {
            if(!self::dataSubbed($suburb))
    		{
    		    if($session_var)
                {
                    Session::set($session_var, true);
                }
    			Form::setError($prefix.'suburb', "A delivery suburb is required for Australian addresses");
    		}
    		if(!self::dataSubbed($state))
    		{
    		    if($session_var)
                {
                    Session::set($session_var, true);
                }
    			Form::setError($prefix.'state', "A delivery state is required for Australian addresses");
    		}
            $aResponse = self::$controller->Eparcel->ValidateSuburb($suburb, $state, str_pad($postcode,4,'0',STR_PAD_LEFT));
            $error_string = "";
            if(isset($aResponse['errors']))
            {
                foreach($aResponse['errors'] as $e)
                {
                    $error_string .= $e['message']." ";
                }
            }
            elseif($aResponse['found'] === false)
            {
                $error_string .= "Postcode does not match suburb or state";
            }
            if(strlen($error_string))
            {
                if($session_var)
                {
                    Session::set($session_var, true);
                }
                Form::setError($prefix.'postcode', $error_string);
            }
        }
    }

    /*******************************************************************
    ** validates empty data fields
    ********************************************************************/
    public static function dataSubbed($data)
    {
        if(!$data || strlen($data = trim($data)) == 0)
        {
            return false;
        }
        else
        {
            return true;
        }
    }//end dataSubbed()

    /*******************************************************************
   ** validates email addresses
   ********************************************************************/
    public static function emailValid($email)
    {
        if(!$email || strlen($email = trim($email)) == 0)
        {
         	return false;
      	}
      	else
        {
            return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
        	 /* Check if valid email address
         	$regex = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i";
         	if(!preg_match($regex,$email))
            {
            	return false;
         	}
         	else
            {
                return true;
            }
            */
      	}
    }//end emailValid()
}
?>