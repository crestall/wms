<?php

?>
<div id="page-wrapper">
    <div id="page_container" class="container">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="row">
            <div class="col-md-6">
                <p><a href="/downloads/downloadFile/file=instructions.docx" class="btn btn-outline-secondary">Instructions</a> <span class="inst">Download instructions on how to upload orders</span></p>
            </div>
            <div class="col-md-6">
                <p><a href="/downloads/downloadFile/file=template.csv" class="btn btn-outline-secondary">Template</a> <span class='inst'>Download a template to modify with your own data</span></p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h2>Bulk Order Importing</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <?php if(isset($_SESSION['feedback'])) :?>
                   <div class='feedbackbox'><?php echo Session::getAndDestroy('feedback');?></div>
                <?php endif; ?>
                <?php if(isset($_SESSION['errorfeedback'])) :?>
                   <div class='errorbox'><?php echo Session::getAndDestroy('errorfeedback');?></div>
                <?php endif; ?>
            </div>
        </div>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <form id="bulk_order_import" method="post" enctype="multipart/form-data" method="post" action="/form/procOrderUpload">
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> CSV File</label>
                <div class="col-md-4">
                    <input type="file" name="csv_file" id="csv_file"  />
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
                    <button type="submit" class="btn btn-outline-secondary">Import Orders</button>
                </div>
            </div>
        </form>
    </div>
</div>