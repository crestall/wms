<?php
$term       = (empty(Form::value('term')))? $term : Form::value('term');
$client_id  = (empty(Form::value('client_id')))? $client_id : Form::value('client_id');
$courier_id  = (empty(Form::value('courier_id')))? $courier_id : Form::value('courier_id');
$date_from_value  = (empty(Form::value('date_from_value')))? $date_from_value : Form::value('date_from_value');
$date_from = ($date_from_value > 0)? date("d/m/Y", $date_from_value) : "";
$date_to_value  = (empty(Form::value('date_to_value')))? $date_to_value : Form::value('date_to_value');
$date_to = ($date_to_value > 0)? date("d/m/Y", $date_to_value) : "";

$user_role = (Session::isAdminUser())? 'admin' : Session::getUserRole();
?>
<div class="row">
    <form id="order_search" method="get" action="/orders/order-search-results">
        <div class="form-group row">
            <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Search Term</label>
            <div class="col-md-4">
                <input type="text" class="form-control required" name="term" id="term" value="<?php echo $term;?>" />
                <!--span class="inst">Only required if searching all orders</span-->
                <?php echo Form::displayError('term');?>
            </div>
        </div>
        <?php if($user_role == "client"):?>
            <input type="hidden" name="client_id" id="client_id" value="<?php echo Session::get("client_id");?>" />
        <?php else:?>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Client</label>
                <div class="col-md-4">
                    <select id="client_id" name="client_id" class="form-control selectpicker"><option value="0">--Select One--</option><?php echo $this->controller->client->getSelectClients($client_id);?></select>
                    <?php echo Form::displayError('client_id');?>
                </div>
            </div>
        <?php endif;?>
        <div class="row form-group">
            <label class="col-md-3 col-form-label">Filter By Courier</label>
            <div class="col-md-4">
                <select id="courier_id" name="courier_id" class="form-control selectpicker"><option value="0">--Select One--</option><?php echo $this->controller->courier->getSelectCouriers($courier_id, false, false);?></select>
            </div>
        </div>
        <div class="row form-group">
            <label class="col-md-3 col-form-label">Filter By Date Dispatched</label>
            <div class="col-md-1">
                <label>From</label>
            </div>
            <div class="col-md-2">
                <div class="input-group">
                    <input type="text" class="form-control" name="date_from" id="date_from" value="<?php echo $date_from;?>" />
                    <span class="input-group-addon"><i class="far fa-calendar-alt"></i></span>
                </div>
            </div>
            <div class="col-md-1">
                <label>To</label>
            </div>
            <div class="col-md-2">
                <div class="input-group">
                    <input type="text" class="form-control" name="date_to" id="date_to" value="<?php echo $date_to;?>" />
                    <span class="input-group-addon"><i class="far fa-calendar-alt"></i></span>
                </div>
            </div>
        </div>
        <input type="hidden" id="date_from_value" name="date_from_value" value="<?php echo $date_from_value;?>" />
        <input type="hidden" id="date_to_value" name="date_to_value" value="<?php echo $date_to_value;?>" />
        <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
        <div class="form-group row">
            <label class="col-md-3 col-form-label">&nbsp;</label>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </div>
    </form>
</div>