<?php
$idisp = "none";
if(!empty(Form::value('csrf_token')))
    $idisp = "block";
?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
    <div class="row">
        <div class="col-md-12">
            <form method="post" action="/form/procSolarReturn" id="solar_return" autocomplete="off">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Owner</label>
                    <div class="col-md-4">
                        <select id="order_type_id" name="order_type_id" class="form-control selectpicker"><option value="0">--Select One--</option><?php echo $this->controller->solarordertype->getSelectSolarOrderTypes(Form::value('order_type_id'));?></select>
                        <?php echo Form::displayError('order_type_id');?>
                    </div>
                </div>
                <div id="item_holder" style="display:<?php echo $idisp;?>">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Name</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control item-searcher required" name="item_name" value="<?php echo Form::value('item_name');?>" />
                            <?php echo Form::displayError('item_name');?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Serial Number</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control required" name="serial_number" value="<?php echo Form::value('serial_number');?>" />
                            <?php echo Form::displayError('serial_number');?>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                <input type="hidden" name="item_id" id="item_id" />
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">&nbsp;</label>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary" id="form_submitter" disabled>Record Return</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>