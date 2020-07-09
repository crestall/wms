<?php
/**
 * Nuchev Location for the Eparcel class.
 *
 *
 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */

class NuchevEparcel extends Eparcel
{
    private $client_id = 5;
    //const    API_BASE_URL = '/testbed/shipping/v1/';
	public function init()
	{
    	$cd = $this->controller->client->getClientInfo($this->client_id);

        if(!empty($cd['api_key']))
        {
            $this->API_KEY = $cd['api_key'];
            $this->API_PWD = $cd['api_secret'];
            $this->ACCOUNT_NO = str_pad($cd['charge_account'], 10, '0', STR_PAD_LEFT);
        }

        $from_address = Config::get("FSG_ADDRESS");
        $this->from_address_array = array(
            'name'      =>  'OLI6 Baby Products (via FSG printing and 3PL)',
            'lines'		=>	array($from_address['address']),
            'suburb'	=>	$from_address['suburb'],
            'postcode'	=>	$from_address['postcode'],
            'state'		=>	$from_address['state'],
            'country'	=>  $from_address['country']
        );
	}
}//end class

?>
