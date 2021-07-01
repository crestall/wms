<?php
echo $client_id;
?>
<div id="page-wrapper">
    <div id="page_container" class="container">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php"); ?>
        <div class="row mb-3">
            <label class="col-md-3">Select a Client</label>
            <div class="col-md-4">
                <select id="client_selector" class="form-control selectpicker" data-style="btn-outline-secondary" data-live-search="true"><option value="0">Select</option><?php echo $this->controller->client->getSelectClients($client_id);?></select>
            </div>
        </div>
        <?php if($client_id > 0):?>
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h2>Inventory Comparing For <?php echo ucwords($client_name);?></h2>
                </div>
            </div>
            <form id="inventory-compare" method="post" action="/form/procInventoryCompare" enctype="multipart/form-data">
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
                    <div class="col-md-4 offset-md-4">
                        <button type="submit" class="btn btn-outline-secondary">Check</button>
                    </div>
                </div>
            </form>
            <div id="feedback_holder" class="row">

            </div>
        <?php endif;?>
    </div>
</div>
