<?php

/**
 * Utility class.
 *
 * Provides methods for manipulating and extracting data from arrays.
 * Also provides various helper functions

 * @author     Mark Solly <mark.solly@fsg.com.au>
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

    public static function formatAddressCSV(array $address)
    {
        $ret_string = self::formatAddressWeb($address);
        $ret_string = str_replace("<br/>", ", ",$ret_string);
        return $ret_string;
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

    public static function splitCamelCase($input)
    {
        return preg_split(
            '/(^[^A-Z]+|[A-Z][^A-Z]+)/',
            $input,
            -1, /* no limit for replacement count */
            PREG_SPLIT_NO_EMPTY /*don't return empty elements*/
                | PREG_SPLIT_DELIM_CAPTURE /*don't strip anything from output array*/
        );
    }
 }
