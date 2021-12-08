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

    private $curl_options = [
        CURLOPT_RETURNTRANSFER    => true,
        CURLOPT_ENCODING          => '',
        CURLOPT_MAXREDIRS         => 10,
        CURLOPT_TIMEOUT           => 0,
        CURLOPT_FOLLOWLOCATION    => true,
        CURLOPT_HTTP_VERSION      => CURL_HTTP_VERSION_1_1,
    ];

    public $controller;

    public function __construct(Controller $controller)
    {
        $this->controller = $controller;
    }

    protected function sendGetRequest($endpoint,$options)
    {
        $get_options = [
            CURLOPT_URL               => $options['ShopUrl'].$endpoint,
            CURLOPT_CUSTOMREQUEST    => 'GET',
            CURLOPT_HTTPHEADER        => array(
                'MARKETPLACER-API-KEY: '.$options['ApiKey'],
                'Authorization: Basic '. base64_encode($options['Username'].":".$options['Password'])
            )
        ];
        $c_options = array_merge($this->curl_options, $get_options);
        $curl = curl_init();
        curl_setopt_array($curl, $c_options);
        $response = curl_exec($curl);
        curl_close($curl);
        var_dump($response);
        die();
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