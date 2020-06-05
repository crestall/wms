<?php

?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
    <?php echo Form::displayError('general');?>
    <div class="row">
        <form id="add-config-value"  method="post" action="/form/procConfigAdd">
            <div class="row">
                <div class="col-lg-12">
                    <h3>Add New Configuration Value</h3>
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
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Value</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="value" id="value" value="<?php echo Form::value('value');?>" />
                    <?php echo Form::displayError('value');?>
                </div>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <div class="form-group row">
                <label class="col-md-3 col-form-label">&nbsp;</label>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Add This</button>
                </div>
            </div>
        </form>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <h2>Current Configuration</h2>
        </div>
    </div>
        <?php if(count($configuraion)):?>
        <div class="row">
            <?php foreach($configuration as $c):?>
                <form class="edit-config" action="/form/procConfigEdit" method="post">
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label class="col-form-label">Name</label>
                            <input type="text" class="form-control required" name="name_<?php echo $c['id'];?>" id="name_<?php echo $c['id'];?>" value="<?php echo $c['name'];?>" />
                            <?php echo Form::displayError("name_{$c['id']}");?>
                        </div>
                        <div class="col-md-3">
                            <label class="col-form-label">Value</label>
                            <input type="text" class="form-control" name="value_<?php echo $c['id'];?>" id="value<?php echo $c['id'];?>" value="<?php echo $c['value'];?>" />
                            <?php echo Form::displayError("value{$c['id']}");?>
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
                    <h2><i class="fas fa-exclamation-triangle"></i> No Couriers Listed</h2>
                    <p>You will need to add some first</p>
                </div>
            </div>
        </div>
    <?php endif;?>
</div>
