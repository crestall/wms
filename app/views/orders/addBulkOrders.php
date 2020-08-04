<?php

?>
<div id="page-wrapper">
    <div id="page_container" class="container">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <?php echo Form::displayError('general');?>
        <div id="bulk_upload_form"class="col-lg-12">
            <form id="bulk-order-csv-upload" method="post" action="/form/procBulkOrderAdd" enctype="multipart/form-data">
                <div class="form-group row">
                    <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Client</label>
                    <div class="col-md-4">
                        <select id="client_id" name="client_id" class="form-control selectpicker" data-style="btn-outline-secondary" required><option value="0">--Select One--</option><?php echo $this->controller->client->getSelectClients(Form::value('client_id'));?></select>
                        <?php echo Form::displayError('client_id');?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> CSV File</label>
                    <div class="col-md-4">
                        <input type="file" name="csv_file" id="csv_file" class="required" />
                        <?php echo Form::displayError('csv_file');?>
                    </div>
                </div>
                <div class="form-group row custom-control custom-checkbox custom-control-right">
                    <input class="custom-control-input" type="checkbox" id="header_row" name="header_row" checked />
                    <label class="custom-control-label col-md-3" for="header_row">My CSV has a header row</label>
                </div>
                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">&nbsp;</label>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-outline-secondary">Upload It</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>