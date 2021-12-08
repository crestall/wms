<?php
/**
 * Marketplacer class.
 *
 * Interacts with the marketplacer api
 * Individual marketplacer accounts are handled in the extending classes

 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class Marketplacer{

    protected $output;
    protected $return_array = array(
        'import_count'          => 0,
        'imported_orders'       => array(),
        'error_orders'          => array(),
        'import_error'          => false,
        'error'                 => false,
        'error_count'           => 0,
        'error_string'          => '',
        'import_error_string'   => ''
    );
    protected $ua;
    protected $order_items;

    public $controller;

    public function __construct(Controller $controller)
    {
        $this->controller = $controller;
    }

    protected function sendGetRequest($endpoint,$options)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $options['ShopUrl'].$endpoint);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'MARKETPLACER-API-KEY: '.$options['ApiKey'],
            'Authorization: Basic '. base64_encode($options['Username'].":".$options['Password']))
        );
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response);
    }

    protected function sendPostRequest($endpoint,$options)
    {
        $post_options = [
            CURLOPT_URL               => $options['ShopUrl'].$endpoint,
            CURLOPT_CUSTOMREQUEST    => 'POST',
            CURLOPT_HTTPHEADER        => array(
                'MARKETPLACER-API-KEY: '.$options['ApiKey'],
                'Authorization: Basic '. base64_encode($options['Username'].":".$options['Password'])
            )
        ];
        $c_options = array_merge($this->curl_options, $post_options);
        $curl = curl_init();
        curl_setopt_array($curl, $c_options);
        $response = curl_exec($curl);
        curl_close($curl);
        var_dump($response);
        die();
    }
}// end class
?>