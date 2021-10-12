<?php
$pickup_id = $pickup['id'];
$client_id = $pickup['client_id'];
$items = explode("~",$pickup['items']);
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php echo "<pre>",print_r($pickup),"</pre>";?>
        <div id="putaway_holder" class="m-y-2 p-2 border border-secondary rounded bg-light">
            <div id="cover">
                <form id="pickup_putaways" method="post" action="/form/procPickupPutaways">
                    <h3 class="text-center">Put Away Items</h3>
                    <?php foreach($items as $i):
                        list($item_id, $item_name, $item_sku, $pallet_count) = explode("|",$i);
                        $pc = 1;
                        while($pc <= $pallet_count):?>
                            <div class="border-bottom border-secondary border-bottom-dashed pt-2">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        Pallet <?php echo $pc;?> of <?php echo $item_name." (".$item_sku.")";?>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <input name="items[<?php echo $item_id;?>]['qty']" class="form-control required number" placeholder="qty">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <select name="items[<?php echo $item_id;?>]['location_id']" class="form-control selectpicker" data-style="btn-outline-secondary" required><option value="0">Select Location</option></select>
                                    </div>
                                </div>
                            </div>
                        <?php ++$pc; endwhile;?>
                    <?php endforeach;?>
                    <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                    <input type="hidden" name="client_id" id="client_id" value="<?php echo $client_id;?>" />
                    <input type="hidden" name="pickup_id" id="pickup_id" value="<?php echo $pickup_id;?>" />
                    <div class="form-group row">
                        <div class="offset-md-6 col-md-4 pt-2">
                            <button type="submit" class="btn btn-sm btn-outline-secondary">Put Items Away</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>