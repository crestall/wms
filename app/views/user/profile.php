<?php
$name = (empty(Form::value('name')))? $info['name'] : Form::value('name');
?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="row">
        <div class="col-lg-12">
            <h2>Update your profile</h2>
        </div>
    </div>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
    <?php echo Form::displayError('general');?>
    <div class="row">
        <form id="profile_update" method="post" enctype="multipart/form-data" action="/form/procProfileUpdate">
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Name</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="name" id="name" value="<?php echo $name;?>" />
                    <?php echo Form::displayError('name');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Email</label>
                <div class="col-md-4">
                    <input type="text" class="form-control disabled" name="email" id="email" value="<?php echo $info['email'];?>" disabled />
                    <span class="inst">Email addresses cannot be changed. If you need to update yours, please contact us</span>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Profile Image</label>
                <div class="col-md-4">
                    <input type="file" name="image" id="image" />
                    <?php echo Form::displayError('image');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Current Image</label>
                <div class="col-md-4">
                    <div class="col-md-4">
                        <img src="<?php echo $info['image'];?>" class="thumbnail profile-thumb" />
                    </div>
                    <div class="col-md-6 checkbox checkbox-default">
                        <input class="form-check-input styled" type="checkbox" id="delete_image" name="delete_image" />
                        <label for="delete_image"><small><em>Revert to default</em></small></label>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">New Password</label>
                <div class="col-md-4">
                    <input type="password" class="form-control" name="new_password" id="new_password" value="" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Confirm New Password</label>
                <div class="col-md-4">
                    <input type="password" class="form-control" name="conf_new_password" id="conf_new_password" value="" />
                    <span class="inst">If you wish to change your password, please retype your new password here</span>
                    <?php echo Form::displayError('conf_new_password');?>
                </div>
            </div>
            <!-- Hidden Inputs -->
            <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <input type="hidden" name="client_id" value="<?php echo $info['client_id'];?>" />
            <input type="hidden" name="role_id" value="<?php echo $info['role_id'];?>" />
            <!-- Hidden Inputs -->
            <div class="form-group row">
                <label class="col-md-3 col-form-label">&nbsp;</label>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </div>
            </div>
        </form>
    </div>
</div>
