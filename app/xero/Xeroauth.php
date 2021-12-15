<?php
/**
 * The authorization class.
 * Keeps track of tokens and authorization for access to the xero API
 *
 * @author     Mark Solly <mark.solly@fsg.com.au>
 */
//use Calcinai\OAuth2\Client\Provider\Xero;
//use XeroPHP\Application;
// Use this class to deserialize error caught
use XeroAPI\XeroPHP\AccountingObjectSerializer;
use XeroAPI\XeroPHP\PayrollAuObjectSerializer;

class Xeroauth{

    public $provider;
    public $token_details;
    public $xero_app;

    private $table = "xero_authorization";


    /* Class constructor */
    public function __construct(){
        $db = Database::openConnection();
        $this->token_details = $db->queryByID($this->table, 1);
        if($this->tokenExpired())
        {
            //Gotta get a new one
            //create the provider object
            //$this->provider = new Xero([
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
            ), 1);
            //update details with new token
            $this->token_details = $db->queryByID($this->table, 1);
        }
        $config = XeroAPI\XeroPHP\Configuration::getDefaultConfiguration()->setAccessToken( (string)$this->token_details['token'] );
        $this->xero_app = new XeroAPI\XeroPHP\Api\AccountingApi(
            new GuzzleHttp\Client(),
            $config
        );

        /*
        $this->xero_app = new \XeroPHP\Application(
            $this->token_details['token'],
            $this->token_details['tenant_id']
        );
        */
    }

    public function getOrganisation()
    {
        return $this->xero_app->load(\XeroPHP\Models\Accounting\Organisation::class)->execute();
    }

    public function getContacts()
    {
        return $this->xero_app->load(\XeroPHP\Models\Accounting\Contact::class)->orderBy('name', 'asc')->execute();
    }

    public function getInvoices($page = 1)
    {
        /*
        $startDateString = date('Y, m, d');
        $endDateString = date('Y, m, d', strtotime('-28 days'));

        //$invoices = $this->xero_app->load('Accounting\\Invoice')
        //$invoices = $this->xero_app->load(Invoice::class)

        //$invoices = $this->xero_auth->load(Invoice::class)->page(1)->execute();


        $invoices = $this->xero_app->load(\XeroPHP\Models\Accounting\Invoice::class)
            ->orderBy("Date", "DESC")
            ->where(sprintf('Date >= DateTime(%s) && Date < DateTime(%s)', $endDateString, $startDateString))
            ->where('Status', \XeroPHP\Models\Accounting\Invoice::INVOICE_STATUS_AUTHORISED)
            ->page($page)
            ->execute();

        return $invoices;
        */
        $xeroTenantId = $this->token_details['tenant_id'] ;
        $ifModifiedSince = new DateTime("2021-12-02");
        $where = "Type=" . \XeroAPI\XeroPHP\Models\Accounting\Invoice::TYPE_ACCPAY . "";
        $statuses = array("PAID");
        $order = "Date DESC";
        $page = 1;
        $unitdp = 4;

        try {
            return $this->xero_app->getInvoices($xeroTenantId, $ifModifiedSince, $where, $order, NULL, NULL, NULL, $statuses, $page, false, false, $unitdp, false);
        } catch (Exception $e) {
            echo 'Exception: ', $e->getMessage(), PHP_EOL;
            die();
        }
    }

    public function getInvoicePDF($invoice_id)
    {
        $xeroTenantId = $this->token_details['tenant_id'] ;
        try {
            return $this->xero_app->getInvoiceAsPdf($xeroTenantId, $invoice_id);
        } catch (Exception $e) {
            echo 'Exception when calling AccountingApi->getInvoiceAsPdf: ', $e->getMessage(), PHP_EOL;
        }
    }

    private function tokenExpired()
    {
        return(time() > $this->token_details['expires']);
    }
}