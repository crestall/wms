<?php
$ship_to = Form::value('ship_to');
$attention = Form::value('attention');
$address = Form::value('address');
$address2 = Form::value('address2');
$suburb = Form::value('suburb');
$state = Form::value('state');
$postcode = Form::value('postcode');
$country = (empty(Form::value('country')))? "AU" : Form::value('country');
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
             <?php include(Config::get('VIEWS_PATH')."forms/delivery_destinations.php");?>
                <div id="delivery_address_holder">
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
                </div>
        </form>
    </div>
</div>