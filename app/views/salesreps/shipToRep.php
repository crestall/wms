<?php
$sn_disabled = (Form::value('chain_id') > 0)? "":"disabled";
?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="row form-group">
        <label class="col-md-3">Select a Client</label>
        <div class="col-md-4">
            <p><select id="client_selector" class="form-control selectpicker"><option value="0">Select</option><?php echo $this->controller->client->getSelectSalesRepClients($client_id);?></select></p>
        </div>
    </div>
    <?php if($client_id > 0):?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <div class="row">
            <form id="ship-to-rep" method="post" action="/form/procShipToRep">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Sales Representative</label>
                    <div class="col-md-4">
                        <select id="rep_id" name="rep_id" class="form-control selectpicker"><option value="0">--Select One--</option><?php echo $this->controller->salesrep->getSelectSalesReps(Form::value('rep_id'), $client_id);?></select>
                        <?php echo Form::displayError('rep_id');?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Store Chain</label>
                    <div class="col-md-4">
                        <select id="chain_id" name="chain_id" class="form-control selectpicker"><option value="0">--Select One--</option><?php echo $this->controller->storechain->getSelectStoreChains(Form::value('chain_id'));?></select>
                        <?php echo Form::displayError('chain_id');?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Store</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="store_name" id="store_name" value="<?php echo Form::value('store_name');?>" <?php echo $sn_disabled;?> />
                        <?php echo Form::displayError('store_name');?>
                    </div>
                </div>
                <div class="row" id="store_details"></div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Products</label>
                    <div class="col-md-9">
                        <label class="col-md-2 col-form-label">Name</label>
                        <input type="text" name="products[0][name]"



                        <input type="text" class="form-control" name="store_name" id="store_name" value="<?php echo Form::value('store_name');?>" <?php echo $sn_disabled;?> />
                        <?php echo Form::displayError('store_name');?>
                    </div>
                </div>
                <div class="row" id="store_details"></div>
                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                <input type="hidden" name="client_id" value="<?php echo $client_id;?>" />
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">&nbsp;</label>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">Create Consignment</button>
                    </div>
                </div>
            </form>
        </div>
    <?php endif;?>
</div>