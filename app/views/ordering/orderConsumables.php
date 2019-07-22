<?php

?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
    <?php echo Form::displayError('general');?>
    <div class="row">
        <form id="order-consumables" method="post" action="/form/procSolarTeamOrderConsumables">
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <?php include(Config::get('VIEWS_PATH')."forms/item_adder.php");?> 
            <input type="hidden" name="client_id" id="client_id" value="67" />
            <div class="form-group row">
                <label class="col-md-3 col-form-label">&nbsp;</label>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Request Order</button>
                </div>
            </div>
        </form>
    </div>
</div>