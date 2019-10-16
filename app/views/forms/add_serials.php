<?php
//echo "<pre>",print_r($items),"</pre>";
?>
<form id="add_serials" method="post" action="/form/procAddSerials">
    <?php foreach($items as $item):
        $serials = $this->controller->orderitemserials->getRecordedSerials($item['order_id'], $item['item_id']);
        //echo "<pre>",print_r($serials),"</pre>";
        $c = 1;
        while($c <= $item['qty']){?>
            <div class="form-group row">
                <label class="col-md-5 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> <?php echo $item['name']." (".$item['sku'].")";?></label>
                <div class="col-md-3"><input type="text" name="serial[<?php echo $item['line_id'];?>]" class="form-control required" placeholder="Serial Number" /></div>
            </div>
        <?php ++$c; };?>
    <?php endforeach;?>
    <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
    <input type="hidden" name="order_id" id="order_id" value="<?php echo $order_id;?>" />
    <div class="form-group row">
        <label class="col-md-5 col-form-label">&nbsp;</label>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary" id="add_serials_submitter">Update Order</button>
        </div>
    </div>
</form>

