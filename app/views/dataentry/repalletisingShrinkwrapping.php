<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <div class="row">
            <div class="col">
                <form id="repalletising_shrinkwrapping" method="post" action="/form/procRepalletiseShrinkwrap">
                    <div class='form-group row'>
                        <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Client</label>
                        <div class="col-md-4">
                            <select id="client_id" name="client_id" class="form-control selectpicker" data-style="btn-outline-secondary" data-live-search="true"><option value="0">--Select One--</option><?php echo $this->controller->client->getSelectClients(Form::value('client_id'));?></select>
                            <?php echo Form::displayError('client_id');?>
                        </div>
                    </div>
                    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/choose_date.php");?>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-lable">Repalletise Count</label>
                        <div class="col-md-4">
                            <input class="form-control digits one_of" type="text" name="repalletise_count" id="repalletise_count" value="<?php echo Form::value('repalletise_count');?>">
                            <?php echo Form::displayError('repalletise_count');?>
                            <?php echo Form::displayError('choose_one');?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-lable">Shrinkwrap Count</label>
                        <div class="col-md-4">
                            <input class="form-control digits one_of" type="text" name="shrinkwrap_count" id="shrinkwrap_count" value="<?php echo Form::value('shrinkwrap_count');?>">
                            <?php echo Form::displayError('shrinkwrap_count');?>
                            <?php echo Form::displayError('choose_one');?>
                        </div>
                    </div>
                    <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                    <div class="form-group row">
                        <div class="col-md-4 offset-md-3">
                            <button type="submit" class="btn btn-outline-secondary">Record Details</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>