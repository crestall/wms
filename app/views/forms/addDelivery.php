<?php
$attention = (empty(Form::value('attention')))? $attention : Form::value('attention');
$delivery_address = (empty(Form::value('delivery_address')))? $client['address'] : Form::value('delivery_address');
$delivery_address2 = (empty(Form::value('delivery_address2')))? $client['address_2'] : Form::value('delivery_address2');
$delivery_suburb = (empty(Form::value('delivery_suburb')))? $client['suburb'] : Form::value('delivery_suburb');
$delivery_state = (empty(Form::value('delivery_state')))? $client['state'] : Form::value('delivery_state');
$delivery_postcode = (empty(Form::value('delivery_postcode')))? $client['postcode'] : Form::value('delivery_postcode');
?>
<?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
<form id="book_delivery" method="post" action="/form/procBookDelivery">
    <div class="row">
        <div class="col-md-12 col-lg-6 mb-3" id="itemslist">
            <div class="card h-100 border-secondary order-card">
                <div class="card-header bg-secondary text-white">
                    Items To Deliver
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-8 offset-2">
                            <input type="text" class="form-control" id="item_searcher" placeholder="Item name/SKU/Product ID">
                            <?php echo Form::displayError('items');?>
                        </div>
                    </div>
                    <div id="items_holder"></div>
                    <input type="hidden" name="selected_items" id="selected_items">
                </div>
            </div>
        </div>
        <div class="col-md-12 col-lg-6 mb-3" id="deliverydetails">
            <div class="card h-100 border-secondary order-card">
                <div class="card-header bg-secondary text-white">
                    Delivery Details
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-md-4">Your Reference</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="client_reference" id="client_reference" value="<?php echo Form::value('client_reference');?>" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-4"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Attention</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control required" name="attention" id="attention" value="<?php echo $attention;?>" />
                            <?php echo Form::displayError('attention');?>
                        </div>
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
                    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/delivery_pickup_address.php");?>
                </div>
                <div class="card-footer">
                    <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                    <input type="hidden" name="client_id" value="<?php echo $client_id; ?>" />
                    <div class="col-md-6 offset-6">
                        <button type="submit" class="btn btn-lg btn-outline-secondary" id="submitter" disabled>Book Delivery</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>