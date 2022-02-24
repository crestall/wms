<?php

?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <?php echo Form::displayError('general');?>
        <form id="add_driver"  method="post" enctype="multipart/form-data" action="/form/procBookCoverAdd">
            <div class="row">
                <div class="col-lg-12">
                    <h3>Add New Driver</h3>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Name</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="name" id="name" value="<?php echo Form::value('name');?>" />
                    <span class="inst">Driver names need to be unique</span>
                    <?php echo Form::displayError('name');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Phone</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="phone" id="phone" value="<?php echo Form::value('phone');?>" />
                    <?php echo Form::displayError('phone');?>
                </div>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <input type="hidden" name="return_url" value="<?php echo $child;?>-settings/drivers" >
            <div class="form-group row">
                <div class="col-md-4 offset-md-3">
                    <button type="submit" class="btn btn-outline-secondary">Add Driver</button>
                </div>
            </div>
        </form>
        <div class="row">
            <div class="col-lg-12">
                <h2>Current Drivers</h2>
            </div>
        </div>
        <?php if(count($drivers)):?>
            <?php foreach($drivers as $d):?>
                <form id="form_<?php echo $d['id'];?>" class="edit_driver border-bottom border-secondary border-bottom-dashed mb-3" action="/form/procDriverEdit" method="post">
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label class="col-form-label">Name</label>
                            <input type="text" class="form-control required driver_name" name="name" id="name_<?php echo $d['id'];?>" value="<?php echo $d['name'];?>" />
                            <?php echo Form::displayError("name_{$d['id']}");?>
                        </div>
                        <div class="col-md-3">
                            <label class="col-form-label">Phone</label>
                            <input type="text" class="form-control" name="phone" id="phone_<?php echo $d['id'];?>" value="<?php echo $d['phone'];?>" />
                            <?php echo Form::displayError("phone{$d['id']}");?>
                        </div>
                        <div class="col-md-1">
                            <label class="col-form-label" for="active_<?php echo $d['id'];?>">Active</label>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="active_<?php echo $d['id'];?>" name="active" <?php if($d['active'] > 0) echo "checked";?> />
                                <label class="custom-control-label" for="active_<?php echo $d['id'];?>"></label>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <label class="col-form-label">&nbsp;</label>
                            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                            <input type="hidden" name="line_id" value="<?php echo $d['id'];?>" />
                            <input type="hidden" class="current_driver_name" name="current_name" id="current_name_<?php echo $d['id'];?>" value="<?php echo $d['name'];?>" />
                            <input type="hidden" name="return_url" value="<?php echo $child;?>-settings/drivers" >
                            <button type="submit" class="btn btn-sm btn-outline-secondary">Update</button>
                        </div>
                    </div>
                </form>
            <?php endforeach;?>
        <?php else:?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="errorbox">
                        <h2><i class="fas fa-exclamation-triangle"></i> No Drivers Listed</h2>
                        <p>You will need to add some first</p>
                    </div>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>