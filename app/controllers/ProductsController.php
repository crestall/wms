<?php

/**
 * Products controller
 *

 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class ProductsController extends Controller
{
    public function beforeAction()
    {
        parent::beforeAction();
    }

    public function index()
    {
        //set the page name for menu display
        Config::setJsConfig('curPage', 'products-index');
        parent::displayIndex(get_class());
    }

    public function addProduct()
    {
        Config::setJsConfig('curPage', "add-product");
        Config::set('curPage', "add-product");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/products/", Config::get('VIEWS_PATH') . 'products/addProduct.php',[
            'page_title'    =>  'Add Product',
            'pht'           =>  ": Add Product",
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
            if(isset($this->request->params['args']['client']))
            {
                $client_id = $this->request->params['args']['client'];
                $client_name = $this->client->getClientName($client_id);
                if(isset($this->request->params['args']['product']))
                {
                    $item_id = $this->request->params['args']['product'];
                    //$item_details = $this->item->getItemById($item_id);
                    $items = $this->item->getCollectionDetails($item_id);
                    if(count($items))
                    {
                        $sis = '';
                        foreach($items as $i):
                            $sis .= $i['linked_item_id'].",";
                        endforeach;
                        $sis = rtrim($sis, ",");
                    }
                }
            }
        }
        //render the page
        Config::setJsConfig('curPage', "collections-edit");
        Config::set('curPage', "collections-edit");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/products/", Config::get('VIEWS_PATH') . 'products/collectionsEdit.php',
        [
            'page_title'    =>  "Collection Update",
            'items'         =>  $items,
            'item_id'       =>  $item_id,
            'sis'           =>  $sis,
            'client_id'     =>  $client_id,
            'client_name'   => $client_name
        ]);
    }

    public function editProduct()
    {
        $product_id = $this->request->params['args']['product'];
        $product_info = $this->item->getItemById($product_id);
        $packing_types = $this->item->getPackingTypesForItem($product_id);
        //render the page
        Config::setJsConfig('curPage', "edit-product");
        Config::set('curPage', "edit-product");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/products/", Config::get('VIEWS_PATH') . 'products/editProduct.php',
        [
            'product'       =>  $product_info,
            'page_title'    =>  "Edit Product",
            'pht'           =>  ": Editing ".$product_info['name'],
            'packing_types' =>  $packing_types
        ]);
    }

    public function clientProductEdit()
    {
        $error = false;
        $product_info = array();
        $product_name = "";
        if(!isset($this->request->params['args']['product']))
        {
            //no product id to update
            //return (new ErrorsController())->error(400)->send();
            $error = "no_product_id";
        }
        $client_id = Session::getUserClientId();
        $client_name = $this->client->getClientName($client_id);
        $product_id = $this->request->params['args']['product'];
        $product_info = $this->item->getItemById($product_id);
        if(empty($product_info))
        {
            //no job data found
            //return (new ErrorsController())->error(404)->send();
            $error = "no_product";
        }
        else
        {
            $product_name = $product_info['name'];
        }
        //render the page
        Config::setJsConfig('curPage', "client-product-edit");
        Config::set('curPage', "client-product-edit");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/products/", Config::get('VIEWS_PATH') . 'products/clientProductEdit.php',
        [
            'product'       => $product_info,
            'page_title'    => "Update Product: ".$product_name,
            'pht'           => ": Updating ".$product_name,
            'error'         => $error
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
                ViewProducts::setClientId($client_id);
                ViewProducts::setActive($active);
            }
        }
        Config::setJsConfig('curPage', "view-products");
        Config::set('curPage', "view-products");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/products/", Config::get('VIEWS_PATH') . 'products/viewProducts.php',[
            'page_title'    =>  'View Products',
            'pht'           =>  ": View Products",
            'client_id'     =>  $client_id,
            'client_name'   =>  $client_name,
            'active'        =>  $active
        ]);
    }

    public function isAuthorized(){
        $role = Session::getUserRole();
        $action = $this->request->param('action');
        $resource = "products";
        //admin users
        Permission::allow(['super admin', 'admin'], $resource, ['*']);

        //warehouse users
        Permission::allow('warehouse', $resource, array(
            "index",
            "addProduct",
            "viewProducts",
            "editProduct"
        ));

        //client users
        Permission::allow('client', $resource, array(
            'clientProductEdit',
            "editProduct"
        ));

        return Permission::check($role, $resource, $action);
    }
}
?>