<div id="page-wrapper">
    <input type="hidden" id="client_id" value="<?php echo $client_id;?>" />
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="row">
        <div class="col-lg-12">
            <div class="form-group">
                <div class="col-lg-2">&nbsp;</div>
                <label class="col-lg-2">Select a Client</label>
                <div class="col-lg-4">
                    <select id="client_selector" class="form-control selectpicker"><option value="0">Select</option><?php echo $this->controller->client->getSelectClients($client_id);?></select>
                </div>
            </div>
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
                        <p class="text-right"><a class="btn btn-warning" href="/products/view-products/client=<?php echo $client_id;?>/active=0">View Inactive Products</a></p>
                    <?php else:?>
                        <p class="text-right"><a class="btn btn-primary" href="/products/view-products/client=<?php echo $client_id;?>">View Active Products</a></p>
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
                                <th>Supplier</th>
                                <th>Barcode</th>
                                <th>Dimensions</th>
                                <th>Weight</th>
                                <th>Pallet Item</th>
                                <th>Double Bay</th>
                                <th>Requires Bubblewrap</th>
                                <th>Preferred Pick location</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($products as $p):
                                $ppl = $this->controller->location->getLocationName($p['preferred_pick_location_id']);
                                //$ppl = ($p['preferred_pick_location_id'] > 0)? $p['preferred_pick_location_id'] : "no";
                                //echo "<pre>",print_r($p),"</pre>";
                                ?>
                                <tr>
                                    <td data-label="Name"><a href="/products/edit-product/product=<?php echo $p['id'];?>"><?php echo $p['name'];?></a></td>
                                    <td data-label="SKU"><?php echo $p['sku'];?></td>
                                    <td data-label="Supplier"><?php echo $p['supplier'];?></td>
                                    <td data-label="Barcode" class="number"><?php echo $p['barcode'];?></td>
                                    <td data-label="Dimensions"><?php echo $p['width']."X".$p['depth']."X".$p['height'];?></td>
                                    <td data-label="Weight" class="number"><?php echo $p['weight'];?> kg</td>
                                    <td data-label="Pallet Item" class='text-center'><?php if($p['palletized'] > 0) echo "Yes"; else echo "No";?></td>
                                    <td data-label="Double Bay" class='text-center'><?php if($p['double_bay'] > 0) echo "Yes"; else echo "No";?></td>
                                    <td data-label="Requires Bubblewrap" class='text-center'><?php if($p['requires_bubblewrap'] > 0) echo "Yes"; else echo "No";?></td>
                                    <td data-label="Preferred pick Location"><?php echo $ppl;?></td>
                                    <td></td>
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
                            <p><a class="btn btn-warning" href="/products/view-products/client=<?php echo $client_id;?>/active=0">View Inactive Products</a></p>
                        <?php else:?>
                            <p><a class="btn btn-primary" href="/products/view-products/client=<?php echo $client_id;?>">View Active Products</a></p>
                        <?php endif;?>
                    </div>
                </div>
            </div>
        <?php endif;?>
    <?php endif;?>
</div>

