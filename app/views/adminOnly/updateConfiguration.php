<?php
$r = 1;

?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php"); ?>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
    <?php echo Form::displayError('general');?>
    <?php echo "Something here ".HASH_KEY;?>
    <div class="row">
        <form id="add-config-value"  method="post" action="/form/procConfigAdd">
            <div class="row">
                <div class="col-lg-12">
                    <h3>Add New Configuration Value</h3>
                    <p class="inst">If the name already exists, it will be updated to the new value</p>
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
                    <input type="text" class="form-control required" name="rawvalue" id="rawvalue" value="<?php echo Form::value('rawvalue');?>" />
                    <?php echo Form::displayError('rawvalue');?>
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
            <h2>Current Configuration Values Stored</h2>
        </div>
    </div>
        <?php if(count($configuration_names)):?>
        <div class="row">
            <div class="col-lg-12">
                <table width="100%" class="table-striped table-hover" id="configuration_list" style="width:100%">
                    <thead>
                        <th></th>
                        <th>Name</th>
                        <th></th>
                    </thead>
                    <tbody>
                        <?php foreach($configuration_names as $c):?>
                            <tr>
                                <td class="number"><?php echo $r;?></td>
                                <td><?php echo $c['name'];?></td>
                                <td><button class="btn btn-danger delete" data-configurationid="<?php echo $c['id'];?>">REMOVE</button></td>
                            </tr>
                        <?php ++$r; endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else:?>
        <div class="row">
            <div class="col-lg-12">
                <div class="errorbox">
                    <h2><i class="fas fa-exclamation-triangle"></i> No Configuration Values Listed</h2>
                    <p>You will need to add some first</p>
                </div>
            </div>
        </div>
    <?php endif;?>
</div>
