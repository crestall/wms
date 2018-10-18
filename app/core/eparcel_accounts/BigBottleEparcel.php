<?php
/**
 * BigBottle Location for the Eparcel class.
 *
 *
 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */

class BigBottleEparcel extends Eparcel
{
    private $client_id = 6;
	public function init()
	{
    	$cd = $this->controller->client->getClientInfo($this->client_id);
        if(!empty($cd['api_key']))
        {
            $this->API_KEY = $cd['api_key'];
            $this->API_PWD = $cd['api_secret'];
            $this->ACCOUNT_NO = str_pad($cd['charge_account'], 10, '0', STR_PAD_LEFT);
        }
	}
}//end class

?>
