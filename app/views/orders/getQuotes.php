<?php

?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div id="response" style="display:none;"></div>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <?php echo Form::displayError('general');?>
        <form id="get_quotes" method="post" action="/form/procGetQuotes">
            <div class="col-12 mb-2">
                <p class="inst">Only the weight is required for Australia Post</p>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <div class="form-group row">
                <div class="col-md-4 offset-md-6">
                    <button type="submit" class="btn btn-outline-secondary">Get Prices</button>
                </div>
            </div>
        </form>
    </div>
</div>