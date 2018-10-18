<?php

 /**
  * Address Class
  *

  * @author     Mark Solly <mark.solly@3plplus.com.au>
  */

class Address extends Model{
    /**
      * Table name for this & extending classes.
      *
      * @var string
      */
    public $table = "addresses";

    /**
     * returns an associative array holds the address info
     *
     * @access public
     * @param  integer $a_id
     * @return array Associative array of address info/data.
     * @throws Exception if $a_id is invalid.
     */
    public function getAddressInfo($a_id)
    {
        $db = Database::openConnection();
        $address = $db->queryById('addresses', $a_id);
        if(empty($address))
        {
            throw new Exception("Address ID " .  $a_id . " doesn't exists");
        }
        $address["id"]    = (int)$address["id"];
        return $address;
    }

    public function getAddressStringForOrder($id)
	{
		$db = Database::openConnection();
		$ret_string = "";
		if(!empty($id))
		{
			//$address = $db->queryRow("SELECT * FROM addresses WHERE id = $id");
            $address = $db->queryRow("SELECT address, address_2, suburb, state, postcode, country FROM orders WHERE id = $id");
			if(!empty($address))
			{
	        	$ret_string = "<p>".$address['address'];
				if(!empty($address['address_2'])) $ret_string .= "<br/>".$address['address_2'];
				$ret_string .= "<br/>".$address['suburb'];
	            $ret_string .= "<br/>".$address['state'];
				$ret_string .= "<br/>".$address['country'];
				$ret_string .= "<br/>".$address['postcode']."</p>";
			}
		}
        return $ret_string;
	}

	public function getAddressCSVStringForOrder($id)
	{
		$db = Database::openConnection();
		$ret_string = "";
		if(!empty($id))
		{
			//$address = $db->queryRow("SELECT * FROM addresses WHERE id = $id");
            $address = $db->queryRow("SELECT address, address_2, suburb, state, postcode, country FROM orders WHERE id = $id");
			if(!empty($address))
			{
	        	$ret_string = $address['address'];
				if(!empty($address['address_2'])) $ret_string .= "\n\r".$address['address_2'];
				$ret_string .= "\n\r".$address['suburb'];
	            $ret_string .= "\n\r".$address['state'];
				$ret_string .= "\n\r".$address['country'];
				$ret_string .= "\n\r".$address['postcode'];
			}
		}
        return $ret_string;
	}

	public function getAddressLabelStringForOrder($id, $label = "Address", $span = false)
	{
		$db = Database::openConnection();
		$ret_string = "";
		$element = ($span)? "span class='label'" : "label";
		if(!empty($id))
		{
			//$address = $db->queryRow("SELECT * FROM addresses WHERE id = $id");
            $address = $db->queryRow("SELECT address, address_2, suburb, state, postcode, country FROM orders WHERE id = $id");
			if(!empty($address))
			{
	        	$ret_string = "<p><$element>".$label."</$element>".$address['address'];
				if(!empty($address['address_2'])) $ret_string .= "<br/><$element class='not-phone'>&nbsp;</$element>".$address['address_2'];
				$ret_string .= "<br/><$element class='not-phone'>&nbsp;</$element>".$address['suburb'];
	            $ret_string .= "<br/><$element class='not-phone'>&nbsp;</$element>".$address['state'];
				$ret_string .= "<br/><$element class='not-phone'>&nbsp;</$element>".$address['country'];
				$ret_string .= "<br/><$element class='not-phone'>&nbsp;</$element>".$address['postcode']."</p>";
			}
		}
        return $ret_string;
	}

    public function getAutocompleteAddress($term)
    {
        $db = Database::openConnection();
        $q = strtoupper($term);
        if (!$q) return;

        $rows = $db->queryData("SELECT * FROM ".$this->table." WHERE (`address` LIKE :term OR `address_2` LIKE :term2)", array('term' => '%'.$q.'%', 'term2' => '%'.$q.'%'));
        //echo $q;
        //print_r($rows);die();
        $return_array = array();

        foreach($rows as $row)
        {
            $row_array['value']     =   $row['address'];
            $row_array['address']   =   $row['address'];
            $row_array['address_2'] =   $row['address_2'];
        	$row_array['suburb']    =   $row['suburb'];
        	$row_array['state']     =   $row['state'];
            $row_array['postcode']  =   $row['postcode'];
            $row_array['country']   =   $row['country'];
        	$row_array['label']     =   $row['address'];
            if(!empty($row['address_2']))
                $row_array['label'] .= ", ".$row['address_2'];
            $row_array['label']     .=  ", ".$row['suburb'];
            $row_array['label']     .=  ", ".$row['state'];
            $row_array['label']     .=  ", ".$row['postcode'];
            $row_array['label']     .=  ", ".$row['country'];
        	array_push($return_array,$row_array);
        }
        return $return_array;
    }
}
?>