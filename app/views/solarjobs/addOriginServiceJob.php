<?php
$address = Form::value('address');
$address2 = Form::value('address2');
$suburb = Form::value('suburb');
$state =  (empty(Form::value('state')))? "VIC" : Form::value('state');
$postcode = Form::value('postcode');
$country = (empty(Form::value('country')))? "AU" : Form::value('country');
$date_filter = "Job Date";
$date = (empty(Form::value('date_value')))? time() : Form::value('date_value');
?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
    <?php echo Form::displayError('general');?>
    <div class="row">
        <div class="col-lg-12">
            <form id="origin-service-job" method="post" action="/form/procAddOriginServiceJob" autocomplete="off">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Team</label>
                    <div class="col-md-4">
                        <select id="team_id" name="team_id" class="form-control selectpicker"><option value="0">--Select One--</option><?php echo $this->controller->solarteam->getSelectTeam(Form::value('team_id'));?></select>
                        <?php echo Form::displayError('team_id');?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Work Order</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control required" name="work_order" id="work_order" value="<?php echo Form::value('work_order');?>" />
                        <?php echo Form::displayError('work_order');?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Customer Name</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control required" name="customer_name" id="customer_name" value="<?php echo Form::value('customer_name');?>" />
                        <?php echo Form::displayError('customer_name');?>
                    </div>
                </div>
                <?php include(Config::get('VIEWS_PATH')."layout/page-includes/select_date.php");?>
                <?php include(Config::get('VIEWS_PATH')."forms/address.php");?>
                <div id="item_selector" class="form-group row">
                    <div class="bs-callout bs-callout-primary bs-callout-more col-md-11">
                        <label class="col-md-3 col-form-label">Items</label>
                        <div class="col-md-9" id="items_holder">
                            <div class="row item_holder">
                                <div class="col-sm-1 add-image-holder">
                                    <a class="add" style="cursor:pointer" title="Add Another Item">
                                        <i class="fas fa-plus-circle fa-2x text-success"></i>
                                    </a>
                                </div>
                                <div class="col-sm-4">
                                    <p><input type="text" class="form-control item-searcher" name="items[0][name]" placeholder="Item Name" /></p>
                                </div>
                                <div class="col-sm-4 qty-holder">

                                </div>
                                <div class="col-sm-3 qty-location"></div>
                                <input type="hidden" name="items[0][id]" class="item_id"  />
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="selected_items" id="selected_items" />
                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                <input type="hidden" name="client_id" id="client_id" value="<?php echo $client_id; ?>" />
                <input type="hidden" name="type_id" id="type_id" value="<?php echo $type_id; ?>" />
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">&nbsp;</label>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary" id="add_origin_service_submitter">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>