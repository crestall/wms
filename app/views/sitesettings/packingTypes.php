<?php

?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
    <?php echo Form::displayError('general');?>
    <div class="row">
        <form id="add-packtype"  method="post" enctype="multipart/form-data" action="/form/procPackTypeAdd">
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
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Width</label>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control number" name="width" id="width" value="<?php echo Form::value('width');?>" />
                        <span class="input-group-addon">cm</span>
                    </div>
                    <?php echo Form::displayError('width');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Depth</label>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control number" name="depth" id="depth" value="<?php echo Form::value('depth');?>" />
                        <span class="input-group-addon">cm</span>
                    </div>
                    <?php echo Form::displayError('depth');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Height</label>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control number" name="height" id="height" value="<?php echo Form::value('height');?>" />
                        <span class="input-group-addon">cm</span>
                    </div>
                    <?php echo Form::displayError('height');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label" for="multiples">Can Fit Multiple</label>
                <div class="checkbox checkbox-default col-md-4">
                    <input class="form-check-input styled" type="checkbox" id="multiples" name="multiples"  />
                    <label for="multiples"></label>
                </div>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <div class="form-group row">
                <label class="col-md-3 col-form-label">&nbsp;</label>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Add Packing Type</button>
                </div>
            </div>
        </form>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <h2>Current Packing Types</h2>
        </div>
    </div>
    <?php if(count($packings)):?>
        <div class="row">
            <?php foreach($packings as $p):?>
                <form class="edit-packtype" action="/form/procPackTypeEdit" method="post">
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label class="col-form-label">Name</label>
                            <input type="text" class="form-control required" name="name_<?php echo $p['id'];?>" id="name_<?php echo $p['id'];?>" value="<?php echo $p['name'];?>" />
                            <?php echo Form::displayError("name_{$p['id']}");?>
                        </div>
                        <div class="col-md-2">
                            <label class="col-form-label">Width</label>
                            <div class="input-group">
                                <input type="text" class="form-control number" name="width_<?php echo $p['id'];?>" id="width_<?php echo $p['id'];?>" value="<?php if($p['width'] > 0) echo $p['width'];?>" />
                                <span class="input-group-addon">cm</span>
                            </div>
                            <?php echo Form::displayError("width_{$p['id']}");?>
                        </div>
                        <div class="col-md-2">
                            <label class="col-form-label">Depth</label>
                            <div class="input-group">
                                <input type="text" class="form-control number" name="depth_<?php echo $p['id'];?>" id="depth_<?php echo $p['id'];?>" value="<?php if($p['depth'] > 0) echo $p['depth'];?>" />
                                <span class="input-group-addon">cm</span>
                            </div>
                            <?php echo Form::displayError("depth_{$p['id']}");?>
                        </div>
                        <div class="col-md-2">
                            <label class="col-form-label">Height</label>
                            <div class="input-group">
                                <input type="text" class="form-control number" name="height_<?php echo $p['id'];?>" id="height_<?php echo $p['id'];?>" value="<?php if($p['height'] > 0) echo $p['height'];?>" />
                                <span class="input-group-addon">cm</span>
                            </div>
                            <?php echo Form::displayError("height_{$p['id']}");?>
                        </div>
                        <div class="col-md-1">
                            <label class="col-form-label">Active</label>
                            <div class="checkbox checkbox-default">
                                <input class="form-check-input styled" type="checkbox" id="active_<?php echo $p['id'];?>" name="active_<?php echo $p['id'];?>" <?php if($p['active'] > 0) echo "checked";?> />
                                <label for="active_<?php echo $p['id'];?>"></label>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <label class="col-form-label">Multiples</label>
                            <div class="checkbox checkbox-default">
                                <input class="form-check-input styled" type="checkbox" id="multiples_<?php echo $p['id'];?>" name="multiples_<?php echo $p['id'];?>" <?php if($p['multiples'] > 0) echo "checked";?> />
                                <label for="multiples_<?php echo $p['id'];?>"></label>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <label class="col-form-label">&nbsp;</label>
                            <div class="input-group">
                                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                                <input type="hidden" name="line_id" value="<?php echo $p['id'];?>" />
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
                    <h2><i class="fas fa-exclamation-triangle"></i> No Packing Types Listed</h2>
                    <p>You will need to add some first</p>
                </div>
            </div>
        </div>
    <?php endif;?>
</div>