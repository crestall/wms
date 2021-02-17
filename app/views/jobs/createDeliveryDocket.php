<?php
$ship_to    = (empty(Form::value('ship_to')))?  $job['ship_to']      : Form::value('ship_to');
$address    = empty(Form::value('address'))?    $job['address']      : Form::value('address');
$address2   = empty(Form::value('address2'))?   $job['address_2']    : Form::value('address2');
$suburb     = empty(Form::value('suburb'))?     $job['suburb']       : Form::value('suburb');
$state      = empty(Form::value('state'))?      $job['state']        : Form::value('state');
$postcode   = empty(Form::value('postcode'))?   $job['postcode']     : Form::value('postcode');
$country    = empty(Form::value('country'))?    $job['country']      : Form::value('country');
$delivery_instructions = empty(Form::value('delivery_instructions'))? $job['delivery_instructions'] : Form::value('delivery_instructions');
$attention = empty(Form::value('attention'))? $job['attention'] : Form::value('attention');
$job_title = empty(Form::value('job_title'))? $job['description'] : Form::value('job_title');
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <?php //echo "<pre>",print_r($job),"</pre>";?>
        <form id="create_delivery_docket" method="post" action="/pdf/createDeliveryDocket">
            <div class="form-group row">
                <label class="col-md-3">Send As</label>
                <div class="col-md-4">
                    <select id="sender_id" name="sender_id" class="form-control selectpicker" data-style="btn-outline-secondary"><?php echo $this->controller->deliverydocketsender->getSelectSender(Form::value('sender_id'));?></select>
                    <?php echo Form::displayError('sender_id');?>
                </div>
            </div>
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
                    <textarea class="form-control" name="delivery_instructions" id="delivery_instructions" placeholder="Instructions For Driver"><?php echo Form::value('delivery_instructions');?></textarea>
                </div>
            </div>
            <?php include(Config::get('VIEWS_PATH')."forms/address_auonly.php");?>
            <div class="form-group row">
                <label class="col-md-3">Purchase Order Number</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="po_number" id="po_number" value="<?php echo Form::value('po_number');?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3">Job Title</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="job_title" id="job_title" value="<?php echo $job_title;?>" />
                </div>
            </div>
        </form>
    </div>
</div>