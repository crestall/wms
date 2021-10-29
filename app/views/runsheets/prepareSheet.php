<?php

?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php if($runsheet_id == 0):?>
            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/no_runsheet_id.php");?>
        <?php elseif(empty($runsheet)):?>
            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/no_runsheet_found.php");?>
        <?php else:?>
            <?php
            //$driver_id = (empty(Form::value('driver_id')))? $runsheet['driver_id'] : Form::value('driver_id');
            //$units = (empty(Form::value('units')))? ($runsheet['units'] > 0)?$runsheet['units']: "" : Form::value('units');
            //echo "<p>Form Values For 381: ".getFormValue()."</p>";
            //echo "<pre>",print_r($runsheet),"</pre>"; die();
            ?>
            <div class="row">
                <div class="col-12">
                    <h2>Runsheet for <?php echo date('D jS M', $runsheet['runsheet_day'] );?></h2>
                </div>
                <div class="col-12">
                    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
                </div>
                <div class="col-12">
                    <?php echo Form::displayError('general');?>
                </div>
                <div class="col-12">
                    <form id="prepare_runsheet" method="post" action="/form/procPrepareRunsheet" >
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Driver</label>
                            <div class="col-md-5">
                                <select id="driver_id" name="driver_id" class="form-control selectpicker" data-style="btn-outline-secondary"><option value="0">--Select One--</option><?php echo $this->controller->driver->getSelectDrivers( Form::value('driver_id') );?></select>
                                <?php echo Form::displayError('driver_id');?>
                            </div>
                        </div>
                        <?php if(count($runsheet['jobs'])):?>
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label">Jobs To Include</label>
                                <div class="col-md-10 mb-3">
                                    <?php foreach($runsheet['jobs'] as $task):
                                        $shipto = (!empty(Form::value('tasks,jobs,'.$task['job_id'].',shipto')))? Form::value('tasks,jobs,'.$task['job_id'].',shipto') : $task['job_shipto'];
                                        $units = (!empty(Form::value('tasks,jobs,'.$task['job_id'].',units')))? Form::value('tasks,jobs,'.$task['job_id'].',units') : $task['job_units'];
                                        $attention = (!empty(Form::value('tasks,jobs,'.$task['job_id'].',attention')))? Form::value('tasks,jobs,'.$task['job_id'].',attention') : $task['job_attention'];
                                        $delivery_instructions = (!empty(Form::value('tasks,jobs,'.$task['job_id'].',delivery_instructions')))? Form::value('tasks,jobs,'.$task['job_id'].',delivery_instructions') : $task['job_delivery_instructions'];
                                        $address = (!empty(Form::value('tasks,jobs,'.$task['job_id'].',address')))? Form::value('tasks,jobs,'.$task['job_id'].',address') : $task['job_address'];
                                        $address2 = (!empty(Form::value('tasks,jobs,'.$task['job_id'].',address2')))? Form::value('tasks,jobs,'.$task['job_id'].',address2') : $task['job_address2'];
                                        $suburb = (!empty(Form::value('tasks,jobs,'.$task['job_id'].',suburb')))? Form::value('tasks,jobs,'.$task['job_id'].',suburb') : $task['job_suburb'];
                                        $postcode = (!empty(Form::value('tasks,jobs,'.$task['job_id'].',postcode')))? Form::value('tasks,jobs,'.$task['job_id'].',postcode') : $task['job_postcode'];
                                        ?>
                                        <div class="form-group row">
                                            <div class="col-12">
                                                <label class="col-form-label" for="task_<?php echo $task['task_id'];?>"></label>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input task" data-taskid="<?php echo $task['task_id'];?>" id="task_<?php echo $task['task_id'];?>" name="tasks[jobs][<?php echo $task['job_id'];?>][include]" <?php if(!empty(Form::value('tasks,jobs,'.$task['job_id'].',include'))) echo "checked";?> />
                                                    <label class="custom-control-label" for="task_<?php echo $task['task_id'];?>"><span class="font-weight-bold"><?php echo $task['job_number'];?></span> - <?php echo $task['job_customer'];?></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="task_address_holder border border-secondary rounded p-3 bg-light" id="task_<?php echo $task['task_id'];?>_address_holder">
                                            <div class="form-group row">
                                                <label class="col-3">Units</label>
                                                <div class="col-6">
                                                    <input type="text" class="form-control" name="tasks[jobs][<?php echo $task['job_id'];?>][units]" value="<?php echo $units;?>">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Deliver To</label>
                                                <div class="col-md-6">
                                                    <input type="text" class="required form-control" name="tasks[jobs][<?php echo $task['job_id'];?>][shipto]" id="task_<?php echo $task['task_id'];?>_shipto" value="<?php echo $shipto;?>">
                                                </div>
                                                <?php echo Form::displayError('shipto_'.$task['task_id']);?>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label">Attention</label>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" name="tasks[jobs][<?php echo $task['job_id'];?>][attention]" id="task_<?php echo $task['task_id'];?>_attention" value="<?php echo $attention?>">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label">Delivery Instructions</label>
                                                <div class="col-md-6">
                                                    <textarea class="form-control" name="tasks[jobs][<?php echo $task['job_id'];?>][delivery_instructions]" id="task_<?php echo $task['task_id'];?>_delivery_instructions" placeholder="Instructions For Driver"><?php echo $delivery_instructions;?></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Address Line 1</label>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control required address_ac" name="tasks[jobs][<?php echo $task['job_id'];?>][address]" id="task_<?php echo $task['task_id'];?>_address" value="<?php echo $address;?>" />
                                                    <?php echo Form::displayError('address_'.$task['task_id']);?>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3">Address Line 2</label>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" name="tasks[jobs][<?php echo $task['job_id'];?>][address2]" id="task_<?php echo $task['task_id'];?>_address2" value="<?php echo $address2;?>" />
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Suburb</label>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control required suburb_ac" name="tasks[jobs][<?php echo $task['job_id'];?>][suburb]" id="task_<?php echo $task['task_id'];?>_suburb" value="<?php echo $suburb;?>" />
                                                    <?php echo Form::displayError('suburb_'.$task['task_id']);?>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 "><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Postcode</label>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control required postcode_ac" name="tasks[jobs][<?php echo $task['job_id'];?>][postcode]" id="task_<?php echo $task['task_id'];?>_postcode" value="<?php echo $postcode;?>" />
                                                    <?php echo Form::displayError('postcode_'.$task['task_id']);?>
                                                </div>
                                            </div>
                                            <input type="hidden" name="tasks[jobs][<?php echo $task['job_id'];?>][task_id]" value="<?php echo $task['task_id'];?>" >
                                        </div>
                                    <?php endforeach;?>
                                </div>
                            </div>
                        <?php endif;?>
                        <?php if(count($runsheet['orders'])):?>
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label">Orders To Include</label>
                                <div class="col-md-10 mb-3">
                                    <?php foreach($runsheet['orders'] as $task):
                                        $shipto = (!empty(Form::value('tasks,orders,'.$task['order_id'].',shipto')))? Form::value('tasks,orders,'.$task['order_id'].',shipto') : $task['order_customer'];
                                        $units = (!empty(Form::value('tasks,orders,'.$task['order_id'].',units')))? Form::value('tasks,orders,'.$task['order_id'].',units') : $task['order_units'];
                                        $delivery_instructions = (!empty(Form::value('tasks,orders,'.$task['order_id'].',delivery_instructions')))? Form::value('tasks,orders,'.$task['order_id'].',delivery_instructions') : $task['order_delivery_instructions'];
                                        $address = (!empty(Form::value('tasks,orders,'.$task['order_id'].',address')))? Form::value('tasks,orders,'.$task['order_id'].',address') : $task['order_address'];
                                        $address2 = (!empty(Form::value('tasks,orders,'.$task['order_id'].',address2')))? Form::value('tasks,orders,'.$task['order_id'].',address2') : $task['order_address2'];
                                        $suburb = (!empty(Form::value('tasks,orders,'.$task['order_id'].',suburb')))? Form::value('tasks,orders,'.$task['order_id'].',suburb') : $task['order_suburb'];
                                        $postcode = (!empty(Form::value('tasks,orders,'.$task['order_id'].',postcode')))? Form::value('tasks,orders,'.$task['order_id'].',postcode') : $task['order_postcode'];
                                        $head_string = "<span class='font-weight-bold'>".$task['order_number']."</span> - ".$task['order_customer']."(".$task['order_client'].")";
                                        $head_string .= (empty($task['client_order_id']))? "" : " - ".$task['client_order_id'];
                                        ?>
                                        <div class="form-group row">
                                            <div class="col-12">
                                                <label class="col-form-label" for="task_<?php echo $task['task_id'];?>"></label>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input task" data-taskid="<?php echo $task['task_id'];?>" id="task_<?php echo $task['task_id'];?>" name="tasks[orders][<?php echo $task['order_id'];?>][include]" <?php if(!empty(Form::value('tasks,orders,'.$task['order_id'].',include'))) echo "checked";?> />
                                                    <label class="custom-control-label" for="task_<?php echo $task['task_id'];?>"><?php echo $head_string;?></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="task_address_holder border border-secondary rounded p-3 bg-light" id="task_<?php echo $task['task_id'];?>_address_holder">
                                            <div class="form-group row">
                                                <label class="col-3">Units</label>
                                                <div class="col-6">
                                                    <input type="text" class="form-control" name="tasks[orders][<?php echo $task['order_id'];?>][units]" value="<?php echo $units;?>">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Deliver To</label>
                                                <div class="col-md-6">
                                                    <input type="text" class="required form-control" name="tasks[orders][<?php echo $task['order_id'];?>][shipto]" id="task_<?php echo $task['task_id'];?>_shipto" value="<?php echo $shipto;?>">
                                                </div>
                                                <?php echo Form::displayError('shipto_'.$task['task_id']);?>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label">Delivery Instructions</label>
                                                <div class="col-md-6">
                                                    <textarea class="form-control" name="tasks[orders][<?php echo $task['order_id'];?>][delivery_instructions]" id="task_<?php echo $task['task_id'];?>_delivery_instructions" placeholder="Instructions For Driver"><?php echo $delivery_instructions;?></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Address Line 1</label>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control required address_ac" name="tasks[orders][<?php echo $task['order_id'];?>][address]" id="task_<?php echo $task['task_id'];?>_address" value="<?php echo $address;?>" />
                                                    <?php echo Form::displayError('address_'.$task['task_id']);?>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3">Address Line 2</label>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" name="tasks[orders][<?php echo $task['order_id'];?>][address2]" id="task_<?php echo $task['task_id'];?>_address2" value="<?php echo $address2;?>" />
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Suburb</label>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control required suburb_ac" name="tasks[orders][<?php echo $task['order_id'];?>][suburb]" id="task_<?php echo $task['task_id'];?>_suburb" value="<?php echo $suburb;?>" />
                                                    <?php echo Form::displayError('suburb_'.$task['task_id']);?>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 "><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Postcode</label>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control required postcode_ac" name="tasks[orders][<?php echo $task['order_id'];?>][postcode]" id="task_<?php echo $task['task_id'];?>_postcode" value="<?php echo $postcode;?>" />
                                                    <?php echo Form::displayError('postcode_'.$task['task_id']);?>
                                                </div>
                                            </div>
                                            <input type="hidden" name="tasks[orders][<?php echo $task['order_id'];?>][task_id]" value="<?php echo $task['task_id'];?>" >
                                        </div>
                                    <?php endforeach;?>
                                </div>
                            </div>
                        <?php endif;?>
                        <?php if(count($runsheet['tasks'])):?>
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label">Extra Tasks To Include</label>
                                <div class="col-md-10 mb-3">
                                    <?php foreach($runsheet['tasks'] as $task):
                                        $shipto = (!empty(Form::value('tasks,tasks,'.$task['task_id'].',shipto')))? Form::value('tasks,tasks,'.$task['task_id'].',shipto') : $task['shipto'];
                                        $units = (!empty(Form::value('tasks,tasks,'.$task['task_id'].',units')))? Form::value('tasks,tasks,'.$task['task_id'].',units') : $task['units'];
                                        $delivery_instructions = (!empty(Form::value('tasks,tasks,'.$task['task_id'].',delivery_instructions')))? Form::value('tasks,tasks,'.$task['task_id'].',delivery_instructions') : $task['delivery_instructions'];
                                        $address = (!empty(Form::value('tasks,tasks,'.$task['task_id'].',address')))? Form::value('tasks,tasks,'.$task['task_id'].',address') : $task['address'];
                                        $address2 = (!empty(Form::value('tasks,tasks,'.$task['task_id'].',address2')))? Form::value('tasks,tasks,'.$task['task_id'].',address2') : $task['address2'];
                                        $suburb = (!empty(Form::value('tasks,tasks,'.$task['task_id'].',suburb')))? Form::value('tasks,tasks,'.$task['task_id'].',suburb') : $task['suburb'];
                                        $postcode = (!empty(Form::value('tasks,tasks,'.$task['task_id'].',postcode')))? Form::value('tasks,tasks,'.$task['task_id'].',postcode') : $task['postcode'];
                                        $head_string = "<span class='font-weight-bold'>Miscellaneous Tasks</span> - ".$task['shipto'];
                                        ?>
                                        <div class="form-group row">
                                            <div class="col-12">
                                                <label class="col-form-label" for="task_<?php echo $task['task_id'];?>"></label>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input task" data-taskid="<?php echo $task['task_id'];?>" id="task_<?php echo $task['task_id'];?>" name="tasks[tasks][<?php echo $task['task_id'];?>][include]" <?php if(!empty(Form::value('tasks,tasks,'.$task['task_id'].',include'))) echo "checked";?> />
                                                    <label class="custom-control-label" for="task_<?php echo $task['task_id'];?>"><?php echo $head_string;?></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="task_address_holder border border-secondary rounded p-3 bg-light" id="task_<?php echo $task['task_id'];?>_address_holder">
                                            <div class="form-group row">
                                                <label class="col-3">Units</label>
                                                <div class="col-6">
                                                    <input type="text" class="form-control" name="tasks[tasks][<?php echo $task['task_id'];?>][units]" value="<?php echo $units;?>">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Deliver To</label>
                                                <div class="col-md-6">
                                                    <input type="text" class="required form-control" name="tasks[tasks][<?php echo $task['task_id'];?>][shipto]" id="task_<?php echo $task['task_id'];?>_shipto" value="<?php echo $shipto;?>">
                                                </div>
                                                <?php echo Form::displayError('shipto_'.$task['task_id']);?>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label">Delivery Instructions</label>
                                                <div class="col-md-6">
                                                    <textarea class="form-control" name="tasks[tasks][<?php echo $task['task_id'];?>][delivery_instructions]" id="task_<?php echo $task['task_id'];?>_delivery_instructions" placeholder="Instructions For Driver"><?php echo $delivery_instructions;?></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Address Line 1</label>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control required address_ac" name="tasks[tasks][<?php echo $task['task_id'];?>][address]" id="task_<?php echo $task['task_id'];?>_address" value="<?php echo $address;?>" />
                                                    <?php echo Form::displayError('address_'.$task['task_id']);?>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3">Address Line 2</label>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" name="tasks[tasks][<?php echo $task['task_id'];?>][address2]" id="task_<?php echo $task['task_id'];?>_address2" value="<?php echo $address2;?>" />
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Suburb</label>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control required suburb_ac" name="tasks[tasks][<?php echo $task['task_id'];?>][suburb]" id="task_<?php echo $task['task_id'];?>_suburb" value="<?php echo $suburb;?>" />
                                                    <?php echo Form::displayError('suburb_'.$task['task_id']);?>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 "><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Postcode</label>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control required postcode_ac" name="tasks[tasks][<?php echo $task['task_id'];?>][postcode]" id="task_<?php echo $task['task_id'];?>_postcode" value="<?php echo $postcode;?>" />
                                                    <?php echo Form::displayError('postcode_'.$task['task_id']);?>
                                                </div>
                                            </div>
                                            <input type="hidden" name="tasks[tasks][<?php echo $task['task_id'];?>][task_id]" value="<?php echo $task['task_id'];?>" >
                                        </div>
                                    <?php endforeach;?>
                                </div>
                            </div>
                        <?php endif;?>
                        <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" >
                        <input type="hidden" name="runsheet_id" id="runsheet_id" value="<?php echo $runsheet_id;?>" >
                        <div class="form-group row">
                            <div class="col-md-5 offset-md-2">
                                <button type="submit" class="btn btn-outline-secondary" id="submitter">Save Updates</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>