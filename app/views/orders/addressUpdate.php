<?php
if(!$error)
{
    $ship_to    = (empty(Form::value('ship_to')))?  $order['ship_to']      : Form::value('ship_to');
    $company    = (empty(Form::value('company')))?  $order['company_name'] : Form::value('company');
    $address    = empty(Form::value('address'))?    $order['address']      : Form::value('address');
    $address2   = empty(Form::value('address2'))?   $order['address_2']    : Form::value('address2');
    $suburb     = empty(Form::value('suburb'))?     $order['suburb']       : Form::value('suburb');
    $state      = empty(Form::value('state'))?      $order['state']        : Form::value('state');
    $postcode   = empty(Form::value('postcode'))?   $order['postcode']     : Form::value('postcode');
    $country    = empty(Form::value('country'))?    $order['country']      : Form::value('country');
}
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php if($error):?>
            <div class="row">
                <div class="col">
                    <div class="errorbox">
                        <div class="row">
                            <div class="col-4 text-right">
                                <i class="fad fa-exclamation-triangle fa-6x"></i>
                            </div>
                            <div class="col-8">
                                <h2>No ID Supplied</h2>
                                <p>No order was supplied to update</p>
                                <p><a href="/orders/view-orders">Please click here to view all orders to choose from</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php elseif(!$order || !count($order)):?>
            <div class="row">
                <div class="col">
                    <div class="errorbox">
                        <div class="row">
                            <div class="col-4 text-right">
                                <i class="fad fa-exclamation-triangle fa-6x"></i>
                            </div>
                            <div class="col-8">
                                <h2>No Order Found</h2>
                                <p>No order was found with that ID</p>
                                <p><a href="/orders/view-orders">Please click here to view all orders to choose from</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else:?>
            <div class="row">
                <div class="col">
                    <a class="btn btn-primary" href="/orders/order-update/order=<?php echo $order_id;?>">Return to Order</a>
                </div>
                <div class="col">
                    <a class="btn btn-primary" href="/orders/view-orders/client=<?php echo $order['client_id'];?>">View Orders For Client</a>
                </div>
            </div>
            <div class="row">
                <div class="col m-3">
                    <h2>Updating Address For Order Number <?php echo $order['order_number'];?></h2>
                </div>
            </div>
            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
            <?php echo Form::displayError('general');?>
                <form id="address-update" method="post" action="/form/procAddressUpdate">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Deliver To</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control required" name="ship_to" id="ship_to" value="<?php echo $ship_to;?>" />
                            <?php echo Form::displayError('ship_to');?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Company</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="company" id="company" value="<?php echo $company;?>" />
                        </div>
                    </div>
                    <?php include(Config::get('VIEWS_PATH')."forms/address.php");?>
                    <input type="hidden" name="order_id" value="<?php echo $order['id'];?>" />
                    <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">&nbsp;</label>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-outline-secondary">Save Changes</button>
                        </div>
                    </div>
                </form>
        <?php endif;?>
    </div>
</div>