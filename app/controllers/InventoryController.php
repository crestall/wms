<?php

/**
 * Inventory controller
 *

 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */

class inventoryController extends Controller
{
    public function beforeAction()
    {
        parent::beforeAction();
    }

    public function solarReturns(){
        Config::setJsConfig('curPage', "solar-returns");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/inventory/", Config::get('VIEWS_PATH') . 'inventory/solarReturns.php',[
            'page_title'    =>  'Solar Return Stock',
        ]);
    }

    public function transferLocation()
    {
        Config::setJsConfig('curPage', "transfer-location");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/inventory/", Config::get('VIEWS_PATH') . 'inventory/transferLocation.php',[
            'page_title'    =>  'Transfer a Location',
        ]);
    }

    public function registerNewStock()
    {
        $client_id = Session::getUserClientId();
        $client_name = $this->client->getClientName($client_id);
        Config::setJsConfig('curPage', "register-newstock");
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
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/inventory/", Config::get('VIEWS_PATH') . 'inventory/clientsLocations.php',[
            'page_title'    =>  'Client Locations',
            'locations'     =>  $locations
        ]);
    }

    public function replenishPickface()
    {
        $client_id = 0;
        $client_name = "";
        //$products = array();
        if(!empty($this->request->params['args']))
        {
            if(isset($this->request->params['args']['client']))
            {
                $client_id = $this->request->params['args']['client'];
                $client_name = $this->client->getClientName($client_id);
                //$products = $this->item->getItemsForClient($client_id, $active);
                //$products = $this->item->getClientInventoryArray($client_id, $active);
            }
        }

        Config::setJsConfig('curPage', "replenish-pickface");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/inventory/", Config::get('VIEWS_PATH') . 'inventory/replenishPickface.php',[
            'page_title'    =>  'Replenish Pickfaces',
            'client_id'     =>  $client_id,
            'client_name'   =>  $client_name,
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
            $location_string .= "\n";
        }
        $rows = (count($item_locations) > 5)? count($item_locations) + 2 : 7;
        //render the page
        Config::setJsConfig('curPage', "move-stock");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/inventory/", Config::get('VIEWS_PATH') . 'inventory/moveStock.php',
        [
            'product_id'        =>  $product_id,
            'page_title'        =>  "Move Stock For ".$product_info['name'],
            'product_info'      =>  $product_info,
            'location_string'   =>  $location_string,
            'show_remove'       =>  count($qc_locations) > 0,
            'rows'              =>  $rows
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
            $location_string .= "\n";
        }
        $rows = (count($item_locations) > 5)? count($item_locations) + 2 : 7;
        //render the page
        Config::setJsConfig('curPage', "quality-control");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/inventory/", Config::get('VIEWS_PATH') . 'inventory/qualityControl.php',
        [
            'product_id'        =>  $product_id,
            'page_title'        =>  "Manage Quality Control For ".$product_info['name'],
            'product_info'      =>  $product_info,
            'location_string'   =>  $location_string,
            'show_remove'       =>  count($qc_locations) > 0,
            'rows'              =>  $rows
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
            $location_string .= "\n";
        }
        $rows = (count($item_locations) > 5)? count($item_locations) + 2 : 7;
        $form_array = array(
            'product_id'        =>  $product_id,
            'product_info'      =>  $product_info,
            'location_string'   =>  $location_string,
            'rows'              =>  $rows
        );
        $location_string = rtrim($location_string, "\n");
        $addform = $this->view->render( Config::get('VIEWS_PATH') . "forms/addstock.php", $form_array);
        $subtractform = $this->view->render( Config::get('VIEWS_PATH') . "forms/subtractstock.php",$form_array);

        //render the page
        Config::setJsConfig('curPage', "add-subtract-stock");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/inventory/", Config::get('VIEWS_PATH') . 'inventory/addSubtractStock.php',
        [
            'product_id'        =>  $product_id,
            'page_title'        =>  "Add or Subtract ".$product_info['name']." (".$product_info['sku'].") from Inventory",
            'addform'           =>  $addform,
            'subtractform'      =>  $subtractform,
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
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/inventory/", Config::get('VIEWS_PATH') . 'inventory/bulkMove.php',[
            'page_title'    =>  'Move Multiple Items',
            'client_id'     =>  $client_id,
            'client_name'   =>  $client_name,
            'products'      =>  $products
        ]);
    }

    public function viewInventory()
    {
        if(Session::getUserRole() == "solar admin")
        {
            return $this->viewSolarInventory();
        }
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
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/inventory/", Config::get('VIEWS_PATH') . 'inventory/viewInventory.php',[
            'page_title'    =>  'View Inventory',
            'client_id'     =>  $client_id,
            'client_name'   =>  $client_name,
            'products'      =>  $products,
            'active'        =>  $active
        ]);
    }

    public function viewSolarInventory()
    {
        $active = (isset($this->request->params['args']['active']))? $this->request->params['args']['active'] : 1;
        //$products = $this->item->getClientInventoryArray($this->client->solar_client_id, $active);
        $products = $this->item->getItemsForClient($this->client->solar_client_id);
        Config::setJsConfig('curPage', "view-solar-inventory");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/inventory/", Config::get('VIEWS_PATH') . 'inventory/viewSolarInventory.php',[
            'page_title'    =>  'Current Solar Inventory',
            'client_id'     =>  $this->client->solar_client_id,
            'products'      =>  $products
        ]);
    }

    public function clientInventory()
    {
        $client_id = Session::getUserClientId();
        $client_name = $this->client->getClientName($client_id);
        $products = $this->item->getItemsForClient($client_id);
        Config::setJsConfig('curPage', "client-inventory");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/inventory/", Config::get('VIEWS_PATH') . 'inventory/clientInventory.php',[
            'page_title'    =>  'Current Inventory',
            'client_id'     =>  $client_id,
            'client_name'   =>  $client_name,
            'products'      =>  $products
        ]);
    }

    public function packItemsManage()
    {
        $item_id = 0;
        $client_id = 0;
        $items = array();
        $make_to_location = 0;
        $s = "";
        $available_packs = array();
        $product_info = array();
        if(!empty($this->request->params['args']))
        {
            if(isset($this->request->params['args']['product']))
            {
                $item_id = $this->request->params['args']['product'];
                $product_info = $this->item->getItemById($item_id);
                $client_id = $product_info['client_id'];
                $items = $this->item->getPackItemDetails($item_id);
                $available_packs = $this->item->getAvailableStock($item_id, $this->order->fulfilled_id);
                $make_to_location = $this->item->getPreferredPickLocationId($item_id);
                $s = ($available_packs != 1)? "s" : "";
            }
        }
        Config::setJsConfig('curPage', "pack-items-manage");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/inventory/", Config::get('VIEWS_PATH') . 'inventory/packItemsManage.php',[
            'page_title'        =>  'Make and Break Packs',
            'item_id'           =>  $item_id,
            'items'             =>  $items,
            'available_packs'   =>  $available_packs,
            'make_to_location'  =>  $make_to_location,
            's'                 =>  $s,
            'client_id'         =>  $client_id,
            'product_info'      =>  $product_info
        ]);
    }

    public function isAuthorized(){
        //$role = Session::getUserRole();
        $role = (Session::isAdminUser())? 'admin' : Session::getUserRole();
        if( isset($role) && ($role === "admin"  || $role === "super admin") )
        {
            return true;
        }
        $action = $this->request->param('action');
        $resource = "inventory";

        //warehouse users
        Permission::allow('warehouse', $resource, array(
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
            "clientInventory",
            'expectedShipments',
            'registerNewStock'
        ));
      
        //solar admin users
        Permission::allow('solar admin', $resource, array(
            "viewInventory"
        ));

        return Permission::check($role, $resource, $action);
        return false;
    }
}
?>