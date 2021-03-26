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
$po_number = empty(Form::value('po_number'))? $job['customer_po_number'] : Form::value('po_number');
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="row">
            <div class="col-lg-12">
                <div class="noticebox">
                    <h4>When delivering a job, please make sure to</h4>
                    <ul>
                        <li>Print out a DELIVERY DOCKET.</li>
                        <li>Write your name and the date of delivery on the JOB BAG.</li>
                        <li>Place the JOB BAG in the DESPATCH tray (warehouse).</li>
                        <li>Mark the JOB STATUS as DELIVERING in the system.</li>
                        <li>Once delivered, return the SIGNED DELIVERY DOCKET and place it in the tray (warehouse).</li>
                    </ul>
                </div>
            </div>
        </div>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <?php //echo "<pre>",print_r($job),"</pre>";?>
        <form id="create_delivery_docket" target="_blank" method="post">
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
                <label class="col-md-3">Customer PO Number</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="po_number" id="po_number" value="<?php echo $po_number;?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3">Job Title</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="job_title" id="job_title" value="<?php echo $job_title;?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3">Quantity</label>
                <div class="col-md-4">
                    <input type="text" class="form-control number" name="quantity" id="quantity" value="<?php echo Form::value('quantity');?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3">Quantity Per Box</label>
                <div class="col-md-4">
                    <input type="text" class="form-control number" name="per_box" id="per_box" value="<?php echo Form::value('per_box');?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3">Box Count</label>
                <div class="col-md-4">
                    <input type="text" class="form-control number" readonly name="box_count" id="box_count" value="<?php echo Form::value('box_count');?>" />
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
            <input type="hidden" name="job_id" id="job_id" value="<?php echo $job['id'];?>" >
                <input type="hidden" name="job_number" id="job_number" value="<?php echo $job['job_id'];?>" >
            <div class="form-group row">
                <div class="col-md-4 offset-md-2">
                    <button type="submit" class="btn btn-outline-info" id="label_submitter" formaction="/pdf/createDeliveryLabels">Create Labels</button>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-outline-fsg" id="docket_submitter" formaction="/pdf/createDeliveryDocket">Create Delivery Docket</button>
                </div>
            </div>
        </form>
    </div>
</div>