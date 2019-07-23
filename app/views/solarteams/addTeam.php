<?php

?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
    <?php echo Form::displayError('general');?>
    <div class="row">
        <form id="add-sales-rep" method="post" action="/form/procSolarTeamAdd">
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Name</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="name" id="name" value="<?php echo Form::value('name');?>" />
                    <?php echo Form::displayError('name');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Team Leader</label>
                <div class="col-md-4">
                    <select id="team_leader_id" name="team_leader_id" class="form-control selectpicker"><option value="0">--Select One--</option><?php echo $this->controller->solarteam->getSelectTeamLeaders(Form::value('team_leader_id'));?></select>
                    <?php echo Form::displayError('team_leader_id');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Comments</label>
                <div class="col-md-4">
                    <textarea class="form-control" name="comments" id="comments"><?php echo Form::value('comments');?></textarea>
                </div>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <div class="form-group row">
                <label class="col-md-3 col-form-label">&nbsp;</label>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Add Team</button>
                </div>
            </div>
        </form>
    </div>
</div>