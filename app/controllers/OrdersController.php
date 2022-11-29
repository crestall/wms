<?php

/**
 * Orders controller
 *

 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class OrdersController extends Controller
{
    public function beforeAction()
    {
        parent::beforeAction();
        $this->Security->config("form", [ 'fields' => ['csrf_token']]);
    }

    public function index()
    {
        //set the page name for menu display
        Config::setJsConfig('curPage', 'orders-index');
        parent::displayIndex(get_class());
    }

    public function createDeliveryDocket()
    {
        if(!isset($this->request->params['args']['order']))
        {
            //no job id to update
            return (new ErrorsController())->error(400)->send();
        }
        $order_id = $this->request->params['args']['order'];
        $order = $this->order->getOrderDetail($order_id);
        if(empty($order))
        {
            //no job data found
            return (new ErrorsController())->error(404)->send();
        }
        //render the page
        Config::setJsConfig('curPage', "create-delivery-docket");
        Config::set('curPage', "create-delivery-docket");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/createDeliveryDocket.php', [
            'page_title'    => "Create Delivery Docket For Order: ".$order['order_number'],
            'pht'           => ": Create Delivery Docket",
            'order'           => $order
        ]);
    }

    public function viewBackorders()
    {
        $client_name = "All Clients";
        $client_id = 0;
        $state = "";
        if(!empty($this->request->params['args']))
        {
            if(isset($this->request->params['args']['client']))
            {
                $client_id = $this->request->params['args']['client'];
                $client_name = $this->client->getClientName($client_id);
            }
        }
        $page_title = "Backorders For $client_name";
        $orders = $this->order->getBackorders($client_id);
        //render the page
        Config::setJsConfig('curPage', "view-backorders");
        Config::set('curPage', "view-backorders");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/viewBackorders.php', [
            'page_title'        =>  $page_title,
            'pht'               =>  ":Backorders",
            'orders'            =>  $orders,
            'client_id'         =>  $client_id
        ]);
    }
    /*
    public function getQuotes()
    {
        //render the page
        Config::setJsConfig('curPage', "get-quotes");
        Config::set('curPage', "get-quotes");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/getQuotes.php', [
            'page_title'        =>  "Get Shipping Estimates",
            'pht'               =>  ":Get Shipping Estimates"
        ]);
    }
    */
    public function bookDirectFreightCollection()
    {
        //render the page
        Config::setJsConfig('curPage', "book-direct-freight-collection");
        Config::set('curPage', "book-direct-freight-collection");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/bookDF.php', [
            'page_title'        =>  "Book Direct Freight Collection",
            'pht'               =>  ": Book DF Collections"
        ]);
    }

    public function orderImporting()
    {

        //render the page
        Config::setJsConfig('curPage', "order-importing");
        Config::set('curPage', "order-importing");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/orderImporting.php', [
            'page_title'        =>  "Import Orders From External Sites",
            'pht'               =>  ": Import Orders From Other Sites",
            'nuchev_clientid'   =>  $this->client->getClientId("NUCHEV"),
            'oneplate_clientid' =>  $this->client->getClientId("One Plate"),
            'pba_clientid'      =>  $this->client->getClientId("Performance Brands Australia"),
            'bb_clientid'       =>  $this->client->getClientId("BuzzBee")
        ]);
    }

    public function importNuchevOrder()
    {
        //echo "<pre>",print_r($_POST),"</pre>";die();
        $bberror = false;
        if(!$response = $this->woocommerce->getNuchevOrder($this->request->data['nuchevwoocommerce_id']))
        {
            $bberror = true;
            $feedback = "<h2><i class='far fa-times-circle'></i>No Order ID Supplied";
            $feedback .= "<p>The order ID was not passed to the form processor correctly</p>";
        }
        else
        {
            if($response['error'])
            {
                $bberror = true;
                $feedback = "<h2><i class='far fa-times-circle'></i>No Order Found With The Supplied ID</h2>";
                $feedback .= "<p>The order you want could not be found</p>";
                $feedback .= "<p>Please recheck the ID and try again</p>";
                Session::set('value_array', $_POST);
                Session::set('error_array', Form::getErrorArray());
            }
            elseif($response['error_count'] > 0)
            {
                $feedback = "<h2><i class='far fa-times-circle'></i>This Order Could Not Be Imported</h2>";
                $feedback .= "<p>The error response is listed below</p>";
                $feedback .= $response['error_string'];
                Session::set('value_array', $_POST);
                Session::set('error_array', Form::getErrorArray());
            }
            elseif($response['import_error'])
            {
                $bberror = true;
                $feedback = "<h2><i class='far fa-times-circle'></i>This Order Could Not Be Imported</h2>";
                $feedback .= "<p>The error response is listed below</p>";
                $feedback .= "<p>".$response['import_error_string']."</p>";
                Session::set('value_array', $_POST);
                Session::set('error_array', Form::getErrorArray());
            }
            else
            {
                $feedback = "<h2><i class='far fa-check-circle'></i>That Order Has Been Imported</h2>";
                $feedback .= "<p>Please check the order list for any duplicates</p>";
            }
        }
        Session::set('feedback', $feedback);
        Session::set('bberror', $bberror);
        return $this->redirector->to(PUBLIC_ROOT."orders/order-importing");
    }

    public function importNuchevOrders()
    {
       $response = $this->woocommerce->getNuchevOrders();
       $feedback = "<h2><i class='far fa-check-circle'></i>Nuchev Orders Imported</h2>";
       $feedback .= "<p>".$response['import_count']." orders have been successfully imported</p>";
       if($response['error_count'] > 0)
       {
           $feedback .= "<p>".$response['error_count']." orders were not imported</p>";
           $feedback .= "<p>The error response is listed below</p>";
           $feedback .= $response['error_string'];
       }
       Session::set('feedback', $feedback);
       return $this->redirector->to(PUBLIC_ROOT."orders/order-importing");
    }

    public function importPBAOrders()
    {
       $response = $this->woocommerce->getPBAOrders();
       $feedback = "<h2><i class='far fa-check-circle'></i>Performance Brands WooCommerce Orders Imported</h2>";
       $feedback .= "<p>".$response['import_count']." orders have been successfully imported</p>";
       if($response['error_count'] > 0)
       {
           $feedback .= "<p>".$response['error_count']." orders were not imported</p>";
           $feedback .= "<p>The error response is listed below</p>";
           $feedback .= $response['error_string'];
       }
       Session::set('feedback', $feedback);
       return $this->redirector->to(PUBLIC_ROOT."orders/order-importing");
    }

    public function importPBAEbayOrders()
    {
        $this->PBAeBay->connect();
       $response = $this->PBAeBay->getCurrentOrders();
       $feedback = "<h2><i class='far fa-check-circle'></i>PBA Perfect Practice Golf eBay Orders Imported</h2>";
       $feedback .= "<p>".$response['import_count']." orders have been successfully imported</p>";
       if($response['error_count'] > 0)
       {
           $feedback .= "<p>".$response['error_count']." orders were not imported</p>";
           $feedback .= "<p>The error response is listed below</p>";
           $feedback .= $response['error_string'];
       }
       Session::set('feedback', $feedback);
       return $this->redirector->to(PUBLIC_ROOT."orders/order-importing");
    }

    public function importPBAPPGShopifyOrders()
    {
       $response = $this->PbaPerfectPracticeGolfShopify->getOrders();
       $feedback = "<h2><i class='far fa-check-circle'></i>PBA Perfect Practice Golf Shopify Orders Imported</h2>";
       $feedback .= "<p>".$response['import_count']." orders have been successfully imported</p>";
       if($response['error_count'] > 0)
       {
           $feedback .= "<p>".$response['error_count']." orders were not imported</p>";
           $feedback .= "<p>The error response is listed below</p>";
           $feedback .= $response['error_string'];
       }
       Session::set('feedback', $feedback);
       return $this->redirector->to(PUBLIC_ROOT."orders/order-importing");
    }

    public function importPBAVCShopifyOrders()
    {
       $response = $this->PbaVoiceCaddyShopify->getOrders();
       $feedback = "<h2><i class='far fa-check-circle'></i>PBA Voice Caddy Shopify Orders Imported</h2>";
       $feedback .= "<p>".$response['import_count']." orders have been successfully imported</p>";
       if($response['error_count'] > 0)
       {
           $feedback .= "<p>".$response['error_count']." orders were not imported</p>";
           $feedback .= "<p>The error response is listed below</p>";
           $feedback .= $response['error_string'];
       }
       Session::set('feedback', $feedback);
       return $this->redirector->to(PUBLIC_ROOT."orders/order-importing");
    }

    public function importPBAHCGShopifyOrders()
    {
       $response = $this->PbaHomeCourseGolfShopify->getOrders();
       $feedback = "<h2><i class='far fa-check-circle'></i>PBA Home Course Golf Shopify Orders Imported</h2>";
       $feedback .= "<p>".$response['import_count']." orders have been successfully imported</p>";
       if($response['error_count'] > 0)
       {
           $feedback .= "<p>".$response['error_count']." orders were not imported</p>";
           $feedback .= "<p>The error response is listed below</p>";
           $feedback .= $response['error_string'];
       }
       Session::set('feedback', $feedback);
       return $this->redirector->to(PUBLIC_ROOT."orders/order-importing");
    }

    public function importPBASSGShopifyOrders()
    {
       $response = $this->PbaSuperspeedGolfShopify->getOrders();
       $feedback = "<h2><i class='far fa-check-circle'></i>PBA Super SpeedGolf Shopify Orders Imported</h2>";
       $feedback .= "<p>".$response['import_count']." orders have been successfully imported</p>";
       if($response['error_count'] > 0)
       {
           $feedback .= "<p>".$response['error_count']." orders were not imported</p>";
           $feedback .= "<p>The error response is listed below</p>";
           $feedback .= $response['error_string'];
       }
       Session::set('feedback', $feedback);
       return $this->redirector->to(PUBLIC_ROOT."orders/order-importing");
    }

    public function importPBARUKKETShopifyOrders()
    {
       $response = $this->PbaRukketGolfShopify->getOrders();
       $feedback = "<h2><i class='far fa-check-circle'></i>PBA Rukket Shopify Orders Imported</h2>";
       $feedback .= "<p>".$response['import_count']." orders have been successfully imported</p>";
       if($response['error_count'] > 0)
       {
           $feedback .= "<p>".$response['error_count']." orders were not imported</p>";
           $feedback .= "<p>The error response is listed below</p>";
           $feedback .= $response['error_string'];
       }
       Session::set('feedback', $feedback);
       return $this->redirector->to(PUBLIC_ROOT."orders/order-importing");
    }

    public function importPBAARCCOSShopifyOrders()
    {
       $response = $this->PbaArccosGolfShopify->getOrders();
       $feedback = "<h2><i class='far fa-check-circle'></i>PBA Arccos Shopify Orders Imported</h2>";
       $feedback .= "<p>".$response['import_count']." orders have been successfully imported</p>";
       if($response['error_count'] > 0)
       {
           $feedback .= "<p>".$response['error_count']." orders were not imported</p>";
           $feedback .= "<p>The error response is listed below</p>";
           $feedback .= $response['error_string'];
       }
       Session::set('feedback', $feedback);
       return $this->redirector->to(PUBLIC_ROOT."orders/order-importing");
    }

    public function importBBShopifyOrders()
    {
       //echo "<p>Shop name: ".$this->BuzzBeeShopify->shop_name."</p>";
       $response = $this->BuzzBeeShopify->getOrders();
       //echo "<pre>",print_r($response),"</pre>"; die();
       $feedback = "<h2><i class='far fa-check-circle'></i>BuzzBee Shopify Orders Imported</h2>";
       $feedback .= "<p>".$response['import_count']." orders have been successfully imported</p>";
       if($response['error_count'] > 0)
       {
           $feedback .= "<p>".$response['error_count']." orders were not imported</p>";
           $feedback .= "<p>The error response is listed below</p>";
           $feedback .= $response['error_string'];
       }
       Session::set('feedback', $feedback);
       return $this->redirector->to(PUBLIC_ROOT."orders/order-importing");
    }

    public function importBBShopifyOrder()
    {
       //echo "<pre>",print_r($_POST),"</pre>";die();
       //$this->BuzzBeeShopify->getAnOrder($this->request->data['bbshopify_orderno']);
       $bberror = false;
        if(!$response = $this->BuzzBeeShopify->getAnOrder($this->request->data['bbshopify_orderno']))
        {
            $bberror = true;
            $feedback = "<h2><i class='far fa-times-circle'></i>No Order ID Supplied";
            $feedback .= "<p>The order ID was not passed to the form processor correctly</p>";
        }
        else
        {
            if($response['error'])
            {
                $bberror = true;
                $feedback = "<h2><i class='far fa-times-circle'></i>No Order Found With The Supplied ID</h2>";
                $feedback .= "<p>The order you want could not be found</p>";
                $feedback .= "<p>Please recheck the ID and try again</p>";
                Session::set('value_array', $_POST);
                Session::set('error_array', Form::getErrorArray());
            }
            elseif($response['error_count'] > 0)
            {
                $feedback = "<h2><i class='far fa-times-circle'></i>This Order Could Not Be Imported</h2>";
                $feedback .= "<p>The error response is listed below</p>";
                $feedback .= $response['error_string'];
                Session::set('value_array', $_POST);
                Session::set('error_array', Form::getErrorArray());
            }
            elseif($response['import_error'])
            {
                $bberror = true;
                $feedback = "<h2><i class='far fa-times-circle'></i>This Order Could Not Be Imported</h2>";
                $feedback .= "<p>The error response is listed below</p>";
                $feedback .= "<p>".$response['import_error_string']."</p>";
                Session::set('value_array', $_POST);
                Session::set('error_array', Form::getErrorArray());
            }
            else
            {
                $feedback = "<h2><i class='far fa-check-circle'></i>That Order Has Been Imported</h2>";
                $feedback .= "<p>Please check the order list for any duplicates</p>";
            }
        }
        Session::set('feedback', $feedback);
        Session::set('bberror', $bberror);
        return $this->redirector->to(PUBLIC_ROOT."orders/order-importing");
    }


    public function importArccosShopifyOrder()
    {
       //echo "<pre>",print_r($_POST),"</pre>";die();
       //$this->BuzzBeeShopify->getAnOrder($this->request->data['bbshopify_orderno']);
       $arccos_error = false;
        if(!$response = $this->PbaArccosGolfShopify->getAnOrder($this->request->data['pbaArccosshopify_orderno']))
        {
            $arccos_error = true;
            $feedback = "<h2><i class='far fa-times-circle'></i>No Order ID Supplied";
            $feedback .= "<p>The order ID was not passed to the form processor correctly</p>";
        }
        else
        {
            if($response['error'])
            {
                $arccos_error = true;
                $feedback = "<h2><i class='far fa-times-circle'></i>No Order Found With The Supplied ID</h2>";
                $feedback .= "<p>The order you want could not be found</p>";
                $feedback .= "<p>Please recheck the ID and try again</p>";
                Session::set('value_array', $_POST);
                Session::set('error_array', Form::getErrorArray());
            }
            elseif($response['error_count'] > 0)
            {
                $feedback = "<h2><i class='far fa-times-circle'></i>This Order Could Not Be Imported</h2>";
                $feedback .= "<p>The error response is listed below</p>";
                $feedback .= $response['error_string'];
                Session::set('value_array', $_POST);
                Session::set('error_array', Form::getErrorArray());
            }
            elseif($response['import_error'])
            {
                $arccos_error = true;
                $feedback = "<h2><i class='far fa-times-circle'></i>This Order Could Not Be Imported</h2>";
                $feedback .= "<p>The error response is listed below</p>";
                $feedback .= "<p>".$response['import_error_string']."</p>";
                Session::set('value_array', $_POST);
                Session::set('error_array', Form::getErrorArray());
            }
            else
            {
                $feedback = "<h2><i class='far fa-check-circle'></i>That Order Has Been Imported</h2>";
                $feedback .= "<p>Please check the order list for any duplicates</p>";
            }
        }
        Session::set('feedback', $feedback);
        Session::set('bberror', $arccos_error);
        return $this->redirector->to(PUBLIC_ROOT."orders/order-importing");
    }

    public function importPBAWoocommerceOrder()
    {
        //echo "<pre>",print_r($_POST),"</pre>";die();
        $bberror = false;
        if(!$response = $this->woocommerce->getPBAOrder($this->request->data['pbawoocommerce_id']))
        {
            $bberror = true;
            $feedback = "<h2><i class='far fa-times-circle'></i>No Order ID Supplied";
            $feedback .= "<p>The order ID was not passed to the form processor correctly</p>";
        }
        else
        {
            if($response['error'])
            {
                $bberror = true;
                $feedback = "<h2><i class='far fa-times-circle'></i>No Order Found With The Supplied ID</h2>";
                $feedback .= "<p>The order you want could not be found</p>";
                $feedback .= "<p>Please recheck the ID and try again</p>";
                Session::set('value_array', $_POST);
                Session::set('error_array', Form::getErrorArray());
            }
            elseif($response['error_count'] > 0)
            {
                $feedback = "<h2><i class='far fa-times-circle'></i>This Order Could Not Be Imported</h2>";
                $feedback .= "<p>The error response is listed below</p>";
                $feedback .= $response['error_string'];
                Session::set('value_array', $_POST);
                Session::set('error_array', Form::getErrorArray());
            }
            elseif($response['import_error'])
            {
                $bberror = true;
                $feedback = "<h2><i class='far fa-times-circle'></i>This Order Could Not Be Imported</h2>";
                $feedback .= "<p>The error response is listed below</p>";
                $feedback .= "<p>".$response['import_error_string']."</p>";
                Session::set('value_array', $_POST);
                Session::set('error_array', Form::getErrorArray());
            }
            else
            {
                $feedback = "<h2><i class='far fa-check-circle'></i>That Order Has Been Imported</h2>";
                $feedback .= "<p>Please check the order list for any duplicates</p>";
            }
        }
        Session::set('feedback', $feedback);
        Session::set('bberror', $bberror);
        return $this->redirector->to(PUBLIC_ROOT."orders/order-importing");
    }

    public function importOneplateOrder()
    {
        //echo "<pre>",print_r($_POST),"</pre>";die();
        $bberror = false;
        if(!$response = $this->woocommerce->getOneplateOrder($this->request->data['oneplatewoocommerce_id']))
        {
            $bberror = true;
            $feedback = "<h2><i class='far fa-times-circle'></i>No Order ID Supplied";
            $feedback .= "<p>The order ID was not passed to the form processor correctly</p>";
        }
        else
        {
            if($response['error'])
            {
                $bberror = true;
                $feedback = "<h2><i class='far fa-times-circle'></i>No Order Found With The Supplied ID</h2>";
                $feedback .= "<p>The order you want could not be found</p>";
                $feedback .= "<p>Please recheck the ID and try again</p>";
                Session::set('value_array', $_POST);
                Session::set('error_array', Form::getErrorArray());
            }
            elseif($response['error_count'] > 0)
            {
                $feedback = "<h2><i class='far fa-times-circle'></i>This Order Could Not Be Imported</h2>";
                $feedback .= "<p>The error response is listed below</p>";
                $feedback .= $response['error_string'];
                Session::set('value_array', $_POST);
                Session::set('error_array', Form::getErrorArray());
            }
            elseif($response['import_error'])
            {
                $bberror = true;
                $feedback = "<h2><i class='far fa-times-circle'></i>This Order Could Not Be Imported</h2>";
                $feedback .= "<p>The error response is listed below</p>";
                $feedback .= "<p>".$response['import_error_string']."</p>";
                Session::set('value_array', $_POST);
                Session::set('error_array', Form::getErrorArray());
            }
            else
            {
                $feedback = "<h2><i class='far fa-check-circle'></i>That Order Has Been Imported</h2>";
                $feedback .= "<p>Please check the order list for any duplicates</p>";
            }
        }
        Session::set('feedback', $feedback);
        Session::set('bberror', $bberror);
        return $this->redirector->to(PUBLIC_ROOT."orders/order-importing");
    }

    public function importOnePlateOrders()
    {
       $response = $this->woocommerce->getOnePlateOrders();
       $feedback = "<h2><i class='far fa-check-circle'></i>One Plate Orders Imported</h2>";
       $feedback .= "<p>".$response['import_count']." orders have been successfully imported</p>";
       if($response['error_count'] > 0)
       {
           $feedback .= "<p>".$response['error_count']." orders were not imported</p>";
           $feedback .= "<p>The error response is listed below</p>";
           $feedback .= $response['error_string'];
       }
       Session::set('feedback', $feedback);
       return $this->redirector->to(PUBLIC_ROOT."orders/order-importing");
    }

    public function importFreedomOrders()
    {
        //up the memory for this
        ini_set('memory_limit', '2048M');
        $encryptedData = $this->FreedomMYOB->callTask('getMYOBOrders',array());
        $invoices =  json_decode($this->FreedomMYOB->getDecryptedData($encryptedData),true);
        /*foreach($invoices as $i)
        {
            echo "<h1>".$i['Customer_Name']."</h1>";
            echo "<h2>",$i['Invoice_Number']."</h2>";
            echo "<pre>",print_r($i['ItemsPurchased']),"</pre>"; //die();
            echo "<hr/>";
        }
        die();*/
        $result = $this->FreedomMYOB->processOrders($invoices);
        echo "<pre>",print_r($result),"</pre>";
        die();
    }

    public function orderDispatching()
    {
        //render the page
        Config::setJsConfig('curPage', "order-dispatching");
         Config::set('curPage', "order-dispatching");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/orderDispatching.php', [
            'page_title'    =>  "Order Dispatching",
            'pht'           =>  ": Order Dispatching"
        ]);
    }

    public function clientOrders()
    {
        //up the memory to allow large database loads
        echo "<pre>",print_r($_SESSION),"</pre>";die();
        ini_set('memory_limit','1024M');
        $from = (isset($this->request->params['args']['from']))? $this->request->params['args']['from'] : strtotime('last monday');
        $to = (isset($this->request->params['args']['to']))? $this->request->params['args']['to'] : time();
        //only visible for client users
        $client_id = Session::getUserClientId();
        $client = $this->client->getClientInfo($client_id);
        $orders = $this->order->getOrdersForClient($client_id, $from, $to);
        //render the page
        Config::setJsConfig('curPage', "client-orders");
        Config::set('curPage', "client-orders");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/clientOrders.php', [
            'pht'           =>  ": Orders-".$client['client_name'],
            'page_title'    =>  "Orders For ".$client['client_name'],
            'client'        =>  $client,
            'client_id'     =>  $client_id,
            'orders'        =>  $orders,
            'from'          =>  $from,
            'to'            =>  $to
        ]);
    }

    public function addressUpdate()
    {
        if(isset($this->request->params['args']['order']))
        {
            $error = false;
            $order_id = $this->request->params['args']['order'];
            $order = $this->order->getOrderDetail($order_id);
        }
        else
        {
            $error = true;
            $order_id = 0;
            $order = array();
        }
        //render the page
        Config::setJsConfig('curPage', "address-update");
        Config::set('curPage', "address-update");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/addressUpdate.php', [
            'pht'           =>  ": Update Address",
            'page_title'    =>  "Update Address",
            'order_id'      =>  $order_id,
            'order'         =>  $order,
            'error'         =>  $error,
        ]);
    }

    public function addOrder()
    {
        $user_role = (Session::isAdminUser())? 'admin' : Session::getUserRole();
        //render the page
        Config::setJsConfig('curPage', "add-order");
        Config::set('curPage', "add-order");
        $form = $this->view->render( Config::get('VIEWS_PATH') . "forms/addorder.php");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/addOrder.php', [
            'page_title'    =>  "Add Order",
            'pht'           =>  ": Add an Order",
            'form'          =>  $form
        ]);
    }

    public function errorData()
    {
        $this->view->assign('page_title', "Error Data");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/default/", Config::get('VIEWS_PATH') . 'orders/errorData.php');
    }

    public function itemsUpdate()
    {
        if(!isset($this->request->params['args']['order']))
        {
            $error = true;
            $order_id = 0;
            $order = array();
            $order_items = array();
        }
        else
        {
            $error = false;
            $order_id = $this->request->params['args']['order'];
            $order = $this->order->getOrderDetail($order_id);
            $order_items = $this->order->getItemsForOrder($order_id);
        }
        //echo "<pre>",print_r($order_items),"</pre>";
        //render the page
        Config::setJsConfig('curPage', "items-update");
        Config::set('curPage', "items-update");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/itemsUpdate.php', [
            'pht'           =>  ": Update Items For Order",
            'page_title'    =>  "Update Items for Order",
            'order_id'      =>  $order_id,
            'order'         =>  $order,
            'error'         =>  $error,
            'order_items'   =>  $order_items
        ]);
    }

    public function orderPacking()
    {
        //render the page
        Config::setJsConfig('curPage', "order-packing");
        Config::set('curPage', "order-packing");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/orderPacking.php', [
            'page_title'    =>  "Order Packing",
            'pht'           =>  ": Order Packing"
        ]);
    }

    public function orderPicking()
    {
        //render the page
        Config::setJsConfig('curPage', "order-picking");
        Config::set('curPage', "order-picking");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/orderPicking.php', [
            'page_title'    =>  "Order Picking",
            'pht'           =>  ": Order Picking"
        ]);
    }

    public function orderSearch()
    {
        $form = $this->view->render( Config::get('VIEWS_PATH') . "forms/ordersearch.php",[
            'term'             =>  "",
            'client_id'        =>  0,
            'courier_id'       =>  0,
            'date_from_value'  =>  0,
            'date_to_value'    =>  0
        ]);
        //render the page
        Config::setJsConfig('curPage', "order-search");
        Config::set('curPage', "order-search");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/orderSearch.php', [
            'page_title'    =>  "Order Search",
            'pht'           =>  ": Order Search",
            'form'          =>  $form
        ]);
    }

    public function orderSearchResults()
    {
        if(!$this->Security->CsrfToken())
        {
            return $this->error(400);
        }
        //$client_id =
        $client_id = $this->request->query['client_id'];
        $courier_id = $this->request->query['courier_id'];
        $date_from_value = $this->request->query['date_from_value'];
        $date_to_value = $this->request->query['date_to_value'];
        //echo "<pre>",print_r($this->request),"</pre>"; die();
        $args = array(
            'term'              =>  $this->request->query['term'],
            'client_id'         =>  $client_id,
            'courier_id'        =>  $courier_id,
            'date_from_value'   =>  $date_from_value,
            'date_to_value'     =>  $date_to_value
        );
        $orders = $this->order->getSearchResults($args);
        $count = count($orders);
        $s = ($count == 1)? "": "s";
        $form = $this->view->render( Config::get('VIEWS_PATH') . "forms/ordersearch.php",$args);
        //render the page
        Config::setJsConfig('curPage', "order-search-results");
        Config::set('curPage', "order-search-results");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/orderSearchResults.php', [
            'page_title'    =>  "Search Results",
            'pht'           =>  ": Oder Search Results",
            'form'          =>  $form,
            'count'         =>  $count,
            's'             =>  $s,
            'term'          =>  $this->request->query['term'],
            'orders'        =>  $orders
        ]);
    }

    public function orderSummaries()
    {
        $from = (isset($this->request->params['args']['from']))? $this->request->params['args']['from']: strtotime("6 days ago");
        $summaries = $this->order->getEparcelSummaries($from);
        //render the page
        Config::setJsConfig('curPage', "order-summaries");
        Config::set('curPage', "order-summaries");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/orderSummaries.php', [
            'page_title'    =>  "eParcel Order Summaries",
            'pht'           =>  ": eParcel Order Summaries",
            'summaries'     =>  $summaries,
            'from'          =>  $from
        ]);
    }

    public function orderEdit()
    {
        if(!isset($this->request->params['args']['order']))
        {
            $error = true;
            $order_id = 0;
            $order = array();
            $deliver_to = "";
            $tracking_email = "";
            $signature_req = false;
            $express_post = false;
            $store_order = false;
            $company_name = "";
            $contact_phone = "";
            $client_order_id = "";
            $delivery_instructions = "";
            $comments = "";
        }
        else
        {
            $error = false;
            $order_id = $this->request->params['args']['order'];
            $order = $this->order->getOrderDetail($order_id);
            $deliver_to = $order['ship_to'];
            $company_name = $order['company_name'];
            $contact_phone = $order['contact_phone'];
            $tracking_email = $order['tracking_email'];
            $signature_req = $order['signature_req'] > 0;
            $express_post = $order['eparcel_express'] > 0;
            $store_order = $order['store_order'] > 0;
            $client_order_id = $order['client_order_id'];
            $delivery_instructions = $order['instructions'];
            $comments = $order['3pl_comments'];
        }
        //render the page
        Config::setJsConfig('curPage', "order-edit");
        Config::set('curPage', "order-edit");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/orderEdit.php', [
            'page_title'        =>  "Edit an Order",
            'pht'               =>  ": Edit Order",
            'order_id'          =>  $order_id,
            'order'             =>  $order,
            'error'             =>  $error,
            'deliver_to'        =>  $deliver_to,
            'tracking_email'    =>  $tracking_email,
            'signature_req'     =>  $signature_req,
            'express_post'      =>  $express_post,
            'store_order'       =>  $store_order,
            'company_name'      =>  $company_name,
            'contact_phone'     =>  $contact_phone,
            'client_order_id'   =>  $client_order_id,
            'instructions'      =>  $delivery_instructions,
            'comments'          =>  $comments
        ]);
    }

    public function orderUpdate()
    {
        //echo "<p>In the orders controller</p>";
        //echo "<pre>",print_r($this->request),"</pre>";
        $cont = (isset($this->request->data['link']))? $this->request->data['link']."-orders" : "orders";
        if(!isset($this->request->params['args']['order']))
        {
            $error = true;
            $order_id = 0;
            $order = array();
            $order_items = array();
            $packages = array();
            $client_name = "";
            $truck_id = $local_id = -5;
            $address_string = "";
            $eb = "";
        }
        else
        {
            $error = false;
            $order_id = $this->request->params['args']['order'];
            $order = $this->order->getOrderDetail($order_id);
            $order_items = $this->order->getItemsForOrder($order_id);
            $packages = $this->order->getPackagesForOrder($order_id);
            $client_name = $this->client->getClientName($order['client_id']);
            $truck_id = $this->courier->getTruckId();
            $local_id = $this->courier->getLocalId();
            $address_string = $order['address'];
            if(!empty($order['address_2']))
                $address_string .= " ".$order['address_2'];
            $address_string .= " ".$order['suburb'];
            $address_string .= " ".$order['state'];
            $address_string .= " ".$order['postcode'];
            $address_string .= " ".$order['country'];
            $eb = $this->user->getUserName( $order['entered_by'] );
            if(empty($eb))
            {
                $eb = "Automatically Imported";
            }
        }
        //render the page
        Config::setJsConfig('curPage', "order-update");
        Config::set('curPage', "order-update");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/orderUpdate.php', [
            'page_title'        =>  "Update an Order",
            'pht'            =>  ": Update Order",
            'order_id'          =>  $order_id,
            'order'             =>  $order,
            'error'             =>  $error,
            'order_items'       =>  $order_items,
            'packages'          =>  $packages,
            'client_name'       =>  $client_name,
            'truck_id'          =>  $truck_id,
            'local_id'          =>  $local_id,
            'address_string'    =>  $address_string,
            'entered_by'        =>  $eb,
            'cont'              =>  $cont
        ]);
    }

    public function orderTracking()
    {
        $order_id = 0;
        $order = array();
        $courier = $order_status = "";
        $tracking = array();
        if(!empty($this->request->params['args']))
        {
            if(isset($this->request->params['args']['order']))
            {
                $order_id = $this->request->params['args']['order'];
                $order = $this->order->getOrderDetail($order_id);
                $courier = $this->courier->getCourierName($order['courier_id']);
                $order_status = $this->order->getStatusName($order['status_id']);
                if($courier == "eParcel" || $courier == "eParcel Express")
                {
                    $eparcel = $this->client->getEparcelClass($order['client_id']);
                    $tracking = $this->$eparcel->GetTracking($order['consignment_id']);
                    //echo "eParcel<pre>",print_r($tracking),"</pre>";die();
                }
                elseif($courier == "Direct Freight")
                {
                    $tracking = $this->directfreight->trackConsignment($order['consignment_id']);
                }
            }
        }
        //render the page
        Config::setJsConfig('curPage', "order-tracking");
        Config::set('curPage', "order-tracking");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/orderTracking.php', [
            'page_title'    =>  "Tracking and Details for ".$order['order_number'],
            'pht'           =>  ": Order Tracking",
            'order_id'      =>  $order_id,
            'order'         =>  $order,
            'courier'       =>  $courier,
            'order_status'  =>  $order_status,
            'tracking'      =>  $tracking
        ]);
    }

    public function orderDetail()
    {
        $order_id = 0;
        $order = array();
        $courier = $order_status = "";
        $products = array();
        if(!empty($this->request->params['args']))
        {
            if(isset($this->request->params['args']['order']))
            {
                $order_id = $this->request->params['args']['order'];
                $order = $this->order->getOrderDetail($order_id);
                $courier = $this->courier->getCourierName($order['courier_id']);
                $order_status = $this->order->getStatusName($order['status_id']);
                $products = $this->order->getItemsForOrder($order_id);
            }
        }
        //render the page
        Config::setJsConfig('curPage', "order-detail");
        Config::set('curPage', "order-detail");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/orderDetail.php', [
            'page_title'    =>  "Order Detail",
            'pht'           =>  ": Order Detail",
            'order_id'      =>  $order_id,
            'order'         =>  $order,
            'courier'       =>  $courier,
            'order_status'  =>  $order_status,
            'products'      =>  $products
        ]);
    }

    public function addBulkOrders()
    {
        //render the page
        Config::setJsConfig('curPage', "add-bulk-orders");
        Config::set('curPage', "add-bulk-orders");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/addBulkOrders.php', [
            'page_title'    =>  "Import/Bulk Upload Orders",
            'pht'           =>  ": Bulk Import Orders",
        ]);
    }

    public function bulkUploadOrders()
    {
        //up the memory for this
        ini_set('memory_limit', '2048M');
        //For Clients
        //render the page
        Config::setJsConfig('curPage', "bulk-upload-orders");
        Config::set('curPage', "bulk-upload-orders");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/importOrders.php', [
            'page_title'    =>  "Import/Bulk Upload Orders",
            'pht'           =>  ": Upload Orders",
        ]);
    }

    public function viewOrders()
    {
        //echo "<pre>",print_r($this->request->params['args']),"</pre>";die();
        $client_name = "All Clients";
        $courier_id = -1;
        $client_id = 0;
        $fulfilled = 0;
        $state = "";
        $ff = "Unfulfilled";
        if(!empty($this->request->params['args']))
        {
            if(isset($this->request->params['args']['client']))
            {
                $client_id = $this->request->params['args']['client'];
                $client_name = $this->client->getClientName($client_id);
            }
            if(isset($this->request->params['args']['courier']))
            {
                $courier_id = $this->request->params['args']['courier'];
            }
            if(isset($this->request->params['args']['fulfilled']))
            {
                $fulfilled = $this->request->params['args']['fulfilled'];
                $ff = "Fulfilled";
            }
            if(isset($this->request->params['args']['state']))
            {
                $state = $this->request->params['args']['state'];
            }
        }
        $page_title = "$ff Orders For $client_name";
        //$orders = $this->order->getUnfulfilledOrders($client_id, $courier_id, 0);     getAllOrders($client_id, $courier_id = -1, $fulfilled = 0, $store_order = -1)
        $orders = $this->order->getAllOrders($client_id, $courier_id, $fulfilled, 0, $state);
        //render the page
        Config::setJsConfig('curPage', "view-orders");
        Config::set('curPage', "view-orders");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/viewOrders.php', [
            'page_title'    =>  $page_title,
            'pht'           =>  ": View Orders",
            'client_name'   =>  $client_name,
            'client_id'     =>  $client_id,
            'courier_id'    =>  $courier_id,
            'orders'        =>  $orders,
            'fulfilled'     =>  $fulfilled,
            'state'         =>  $state
        ]);
    }

    public function updatePickup()
    {
        if(!isset($this->request->params['args']['pickup']))
        {
            $error = true;
            $pickup_id = 0;
            $pickup = array();
            $pickup_address = "";
            $dropoff_address = "";
            $pallets = 1;
        }
        else
        {
            $error = false;
            $pickup_id = $this->request->params['args']['pickup'];
            $pickup = $this->pickup->getPickup($pickup_id);
            $pickup_address = $this->pickup->getAddressString($pickup['id'], "pu");
            $dropoff_address = $this->pickup->getAddressString($pickup['id']);
            $puaddress_string = $pickup['puaddress'];
            if(!empty($pickup['puaddress_2']))
                $puaddress_string .= " ".$pickup['puaddress_2'];
            $puaddress_string .= " ".$pickup['pusuburb'];
            $puaddress_string .= " "."VIC";
            $puaddress_string .= " ".$pickup['pupostcode'];
            $puaddress_string .= " "."AU";
            $address_string = $pickup['address'];
            if(!empty($pickup['address_2']))
                $address_string .= " ".$pickup['address_2'];
            $address_string .= " ".$pickup['suburb'];
            $address_string .= " "."VIC";
            $address_string .= " ".$pickup['postcode'];
            $address_string .= " "."AU";
            $pallets = ($pickup['pallets'] < 1)? 1 : $pickup['pallets'];
        }
        //render the page
        Config::setJsConfig('curPage', "pickup-update");
        Config::set('curPage', "pickup-update");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/pickupUpdate.php', [
            'page_title'        =>  "Update a Pickup",
            'pickup_id'         =>  $pickup_id,
            'pickup'            =>  $pickup,
            'error'             =>  $error,
            'pickup_address'    => $pickup_address,
            'dropoff_address'   => $dropoff_address,
            'puaddress_string'  => $puaddress_string,
            'address_string'    => $address_string,
            'pallets'           => $pallets
        ]);
    }

    public function isAuthorized(){
        $action = $this->request->param('action');
        //$role = Session::getUserRole();
        $role = (Session::isAdminUser())? 'admin' : Session::getUserRole();
        $resource = "orders";

        //only for admin
        Permission::allow('admin', $resource, "*");
        Permission::allow('super admin', $resource, "*");
        //production users
        $allowed_resources = array(
            "orderUpdate",
            "createDeliveryDocket"
        );
        Permission::allow('production admin', $resource, $allowed_resources);
        //warehouse users
        Permission::allow('warehouse', $resource, array(
            "index",
            "orderDispatching",
            "orderPacking",
            "orderPicking",
            "orderSearch",
            "orderSearchResults",
            "viewOrders",
            "orderUpdate",
            "addressUpdate",
            "orderEdit",
            "viewDetails",
            "viewStoreorders",
            "getQotes",
            "bookItemCollection"
        ));
        //only for clients
        $allowed_resources = array(
            "index",
            "addOrder",
            "addOrderTest",
            "bulkUploadOrders",
            "clientOrders",
            "orderTracking",
            "orderDetail",
        );
        Permission::allow(['client', 'freedom warehouse'], $resource, $allowed_resources);
        return Permission::check($role, $resource, $action);
    }
}
?>