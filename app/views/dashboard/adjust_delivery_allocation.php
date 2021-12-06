<?php
$items = explode("~",$dd['items']);
?>
<div class="page-wrapper">
    <div class="row" id="feedback_holder" style="display:none"></div>
    <form id="adjust-delivery-allocation" method="post" action="/form/procAdjustDeliveryAllocations">
        <div class="p-3 pb-0 mb-2 rounded mid-grey">
            <?php foreach($items as $i):
                list($item_id, $item_name, $item_sku, $item_qty, $location_id, $line_id) = explode("|",$i);?>
                <div class="form-group row">
                    <label class="col-10 offset-1 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Locations With <?php echo $item_qty;?> of <?php echo $item_name." (".$item_sku.")";?></label>
                    <div class="col-12">
                        <select  name="allocation[<?php echo $line_id;?>]" class="form-control selectpicker" data-live-search="true" data-style="btn-outline-secondary"  required><option value="">--Select One--</option><?php echo $this->controller->location->getSelectLocationsForDeliveryItem($item_id, $item_qty, $location_id);?></select>
                    </div>
                </div>
            <?php endforeach;?>
        </div>
        <input type="hidden" id="delivery_id" name="delivery_id" value="<?php echo $dd['id'];?>">
        <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
        <div class="form-group row">
            <div class="col-md-3 offset-9">
                <button type="submit" class="btn btn-outline-fsg">Submit</button>
            </div>
        </div>
    </form>
</div>