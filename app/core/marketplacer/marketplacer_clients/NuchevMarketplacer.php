<?php
/**
 * Nuchev location for the marketplacer class.
 *
 *
 * @author     Mark Solly <mark.solly@fsg.com.au>
 */
class NuchevMarketplacer extends Marketplacer{

    private $client_id = 5;
    private $from_address_array = array();
    private $config = array();

    public function __construct($controller)
    {
        parent::__construct($controller);
        $this->ua = (isset($this->controller->request->params['args']['ua']))?$this->controller->request->params['args']['ua']:"FSG";
        $this->config = array(
            'ShopUrl'        => 'https://woolworths.marketplacer.com/api/v2/client/',
            'ApiKey'         => Config::get('NUCHEVMARKETPLACERAPIKEY'),
            'Username'       => Config::get('NUCHEVMARKETPLACERUSERNAME'),
            'Password'       => Config::get('NUCHEVMARKETPLACERPASSWORD')
        );

        //echo "<pre>",print_r($this->config),"</pre>";die();

        $from_address = Config::get("FSG_ADDRESS");
        $this->from_address_array = array(
            'name'      =>  'Nuchev - OLI6 (via FSG 3PL)',
            'lines'		=>	array($from_address['address']),
            'suburb'	=>	$from_address['suburb'],
            'postcode'	=>	$from_address['postcode'],
            'state'		=>	$from_address['state'],
            'country'	=>  $from_address['country']
        );
    }

    public function getOrders()
    {
        $endpoint = "/invoices?since=2015-11-09T00:00:00Z&status=paid&include=line_items,customer";
        $collected_orders = $this->sendGetRequest($endpoint, $this->config);
        return $collected_orders;
    }
}//end class
?>