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
        $this->output = "=========================================================================================================".PHP_EOL;
        $this->output .= "Nuchev MARKETPLACER ORDER IMPORTING FOR ".date("jS M Y (D), g:i a (T)").PHP_EOL;
        $this->output .= "=========================================================================================================".PHP_EOL;

        $endpoint = "/invoices?since=2021-12-01T00:00:00Z&status=paid&include=line_items,customer";
        $collected_orders = $this->sendGetRequest($endpoint, $this->config);
        if($orders = $this->procOrders($collected_orders['data']))
        {
            $this->addNuchevOrders($orders);
        }
        die('procOrders failed');
    }

    private function addNuchevOrders($orders)
    {
        $nuchevoitems = $this->controller->allocations->createOrderItemsArray($orders['orders_items']);
        unset($orders['orders_items']);
        echo "<pre>",print_r($nuchevoitems),"</pre>";die();
    }
}//end class
?>