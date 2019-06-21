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
                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
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