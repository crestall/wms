<?php
$order_val = ($order_number == 0)? "": $order_number;

//echo "<pre>",print_r($od),"</pre>";
?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="form-group row">
        <label class="col-form-label col-md-3">Order Number</label>
        <div class="col-md-4">
            <input type="text" class="form-control" name="order_number" id="order_number" placeholder="Order Number" value="<?php echo $order_val;?>" />
        </div>
        <div class="col-md-2">
            <button id="find_order" class="btn btn-sm btn-success">Locate Order Items</button>
        </div>
        <div class="col-md-2">
            <button style="display:none" class="btn btn-sm btn-warning" id="clear_order">Clear Order Items</button>
        </div>
    </div>
    <div id="order_details"></div>
</div>