<?php
$pickup_id = $pickup['id'];
$client_id = $pickup['client_id'];
$items = explode("~",$pickup['items']);
$cover_class = (!empty($pickup['vehicle_type']))? "" : "covered";
$repalletize_charge = empty(Form::value('repalletize_charge'))? "0.00" : Form::value('repalletize_charge');
$rewrap_charge = empty(Form::value('rewrap_charge'))? "0.00" : Form::value('rewrap_charge');
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php //echo "<pre>",print_r($pickup),"</pre>";?>
        <div class="row">
            <div class="form_instructions col">
                <h3>Instructions</h3>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item ">
                        A Vehicle type must be chosen before the Pickup Docket can be printed
                    </li>
                    <li class="list-group-item">
                        The Pickup Docket must be printed before the Put Away Items form gets activated
                    </li>
                    <li class="list-group-item">
                        Clicking the &ldquo;Print Pickup Docket&rdquo; button will assign the selected vehicle type
                    </li>
                    <li class="list-group-item">
                        Read the number of items on each pallet from its docket/label and select the location it has been put in
                    </li>
                    <li class="list-group-item">
                        Current repalletizing charge is $20 per pallet
                    </li>
                </ul>
            </div>
        </div>
        <div id="print_docket_holder" class="mt-2 mb-3 p-2 border border-secondary rounded bg-fsg">
            <h3 class="text-center">Assign Vehicle</h3>
            <div class="m-2 p-2 border border-secondary rounded bg-light">
                <h4 class="text-center">Print Pickup Docket</h4>
                <div class="row">
                    <div class="offset-md-2 col-md-4 mb-3">
                        <select name="vehicle_type" class="selectpicker vehicle_type" data-pickupid='<?php echo $pickup_id;?>' data-style="btn-outline-secondary btn-sm" <?php if($pickup['private_courier'] > 0) echo "disabled";?>><option value="0">--Select Vehicle Type--</option><?php echo Utility::getVehicleTypeSelect($pickup['vehicle_type']);?></select>
                    </div>
                    <div class="col-md-4">
                        <a id="print_docket_<?php echo $pickup_id;?>" class="btn btn-block btn-outline-secondary print_docket <?php if($pickup['private_courier'] > 0) echo "disabled";?>" role="button" target="_blank" href="/pdf/printPickupDocket/pickup=<?php echo $pickup_id;?>/vehicle=<?php echo $pickup['vehicle_type'];?>">Print Pickup Docket</a>
                    </div>
                </div>
            </div>
        </div>
        <div id="putaway_holder" class="mt-2 mb-3 p-2 border border-secondary rounded bg-fsg">
            <h3 class="text-center">Finalise Pickup</h3>
            <div id="cover" class="<?php echo $cover_class;?>">
                <form id="pickup_putaways" method="post" action="/form/procPickupPutaways">
                    <div class="m-2 p-2 border border-secondary rounded bg-light">
                        <h4 class="text-center">Put Away Items</h4>
                        <?php if(Form::$num_errors > 0) :?>
                            <div class='row errorbox'>
                                <div class="col-4 text-right">
                                    <i class="fad fa-exclamation-triangle fa-6x"></i>
                                </div>
                                <div class="col-8">
                                    <h2>An Error Was Found In The Form</h2>
                                    <p><?php echo Form::displayError('item_errors');?></p>
                                    <p><?php echo Form::displayError('repalletize_charge');?></p>
                                </div>
                                <?php //echo "<pre>",print_r(Form::$values),"</pre>";?>
                            </div>
                        <?php endif;?>
                        <?php $ii = 0;
                        foreach($items as $i):
                            list($item_id, $item_name, $item_sku, $pallet_count) = explode("|",$i);
                            $pc = 1;
                            while($pc <= $pallet_count):?>
                                <input type="hidden" name="locations[<?php echo $ii;?>][item_id]" value="<?php echo $item_id;?>">
                                <div class="pallet_holder border-bottom border-secondary border-bottom-dashed pt-2">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="col-12">&nbsp;</label>
                                            Pallet <?php echo $pc;?> of <?php echo $item_name." (".$item_sku.")";?>
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label class="col-12">Qty</label>
                                            <input name="locations[<?php echo $ii;?>][qty]" class="form-control required digits" value="<?php echo Form::value("locations,$ii,qty");?>">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="col-12">Pallet Size</label>
                                            <select id="size_<?php echo $ii;?>" name="locations[<?php echo $ii;?>][size]" class="form-control selectpicker pallet_size" data-live-search="true" data-style="btn-outline-secondary" required><option value="0">Select Size</option><?php echo Utility::getPalletSizeSelect(Form::value("locations,$ii,size"));?></select>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="col-12">Location</label>
                                            <select id="location_id_<?php echo $ii;?>" name="locations[<?php echo $ii;?>][location_id]" class="form-control selectpicker pallet_location" data-live-search="true" data-style="btn-outline-secondary" required><option value="0">--Select One--</option><?php echo $this->controller->location->getSelectEmptyLocations(Form::value("locations,$ii,location_id"));?></select>
                                        </div>
                                    </div>
                                </div>
                            <?php ++$pc; ++$ii; endwhile;?>
                        <?php endforeach;?>
                    </div>
                    <div class="m-2 p-2 border border-secondary rounded bg-light">
                        <h4 class="text-center">Miscellaneous Charges</h4>
                        <div class="form-group row">
                            <label class="col-md-4">Repalletizing Charge</label>
                            <div class="col-md-2 input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-dollar-sign"></i></span>
                                </div>
                                <input type="text" class="form-control" name="repalletize_charge" id="repalletize_charge" value="<?php echo $repalletize_charge;?>">
                                <span class="inst">DO NOT include GST - this is added automatically later</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-4">Rewrapping Charge</label>
                            <div class="col-md-2 input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-dollar-sign"></i></span>
                                </div>
                                <input type="text" class="form-control" name="rewrap_charge" id="rewrap_charge" value="<?php echo $rewrap_charge;?>">
                                <span class="inst">DO NOT include GST - this is added automatically later</span>
                            </div>
                        </div>
                    </div>
                    <div class="m-2 p-2 border border-secondary rounded bg-light">
                        <h4 class="text-center">Finalise Pickup</h4>
                        <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                        <input type="hidden" name="client_id" id="client_id" value="<?php echo $client_id;?>" />
                        <input type="hidden" name="pickup_id" id="pickup_id" value="<?php echo $pickup_id;?>" />
                        <div class="form-group row">
                            <div class="offset-md-4 col-sm-md pt-2">
                                <button type="submit" class="btn btn-outline-secondary">Submit Form</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>