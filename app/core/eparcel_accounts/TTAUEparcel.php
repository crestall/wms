<?php
/**
 * Twin Towers Location for the Eparcel class.
 *
 *
 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */

class TTAUEparcel extends Eparcel
{
    private $client_id = 66;
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
	}

    public function getShipmentDetails($od, $items, $use_express = false)
    {
        $express = ($od['eparcel_express'] == 1);

        if(!$express)
        {
            $express = $use_express;
        }
        $order_id = $od['id'];
        $ad = array(
            'address'   =>  $od['address'],
            'address_2' =>  $od['address_2'],
            'state'     =>  $od['state'],
            'suburb'    =>  $od['suburb'],
            'postcode'  =>  $od['postcode'],
            'country'   =>  $od['country'],
            'phone'     =>  $od['contact_phone']
        );
        //$items = $this->controller->order->getItemsForOrder($order_id);
        $delivery_instructions = (!empty($od['instructions']))? $od['instructions'] : "Please leave in a safe place out of the weather";
        if(empty($od['ref_1']))
        {
            $ref_1 = strtoupper(str_replace(" ", "", $this->controller->client->getClientName($od['client_id'])));
        }
        else
        {
            $ref_1 = $od['ref_1'];
        }
        if($od['signature_req'] == 1)
            $delivery_instructions = (!empty($od['instructions']))? $od['instructions'] : "";
        $shipment = array(
            'shipment_reference'		=> 	$order_id,
            'email_tracking_enabled'	=>	!is_null($od['tracking_email']),
            'from'						=>	array(),
            'to'						=>	array(),
            'items'						=>	array(),
            "sender_references"			=>	array($ref_1, $od['order_number']),

        );
        $shipment['to'] = array(
    		'name'	   				=>	$od['ship_to'],
    		'lines'					=>	array(),
    		'suburb'				=>	trim($od['suburb']),
    		'postcode'				=>	trim($od['postcode']),
    		'state'					=>	trim($od['state']),
            'country'				=>	trim($od['country']),
            'delivery_instructions'	=>	$delivery_instructions
    	);
        if(!empty($od['company_name'])) $shipment['to']['business_name'] = $od['company_name'];
        if(!empty($od['tracking_email'])) $shipment['to']['email'] = $od['tracking_email'];
        if(!empty($od['contact_phone'])) $shipment['to']['phone'] = $od['contact_phone'];
        $shipment['to']['lines'][] = $od['address'];
        if(!empty($od['address_2'])) $shipment['to']['lines'][] = $od['address_2'];
        $threepl_address = Config::get("THREEPL_ADDRESS");
        $shipment['from'] = array(
            'name'      =>  '3PLPLUS',
            'lines'		=>	array($threepl_address['address']),
            'suburb'	=>	$threepl_address['suburb'],
            'postcode'	=>	$threepl_address['postcode'],
            'state'		=>	$threepl_address['state'],
            'country'	=>  $threepl_address['country']
        );
        $packages = $this->controller->order->getPackagesForOrder($order_id);
        $weight = 0;
        $array = array();
        if($ad['country'] == "AU")
        {
           	$array['authority_to_leave'] = ($od['signature_req'] == 0);
        }
        else
        {
            $array['commercial_value'] = false;
            $array['classification_type'] = 'GIFT';
            if($ad['country'] == "CA")
                $array['classification_type'] = 'SAMPLE';
        }
        $val = 0;
        foreach($items as $i)
        {
            $ival = ($i['price'] == 0)? $i['qty'] : $i['price'] * $i['qty'];
            if($od['client_id'] == 6 && $ad['country'] != "AU")
                    $ival = $i['qty'] * 1.81;
            $val += $ival;
        }
        $parcels = Packaging::getPackingForOrder($od,$items,$packages, $val);
        foreach($parcels as $p)
        {
            $array['item_reference'] = $p['item_reference'];
            $array['product_id'] = $this->getEparcelChargeCode($ad, $p['weight'], $express);
            $array['width'] = $p['width'];
            $array['height'] = $p['height'];
            $array['length'] = $p['depth'];
            $array['weight'] = $p['weight'];
            $array['item_contents'] = array();
            if($ad['country'] != "AU")
            {
                $pval = round($val/count($parcels), 2);
                if(empty($this->controller->client->getProductsDescription($od['client_id'])))
                {
                    if(empty($items[0]['description']))
                    {
                        $description = $items[0]['name'];
                    }
                    else
                    {
                        $description = $items[0]['description'];
                    }
                }
                else
                {
                    $description = $this->controller->client->getProductsDescription($od['client_id']);
                }
                $array['item_contents'][] = array(
                    'description'   =>  $description,
                    'value'         =>  $pval,
                    'quantity'      =>  1
                );
            }
            $shipment['items'][] = $array;

        }

        return $shipment;
    }

    protected function getEparcelChargeCode($ad, $weight = 0, $expresspost = false)
    {
        //return "7E55";
        $pti8_countries = array(
            "BE",
            "CA",
            "CN",
            "HR",
            "DK",
            "EE",
            "FR",
            "DE",
            "HK",
            "HU",
            "IE",
            "IL",
            "LT",
            "MY",
            "MT",
            "NL",
            "NZ",
            "PL",
            "PT",
            "SG",
            "SI",
            "ES",
            "SE",
            "GB",
            "US"
        );
        if($ad['country'] == "AU")
        {
            if($expresspost) return '7J85';
            return '7D85';
        }
        else
        {
            //if( $weight > 22 || !in_array($ad['country'], $pti8_countries) )
            if( $weight > 22 )
            {
                return 'ECM8';
                //return 'AIR8';
            }
            else
            {
                return 'PTI7';
            }
        }
    }
}//end class

?>
