<?php
$address = Form::value('address');
$address2 = Form::value('address2');
$suburb = Form::value('suburb');
$state = Form::value('state');
$postcode = Form::value('postcode');
$country = Form::value('country');
$user_role = (Session::isAdminUser())? 'admin' : Session::getUserRole();
$idisp = "none";
if(!empty(Form::value('items')))
    $idisp = "block";
if($user_role == "client")
    $idisp = "block";
$inverter_qty = empty(Form::value('inverter_qty'))? 1 : Form::value('inverter_qty');
$type_id = $this->controller->solarordertype->getTypeId('solar gain');
$date_filter = "Install Date";
$date = (empty(Form::value('date_value')))? time() : Form::value('date_value');
?>
<?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
<?php echo Form::displayError('general');?>
<?php //echo "<pre>",var_dump(Form::value('items')),"</pre>";?>
<div class="row">
    <div class="col-lg-12">
        <h2>Need to get some info for this <?php echo $type_id;?></h2>
    </div>
</div>