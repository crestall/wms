<?php

?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
    <?php echo Form::displayError('general');?>
    <div class="row">
        <form id="add-userrole"  method="post" enctype="multipart/form-data" action="/form/procUserRoleAdd">
            <div class="row">
                <div class="col-lg-12">
                    <h3>Add New Role</h3>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Name</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="name" id="name" value="<?php echo Form::value('name');?>" />
                    <?php echo Form::displayError('name');?>
                </div>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <div class="form-group row">
                <label class="col-md-3 col-form-label">&nbsp;</label>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Add Role</button>
                </div>
            </div>
        </form>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <h2>Current User Roles</h2>
        </div>
    </div>
    <?php if(count($roles)):?>
        <div class="row" id="sortable">
            <?php foreach($roles as $r):?>
                <form class="edit-userrole" action="/form/procUserRoleEdit" method="post">
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label class="col-form-label">Name</label>
                            <input type="text" class="form-control required userrolename" name="name_<?php echo $r['id'];?>" id="name_<?php echo $r['id'];?>" value="<?php echo ucwords($r['name']);?>" />
                            <input type="hidden" name="currentname_<?php echo $r['id'];?>" value="<?php echo $r['name'];?>"/>
                            <?php echo Form::displayError("name_{$r['id']}");?>
                        </div>
                        <div class="col-md-1">
                            <label class="col-form-label">Active</label>
                            <div class="checkbox checkbox-default">
                                <input class="form-check-input styled" type="checkbox" id="active_<?php echo $r['id'];?>" name="active_<?php echo $r['id'];?>" <?php if($r['active'] > 0) echo "checked";?> />
                                <label for="active_<?php echo $r['id'];?>"></label>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <label class="col-form-label">&nbsp;</label>
                            <div class="input-group">
                                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                                <input type="hidden" name="line_id" value="<?php echo $r['id'];?>" />
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </div>
                    </div>
                </form>
            <?php endforeach;?>
        </div>
    <?php else:?>
        <div class="row">
            <div class="col-lg-12">
                <div class="errorbox">
                    <h2><i class="fas fa-exclamation-triangle"></i> No User Roles Listed</h2>
                    <p>You will need to add some first</p>
                </div>
            </div>
        </div>
    <?php endif;?>
</div>