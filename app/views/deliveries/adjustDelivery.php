<?php
$items = explode("~",$delivery['items']);
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php echo "Delivery<pre>",print_r($delivery),"</pre>";?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <form id="adjust-delivery-items" method="post" action="/form/procAdjustDeliveryItems">
            <div class="row">
                <div class="col-md-12 col-lg-6 mb-3" id="additems">
                    <div class="card h-100 border-secondary order-card">
                        <div class="card-header bg-secondary text-white">
                            Add Items
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-8 offset-2">
                                    <input type="text" class="form-control" id="item_searcher" placeholder="Item name/SKU/Product ID">
                                    <?php echo Form::displayError('items');?>
                                </div>
                            </div>
                            <div id="items_holder"></div>
                            <input type="hidden" name="selected_items" id="selected_items">
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-lg-6 mb-3" id="itemlocations">
                    <div class="card h-100 border-secondary order-card">
                        <div class="card-header bg-secondary text-white">
                            Item Locations
                        </div>
                        <div class="card-body">
                            <?php foreach($items as $i):
                                list($item_id, $item_name, $item_sku, $item_qty, $location_id, $line_id) = explode("|",$i);?>
                                <div class="form-group row">
                                    <label class="col-11 offset-1 col-form-label">Locations With <?php echo $item_qty;?> of <?php echo $item_name." (".$item_sku.")";?></label>
                                    <div class="col-12">
                                        <select  name="allocation[<?php echo $line_id;?>]" class="form-control selectpicker" data-live-search="true" data-style="btn-outline-secondary"  required><option value="">--Select One--</option><?php echo $this->controller->location->getSelectLocationsForDeliveryItem($item_id, $item_qty, $location_id);?></select>
                                    </div>
                                </div>
                            <?php endforeach;?>
                        </div>
                        <div class="card-footer">
                            <input type="hidden" id="delivery_id" name="delivery_id" value="<?php echo $delivery['id'];?>">
                            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                            <div class="col-md-6 offset-6">
                                <button type="submit" class="btn btn-outline-fsg">Submit Changes</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>