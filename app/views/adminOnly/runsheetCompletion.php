<?php

?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php"); ?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <?php echo Form::displayError('general');?>
        <div class="row">
            <div class="col-12">
                <form id="runsheet_completion_updater" method="post" action="/form/procRunsheetCompletionUpdate">
                    <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                    <div class="form-group row">
                        <div class="col-md-4 offset-md-3">
                            <button type="submit" class="btn btn-outline-secondary" id="submitter">Update Completed Status</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
