<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <div class="row">
            <div class="col-12">
                <form method="post" action="/form/procTransferLocation" id="transfer_location" autocomplete="off">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Location to Move</label>
                        <div class="col-md-5">
                            <select id="move_from_location" name="move_from_location" class="form-control selectpicker" data-live-search="true" data-style="btn-outline-secondary"><option value="0">--Select One--</option><?php echo $this->controller->location->getSelectNonEmptyUnallocatedLocations(Form::value('move_from_location'));?></select>
                            <span class="inst">Empty locations or locations with allocated items in them will not appear</span>
                            <?php echo Form::displayError('move_from_location');?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Move To</label>
                        <div class="col-md-5">
                            <select id="move_to_location" name="move_to_location" class="form-control selectpicker" data-live-search="true" data-style="btn-outline-secondary"><option value="0">--Select One--</option><?php echo $this->controller->location->getSelectMultiSKULocations(Form::value('move_to_location'));?></select>
                            <span class="inst">Only Multi-SKU locations will appear</span>
                            <?php echo Form::displayError('move_to_location');?>
                        </div>
                    </div>
                    <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                    <div class="form-group row">
                        <div class="col-md-5 offset-md-3">
                            <button type="submit" class="btn btn-outline-secondary" id="form_submitter">Move Them</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>