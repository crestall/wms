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
        <?php if($shipment['courier_id'] != 0):?>
            <div class="row">
                <div class="col">
                    <div class="errorbox">
                        <div class="row">
                            <div class="col-4 text-right">
                                <i class="fad fa-exclamation-triangle fa-6x"></i>
                            </div>
                            <div class="col-8">
                                <h2>Courier Already Selected</h2>
                                <p>Sorry, dispatches that have had their courier assigned cannot have the address updated</p>
                                <p>See the warehouse about what can be done</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else:?>
            <div class="row">
                <div class="col m-3">
                    <h2>Updating Shipment Address For Job <?php echo $shipment['job_number'];?></h2>
                </div>
            </div>
            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
            <?php echo Form::displayError('general');?>

        <?php endif;?>
    </div>
</div>