<?php
$pickup_address = (empty(Form::value('pickup_address')))? $client['address'] : Form::value('pickup_address');
$pickup_address2 = (empty(Form::value('pickup_address2')))? $client['address_2'] : Form::value('pickup_address2');
$pickup_suburb = (empty(Form::value('pickup_suburb')))? $client['suburb'] : Form::value('pickup_suburb');
$pickup_state = (empty(Form::value('pickup_state')))? $client['state'] : Form::value('pickup_state');
$pickup_postcode = (empty(Form::value('pickup_postcode')))? $client['postcode'] : Form::value('pickup_postcode');
?>
<input type="hidden" name="client_id" id="client_id" value="<?php echo $client_id;?>" />
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    </div>
</div>