<?php

?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
    <?php echo Form::displayError('general');?>
    <div class="row">
        <form id="add-solartype"  method="post" enctype="multipart/form-data" action="/form/procSolarTypeAdd">
            <div class="row">
                <div class="col-lg-12">
                    <h3>Add New Type</h3>
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
                    <button type="submit" class="btn btn-primary">Add Solar Order Type</button>
                </div>
            </div>
        </form>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <h2>Current Solar Order Types</h2>
        </div>
    </div>
    <?php if(count($types)):?>
        <div class="row">
            <?php foreach($types as $c):?>
                <form class="edit-solartype" action="/form/procSolarTypeEdit" method="post">
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label class="col-form-label">Name</label>
                            <input type="text" class="form-control required" name="name_<?php echo $c['id'];?>" id="name_<?php echo $c['id'];?>" value="<?php echo $c['name'];?>" />
                            <?php echo Form::displayError("name_{$c['id']}");?>
                        </div>
                        <div class="col-md-1">
                            <label class="col-form-label">Active</label>
                            <div class="checkbox checkbox-default">
                                <input class="form-check-input styled" type="checkbox" id="active_<?php echo $c['id'];?>" name="active_<?php echo $c['id'];?>" <?php if($c['active'] > 0) echo "checked";?> />
                                <label for="active_<?php echo $c['id'];?>"></label>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <label class="col-form-label">&nbsp;</label>
                            <div class="input-group">
                                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                                <input type="hidden" name="line_id" value="<?php echo $c['id'];?>" />
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
                    <h2><i class="fas fa-exclamation-triangle"></i> No Solar Types Listed</h2>
                    <p>You will need to add some first</p>
                </div>
            </div>
        </div>
    <?php endif;?>
</div>