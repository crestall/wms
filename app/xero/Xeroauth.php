<?php
/**
 * The authorization class.
 * Keeps track of tokens and authorization for access to the xero API
 *
 * @author     Mark Solly <mark.solly@fsg.com.au>
 */
use Calcinai\OAuth2\Client\Provider\Xero;
use XeroPHP\Application;

class Xeroauth{

    public $provider;
    public $token_details;
    public $xero_app;

    private $table = "xero_authorization";


    /* Class constructor */
    public function __construct(){
        $db = Database::openConnection();
        try{
             $this->provider = new Xero([
                'clientId'      => Config::get('PBAXEROCLIENTID'),
                'clientSecret'  => Config::get('PBAXEROCLIENTSECRET'),
                'redirectUri'   => Config::get('PBAXEROREDIRECTURL'),
            ]);
        } catch (ForbiddenException $e){
           var_dump($e);
        };
        $this->token_details = $db->queryByID($this->table, 1);
        //echo "<pre>",print_r($this->token_details),"</pre>";die();
        //die('refresh token: '.$this->token_details['expires']);
        if($this->tokenExpired())
        {
            //Gotta get a new one
            //die('expired token');
            $newAccessToken = $this->provider->getAccessToken('refresh_token', [
                'refresh_token' => $this->token_details['refresh_token']
            ]);
            // Save my new token, expiration and refresh token
            $db->updateDatabaseFields($this->table, array(
                'token' => $newAccessToken->getToken(),
                'refresh_token' => $newAccessToken->getRefreshToken(),
                'expires'       => $newAccessToken->getExpires()
            ), 1);
            //update details with new token
            $this->token_details = $db->queryByID($this->table, 1);
        }

        $this->xero_app = new \XeroPHP\Application(
            $this->token_details['token'],
            $this->token_details['tenant_id']
        );
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
        $startDateString = date('Y, m, d');
        $endDateString = date('Y, m, d', strtotime('-28 days'));

        //$invoices = $this->xero_app->load('Accounting\\Invoice')
        $invoices = $this->xero_app->load(Invoice::class)
            ->orderBy("Date", "DESC")
            ->where(sprintf('Date >= DateTime(%s) && Date < DateTime(%s)', $endDateString, $startDateString))
            ->where('Status', \XeroPHP\Models\Accounting\Invoice::INVOICE_STATUS_AUTHORISED)
            ->page($page)
            ->execute();

        return $invoices;
    }

    private function tokenExpired()
    {
        //die( time()." < ".$this->token_details['expires']);
        return(time() > $this->token_details['expires']);
    }
}