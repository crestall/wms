<?php
if(!empty($order['company_name']))
{
    $ship_to    = (empty(Form::value('ship_to')))?  $order['company_name']      : Form::value('ship_to');
    $attention  = empty(Form::value('attention'))? $order['ship_to'] : Form::value('attention');
}
else
{
    $ship_to    = (empty(Form::value('ship_to')))?  $order['ship_to']      : Form::value('ship_to');
    $attention  = empty(Form::value('attention'))? $order['ship_to'] : Form::value('attention');
}
$address    = empty(Form::value('address'))?    $order['address']      : Form::value('address');
$address2   = empty(Form::value('address2'))?   $order['address_2']    : Form::value('address2');
$suburb     = empty(Form::value('suburb'))?     $order['suburb']       : Form::value('suburb');
$state      = empty(Form::value('state'))?      $order['state']        : Form::value('state');
$postcode   = empty(Form::value('postcode'))?   $order['postcode']     : Form::value('postcode');
$country    = empty(Form::value('country'))?    $order['country']      : Form::value('country');
$delivery_instructions = empty(Form::value('delivery_instructions'))? $order['instructions'] : Form::value('delivery_instructions');
$job_number = empty(Form::value('job_number'))? $order['order_number'] : Form::value('job_number');
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <?php //echo "<pre>",print_r($order),"</pre>";?>
        <form id="create_warehouse_delivery_docket" target="_blank" method="post">
            <input type="hidden" name="sender_id" id="sender_id" value="1">
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Deliver To</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="ship_to" id="ship_to" value="<?php echo $ship_to;?>" />
                    <?php echo Form::displayError('ship_to');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Attention</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="attention" id="attention" value="<?php echo $attention;?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Delivery Instructions</label>
                <div class="col-md-4">
                    <textarea class="form-control" name="delivery_instructions" id="delivery_instructions" placeholder="Instructions For Driver"><?php echo $delivery_instructions;?></textarea>
                </div>
            </div>
            <?php include(Config::get('VIEWS_PATH')."forms/address_auonly.php");?>
            <div class="form-group row">
                <label class="col-md-3">Job Title</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="job_number" id="job_number" value="<?php echo $job_number;?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3">Quantity</label>
                <div class="col-md-4">
                    <input type="text" class="form-control number" name="quantity" id="quantity" value="<?php echo Form::value('quantity');?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3">Box Count</label>
                <div class="col-md-4">
                    <input type="text" class="form-control number count" name="box_count" id="box_count" value="<?php echo Form::value('box_count');?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3">Pallet Count</label>
                <div class="col-md-4">
                    <input type="text" class="form-control number count" name="pallet_count" id="pallet_count" value="<?php echo Form::value('pallet_count');?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3">Packed As</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="packed_as" id="packed_as" value="<?php echo Form::value('packed_as');?>" />
                    <span class="inst">(eg cartons, pallets, skids, etc)</span>
                </div>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <input type="hidden" name="order_id" id="order_id" value="<?php echo $order['id'];?>" >
                <input type="hidden" name="order_number" id="order_number" value="<?php echo $order['order_number'];?>" >
            <div class="form-group row">
                <div class="col-md-4 offset-md-3">
                    <button type="submit" class="btn btn-outline-fsg" id="docket_submitter" formaction="/pdf/createDeliveryDocket">Create Delivery Docket</button>
                </div>
            </div>
        </form>
    </div>
</div>