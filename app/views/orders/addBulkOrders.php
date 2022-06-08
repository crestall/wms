<?php

?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <?php echo Form::displayError('general');?>
        <div id="bulk_upload_form"class="col-lg-12">
            <form id="bulk-order-csv-upload" method="post" action="/form/procBulkOrderAdd" enctype="multipart/form-data">
                <div class="p-3 pb-0 mb-2 rounded-top form-section-holder">
                    <div class="row">
                        <div class="col">
                            <h3>File Details</h3>
                        </div>
                    </div>
                    <div class="p-3 light-grey mb-3">
                        <div class="form-group row">
                            <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Client</label>
                            <div class="col-md-4">
                                <select id="client_id" name="client_id" class="form-control selectpicker" data-style="btn-outline-secondary" required><option value="0">--Select One--</option><?php echo $this->controller->client->getSelectPPClients(Form::value('client_id'));?></select>
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
                    </div>
                    <div class="form-group row custom-control custom-checkbox custom-control-right">
                        <input class="custom-control-input" type="checkbox" id="header_row" name="header_row" checked />
                        <label class="custom-control-label col-md-3" for="header_row">My CSV has a header row</label>
                    </div>
                </div>
                <div class="p-3 pb-0 mb-2 rounded-top form-section-holder">
                    <div class="row">
                        <div class="col">
                            <h3>Upload File</h3>
                        </div>
                   </div>
                   <div class="p-3 light-grey mb-3">
                        <div class="form-group row">
                            <div class="col-md-4 offset-md-3 text-center text-md-left">
                                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                                <button type="submit" class="btn btn-outline-fsg" id="submitter">Import Orders</button>
                            </div>
                        </div>
                   </div>
                </div>
            </form>
        </div>
    </div>
</div>