<?php
$role = Session::getUserRole();
$charge_level = (empty(Form::value('charge_level')))? "Standard" : "Urgent";
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <?php echo Form::displayError('general');?>
        <form id="add-movementreason"  method="post" enctype="multipart/form-data" action="/form/procUrgencyAdd" class="mb-3">
            <div class="row">
                <div class="col-lg-12">
                    <h3>Add Urgency</h3>
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
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Cut Off Time</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="cut_off" id="cut_off" value="<?php echo Form::value('cut_off');?>" />
                    <?php echo Form::displayError('cut_off');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Charge Level</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="charge_level" id="charge_level" value="<?php echo $charge_level;?>" <?php if($role !== "super admin") echo "readonly";?> >
                </div>
            </div>
            <?php if($role === "super admin"):?>
                <div class="form-group row custom-control custom-checkbox custom-control-right">
                    <input class="custom-control-input" type="checkbox" id="locked" name="locked" <?php if(!empty(Form::value('locked'))) echo 'checked';?> />
                    <label class="custom-control-label col-md-3" for="locked">Locked</label>
                </div>
            <?php endif;?>
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <div class="form-group row">
                <label class="col-md-3 col-form-label">&nbsp;</label>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-sm btn-outline-secondary">Add Urgency</button>
                </div>
            </div>
        </form>
        <div id="waiting" class="row">
            <div class="col-lg-12 text-center">
                <h2>Drawing Table..</h2>
                <p>May take a few moments</p>
                <img class='loading' src='/images/preloader.gif' alt='loading...' />
            </div>
        </div>
        <div id="table_holder" style="display:none">
            <div class="row" id="tablefeedback" style="display: none"></div>
            <?php if(count($urgencies)):?>
                <div class="row">
                    <div class="col-lg-12">
                        <table width="100%" class="table-striped table-hover" id="view_urgencies_table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>
                                        Cut Off Time<br>
                                        <span class="inst">Only use whole hour numbers up to 24</span>
                                    </th>
                                    <th>Active</th>
                                    <?php if($role === "super admin"):?>
                                        <th>Locked</th>
                                    <?php endif;?>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($urgencies as $r): ?>
                                    <tr id="row_<?php echo $r['id'];?>">
                                        <td>
                                            <input type="text" class="form-control required" name="name_<?php echo $r['id'];?>" id="name_<?php echo $r['id'];?>" value="<?php echo $r['name'];?>" <?php if($role != "super admin" && $r['locked'] > 0) echo "disabled";?>  />
                                            <input type="hidden" name="current_name_<?php echo $r['id'];?>" id="current_name_<?php echo $r['id'];?>" value="<?php echo $r['name'];?>" />
                                        </td>
                                        <td>
                                            <input type="text" class="form-control required" name="cutoff_<?php echo $r['id'];?>" id="cutoff_<?php echo $r['id'];?>" value="<?php echo $r['cut_off'];?>" <?php if($role != "super admin" && $r['locked'] > 0) echo "disabled";?>  />
                                        </td>
                                        <td>
                                            <div class="form-group row custom-control custom-checkbox custom-control-right">
                                                <input class="custom-control-input" type="checkbox" id="active_<?php echo $r['id'];?>" name="active_<?php echo $r['id'];?>" <?php if($r['active'] > 0) echo 'checked';?> />
                                                <label class="custom-control-label col-md-3" for="active_<?php echo $r['id'];?>"></label>
                                            </div>
                                        </td>
                                        <?php if($role === "super admin"):?>
                                            <td>
                                                <div class="form-group row custom-control custom-checkbox custom-control-right">
                                                    <input class="custom-control-input" type="checkbox" id="locked_<?php echo $r['id'];?>" name="locked_<?php echo $r['id'];?>" <?php if($r['locked'] > 0) echo 'checked';?> />
                                                    <label class="custom-control-label col-md-3" for="locked_<?php echo $r['id'];?>"></label>
                                                </div>
                                            </td>
                                        <?php endif;?>
                                        <td>
                                            <a class="btn btn-outline-secondary update_urgency" data-urgencyid="<?php echo $r['id'];?>">Update Details</a><span class="label label-success" id="updated_<?php echo $r['id'];?>"></span>
                                        </td>
                                    </tr>
                                <?php endforeach;?>
                            </tbody>

                        </table>
                    </div>
                </div>
            <?php else:?>
                <div class="col-lg-12">
                    <div class="errorbox">
                        <p>No reasons listed yet</p>
                    </div>
                </div>
            <?php endif;?>
        </div>
    </div>
</div>