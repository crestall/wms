<?php
$date_filter = "Completed";
function getDriverTable($driver)
{
    $driver_name = ucwords($driver['name']);
    $drows = count($driver['tasks']);
    $html = "<table width='100%'>";
    $html .= "
            <thead>
                <tr>
                    <td>Driver</td>
                    <td>Job/Order Number</td>
                    <td>Client</td>
                    <td>Units</td>
                    <td>Deliver To</td>
                </tr>
            </thead>
    ";
    $html .= "<tbody>";
    $html .= "<tr>";
    $html .= "<td rowspan='$drows'>$driver_name</td>";
    foreach($driver['tasks'] as $task)
    {
        $task_number = ($task['job_number'] > 0)? "JOB: ".$task['job_number'] : "ORDER: ".$task['order_number'];
        $html .= "<td>$task_number</td>";
        $html .= "<td>{$task['client']}</td>";
        $html .= "<td>{$task['units']}</td>";
        $address = $task['customer']."<br>".Utility::formatAddressWeb($task['address']);
        $html .= "<td>$address</td>";
        $html .= "</tr><tr>";
    }
    rtrim($html, "<tr>");
    $html .= "</tbody></tr></table>";
    return $html;
}
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/between_dates.php");?>
        <div class="row">
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="form-group">
                    <label>Filter By Driver</label>
                    <select id="driver_selector" class="form-control selectpicker" data-style="btn-outline-secondary" data-live-search="true"><option value="0">All Drivers</option><?php echo $this->controller->driver->getSelectDrivers($driver_id);?></select>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="form-group">
                    <label>Filter By Client</label>
                    <select id="client_selector" class="form-control selectpicker" data-style="btn-outline-secondary" data-live-search="true"><option value="0">All Clients</option><?php echo $this->controller->client->getSelectClients($client_id);?></select>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="form-group">
                    <label>Filter By Customer</label>
                    <select id="customer_selector" class="form-control selectpicker" data-style="btn-outline-secondary" data-live-search="true"><option value="0">All Customers</option><?php echo $this->controller->productioncustomer->getSelectCustomers($customer_id);?></select>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="form-group">
                    <label style="width:100%">&nbsp;</label>
                    <button id="csv_download" class="btn btn-outline-success" style="width:100%"><i class="far fa-file-alt"></i>&nbsp;Download As CSV</button>
                </div>
            </div>
        </div>
        <?php if(count($runsheets)):?>
            <?php //echo "<pre>",print_r($runsheets),"</pre>"; //die();?>
            <div class="row">
                <div class="col-12">
                    <?php if(isset($_SESSION['feedback'])) :?>
                       <div class='feedbackbox'><?php echo Session::getAndDestroy('feedback');?></div>
                    <?php endif; ?>
                    <?php if(isset($_SESSION['errorfeedback'])) :?>
                       <div class='errorbox'><?php echo Session::getAndDestroy('errorfeedback');?></div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="row">
                <?php foreach($runsheets as $timestamp => $rs):?>
                    <div class="col-sm-12 col-lg-6 mb-3">
                        <div class="card h-100 border-secondary runsheet-card">
                            <div class="card-header bg-secondary text-white">
                                Runsheet for <?php echo date('D jS M', $timestamp );?><br>
                                Completed on <?php echo date('d/m/Y', $rs['updated_date'] );?>
                            </div>
                            <div class="card-body">
                                <?php foreach($rs['drivers'] as $driver):?>
                                    <div class="col-12 mb-3">
                                        <div class="card h-100 border-secondary driver-card">
                                            <div class="card-header font-weight-bold">
                                                <?php echo ucwords($driver['name']);?>
                                            </div>
                                            <div class="card-body">
                                                <?php foreach($driver['tasks'] as $task):
                                                    $task_number = ($task['job_number'] > 0)? $task['job_number'] : $task['order_number'];?>
                                                    <div class="border-bottom border-secondary border-bottom-dashed mb-3">
                                                        <div class="row">
                                                            <label class="col-5">Job/Order Number:</label>
                                                            <div class="col-7"><?php echo $task_number;?></div>
                                                        </div>
                                                        <?php if(!empty($task['client'])):?>
                                                            <div class="row">
                                                                <label class="col-5">Client:</label>
                                                                <div class="col-7"><?php echo $task['client'];?></div>
                                                            </div>
                                                        <?php endif;?>
                                                        <?php if(!empty($task['units'])):?>
                                                            <div class="row">
                                                                <label class="col-5">Units:</label>
                                                                <div class="col-7"><?php echo $task['units'];?></div>
                                                            </div>
                                                        <?php endif;?>
                                                        <div class="row">
                                                            <label class="col-5">Deliver To:</label>
                                                            <div class="col-7"><?php echo $task['customer'];?></div>
                                                        </div>
                                                        <div class="row">
                                                            <label class="col-5">Address:</label>
                                                            <div class="col-7"><?php echo $task['address']['address'];?></div>
                                                        </div>
                                                        <?php if(!empty($task['address2'])):?>
                                                            <div class="row">
                                                                <div class="col-7 offset-5"><?php echo $task['address']['address2'];?></div>
                                                            </div>
                                                        <?php endif;?>
                                                        <div class="row">
                                                            <div class="col-7 offset-5"><?php echo $task['address']['suburb'];?></div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-7 offset-5"><?php echo $task['address']['postcode'];?></div>
                                                        </div>
                                                    </div>
                                                <?php endforeach;?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach;?>
                            </div>
                        </div>
                    </div>
                <?php endforeach;?>
            </div>
        <?php else:?>
            <div class="row">
                <div class="col-12">
                    <div class="errorbox">
                        <h2><i class="fas fa-exclamation-triangle"></i> No Completed Runsheets Listed</h2>
                        <p>Either there are no completed runsheets or you need to remove some filters</p>
                    </div>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>