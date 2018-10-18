<?php

?>
<div class="row">
    <div class="col-md-12">
        <h2>Items In Order</h2>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <form id="order_packing" method="post" action="/form/procPackOrder">
                <?php foreach($items as $i):?>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label"><?php echo $i['name'];?></label>
                        <div class="col-md-4">
                            <input type="text" data-ordercount="<?php echo $i['qty'];?>" class="form-control required number" disabled name="packed[<?php echo $i['item_id'];?>]" id="packed_<?php echo $i['item_id'];?>" placeholder="Enter Amount Packed After Scanning Barcode" />
                        </div>
                        <i class="fas fa-check-circle fa-2x text-success col-md-1" style="display:none" id="good_<?php echo $i['item_id'];?>"></i>
                        <i class="fas fa-times-circle fa-2x text-danger col-md-1" style="display:none" id="bad_<?php echo $i['item_id'];?>"></i>
                    </div>
                <?php endforeach;?>
                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                <input type="hidden" name="client_id" id="client_id" value="<?php echo $order['client_id'];?>" />
                <input type="hidden" name="order_id" id="order_id" value="<?php echo $order['id'];?>" />
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">&nbsp;</label>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary" id="submit_button" disabled>Pack Order</button>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-danger" id="reset">Reset Page</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>