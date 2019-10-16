<?php

/**
 * Orders controller
 *

 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */

class OrdersController extends Controller
{
    public function beforeAction()
    {
        parent::beforeAction();
        $this->Security->config("form", [ 'fields' => ['csrf_token']]);
    }

    public function addSerials()
    {
        //echo "<pre>",print_r($this->request->params['args']),"</pre>";die();
        $order_id = $order_number = 0;
        $od = $items = array();
        if(!empty($this->request->params['args']))
        {
            if(isset($this->request->params['args']['order']))
            {
                $order_id = $this->request->params['args']['order'];
                $od = $this->order->getOrderDetail($order_id);
                $items = $this->order->getItemsForOrderNoLocations($order_id);
                $order_number = $od['order_number'];
            }
        }

        //render the page
        Config::setJsConfig('curPage', "add-serials");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/addSerials.php', [
            'page_title'    =>  'Add Serial Numbers to Order',
            'order_id'      =>  $order_id,
            'order_number'  =>  $order_number,
            'od'            =>  $od,
            'items'         =>  $items
        ]);
    }

    public function manageSwatches()
    {
        //echo "<pre>",print_r($this->request->params['args']),"</pre>";die();
        $client_id = 59;
        $client_name = "NOA Sleep";
        $state = "";
        $posted = 0;
        $ff = "Not Posted";
        if(!empty($this->request->params['args']))
        {
            if(isset($this->request->params['args']['client']))
            {
                $client_id = $this->request->params['args']['client'];
                $client_name = $this->client->getClientName($client_id);
            }
            if(isset($this->request->params['args']['posted']))
            {
                $posted = $this->request->params['args']['fulfilled'];
                $ff = "Posted";
            }
            if(isset($this->request->params['args']['state']))
            {
                $state = $this->request->params['args']['state'];
            }
        }
        $page_title = "$ff Swatches For $client_name";
        $swatches = $this->swatch->getAllSwatches($client_id, $posted, $state);
        //render the page
        Config::setJsConfig('curPage', "manage-swatches");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/manageSwatches.php', [
            'page_title'    =>  $page_title,
            'client_name'   =>  $client_name,
            'client_id'     =>  $client_id,
            'swatches'      =>  $swatches,
            'posted'        =>  $posted,
            'state'         =>  $state
        ]);



    }

    public function recordPickup()
    {
        $client_id = 0;
        $client_name = "";
        $page_title = "Record a Pickup";
        if(!empty($this->request->params['args']))
        {
            if(isset($this->request->params['args']['client']))
            {
                $client_id = $this->request->params['args']['client'];
                $client_name = $this->client->getClientName($client_id);
                $page_title .= " for $client_name";
            }
        }
        Config::setJsConfig('curPage', "record-pickup");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/recordPickup.php',[
            'page_title'    =>  $page_title,
            'client_id'     =>  $client_id,
            'client_name'   =>  $client_name
        ]);
    }

    public function bookPickup()
    {
        $client = $this->client->getClientInfo(Session::getUserClientId());
        //render the page
        Config::setJsConfig('curPage', "book-pickup");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/bookPickup.php', [
            'page_title'    =>  "Book A Pickup",
            'client'        => $client
        ]);
    }

    public function orderCSVUpload()
    {

        //render the page
        Config::setJsConfig('curPage', "order-csv-upload");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/csvUpload.php', [
            'page_title'    =>  "CSV Import"
        ]);
    }

    public function truckUsage()
    {
        //render the page
        Config::setJsConfig('curPage', "truck-usage");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/truckUsage.php', [
            'page_title'        =>  "Truck Usage Manual Entry"
        ]);
    }

    public function orderImporting()
    {

        //render the page
        Config::setJsConfig('curPage', "order-importing");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/orderImporting.php', [
            'page_title'        =>  "Import Orders From External Sites",
            'bb_clientid'       =>  $this->client->getClientId("THE BIG BOTTLE CO"),
            'nuchev_clientid'   =>  $this->client->getClientId("NUCHEV"),
            'noa_clientid'      =>  $this->client->getClientId("Noa Sleep"),
            'ttau_clientid'     =>  $this->client->getClientId("Two T Australia")
        ]);
    }

    public function importBBOrder()
    {
        //echo "<pre>",print_r($_POST),"</pre>";die();
        $bberror = false;
        if(!$response = $this->woocommerce->getBBOrder($this->request->data['bbwoocommerce_id']))
        {
            $bberror = true;
            $feedback = "<h2><i class='far fa-times-circle'></i>No Order ID Supplied</h2>";
            $feedback .= "</p>The order ID was not passed to the form processor correctly</p>";
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

    public function importNoaOrder()
    {
        //echo "<pre>",print_r($_POST),"</pre>";die();
        $bberror = false;
        if(!$response = $this->woocommerce->getNoaOrder($this->request->data['noawoocommerce_id']))
        {
            $bberror = true;
            $feedback = "<h2><i class='far fa-times-circle'></i>No Order ID Supplied</h2>";
            $feedback .= "</p>The order ID was not passed to the form processor correctly</p>";
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

    public function importFEightOrders()
    {
        //$response = $this->woocommerce->getBBOrders();
       $response = $this->emailordersparser->getFigure8Orders();
       $feedback = "<h2><i class='far fa-check-circle'></i>Figure 8 Orders Imported</h2>";
       $feedback .= "<p>".$response['import_count']." orders have been successfully imported</p>";
       /* */
       if($response['error_count'] > 0)
       {
           $feedback .= "<p>".$response['error_count']." orders were not imported</p>";
           $feedback .= "<p>The error response is listed below</p>";
           $feedback .= $response['error_string'];
       }

       Session::set('feedback', $feedback);
       return $this->redirector->to(PUBLIC_ROOT."orders/order-importing");
    }

    public function importNuchevSamples()
    {
        //$response = $this->woocommerce->getBBOrders();
       $response = $this->emailordersparser->getNuchevSamples();
       $feedback = "<h2><i class='far fa-check-circle'></i>Nuchev Samples Imported</h2>";
       $feedback .= "<p>".$response['import_count']." orders have been successfully imported</p>";
       /* */
       if($response['error_count'] > 0)
       {
           $feedback .= "<p>".$response['error_count']." orders were not imported</p>";
           $feedback .= "<p>The error response is listed below</p>";
           $feedback .= $response['error_string'];
       }

       Session::set('feedback', $feedback);
       return $this->redirector->to(PUBLIC_ROOT."orders/order-importing");
    }

    public function importBBOrders()
    {
       $response = $this->woocommerce->getBBOrders();
       $feedback = "<h2><i class='far fa-check-circle'></i>Big Bottle Orders Imported</h2>";
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

    public function importTeamTimbuktuOrders()
    {
        $response = $this->shopify->getTeamTimbuktuOrders();
        echo "Response<pre>",print_r($response),"</pre>";
       $feedback = "<h2><i class='far fa-check-circle'></i>Team Timbuktu Orders Imported</h2>";
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

    public function importTTAUOrders()
    {
       $response = $this->woocommerce->getTTOrders();
       $feedback = "<h2><i class='far fa-check-circle'></i>TT Aust Orders Imported</h2>";
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

    public function importNoaOrders()
    {
       $response = $this->woocommerce->getNoaOrders();
       $feedback = "<h2><i class='far fa-check-circle'></i>Noa Sleep Orders Imported</h2>";
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

    public function orderDispatching()
    {
        //render the page
        Config::setJsConfig('curPage', "order-dispatching");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/orderDispatching.php', [
            'page_title'    =>  "Order Dispatching"
        ]);
    }

    public function clientOrders()
    {
        //up the memory to allow large database loads
        ini_set('memory_limit','1024M');
        $from = (isset($this->request->params['args']['from']))? $this->request->params['args']['from'] : strtotime('last monday');
        $to = (isset($this->request->params['args']['to']))? $this->request->params['args']['to'] : time();
        //only visible for client users
        $client = $this->client->getClientInfo(Session::getUserClientId());
        $orders = $this->order->getOrdersForClient(Session::getUserClientId(), $from, $to);
        //render the page
        Config::setJsConfig('curPage', "clients-orders");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/clientOrders.php', [
            'page_title'    =>  "Orders For ".$client['client_name'],
            'client'        =>  $client,
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
            $table = "orders";
        }
        elseif(isset($this->request->params['args']['swatch']))
        {
            $error = false;
            $order_id = $this->request->params['args']['swatch'];
            $order = $this->swatch->getSwatchDetail($order_id);
            $table = "swatches";
        }
        else
        {
            $error = true;
            $order_id = 0;
            $order = array();
            $table = "";
        }
        //render the page
        Config::setJsConfig('curPage', "address-update");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/addressUpdate.php', [
            'page_title'    =>  "Update Address",
            'order_id'      =>  $order_id,
            'order'         =>  $order,
            'error'         =>  $error,
            'table'         =>  $table
        ]);
    }

    public function addOrder()
    {
        $user_role = (Session::isAdminUser())? 'admin' : Session::getUserRole();
        //render the page
        Config::setJsConfig('curPage', "add-order");
        $form = $this->view->render( Config::get('VIEWS_PATH') . "forms/addorder.php");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/addOrder.php', [
            'page_title'    =>  "Add Order",
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
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/itemsUpdate.php', [
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
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/orderPacking.php', [
            'page_title'    =>  "Order Packing"
        ]);
    }

    public function orderPicking()
    {
        //render the page
        Config::setJsConfig('curPage', "order-picking");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/orderPicking.php', [
            'page_title'    =>  "Order Picking"
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
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/orderSearch.php', [
            'page_title'    =>  "Order Search",
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
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/orderSearchResults.php', [
            'page_title'    =>  "Search Results",
            'form'          =>  $form,
            'count'         =>  $count,
            's'             =>  $s,
            'term'          =>  $this->request->query['term'],
            'orders'        =>  $orders
        ]);
    }

    public function orderSummaries()
    {
        $summaries = $this->order->getEparcelSummaries(isset($this->request->params['args']['all']));
        //render the page
        Config::setJsConfig('curPage', "order-summaries");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/orderSummaries.php', [
            'page_title'    =>  "eParcel Order Summaries",
            'summaries'     =>  $summaries,
            'all'           =>  isset($this->request->params['args']['all'])
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
            $client_order_id = $order['client_order_id'];
            $delivery_instructions = $order['instructions'];
            $comments = $order['3pl_comments'];
        }
        //render the page
        Config::setJsConfig('curPage', "order-edit");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/orderEdit.php', [
            'page_title'        =>  "Edit an Order",
            'order_id'          =>  $order_id,
            'order'             =>  $order,
            'error'             =>  $error,
            'deliver_to'        =>  $deliver_to,
            'tracking_email'    =>  $tracking_email,
            'signature_req'     =>  $signature_req,
            'express_post'      =>  $express_post,
            'company_name'      =>  $company_name,
            'contact_phone'     =>  $contact_phone,
            'client_order_id'   =>  $client_order_id,
            'instructions'      =>  $delivery_instructions,
            'comments'          =>  $comments
        ]);
    }

    public function orderUpdate()
    {
        if(!isset($this->request->params['args']['order']))
        {
            $error = true;
            $order_id = 0;
            $order = array();
            $order_items = array();
            $packages = array();
            $client_name = "";
            $truck_id = $local_id = -5;
            $store_order = false;
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
            $store_order = $order['store_order'] > 0;
            $eb = $this->user->getUserName( $order['entered_by'] );
            if(empty($eb))
            {
                $eb = "Automatically Imported";
            }
        }
        //render the page
        Config::setJsConfig('curPage', "order-update");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/orderUpdate.php', [
            'page_title'        =>  "Update an Order",
            'order_id'          =>  $order_id,
            'order'             =>  $order,
            'error'             =>  $error,
            'order_items'       =>  $order_items,
            'packages'          =>  $packages,
            'client_name'       =>  $client_name,
            'truck_id'          =>  $truck_id,
            'local_id'          =>  $local_id,
            'address_string'    =>  $address_string,
            'store_order'       =>  $store_order,
            'entered_by'        =>  $eb
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
                elseif($courier == "Hunters")
                {
                    $tracking = $this->Hunters3KG->GetTracking($order['consignment_id']);
                    //echo "Hunters<pre>",print_r($tracking),"</pre>";die();
                }
                elseif($courier == "HuntersPLU")
                {
                    $tracking = $this->HuntersPLU->GetTracking($order['consignment_id']);
                    //echo "HuntersPLU<pre>",print_r($tracking),"</pre>";die();
                }
            }
        }
        //render the page
        Config::setJsConfig('curPage', "order-tracking");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/orderTracking.php', [
            'page_title'    =>  "Order Tracking",
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
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/orderDetail.php', [
            'page_title'    =>  "Order Detail",
            'order_id'      =>  $order_id,
            'order'         =>  $order,
            'courier'       =>  $courier,
            'order_status'  =>  $order_status,
            'products'      =>  $products
        ]);
    }

    public function bulkUploadOrders()
    {
        //render the page
        Config::setJsConfig('curPage', "import-orders");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/importOrders.php', [
            'page_title'    =>  "Import/Bulk Upload Orders"
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
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/viewOrders.php', [
            'page_title'    =>  $page_title,
            'client_name'   =>  $client_name,
            'client_id'     =>  $client_id,
            'courier_id'    =>  $courier_id,
            'orders'        =>  $orders,
            'fulfilled'     =>  $fulfilled,
            'state'         =>  $state
        ]);
    }

    public function viewSolarorders()
    {
        //echo "<pre>",print_r($this->request->params['args']),"</pre>";die();
        $order_type = "All Types";
        $type_id = 0;
        $ff = "Unfulfilled";
        $fulfilled = 0;
        if(!empty($this->request->params['args']))
        {
            if(isset($this->request->params['args']['type']))
            {
                $type_id = $this->request->params['args']['type'];
                $order_type = $this->solarordertype->getSolarOrderType($type_id);
            }
            if(isset($this->request->params['args']['fulfilled']))
            {
                $fulfilled = $this->request->params['args']['fulfilled'];
                $ff = "Fulfilled";
            }
        }
        $page_title = "$ff Orders For $order_type";

        $orders = $this->solarorder->getSolarAllOrders($type_id, $fulfilled);
        //render the page
        Config::setJsConfig('curPage', "view-solarorders");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/viewSolarOrders.php', [
            'page_title'    =>  $page_title,
            'order_type'    =>  $order_type,
            'type_id'       =>  $type_id,
            'orders'        =>  $orders,
            'fulfilled'     =>  $fulfilled
        ]);
    }

    public function viewStoreorders()
    {
        //echo "<pre>",print_r($this->request->params['args']),"</pre>";die();
        $client_name = "All Clients";
        $client_id = 0;
        $fulfilled = 0;
        $ff = "Unfulfilled";
        if(!empty($this->request->params['args']))
        {
            if(isset($this->request->params['args']['client']))
            {
                $client_id = $this->request->params['args']['client'];
                $client_name = $this->client->getClientName($client_id);
            }
            if(isset($this->request->params['args']['fulfilled']))
            {
                $fulfilled = $this->request->params['args']['fulfilled'];
                $ff = "Fulfilled";
            }
        }
        $page_title = "$ff Store Orders For $client_name";
        //$orders = $this->order->getUnfulfilledOrders($client_id, -1, 1);
        $orders = $this->order->getAllOrders($client_id, -1, $fulfilled, 1);
        //render the page
        Config::setJsConfig('curPage', "view-storeorders");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/viewStoreOrders.php', [
            'page_title'    =>  $page_title,
            'client_name'   =>  $client_name,
            'client_id'     =>  $client_id,
            'fulfilled'     => $fulfilled,
            'orders'        =>  $orders
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

    public function viewPickups()
    {
        //echo "<pre>",print_r($this->request->params['args']),"</pre>";die();
        $client_name = "All Clients";
        $client_id = 0;
        if(!empty($this->request->params['args']))
        {
            if(isset($this->request->params['args']['client']))
            {
                $client_id = $this->request->params['args']['client'];
                $client_name = $this->client->getClientName($client_id);
            }
        }
        $pickups = $this->pickup->getPickups($client_id);
        //render the page
        Config::setJsConfig('curPage', "view-pickups");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/viewPickups.php', [
            'page_title'    =>  "View Pickups",
            'client_name'   =>  $client_name,
            'client_id'     =>  $client_id,
            'pickups'       =>  $pickups
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
        Permission::allow('md admin', $resource, "*");
        //warhouse users
        Permission::allow('warehouse', $resource, array(
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
            "viewStoreorders"
        ));
        //only for clients
        $allowed_resources = array(
            "addOrder",
            "addOrderTest",
            "bookPickup",
            "bulkUploadOrders",
            "clientOrders",
            "orderTracking",
            "orderDetail",
        );
        //solar admin users
        Permission::allow('solar admin', $resource, array(
            "addSolarOrder",
            "addOriginOrder",
            "addServiceJob",
            "addOriginServiceJob",
            "addTLJServiceJob",
            "addTLJOrder"
        ));
        Permission::allow('client', $resource, $allowed_resources);
        return Permission::check($role, $resource, $action);
    }
}
?>