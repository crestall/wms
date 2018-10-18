<?php
$display = (!empty(Form::value('role_id')) && Form::value('role_id') == $client_role_id)? "block" : "none";
?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
    <?php echo Form::displayError('general');?>
    <div class="row">
        <form id="add_user" method="post" action="/form/procUserAdd">
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Name</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="name" id="name" value="<?php echo Form::value('name');?>" />
                    <?php echo Form::displayError('name');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Email</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required email" name="email" id="email" value="<?php echo Form::value('email');?>" />
                    <?php echo Form::displayError('email');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Role</label>
                <div class="col-md-4">
                    <select id="role_id" name="role_id" class="form-control selectpicker"><option value="0">--Select One--</option><?php echo $this->controller->user->getSelectUserRoles(Form::value('role_id'));?></select>
                    <?php echo Form::displayError('role_id');?>
                </div>
            </div>
            <div id="client_holder" style="display: <?php echo $display;?>">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Client</label>
                    <div class="col-md-4">
                        <select id="client_id" name="client_id" class="form-control selectpicker"><option value="0">--Select One--</option><?php echo $this->controller->client->getSelectClients(Form::value('client_id'));?></select>
                        <?php echo Form::displayError('client_id');?>
                    </div>
                </div>
            </div>
            <?php if(Session::getUserRole() == "super admin"):?>
                <div class="form-group row">
                    <div class="form-check">
                        <label class="form-check-label col-md-3" for="test_user">Test User</label>
                        <div class="col-md-4 checkbox checkbox-default">
                            <input class="form-check-input styled" type="checkbox" id="test_user" name="test_user" <?php if(!empty(Form::value('test_user'))) echo "checked";?> />
                            <label for="test_user"></label>
                        </div>
                    </div>
                </div>
            <?php endif;?>
            <!-- Hidden Inputs -->
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <input type="hidden" name="client_role_id" id="client_role_id" value="<?php echo $client_role_id;?>" />
            <!-- Hidden Inputs -->
            <div class="form-group row">
                <label class="col-md-3 col-form-label">&nbsp;</label>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Add User</button>
                </div>
            </div>
        </form>
    </div>
</div>