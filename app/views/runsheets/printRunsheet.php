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
            <p><button class="btn runsheet">Print</button></p>
            <?php echo "<pre>",print_r($runsheet),"</pre>";?>
            <div class="row">
                <div class="col-12">
                    <h2>Runsheet Details for <?php echo date('D jS M', $runsheet[0]['runsheet_day'] );?></h2>
                </div>
                <div class="col-12">
                    <form id="print_runsheet" method="post" action="/pdf/printRunsheet" target="_blank">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Driver</label>
                            <div class="col-md-4">
                                <select id="driver_id" name="driver_id" class="form-control selectpicker" data-style="btn-outline-secondary"><option value="0">--Select One--</option><?php echo $this->controller->driver->getSelectDrivers( Form::value('driver_id') );?></select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Units</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control number" name="units" id="units" value="<?php echo Form::value('units');?>" />
                                <?php echo Form::displayError('units');?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-4 offset-md-3">
                                <div class="card h-100 border-secondary job-card">
                                    <div class="card-header bg-secondary text-white">
                                        Jobs To Be Printed
                                    </div>
                                    <div class="card-body">
                                        <?php foreach($runsheet as $task):?>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label class="col-form-label" for="task_<?php echo $task['id'];?>"></label>
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="task_<?php echo $task['id'];?>" name="task_<?php echo $task['id'];?>" checked />
                                                        <label class="custom-control-label" for="task_<?php echo $task['id'];?>"><span class="font-weight-bold"><?php echo $task['job_id'];?></span> - <?php echo $task['customer_name'];?></label>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach;?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                        <div class="form-group row">
                            <div class="col-md-4 offset-md-3">
                                <button type="submit" class="btn btn-outline-secondary" id="submitter">Add This Order</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>