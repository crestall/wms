<div class="page-wrapper">
    <div class="row">
        <form id="adjust-allocation" method="post" action="/form/procAdjustAllocations">
            <?php foreach($items as $item):?>
                <div class="form-group row">
                    <label class="col-md-8 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> <?php echo $item['name']." (".$item['qty'].")";?></label>
                    <div class="col-md-4">
                        <select id="allocation_id_<?php echo $item['item_id'];?>" name="allocation_id[<?php echo $item['item_id'];?>]" class="form-control selectpicker" data-live-search="true" required><option value="">--Select One--</option><?php echo $this->controller->location->getSelectItemInLocations($item['item_id'], $item['location_id'], false, $item['qty']) ;?></select>
                        <?php echo Form::displayError("allocation_id_{$item['item_id']}");?>
                    </div>
                </div>
            <?php endforeach;?>
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