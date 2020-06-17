<?php

?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php"); ?>
    <div class="row">
        <div class="col-md-12">
            <h2>Import Current Reece Departments</h2>
        </div>
    </div>
    <div class="row">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <form id="reece-department-upload" method="post" action="/form/procReeceDepartmentUpload" enctype="multipart/form-data">
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Current Data Spreadsheet</label>
                <div class="col-md-4">
                    <input type="file" name="csv_file" id="csv_file" />
                    <?php echo Form::displayError('csv_file');?>
                </div>
            </div>
            <div class="form-group row">
                <div class="form-check">
                    <label class="form-check-label col-md-3" for="header_row">My CSV has a header row</label>
                    <div class="col-md-4 checkbox checkbox-default">
                        <input class="form-check-input styled" type="checkbox" id="header_row" name="header_row" checked />
                        <label for="header_row"></label>
                    </div>
                </div>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <div class="form-group row">
                <label class="col-md-3 col-form-label">&nbsp;</label>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Upload It</button>
                </div>
            </div>
        </form>
    </div>
</div>
