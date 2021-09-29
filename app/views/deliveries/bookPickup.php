<?php
$pickup_address = (empty(Form::value('pickup_address')))? $client['address'] : Form::value('pickup_address');
$pickup_address2 = (empty(Form::value('pickup_address2')))? $client['address_2'] : Form::value('pickup_address2');
$pickup_suburb = (empty(Form::value('pickup_suburb')))? $client['suburb'] : Form::value('pickup_suburb');
$pickup_state = (empty(Form::value('pickup_state')))? $client['state'] : Form::value('pickup_state');
$pickup_postcode = (empty(Form::value('pickup_postcode')))? $client['postcode'] : Form::value('pickup_postcode');
?>
<input type="hidden" name="client_id" id="client_id" value="<?php echo $client_id;?>" />
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php //echo "<pre>",print_r(Form::$values),"</pre>"; die();?>
        <div class="row">
            <div class="form_instructions col">
                <h3>Instructions</h3>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        Start typing the item name, SKU or product ID in the text field at the top of the "Items" card below
                    </li>
                    <li class="list-group-item">
                        Choose the required item from the list that appears.
                    </li>
                    <li class="list-group-item">
                        You can also choose "Add A New Item" at the top of the list to add a new item to our system. You will need to enter the item's name and a unique product ID
                    </li>
                    <li class="list-group-item">
                        Once you have selected or created the item that needs collecting, please enter the number of items on each pallet and the total pallets you would like collected<br>
                        If you have pallets with different quantites on them, you will need to make a separate entry for each
                    </li>
                    <li class="list-group-item">
                        To add more items, start typing the new name, SKU, or product ID in the text field again
                    </li>
                    <li class="list-group-item">
                        You can add a reference to help you find this booking in the future
                    </li>
                </ul>
            </div>
        </div>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <form id="book_pickup" method="post" action="/form/procBookPickup">
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
                                <label class="col-md-4">Your Reference</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="client_reference" id="client_reference" value="<?php echo Form::value('client_reference');?>" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4">Requested By</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="requested_by" id="requested_by" readonly value="<?php echo Session::getUsersName(); ?>" />
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
                            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/pickup_address.php");?>
                        </div>
                        <div class="card-footer">
                            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                            <input type="hidden" name="client_id" value="<?php echo $client_id; ?>" />
                            <div class="col-md-6 offset-6">
                                <button type="submit" class="btn btn-lg btn-outline-secondary" id="submitter" disabled>Book Pickup</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>