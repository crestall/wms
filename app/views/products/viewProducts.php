<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <input type="hidden" id="client_id" value="<?php echo $client_id;?>" />
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="row">
            <label class="col-md-3">Select a Client</label>
            <div class="col-md-4">
                <select id="client_selector" class="form-control selectpicker" data-style="btn-outline-secondary" data-live-search="true"><option value="0">Select</option><?php echo $this->controller->client->getSelectClients($client_id);?></select>
            </div>
        </div>
        <?php if($client_id > 0):?>
            <div class="row">
                <div class="col-lg-12">
                    <h2>Products for <?php echo $client_name;?></h2>
                </div>
            </div>
            <?php if(count($products)):?>
                <div id="waiting" class="row">
                    <div class="col-lg-12 text-center">
                        <h2>Drawing Table..</h2>
                        <p>May take a few moments</p>
                        <img class='loading' src='/images/preloader.gif' alt='loading...' />
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <?php if($active == 1):?>
                            <p class="text-right"><a class="btn btn-outline-fsg" href="/products/view-products/client=<?php echo $client_id;?>/active=0">View Inactive Products</a></p>
                        <?php else:?>
                            <p class="text-right"><a class="btn btn-outline-fsg" href="/products/view-products/client=<?php echo $client_id;?>">View Active Products</a></p>
                        <?php endif;?>
                    </div>
                </div>
                <div class="row" id="table_holder" style="display:none">
                    <div class="col-lg-12">
                        <table width="100%" class="table-striped table-hover" id="view_items_table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>SKU</th>
                                    <th>Client Product ID</th>
                                    <th>Supplier</th>
                                    <th>Barcode</th>
                                    <th>Dimensions</th>
                                    <th>Weight</th>
                                    <th>Pallet Item</th>
                                    <th>Boxed Item</th>
                                    <th>Requires Bubblewrap</th>
                                    <th style="width:200px">Product Image</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($products as $p):
                                $image = "";
                                    if(preg_match('/https?/i', $p['image']))
                                    {
                                        $image = "<img src='".$p['image']."' class='img-thumbnail img-fluid'>";
                                    }
                                    elseif(!empty($p['image']))
                                    {
                                        $image = "<img src='/images/products/tn_".$p['image']."' class='img-fluid img-thumbnail'>";
                                    }?>
                                    <tr>
                                        <td data-label="Name"><a href="/products/edit-product/product=<?php echo $p['id'];?>"><?php echo $p['name'];?></a></td>
                                        <td data-label="SKU"><?php echo $p['sku'];?></td>
                                        <td data-label="Client Product ID"><?php echo $p['client_product_id'];?></td>
                                        <td data-label="Supplier"><?php echo $p['supplier'];?></td>
                                        <td data-label="Barcode" class="number"><?php echo $p['barcode'];?></td>
                                        <td data-label="Dimensions"><?php echo $p['width']."X".$p['depth']."X".$p['height'];?></td>
                                        <td data-label="Weight" class="number"><?php echo $p['weight'];?> kg</td>
                                        <td data-label="Pallet Item" class='text-center'><?php if($p['palletized'] > 0) echo "Yes"; else echo "No";?></td>
                                        <td data-label="Boxed Item" class='text-center'><?php if($p['boxed_item'] > 0) echo "Yes"; else echo "No";?></td> 
                                        <td data-label="Requires Bubblewrap" class='text-center'><?php if($p['requires_bubblewrap'] > 0) echo "Yes"; else echo "No";?></td>
                                        <td data-label="Product Image"><?php echo $image;?></td>
                                    </tr>
                                <?php endforeach;?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else:?>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="errorbox">
                            <h2><i class='far fa-times-circle'></i>No Products Listed</h2>
                            <p>There does not appear to be any <?php if($active == 1) echo "active"; else echo "inactive";?> products listed for <?php echo $client_name;?></p>
                            <?php if($active == 1):?>
                                <p><a class="btn btn-outline-fsg" href="/products/view-products/client=<?php echo $client_id;?>/active=0">View Inactive Products</a></p>
                            <?php else:?>
                                <p><a class="btn btn-outline-fsg" href="/products/view-products/client=<?php echo $client_id;?>">View Active Products</a></p>
                            <?php endif;?>
                        </div>
                    </div>
                </div>
            <?php endif;?>
        <?php endif;?>
    </div>
</div>

