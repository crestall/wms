<?php
$pickup_address = (empty(Form::value('pickup_address')))? (isset($client['address']))?$client['address']: "" : Form::value('pickup_address');
$pickup_address2 = (empty(Form::value('pickup_address2')))? (isset($client['address_2']))?$client['address_2']:"" : Form::value('pickup_address2');
$pickup_suburb = (empty(Form::value('pickup_suburb')))? (isset($client['suburb']))?$client['suburb']:"" : Form::value('pickup_suburb');
$pickup_state = (empty(Form::value('pickup_state')))? (isset($client['state']))?$client['state']:"" : Form::value('pickup_state');
$pickup_postcode = (empty(Form::value('pickup_postcode')))? (isset($client['postcode']))?$client['postcode']:"" : Form::value('pickup_postcode');
$manually_entered = (!Session::isDeliveryClientUser())? 1:0;
?>
<?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
<form id="book_pickup" method="post" action="/form/procBookAPickup">
    <div class="row">
        <div class="col-md-12 col-lg-6 mb-3" id="itemslist">
            <div class="card h-100 border-secondary order-card">
                <div class="card-header bg-secondary text-white">
                    Items To Collect
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-8 offset-2">
                            <input type="text" class="form-control" id="item_searcher" placeholder="Item name/SKU/Product ID">
                            <?php echo Form::displayError('items');?>
                        </div>
                    </div>
                    <div id="feedback_holder"></div>
                    <div id="form_holder"></div>
                    <div id="items_holder"></div>
                    <input type="hidden" name="selected_items" id="selected_items">
                </div>
            </div>
        </div>
        <div class="col-md-12 col-lg-6 mb-3" id="deliverydetails">
            <div class="card h-100 border-secondary order-card">
                <div class="card-header bg-secondary text-white">
                    Pickup Details
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-md-4">Reference</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="client_reference" id="client_reference" value="<?php echo Form::value('client_reference');?>" />
                        </div>
                    </div>
                    <?php if(Session::isDeliveryClientUser()):?>
                        <div class="form-group row">
                            <label class="col-md-4">Requested By</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="requested_by_name" id="requested_by_name" readonly value="<?php echo Session::getUsersName(); ?>" />
                                <input type="hidden" name="requested_by" value="<?php echo Session::getUserId();?>">
                            </div>
                        </div>
                    <?php endif;?>
                    <div class="form-group row custom-control custom-checkbox custom-control-right">
                        <input class="custom-control-input" type="checkbox" id="private_courier" name="private_courier"  />
                        <label class="custom-control-label col-md-5" for="private_courier">Use Your Own Carrier</label>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-4"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Urgency</label>
                        <div class="col-md-8">
                            <select id="urgency" class="form-control selectpicker" name="urgency" data-style="btn-outline-secondary"><option value="0">-- Select One --</option><?php echo $this->controller->deliveryurgency->getSelectUrgencies(Form::value('urgency'));?></select>
                            <?php echo Form::displayError('urgency');?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-4">Notes For FSG</label>
                        <div class="col-md-8">
                            <textarea class="form-control" name="notes" id="instructions" placeholder="Special Instructions/Requests"><?php echo Form::value('notes');?></textarea>
                        </div>
                    </div>
                    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/pickup_address.php");?>
                </div>
                <div class="card-footer">
                    <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                    <input type="hidden" name="client_id" id="client_id" value="<?php echo $client_id; ?>" />
                    <input type="hidden" name="manually_entered" value="<?php echo $manually_entered; ?>" />
                    <div class="col-md-6 offset-6">
                        <button type="submit" class="btn btn-lg btn-outline-secondary" id="submitter" disabled>Book Pickup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>