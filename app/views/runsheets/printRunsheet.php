<?php
$driver_id = (empty(Form::value('driver_id')))? $runsheet['driver_id'] : Form::value('driver_id');
$units = (empty(Form::value('units')))? ($runsheet['units'] > 0)?$runsheet['units']: "" : Form::value('units');
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php if($runsheet_id == 0):?>
            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/no_runsheet_id.php");?>
        <?php elseif(empty($runsheet)):?>
            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/no_runsheet_found.php");?>
        <?php else:?>
            <?php //echo "<pre>",print_r($runsheet),"</pre>"; //die();?>
            <div class="row">
                <div class="col-12">
                    <h2>Runsheet Details for <?php echo date('D jS M', $runsheet['runsheet_day'] );?></h2>
                </div>
                <div class="col-12">
                    <form id="print_runsheet" method="post" action="/pdf/printRunsheet" target="_blank">
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label">Driver</label>
                            <div class="col-md-5">
                                <select id="driver_id" name="driver_id" class="form-control selectpicker" data-style="btn-outline-secondary"><option value="0">--Select One--</option><?php echo $this->controller->driver->getSelectDrivers( $driver_id );?></select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label">Units</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control number" name="units" id="units" value="<?php echo $units;?>" />
                                <?php echo Form::displayError('units');?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-2"><input type="hidden" name="task" id="task"></div>
                            <?php if(count($runsheet['jobs'])):?>
                                <div class="col-md-5">
                                    <div class="card h-100 border-secondary job-card">
                                        <div class="card-header bg-secondary text-white">
                                            Jobs To Be Included
                                        </div>
                                        <div class="card-body">
                                            <?php foreach($runsheet['jobs'] as $task):?>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label class="col-form-label" for="task_<?php echo $task['task_id'];?>"></label>
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input task" id="task_<?php echo $task['task_id'];?>" name="tasks[jobs][<?php echo $task['job_id'];?>]" checked />
                                                            <label class="custom-control-label" for="task_<?php echo $task['task_id'];?>"><span class="font-weight-bold"><?php echo $task['job_number'];?></span> - <?php echo $task['job_customer'];?></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach;?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif;
                            if(count($runsheet['orders'])):?>
                                <div class="col-md-5 offset-md-2">
                                    <div class="card h-100 border-secondary job-card">
                                        <div class="card-header bg-secondary text-white">
                                            Orders To Be Included
                                        </div>
                                        <div class="card-body">
                                            <?php foreach($runsheet['orders'] as $task):?>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label class="col-form-label" for="task_<?php echo $task['task_id'];?>"></label>
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input task" id="task_<?php echo $task['task_id'];?>" name="tasks[orders][<?php echo $task['order_id'];?>]" checked />
                                                            <label class="custom-control-label" for="task_<?php echo $task['task_id'];?>"><span class="font-weight-bold"><?php echo $task['order_number'];?></span> - <?php echo $task['order_customer']."(".$task['order_client_name'].")";?></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach;?>
                                        </div>
                                    </div>
                                </div>
                        <?php endif;?>
                        </div>
                        <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" >
                        <div class="form-group row">
                            <div class="col-md-5 offset-md-2">
                                <button type="submit" class="btn btn-outline-secondary" id="submitter">Print Runsheet</button><br>
                                <span class="inst">Please save a copy if you wish to re-print this particular sheet.</span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>