<?php

/**
 * Utility class.
 *
 * Provides methods for manipulating and extracting data from arrays.
 * Also provides various helper functions

 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */
class Utility{

    private function __construct(){}

    /**
     * Normalizes an array, and converts it to a standard format.
     *
     * @param  array $arr
     * @return array normalized array
     */
    public static function normalize($arr){

        $keys = array_keys($arr);
        $count = count($keys);

        $newArr = [];
        for ($i = 0; $i < $count; $i++) {
            if (is_int($keys[$i])) {
                $newArr[$arr[$keys[$i]]] = null;
            } else {
                $newArr[$keys[$i]] = $arr[$keys[$i]];
            }
        }
        return $newArr;
    }

    /**
     * Recursive in_array function
     *
     * in_array function for  through multidimansional arrays
     * @param mixed $needle
     * @param array $haystack
     * @param boolean strict
     * @returns boolean in needle is found in haystack
     */

    public static function in_array_r($needle, $haystack, $strict = false)
    {
        foreach ($haystack as $index => $item)
        {
            if( ! $strict && is_string( $needle ) && ( is_float( $item ) || is_int( $item ) ) )
            {
                $item = (string)$item;
            }
            if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && self::in_array_r($needle, $item, $strict)))
            {
                return $index;
            }
        }
        return false;
    }

    /**
     * returns a string by separating array elements with commas
     *
     * @param  array $arr
     * @return array
     */
    public static function commas($arr){
        return implode(",", (array)$arr);
    }

    /**
     * Merging two arrays
     *
     * @param  mixed   $arr1
     * @param  mixed   $arr2
     * @return array   The merged array
     *
     */
    public static function merge($arr1, $arr2){
        return array_merge((array)$arr1, (array)$arr2);
    }

    public static function toCamelCase($str, $capitalise_first_char = false)
    {
        /*
         * This will take any dash or underscore turn it into a space, run ucwords against
         * it so it capitalizes the first letter in all words separated by a space then it
         * turns and deletes all spaces.
         */
        if($capitalise_first_char)
        {
            return str_replace(' ', '', ucwords(preg_replace('/[^a-zA-Z0-9]+/', ' ', $str))); 
        }
        else
        {
            return lcfirst(str_replace(' ', '', ucwords(preg_replace('/[^a-zA-Z0-9]+/', ' ', $str))));
        }

    }

    public static function getStateSelect($selected = false)
    {
        $return_string = "";
        $options = array("ACT","NSW","NT","QLD","SA","TAS","VIC","WA");
        foreach($options as $state)
        {
        	$return_string .= "<option ";
        	if($selected && $selected == $state)
        	{
        		$return_string .= "selected='selected' ";
        	}
        	$return_string .= ">$state</option>";
        }
        return $return_string;
    }

    public static function randomNumber($length = 6)
    {
        $result = mt_rand(1, 9);
        for($i = 1; $i < $length; $i++) {
            $result .= mt_rand(0, 9);
        }

        return $result;
    }

    public static function generateRandString($length = 8)
	{
		$randstr = "";
      	for($i=0; $i<$length; $i++)
		{
         	$randnum = mt_rand(0,61);
         	if($randnum < 10)
			{
            	$randstr .= chr($randnum+48);
         	}
			else if($randnum < 36)
			{
            	$randstr .= chr($randnum+55);
         	}
			else
			{
            	$randstr .= chr($randnum+61);
         	}
      	}
      	return $randstr;
	}

    public static function ean13_check_digit($digits)
    {
        //first change digits to a string so that we can access individual numbers
        $digits =(string)$digits;
        // 1. Add the values of the digits in the even-numbered positions: 2, 4, 6, etc.
        $even_sum = $digits{1} + $digits{3} + $digits{5} + $digits{7} + $digits{9} + $digits{11};
        // 2. Multiply this result by 3.
        $even_sum_three = $even_sum * 3;
        // 3. Add the values of the digits in the odd-numbered positions: 1, 3, 5, etc.
        $odd_sum = $digits{0} + $digits{2} + $digits{4} + $digits{6} + $digits{8} + $digits{10};
        // 4. Sum the results of steps 2 and 3.
        $total_sum = $even_sum_three + $odd_sum;
        // 5. The check character is the smallest number which, when added to the result in step 4,  produces a multiple of 10.
        $next_ten = (ceil($total_sum/10))*10;
        $check_digit = $next_ten - $total_sum;
        return $digits . $check_digit;
    }

    public static function validate_EAN13Barcode($barcode)
    {
        // check to see if barcode is 13 digits long
        if (!preg_match("/^[0-9]{13}$/", $barcode))
        {
            return false;
        }
        $digits = $barcode;
        // 1. Add the values of the digits in the
        // even-numbered positions: 2, 4, 6, etc.
        $even_sum = $digits[1] + $digits[3] + $digits[5] +
                    $digits[7] + $digits[9] + $digits[11];
        // 2. Multiply this result by 3.
        $even_sum_three = $even_sum * 3;
        // 3. Add the values of the digits in the
        // odd-numbered positions: 1, 3, 5, etc.
        $odd_sum = $digits[0] + $digits[2] + $digits[4] +
                   $digits[6] + $digits[8] + $digits[10];
        // 4. Sum the results of steps 2 and 3.
        $total_sum = $even_sum_three + $odd_sum;
        // 5. The check character is the smallest number which,
        // when added to the result in step 4, produces a multiple of 10.
        $next_ten = (ceil($total_sum / 10)) * 10;
        $check_digit = $next_ten - $total_sum;
        // if the check digit and the last digit of the
        // barcode are OK return true;
        if ($check_digit == $digits[12])
        {
            return true;
        }

        return false;
    }

    public static function code39_check_digit($code)
    {
        $count = 0;
        $bits = str_split($code);
        foreach ($bits as $char) {
            $count += $this->code39_chars[$char];
        }

        $mod = $count % 43;

        $check_digit = array_search($mod, $this->code39_chars);

        return $code.$check_digit;
    }

    public static function validate_code39($value)
    {
        $checksum = substr($value, -1, 1);
        $value    = str_split(substr($value, 0, -1));
        $count    = 0;
        foreach ($value as $char) {
            $count += $this->code39_chars[$char];;
        }
        $mod = $count % 43;
        if ($mod == $this->code39_chars[$checksum]) {
            return true;
        }
        return false;
    }

    public static function formatAddressWeb(array $address)
    {
        $ret_string = $address['address'];
        if(!empty($address['address_2'])) $ret_string .= "<br/>".$address['address_2'];
        $ret_string .= "<br/>".$address['suburb'];
        $ret_string .= "<br/>".$address['state'];
        $ret_string .= "<br/>".$address['country'];
        $ret_string .= "<br/>".$address['postcode'];

        return $ret_string;
    }

    public static function getBBCharge( $country, $state, $num_items, $express )
    {
        $db = Database::openConnection();
        if($country == "AU")
        {
            if($express)
            {
                /*
                if($num_items == 1) return 9.7;
                elseif($num_items < 4)  return 10.50;
                elseif($num_items < 7) return 12.20;
                elseif($num_items < 13) return 15.6;
                else  return 20.8;
                */
                if(strtoupper($state) == "VIC")
                {
                    if($num_items == 1) return 8.75;
                    else  return 8.99;
                }
                else
                {
                    if($num_items == 1) return 10.25;
                    elseif($num_items < 4)  return 15.06;
                    else  return 19.55;
                }
            }
            else
            {
                if($num_items == 1) return 8.25;
                elseif($num_items < 4)  return 9;
                elseif($num_items < 7) return 9.9;
                elseif($num_items < 12) return 10.45;
                else  return 13;
            }
        }
        else
        {
            $zone = $db->queryValue('countries', array('iso_code_2' => $country), 'dhl_zone');
            if($zone == 1)
            {
                if($num_items == 1) return 10.73;
                elseif($num_items == 2) return 11.73;
                elseif($num_items == 3) return 13.08;
                elseif($num_items == 4) return 13.9;
                elseif($num_items == 5) return 15.53;
                elseif($num_items == 6) return 15.53;
                elseif($num_items == 7) return 15.53;
                elseif($num_items == 8) return 20.62;
                elseif($num_items == 9) return 21.33;
                elseif($num_items == 10) return 21.97;
                elseif($num_items == 11) return 22.61;
                else return 22.93;
            }
            elseif($zone == 2)
            {
                if($num_items == 1) return 20.03;
                elseif($num_items == 2) return 21.38;
                elseif($num_items == 3) return 23.26;
                elseif($num_items == 4) return 24.44;
                elseif($num_items == 5) return 26.37;
                elseif($num_items == 6) return 27.50;
                elseif($num_items == 7) return 28.90;
                elseif($num_items == 8) return 29.98;
                elseif($num_items == 9) return 31.16;
                elseif($num_items == 10) return 32.23;
                elseif($num_items == 11) return 33.85;
                else return 34.38;
            }
            elseif($zone == 3)
            {
                if($num_items == 1) return 12.00;
                elseif($num_items == 2) return 14.96;
                elseif($num_items == 3) return 18.91;
                elseif($num_items == 4) return 21.37;
                elseif($num_items == 5) return 26.68;
                elseif($num_items == 6) return 29.15;
                elseif($num_items == 7) return 32.61;
                elseif($num_items == 8) return 61.10;
                elseif($num_items == 9) return 62.49;
                elseif($num_items == 10) return 63.75;
                elseif($num_items == 11) return 65.01;
                else return 65.64;
            }
            elseif($zone == 4)
            {
                if($num_items == 1) return 12.00;
                elseif($num_items == 2) return 14.95;
                elseif($num_items == 3) return 18.94;
                elseif($num_items == 4) return 21.43;
                elseif($num_items == 5) return 26.79;
                elseif($num_items == 6) return 29.28;
                elseif($num_items == 7) return 32.77;
                elseif($num_items == 8) return 55.14;
                elseif($num_items == 9) return 56.99;
                elseif($num_items == 10) return 58.68;
                elseif($num_items == 11) return 60.36;
                else return 61.21;
            }
            else
            {
                if($num_items == 1) return 27.21;
                elseif($num_items == 2) return 28.75;
                elseif($num_items == 3) return 30.90;
                elseif($num_items == 4) return 32.26;
                elseif($num_items == 5) return 34.47;
                elseif($num_items == 6) return 35.76;
                elseif($num_items == 7) return 37.36;
                elseif($num_items == 8) return 38.59;
                elseif($num_items == 9) return 39.94;
                elseif($num_items == 10) return 41.17;
                elseif($num_items == 11) return 43.02;
                else return 43.63;
            }
        }
    }

    public static function convertObjectToArray($data)
    {
        if (is_object($data))
        {
            $data = get_object_vars($data);
        }
        if (is_array($data))
        {
            return array_map(__METHOD__, $data);
        }
        else
        {
            return $data;
        }
    }

    public static function deepTrim($string)
    {
        return trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $string)));
    }

    public static function formatMobileString($string, $nz = false)
    {
        //echo "<pre>",var_dump($nz),"</pre>$string";
        if($nz)
        {
            if(  preg_match( '/^(\d{2})(\d{2,4})(\d{3})(\d{3})/', $string,  $matches ) )
            {
                return "+".$matches[1] . ' ' .$matches[2] . ' ' . $matches[3] . ' '. $matches[4];
            }
            else
            {
                return false;
            }
        }
        else
        {
            if(  preg_match( '/^(\d{2})(\d{3})(\d{3})(\d{3})/', $string,  $matches ) )
            {
                return "+".$matches[1] . ' ' .$matches[2] . ' ' . $matches[3] . ' '. $matches[4];
            }
            else
            {
                return false;
            }
        }
    }

    public static function formatPhoneString($string, $nz = false)
    {
        if($nz)
        {
            if(  preg_match( '/^(\d{2})(\d{1})(\d{3})(\d{4})$/', $string,  $matches ) )
            {
                return "+".$matches[1] . ' ' .$matches[2] . ' ' . $matches[3] . ' '. $matches[4];
            }
            else
            {
                return false;
            }
        }
        else
        {
            if(  preg_match( '/^(\d{2})(\d{1})(\d{4})(\d{4})$/', $string,  $matches ) )
            {
                return "+".$matches[1] . ' ' .$matches[2] . ' ' . $matches[3] . ' '. $matches[4];
            }
            else
            {
                return false;
            }
        }
    }

    public static function streetAbbreviations($string)
    {
        $str = strtolower($string);
        $list = array(
            'ave'  => 'avenue',
            'blvd' => 'boulevard',
            'cir'  => 'circle',
            'cct'   => 'circuit',
            'cl'    => 'close',
            'cres'  => 'crescent',
            'crt'   => 'court',
            'dr'    => 'drive',
            'expy' => 'expressway',
            'fwy'  => 'freeway',
            'hwy'   => 'highway',
            'ln'   => 'lane',
            'pde'   => 'parade',
            'pky'  => 'parkway',
            'pl'    => 'place',
            'rd'   => 'road',
            'sq'   => 'square',
            'st'   => 'street',
            'tpke' => 'turnpike',
        );
        return ucwords(str_ireplace(array_values($list), array_keys($list), $str));
    }
 }
