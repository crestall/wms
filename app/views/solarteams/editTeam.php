<?php
?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="row">
        <div class="col-md-12">
            <p><a class="btn btn-primary" href="/solar-teams/view-teams">View List of Teams</a></p>
        </div>
    </div>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
    <?php echo Form::displayError('general');?>
    <div class="row">
        <form id="edit-solar-team" method="post" action="/form/procSolarTeamEdit">
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Name</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="name" id="name" value="<?php echo $team['name'];?>" />
                    <?php echo Form::displayError('name');?>
                </div>
            </div>
            <div class="form-group row">
                <div class="form-check">
                    <label class="form-check-label col-md-3" for="active">Active</label>
                    <div class="col-md-4 checkbox checkbox-default">
                        <input class="form-check-input styled" type="checkbox" id="active" name="active" <?php if($team['active'] > 0) echo "checked";?> />
                        <label for="active"></label>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Comments</label>
                <div class="col-md-4">
                    <textarea class="form-control" name="comments" id="comments"><?php echo $team['comments'];?></textarea>
                </div>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <input type="hidden" name="team_id" value="<?php echo $team['id']; ?>" />
            <div class="form-group row">
                <label class="col-md-3 col-form-label">&nbsp;</label>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Edit Team</button>
                </div>
            </div>
        </form>
    </div>
</div>