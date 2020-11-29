<?php

?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <?php echo Form::displayError('general');?>
        <form id="complete_runsheet_tasks" method="post" action="/form/procAddMiscTask">
            <div class="form-group row">
                <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Deliver To</label>
                <div class="col-md-4">
                    <input type="text" class="required form-control" name="ship_to" id="ship_to" value="<?php echo Form::value('ship_to');?>">
                </div>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" >
            <input type="hidden" name="runsheet_id" id="runsheet_id" value="<?php echo $runsheet_id;?>" >
            <div class="form-group row">
                <div class="col-md-5 offset-md-3">
                    <button type="submit" class="btn btn-outline-secondary" id="submitter">Add Task To Runsheet</button>
                </div>
            </div>
        </form>
    </div>
</div>