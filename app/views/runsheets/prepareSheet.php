<?php

?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php if($runsheet_id == 0):?>
            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/no_runsheet_id.php");?>
        <?php elseif(empty($runsheet)):?>
            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/no_runsheet_found.php");?>
        <?php else:?>
            <?php
            //$driver_id = (empty(Form::value('driver_id')))? $runsheet['driver_id'] : Form::value('driver_id');
            //$units = (empty(Form::value('units')))? ($runsheet['units'] > 0)?$runsheet['units']: "" : Form::value('units');
            echo "Form Values<pre>",print_r(Form::$values),"</pre>";
            ?>
            <div class="row">
                <div class="col-12">
                    <h2>Runsheet for <?php echo date('D jS M', $runsheet['runsheet_day'] );?></h2>
                </div>
                <div class="col-12">
                    <form id="print_runsheet" method="post" action="/form/procPrepareRunsheet" >
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label">Driver</label>
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
                                        echo "Form Values ".Form::value("tasks['jobs'][{$task['job_id']}]['units']");?>
                                        <div class="form-group row">
                                            <div class="col-12">
                                                <label class="col-form-label" for="task_<?php echo $task['task_id'];?>"></label>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input task" data-taskid="<?php echo $task['task_id'];?>" id="task_<?php echo $task['task_id'];?>" name="tasks[jobs][<?php echo $task['job_id'];?>][include]" checked />
                                                    <label class="custom-control-label" for="task_<?php echo $task['task_id'];?>"><span class="font-weight-bold"><?php echo $task['job_number'];?></span> - <?php echo $task['job_customer'];?></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="task_address_holder border border-secondary rounded p-3 bg-light" id="task_<?php echo $task['task_id'];?>_address_holder">
                                            <div class="form-group row">
                                                <label class="col-3">Units</label>
                                                <div class="col-6">
                                                    <input type="text" class="form-control" name="tasks[jobs][<?php echo $task['job_id'];?>][units]" value="<?php if(isset(Form::$values["tasks"]['jobs'][$task['job_id']]['units'])) echo Form::$values["tasks"]['jobs'][$task['job_id']]['units'];?> ">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Deliver To</label>
                                                <div class="col-md-6">
                                                    <input type="text" class="required form-control" name="tasks[jobs][<?php echo $task['job_id'];?>][shipto]" id="task_<?php echo $task['task_id'];?>_shipto" value="<?php echo $task['job_shipto'];?>">
                                                </div>
                                                <?php echo Form::displayError('address');?>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label">Attention</label>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" name="tasks[jobs][<?php echo $task['job_id'];?>][attention]" id="task_<?php echo $task['task_id'];?>_attention" value="<?php echo $task['job_attention'];?>">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label">Delivery Instructions</label>
                                                <div class="col-md-6">
                                                    <textarea class="form-control" name="tasks[jobs][<?php echo $task['job_id'];?>][delivery_instructions]" id="task_<?php echo $task['task_id'];?>_delivery_instructions" placeholder="Instructions For Driver"><?php echo $task['job_delivery_instructions'];?></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Address Line 1</label>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control required" name="tasks[jobs][<?php echo $task['job_id'];?>][address]" id="task_<?php echo $task['task_id'];?>_address" value="<?php echo $task['job_address'];?>" />
                                                    <?php echo Form::displayError('address');?>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3">Address Line 2</label>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" name="tasks[jobs][<?php echo $task['job_id'];?>][address2]" id="task_<?php echo $task['task_id'];?>_address2" value="<?php echo $task['job_address2'];?>" />
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Suburb</label>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control required" name="tasks[jobs][<?php echo $task['job_id'];?>][suburb]" id="task_<?php echo $task['task_id'];?>_suburb" value="<?php echo $task['job_suburb'];?>" />
                                                    <?php echo Form::displayError('suburb');?>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 "><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Postcode</label>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control required" name="tasks[jobs][<?php echo $task['job_id'];?>][postcode]" id="task_<?php echo $task['task_id'];?>_postcode" value="<?php echo $task['job_postcode'];?>" />
                                                    <?php echo Form::displayError('postcode');?>
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
                                    <?php foreach($runsheet['orders'] as $task):?>
                                        <div class="form-group row">
                                            <div class="col-12">
                                                <label class="col-form-label" for="task_<?php echo $task['task_id'];?>"></label>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input task" data-taskid="<?php echo $task['task_id'];?>" id="task_<?php echo $task['task_id'];?>" name="tasks[orders][<?php echo $task['order_id'];?>][include]" checked />
                                                    <label class="custom-control-label" for="task_<?php echo $task['task_id'];?>"><span class="font-weight-bold"><?php echo $task['order_number'];?></span> - <?php echo $task['order_customer']."(".$task['order_client'].")";?></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="task_address_holder border border-secondary rounded p-3 bg-light" id="task_<?php echo $task['task_id'];?>_address_holder">
                                            <div class="form-group row">
                                                <label class="col-3">Units</label>
                                                <div class="col-6">
                                                    <input type="text" class="form-control" name="tasks[orders][<?php echo $task['order_id'];?>][units]" value="<?php echo Form::value("tasks['orders']['".$task['order_id']."']['units']");?>">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Deliver To</label>
                                                <div class="col-md-6">
                                                    <input type="text" class="required form-control" name="tasks[orders][<?php echo $task['order_id'];?>][shipto]" id="task_<?php echo $task['task_id'];?>_shipto" value="<?php echo $task['order_customer'];?>">
                                                </div>
                                                <?php echo Form::displayError('address');?>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label">Delivery Instructions</label>
                                                <div class="col-md-6">
                                                    <textarea class="form-control" name="tasks[orders][<?php echo $task['order_id'];?>][delivery_instructions]" id="task_<?php echo $task['task_id'];?>_delivery_instructions" placeholder="Instructions For Driver"><?php echo $task['order_delivery_instructions'];?></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Address Line 1</label>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control required" name="tasks[orders][<?php echo $task['order_id'];?>][address]" id="task_<?php echo $task['task_id'];?>_address" value="<?php echo $task['order_address'];?>" />
                                                    <?php echo Form::displayError('address');?>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3">Address Line 2</label>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" name="tasks[orders][<?php echo $task['order_id'];?>][address2]" id="task_<?php echo $task['task_id'];?>_address2" value="<?php echo $task['order_address2'];?>" />
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Suburb</label>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control required" name="tasks[orders][<?php echo $task['order_id'];?>][suburb]" id="task_<?php echo $task['task_id'];?>_suburb" value="<?php echo $task['order_suburb'];?>" />
                                                    <?php echo Form::displayError('suburb');?>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 "><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Postcode</label>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control required" name="tasks[orders][<?php echo $task['order_id'];?>][postcode]" id="task_<?php echo $task['task_id'];?>_postcode" value="<?php echo $task['order_postcode'];?>" />
                                                    <?php echo Form::displayError('postcode');?>
                                                </div>
                                            </div>
                                            <input type="hidden" name="tasks[orders][<?php echo $task['order_id'];?>][task_id]" value="<?php echo $task['task_id'];?>" >
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