<?php
if($table == "orders")
{
    $ship_to    = (empty(Form::value('ship_to')))?  $order['ship_to']      : Form::value('ship_to');
    $company    = (empty(Form::value('company')))?  $order['company_name'] : Form::value('company');
    $address    = empty(Form::value('address'))?    $order['address']      : Form::value('address');
    $address2   = empty(Form::value('address2'))?   $order['address_2']    : Form::value('address2');
    $suburb     = empty(Form::value('suburb'))?     $order['suburb']       : Form::value('suburb');
    $state      = empty(Form::value('state'))?      $order['state']        : Form::value('state');
    $postcode   = empty(Form::value('postcode'))?   $order['postcode']     : Form::value('postcode');
    $country    = empty(Form::value('country'))?    $order['country']      : Form::value('country');
    $link = "/orders/view-orders";
    $buttons = '
        <div class="col-lg-4">
            <a class="btn btn-primary" href="/orders/order-update/order='.$order_id.'">Return to Order</a>
        </div>
        <div class="col-lg-4">
            <a class="btn btn-primary" href="/orders/view-orders/client='.$order['client_id'].'">View Orders For Client</a>
        </div>
    ';
    $head = '
        <div class="col-lg-12">
            <h2>Updating Address For Order Number '.$order['order_number'].'</h2>
        </div>
    ';
}
elseif($table =="swatches")
{
    $ship_to    = (empty(Form::value('ship_to')))?  $order['name']         : Form::value('ship_to');
    $address    = empty(Form::value('address'))?    $order['address']      : Form::value('address');
    $address2   = empty(Form::value('address2'))?   $order['address_2']    : Form::value('address2');
    $suburb     = empty(Form::value('suburb'))?     $order['suburb']       : Form::value('suburb');
    $state      = empty(Form::value('state'))?      $order['state']        : Form::value('state');
    $postcode   = empty(Form::value('postcode'))?   $order['postcode']     : Form::value('postcode');
    $country    = empty(Form::value('country'))?    $order['country']      : Form::value('country');
    $link = "/orders/manage-swatches";
    $buttons = '
            <div class="col-lg-4">
                <a class="btn btn-primary" href="/orders/manage-swatches">View All Swatches</a>
            </div>
        ';
    $head = '
        <div class="col-lg-12">
            <h2>Updating Address For Swatch Request</h2>
        </div>
    ';
}

?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php if($error):?>
        <div class="row">
            <div class="col-lg-12">
                <div class="errorbox">
                    <div class="row">
                        <div class="col-lg-2" style="font-size:96px">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="col-lg-6">
                            <h2>No ID Supplied</h2>
                            <p>No order/request was supplied to update</p>
                            <p><a href="<?php echo $link;?>">Please click here to view all orders/requests to choose from</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php elseif(!$order || !count($order)):?>
        <div class="row">
            <div class="col-lg-12">
                <div class="errorbox">
                    <div class="row">
                        <div class="col-lg-2" style="font-size:96px">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="col-lg-6">
                            <h2>No Order/Request Found</h2>
                            <p>No order was found with that ID</p>
                            <p><a href="<?php echo $link;?>">Please click here to view all orders/requests to choose from</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else:?>
        <div class="row">
            <?php echo $buttons;?>
        </div>
        <div class="row">
            <?php echo $head;?>
        </div>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <?php echo Form::displayError('general');?>
        <div class="row">
            <form id="address-update" method="post" action="/form/procAddressUpdate">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup>Deliver To</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control required" name="ship_to" id="ship_to" value="<?php echo $ship_to;?>" />
                        <?php echo Form::displayError('ship_to');?>
                    </div>
                </div>
                <?php if($table == "orders"):?>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Company</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="company" id="company" value="<?php echo $company;?>" />
                        </div>
                    </div>
                <?php endif;?>
                <?php include(Config::get('VIEWS_PATH')."forms/address.php");?>
                <input type="hidden" name="order_id" value="<?php echo $order['id'];?>" />
                <input type="hidden" name="table" value="<?php echo $table;?>" />
                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">&nbsp;</label>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">Update Address</button>
                    </div>
                </div>
            </form>
        </div>
    <?php endif;?>
</div>