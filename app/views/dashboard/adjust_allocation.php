<div class="page-wrapper">
    <div class="row" id="feedback_holder" style="display:none"></div>
    <div class="row">
        <form id="adjust-allocation" method="post" action="/form/procAdjustAllocations">
            <?php $c = 0; foreach($items as $item):?>
                <div class="form-group row">
                    <label class="col-md-8 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> <?php echo $item['name']." (".$item['qty'].")";?></label>
                    <div class="col-md-4">
                        <select id="allocation_id_<?php echo $c;?>" name="allocation[<?php echo $item['item_id'];?>][<?php echo $c;?>][location_id]" class="form-control selectpicker" data-live-search="true" required><option value="">--Select One--</option><?php echo $this->controller->location->getSelectItemInLocations($item['item_id'], $item['location_id'], false, $item['qty']) ;?></select>
                    </div>
                </div>
                <input type="hidden" name="allocation[<?php echo $item['item_id'];?>][<?php echo $c;?>][qty]" value="<?php echo $item['qty'];?>" />
            <?php ++$c; endforeach;?>
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <input type="hidden" name="order_id" value="<?php echo $order_id;?>" />
            <div class="form-group row">
                <label class="col-md-8 col-form-label">&nbsp;</label>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>