<?php

/**
 * Inventory controller
 *

 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */

class InventoryController extends Controller
{
    public function beforeAction()
    {
        parent::beforeAction();
    }

    public function index()
    {
        //set the page name for menu display
        Config::setJsConfig('curPage', 'inventory-index');
        parent::displayIndex(get_class());
    }

    public function moveAllClientStock(){
        $client_id = 0;
        $client_name = "";
        $page_title =  'Move All Stock For Client';
        $receiving_id = $this->location->receiving_id;
        $bayswater_receiving_id = $this->location->bayswater_receiving_id;
        if(!empty($this->request->params['args']))
        {
            if(isset($this->request->params['args']['client']))
            {
                $client_id = $this->request->params['args']['client'];
                $client_name = $this->client->getClientName($client_id);
                $page_title =  'Move All Stock For '.$client_name;
            }
        }
        Config::setJsConfig('curPage', "move-all-client-stock");
        Config::set('curPage', "move-all-client-stock");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/inventory/", Config::get('VIEWS_PATH') . 'inventory/moveAllStock.php',[
            'page_title'                =>  $page_title,
            'client_name'               =>  $client_name,
            'client_id'                 =>  $client_id,
            'receiving_id'              =>  $receiving_id,
            'bayswater_receiving_id'    =>  $bayswater_receiving_id
        ]);
    }

    public function transferLocation()
    {
        Config::setJsConfig('curPage', "transfer-location");
        Config::set('curPage', "transfer-location");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/inventory/", Config::get('VIEWS_PATH') . 'inventory/transferLocation.php',[
            'page_title'    =>  'Transfer a Location',
        ]);
    }

    public function registerNewStock()
    {
        $client_id = Session::getUserClientId();
        $client_name = $this->client->getClientName($client_id);
        Config::setJsConfig('curPage', "register-new-stock");
        Config::set('curPage', "register-new-stock");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/inventory/", Config::get('VIEWS_PATH') . 'inventory/registerNewStock.php',[
            'page_title'    =>  'Register New Stock',
            'client_id'     =>  $client_id,
            'client_name'   =>  $client_name
        ]);
    }

    public function expectedShipments()
    {
        $client_id = Session::getUserClientId();
        $client_name = $this->client->getClientName($client_id);
        $shipments = $this->shipment->getExpectedShipments($client_id);
        Config::setJsConfig('curPage', "expected-shipments");
        Config::set('curPage', "expected-shipments");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/inventory/", Config::get('VIEWS_PATH') . 'inventory/expectedShipments.php',[
            'page_title'    =>  'Expected Shipments',
            'shipments'     =>  $shipments,
            'client_id'     =>  $client_id,
            'client_name'   =>  $client_name
        ]);
    }

    public function goodsIn()
    {
        $client_id = 0;
        $client_name = "";
        if(!empty($this->request->params['args']))
        {
            if(isset($this->request->params['args']['client']))
            {
                $client_id = $this->request->params['args']['client'];
                $client_name = $this->client->getClientName($client_id);
            }
        }
        Config::setJsConfig('curPage', "goods-in");
        Config::set('curPage', "goods-in");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/inventory/", Config::get('VIEWS_PATH') . 'inventory/goodsIn.php',[
            'page_title'    =>  'Goods In',
            'client_id'     =>  $client_id,
            'client_name'   => $client_name
        ]);
    }

    public function goodsOut()
    {
        $client_id = 0;
        $client_name = "";
        if(!empty($this->request->params['args']))
        {
            if(isset($this->request->params['args']['client']))
            {
                $client_id = $this->request->params['args']['client'];
                $client_name = $this->client->getClientName($client_id);
            }
        }
        Config::setJsConfig('curPage', "goods-out");
        Config::set('curPage', "goods-out");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/inventory/", Config::get('VIEWS_PATH') . 'inventory/goodsOut.php',[
            'page_title'    =>  'Goods Out',
            'client_id'     =>  $client_id,
            'client_name'   => $client_name
        ]);
    }

    public function clientLocations()
    {
        $locations = $this->clientslocation->getCurrentLocations();

        Config::setJsConfig('curPage', "clients-locations");
        Config::set('curPage', "clients-locations");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/inventory/", Config::get('VIEWS_PATH') . 'inventory/clientsLocations.php',[
            'page_title'    =>  'Client Locations',
            'locations'     =>  $locations
        ]);
    }

    public function scanToInventory()
    {
        $client_id = 0;
        $client_name = "";
        if(!empty($this->request->params['args']))
        {
            if(isset($this->request->params['args']['client']))
            {
                $client_id = $this->request->params['args']['client'];
                $client_name = $this->client->getClientName($client_id);
            }
        }
        Config::setJsConfig('curPage', "scan-to-inventory");
        Config::set('curPage', "scan-to-inventory");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/inventory/", Config::get('VIEWS_PATH') . 'inventory/scanToInventory.php',[
            'page_title'    =>  'Scan Products To Inventory',
            'client_id'     =>  $client_id,
            'client_name'   =>  $client_name
        ]);
    }

    public function moveStock()
    {
        //echo "<pre>",print_r($this->request),"</pre>"; //die();
        if( !isset($this->request->params['args']['product']) )
        {
            return $this->redirector->to(PUBLIC_ROOT."products/view-products");
        }
        $product_id = $this->request->params['args']['product'];
        $product_info = $this->item->getItemById($product_id);
        $error = false;
        $qc_locations = $this->location->getQCLocationsForItem($product_id);
        $location_string = "";
        $item_locations = $this->item->getLocationsForItem($product_id);
        foreach($item_locations as $il)
        {
            $location_string .= $il['location']." (".$il['qty'].")";
            if($il['qc_count'] > 0)
            {
                $location_string .= ", QC(".$il['qc_count'].")";
            }
            if($il['allocated'] > 0)
            {
                $location_string .= ", Allocated(".$il['allocated'].")";
            }
            $location_string .= "<br/>";
        }
        $location_string = rtrim($location_string, "<br/>");
        //render the page
        Config::setJsConfig('curPage', "move-stock");
        Config::set('curPage', "move-stock");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/inventory/", Config::get('VIEWS_PATH') . 'inventory/moveStock.php',
        [
            'product_id'        =>  $product_id,
            'page_title'        =>  "Move Stock For ".$product_info['name']." (".$product_info['sku'].")",
            'product_info'      =>  $product_info,
            'location_string'   =>  $location_string
        ]);

    }

    public function qualityControl()
    {
        //echo "<pre>",print_r($this->request),"</pre>"; //die();
        if( !isset($this->request->params['args']['product']) )
        {
            return $this->redirector->to(PUBLIC_ROOT."products/view-products");
        }
        $product_id = $this->request->params['args']['product'];
        $product_info = $this->item->getItemById($product_id);
        $error = false;
        $qc_locations = $this->location->getQCLocationsForItem($product_id);
        $location_string = "";
        $item_locations = $this->item->getLocationsForItem($product_id);
        foreach($item_locations as $il)
        {
            $location_string .= $il['location']." (".$il['qty'].")";
            if($il['qc_count'] > 0)
            {
                $location_string .= ", QC(".$il['qc_count'].")";
            }
            if($il['allocated'] > 0)
            {
                $location_string .= ", Allocated(".$il['allocated'].")";
            }
            $location_string .= "<br/>";
        }
        $location_string = rtrim($location_string, "<br/>");
        //render the page
        Config::setJsConfig('curPage', "quality-control");
        Config::set('curPage', "quality-control");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/inventory/", Config::get('VIEWS_PATH') . 'inventory/qualityControl.php',
        [
            'product_id'        =>  $product_id,
            'page_title'        =>  "Manage Quality Control For ".$product_info['name'],
            'product_info'      =>  $product_info,
            'location_string'   =>  $location_string,
            'show_remove'       =>  count($qc_locations) > 0
        ]);

    }

    public function addSubtractStock()
    {
        //echo "<pre>",print_r($this->request),"</pre>"; //die();
        if( !isset($this->request->params['args']['product']) )
        {
            return $this->redirector->to(PUBLIC_ROOT."products/view-products");
        }
        $product_id = $this->request->params['args']['product'];
        $product_info = $this->item->getItemById($product_id);
        $location_string = "";
        $item_locations = $this->item->getLocationsForItem($product_id);
        foreach($item_locations as $il)
        {
            $location_string .= $il['location']." (".$il['qty'].")";
            if($il['qc_count'] > 0)
            {
                $location_string .= ", QC(".$il['qc_count'].")";
            }
            if($il['allocated'] > 0)
            {
                $location_string .= ", Allocated(".$il['allocated'].")";
            }
            if($il['oversize'] > 0)
            {
                $location_string .= " - Oversize Location";
            }
            $location_string .= "<br/>";
        }
        $form_array = array(
            'product_id'        =>  $product_id,
            'product_info'      =>  $product_info,
            'location_string'   =>  $location_string
        );
        $location_string = rtrim($location_string, "<br/>");
        //render the page
        Config::setJsConfig('curPage', "add-subtract-stock");
        Config::set('curPage', "add-subtract-stock");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/inventory/", Config::get('VIEWS_PATH') . 'inventory/addSubtractStock.php',
        [
            'product_id'        =>  $product_id,
            'page_title'        =>  "Add or Subtract ".$product_info['name']." (".$product_info['sku'].") from Inventory",
            'location_string'   =>  $location_string,
            'onhand'            =>  $this->item->getStockOnHand($product_id),
            'product_info'      =>  $product_info
        ]);
    }
    public function moveBulkItems()
    {
        $client_id = 0;
        $client_name = "";
        $products = array();
        if(!empty($this->request->params['args']))
        {
            if(isset($this->request->params['args']['client']))
            {
                $client_id = $this->request->params['args']['client'];
                $client_name = $this->client->getClientName($client_id);
                $products = $this->item->getClientInventoryArray($client_id);
            }
        }
        Config::setJsConfig('curPage', "move-bulk-items");
        Config::set('curPage', "move-bulk-items");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/inventory/", Config::get('VIEWS_PATH') . 'inventory/bulkMove.php',[
            'page_title'    =>  'Move Multiple Items',
            'client_id'     =>  $client_id,
            'client_name'   =>  $client_name,
            'products'      =>  $products
        ]);
    }

    public function viewInventory()
    {
        $client_id = 0;
        $active = 1;
        $client_name = "";
        $products = array();
        if(!empty($this->request->params['args']))
        {
            $active = (isset($this->request->params['args']['active']))? $this->request->params['args']['active'] : 1;
            if(isset($this->request->params['args']['client']))
            {
                $client_id = $this->request->params['args']['client'];
                $client_name = $this->client->getClientName($client_id);
                //$products = $this->item->getItemsForClient($client_id, $active);
                $products = $this->item->getClientInventoryArray($client_id, $active);
            }
        }
        Config::setJsConfig('curPage', "view-inventory");
        Config::set('curPage', "view-inventory");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/inventory/", Config::get('VIEWS_PATH') . 'inventory/viewInventory.php',[
            'page_title'    =>  'View Inventory',
            'client_id'     =>  $client_id,
            'client_name'   =>  $client_name,
            'products'      =>  $products,
            'active'        =>  $active
        ]);
    }

    public function clientInventory()
    {
        //up the memory for this
        ini_set('memory_limit', '2048M');
        $client_id = Session::getUserClientId();
        $client_name = $this->client->getClientName($client_id);
        //$products = $this->item->getItemsForClient($client_id);
        $products = $this->item->getClientInventoryArray($client_id);
        Config::setJsConfig('curPage', "client-inventory");
        Config::set('curPage', "client-inventory");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/inventory/", Config::get('VIEWS_PATH') . 'inventory/clientInventory.php',[
            'page_title'    =>  'Current Inventory',
            'client_id'     =>  $client_id,
            'client_name'   =>  $client_name,
            'products'      =>  $products
        ]);
    }

    public function isAuthorized(){
        $role = Session::getUserRole();
        $action = $this->request->param('action');
        $resource = "inventory";
        //admin users
        Permission::allow(['super admin', 'admin'], $resource, ['*']);

        //warehouse users
        Permission::allow('warehouse', $resource, array(
            'index',
            "viewInventory",
            "addSubtractStock",
            "moveStock",
            "qualityControl",
            "scanToInventory",
            "goodsIn",
            "goodsOut",
            "packItemsManage"
        ));

        //client users
        Permission::allow('client', $resource, array(
            'index',
            "clientInventory",
            'expectedShipments',
            'registerNewStock'
        ));

        return Permission::check($role, $resource, $action); 
    }
}
?>