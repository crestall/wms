<?php
$pickup_address = (empty(Form::value('pickup_address')))? (isset($client['address']))?$client['address']: "" : Form::value('pickup_address');
$pickup_address2 = (empty(Form::value('pickup_address2')))? (isset($client['address_2']))?$client['address_2']:"" : Form::value('pickup_address2');
$pickup_suburb = (empty(Form::value('pickup_suburb')))? (isset($client['suburb']))?$client['suburb']:"" : Form::value('pickup_suburb');
$pickup_state = (empty(Form::value('pickup_state')))? (isset($client['state']))?$client['state']:"" : Form::value('pickup_state');
$pickup_postcode = (empty(Form::value('pickup_postcode')))? (isset($client['postcode']))?$client['postcode']:"" : Form::value('pickup_postcode');
?>
<input type="hidden" name="client_id" id="client_id" value="<?php echo $client_id;?>" />
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="row form-group">
            <label class="col-md-3">Select a Client</label>
            <div class="col-md-4">
                <p><select id="client_selector" class="form-control selectpicker"  data-style="btn-outline-secondary"><option value="0">Select</option><?php echo $this->controller->client->getSelectDeliveryClients($client_id);?></select></p>
            </div>
        </div>
        <?php if($client_id > 0):?>
            put the form here
        <?php endif;?>
    </div>
</div>