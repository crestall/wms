<?php

/**
 * Products controller
 *

 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */

class productsController extends Controller
{
    public function beforeAction()
    {
        parent::beforeAction();
    }

    public function addProduct()
    {
        Config::setJsConfig('curPage', "add-product");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/products/", Config::get('VIEWS_PATH') . 'products/addProduct.php',[
            'page_title'    =>  'Add Product'
        ]);
    }

    public function packItemsEdit()
    {
        $client_id = 0;
        $item_id = 0;
        $client_name = "";
        $items = array();
        $sis = 0;
        if(!empty($this->request->params['args']))
        {
            if(isset($this->request->params['args']['product']))
            {
                $item_id = $this->request->params['args']['product'];
                $item_details = $this->item->getItemById($item_id);
                $items = $this->item->getPackItemDetails($item_id);
                if(count($items))
                {
                    $sis = '';
                    foreach($items as $i):
                        $sis .= $i['linked_item_id'].",";
                    endforeach;
                    $sis = rtrim($sis, ",");
                }
                $client_id = $item_details['client_id'];
            }
        }

        //render the page
        Config::setJsConfig('curPage', "pack-items-edit");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/products/", Config::get('VIEWS_PATH') . 'products/packItemsEdit.php',
        [
            'page_title'  =>  "Edit Pack Items",
            'items'       =>  $items,
            'item_id'     =>  $item_id,
            'sis'         =>  $sis,
            'client_id'   =>  $client_id,
            'item_details'
        ]);
    }

    public function collectionsEdit()
    {
        $client_id = 0;
        $item_id = 0;
        $client_name = "";
        $items = array();
        $sis = 0;
        if(!empty($this->request->params['args']))
        {
            if(isset($this->request->params['args']['product']))
            {
                $item_id = $this->request->params['args']['product'];
                $item_details = $this->item->getItemById($item_id);
                $items = $this->item->getCollectionDetails($item_id);
                if(count($items))
                {
                    $sis = '';
                    foreach($items as $i):
                        $sis .= $i['linked_item_id'].",";
                    endforeach;
                    $sis = rtrim($sis, ",");
                }
                $client_id = $item_details['client_id'];
            }
        }

        //render the page
        Config::setJsConfig('curPage', "collections-edit");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/products/", Config::get('VIEWS_PATH') . 'products/collectionsEdit.php',
        [
            'page_title'  =>  "Collection Update",
            'items'       =>  $items,
            'item_id'     =>  $item_id,
            'sis'         =>  $sis,
            'client_id'   =>  $client_id,
            'item_details'
        ]);
    }

    public function editProduct()
    {
        $product_id = $this->request->params['args']['product'];
        $product_info = $this->item->getItemById($product_id);
        $packing_types = $this->item->getPackingTypesForItem($product_id);
        //render the page
        Config::setJsConfig('curPage', "edit-product");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/products/", Config::get('VIEWS_PATH') . 'products/editProduct.php',
        [
            'product'       =>  $product_info,
            'page_title'    =>  "Edit Product",
            'packing_types' =>  $packing_types
        ]);
    }

    public function viewProducts()
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
                $products = $this->item->getItemsForClient($client_id, $active);
            }
        }
        Config::setJsConfig('curPage', "view-products");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/products/", Config::get('VIEWS_PATH') . 'products/viewProducts.php',[
            'page_title'    =>  'View Products',
            'client_id'     =>  $client_id,
            'client_name'   =>  $client_name,
            'products'      =>  $products,
            'active'        =>  $active
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
        $resource = "products";

        //warehouse users
        Permission::allow('warehouse', $resource, array(
            "addProduct",
            "viewProducts",
            "editProduct"
        ));

        //solar admin users
        Permission::allow('solar admin', $resource, array(
            "viewProducts"
        ));

        return Permission::check($role, $resource, $action);
        return false;
    }
}
?>