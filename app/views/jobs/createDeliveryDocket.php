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
        <?php echo "<pre>",print_r($job),"</pre>";?>
        <form id="create_delivery_docket" method="post" action="/pdf/createDeliveryDocket">
            <div class="form-group row">
                <label class="col-md-3">Send As</label>
                <div class="col-md-4">
                    <select id="sender_id" name="sender_id" class="form-control selectpicker" data-style="btn-outline-secondary"><?php echo $this->controller->deliverydocketsender->getSelectSender(Form::value('sender_id'));?></select>
                    <?php echo Form::displayError('sender_id');?>
                </div>
            </div>
            <div class="form-group row">
                <div class=" offset-1 col-5 checkbox checkbox-default ">
                    <input class="form-check-input styled send_to_address" type="checkbox" id="send_to_customer" name="send_to_customer" />
                    <label for="send_to_customer">Send to Customer</label>
                </div>
                <div class="col-6 checkbox checkbox-default">
                    <input class="form-check-input styled send_to_address" type="checkbox" id="send_to_finisher" name="send_to_finisher" />
                    <label for="send_to_finisher">Send to Finisher One</label>
                </div>
                <div class="offset-1 col-5 checkbox checkbox-default">
                    <input class="form-check-input styled send_to_address" type="checkbox" id="send_to_finisher2" name="send_to_finisher2" />
                    <label for="send_to_finisher2">Send to Finisher Two</label>
                </div>
                <div class="col-md-6 checkbox checkbox-default">
                    <input class="form-check-input styled send_to_address" type="checkbox" id="send_to_finisher3" name="send_to_finisher3" />
                    <label for="send_to_finisher3">Send to Finisher Three</label>
                </div>
                <div class="offset-1 col-5 checkbox checkbox-default">
                    <input class="form-check-input styled send_to_address" type="checkbox" id="held_in_store" name="held_in_store" />
                    <label for="held_in_store">Held In Store</label>
                </div>
            </div>
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