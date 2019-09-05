<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
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
                    <p class="text-right"><a class="btn btn-warning" href="/products/view-products/active=0">View Inactive Products</a></p>
                <?php else:?>
                    <p class="text-right"><a class="btn btn-primary" href="/products/view-products">View Active Products</a></p>
                <?php endif;?>
            </div>
        </div>
        <div class="row" id="table_holder" style="display:none">
            <div class="col-lg-12">
                <table width="100%" class="table-striped table-hover" id="view_solar_items_table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>SKU</th>
                            <th>Supplier</th>
                            <th>Owner</th>
                            <th>Barcode</th>
                            <th>Low Stock Level</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($products as $p):
                            $owner = ($p['solar_type_id'] > 0)?$this->controller->solarordertype->getSolarOrderType($p['solar_type_id']): "";?>
                            <tr>
                                <td data-label="Name"><a href="/products/edit-product/product=<?php echo $p['id'];?>"><?php echo $p['name'];?></a></td>
                                <td data-label="SKU"><?php echo $p['sku'];?></td>
                                <td data-label="Supplier"><?php echo $p['supplier'];?></td>
                                <td data-label="Owner"><?php echo $owner;?></td>
                                <td data-label="Barcode" class="number"><?php echo $p['barcode'];?></td>
                                <td>
                                    <p><input type="text" class="form-control number" id="lowstock_<?php echo $p['id'];?>" name="lowstock_<?php echo $p['id'];?>" value="<?php echo $p['low_stock_warning'];?>" placeholder="Enter Min Value" /></p>
                					<p><button class="btn btn-primary btn-sm update_product" data-productid="<?php echo $p['id'];?>">Update</button> </p>
                                    <div class="errorbox" style="display:none;" id="error_<?php echo$p['id'];?>">Only input whole, positive numbers please</div>
                                	<div class="feedbackbox" style="display:none;" id="feedback_<?php echo$p['id'];?>">Product warning level updated</div>
                                </td>
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
                    <p>There does not appear to be any <?php if($active == 1) echo "active"; else echo "inactive";?> products listed.</p>
                    <?php if($active == 1):?>
                        <p><a class="btn btn-warning" href="/products/view-products/active=0">View Inactive Products</a></p>
                    <?php else:?>
                        <p><a class="btn btn-primary" href="/products/view-products">View Active Products</a></p>
                    <?php endif;?>
                </div>
            </div>
        </div>
    <?php endif;?>
</div>

