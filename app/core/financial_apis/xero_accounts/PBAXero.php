<?php
/**
 * PBA Xero extension for the Xero class.
 *
 *
 * @author     Mark Solly <mark.solly@fsg.com.au>
 */
class PBAXero extends Xero
{
    private $client_id = 87;
    private $from_address_array = array();
    private $pbconfig = array();
    private $provider;
    private $token_details = array();

    public $xero;

    public function __construct($controller)
    {
        //create instance of parent
        parent::__construct($controller);
        //set xero API access
        $db = Database::openConnection();
        $this->token_details = $db->queryRow("SELECT * FROM {$this->table} WHERE client_id = :client_id", ['client_id' => $this->client_id]);
        if($this->tokenExpired($this->token_details['expires']))
        {
            //Gotta get a new one
            $this->provider = new \League\OAuth2\Client\Provider\GenericProvider([
                'clientId'                => Config::get('PBAXEROCLIENTID'),
                'clientSecret'            => Config::get('PBAXEROCLIENTSECRET'),
                'redirectUri'             => Config::get('PBAXEROREDIRECTURL'),
                'urlAuthorize'            => 'https://login.xero.com/identity/connect/authorize',
                'urlAccessToken'          => 'https://identity.xero.com/connect/token',
                'urlResourceOwnerDetails' => 'https://identity.xero.com/resources'
            ]);
            $newAccessToken = $this->provider->getAccessToken('refresh_token', [
                'refresh_token' => $this->token_details['refresh_token']
            ]);
            // Save the new token, expiration and refresh token
            $db->updateDatabaseFields($this->table, array(
                'token'         => $newAccessToken->getToken(),
                'refresh_token' => $newAccessToken->getRefreshToken(),
                'expires'       => $newAccessToken->getExpires()
            ), $this->client_id, 'client_id');
            //update details with new token
            $this->token_details = $db->queryRow("SELECT * FROM {$this->table} WHERE client_id = :client_id", ['client_id' => $this->client_id]);
        }
        $config = XeroAPI\XeroPHP\Configuration::getDefaultConfiguration()->setAccessToken( (string)$this->token_details['token'] );
        $this->xero = new XeroAPI\XeroPHP\Api\AccountingApi(
            new GuzzleHttp\Client(),
            $config
        );
        //reusable parameters
        $this->ua = (isset($this->controller->request->params['args']['ua']))?$this->controller->request->params['args']['ua']:"FSG";
        //parameters for this instance
        $from_address = Config::get("FSG_ADDRESS");
        $this->from_address_array = array(
            'name'      =>  'Performance Brands Australia (via FSG 3PL)',
            'lines'		=>	array($from_address['address']),
            'suburb'	=>	$from_address['suburb'],
            'postcode'	=>	$from_address['postcode'],
            'state'		=>	$from_address['state'],
            'country'	=>  $from_address['country']
        );
    }

    public function getInvoices($page = 1)
    {
        $today = new DateTime();
        $past = $today->sub(new DateInterval("P1D"));
        $xeroTenantId = $this->token_details['tenant_id'] ;
        $ifModifiedSince = $past;
        //$where = 'Type=="' . \XeroAPI\XeroPHP\Models\Accounting\Invoice::TYPE_ACCREC . '" AND Reference!="null"';
        $where = 'Type=="' . \XeroAPI\XeroPHP\Models\Accounting\Invoice::TYPE_ACCREC . '"';
        //$iDs = ["6da5160c-347d-4eba-b487-942dd16c7d44"];
        $statuses = array("PAID");
        $order = "Date DESC";
        $page = 1;
        $unitdp = 4;

        try {
            return $this->xero->getInvoices($xeroTenantId, $ifModifiedSince, $where, $order, NULL, NULL, NULL, $statuses, $page, false, false, $unitdp, false);
        } catch (Exception $e) {
            echo 'Exception when calling AccountingApi->getInvoices: ', $e->getMessage(), PHP_EOL;
            //die();
        }
    }

    public function getInvoicePDF($invoice_id)
    {
        $xeroTenantId = $this->token_details['tenant_id'] ;
        try {
            return $this->xero->getInvoiceAsPdf($xeroTenantId, $invoice_id);
        } catch (Exception $e) {
            echo 'Exception when calling AccountingApi->getInvoiceAsPdf: ', $e->getMessage(), PHP_EOL;
        }
    }
}
?>