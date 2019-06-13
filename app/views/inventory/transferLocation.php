<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
    <div class="row">
        <div class="col-md-12">
            <form method="post" action="/form/procTransferLocation" id="transfer_location" autocomplete="off">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Location to Move</label>
                    <div class="col-md-4">
                        <select id="move_from_location" name="move_from_location" class="form-control selectpicker" data-live-search="true"><option value="0">--Select One--</option><?php echo $this->controller->location->getSelectNonEmptyUnallocatedLocations(Form::value('move_from_location'));?></select>
                        <span class="inst">Empty locations or locations with allocated items in them will not appear</span>
                        <?php echo Form::displayError('move_from_location');?>
                    </div>
                </div>
                <div id="move_to_holder" styel="display:none">
                    <div id="content_holder">
                        
                    </div>
                </div>
                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">&nbsp;</label>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary" id="form_submitter" disabled>Transfer</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>