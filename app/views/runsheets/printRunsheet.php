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
                    </form>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>