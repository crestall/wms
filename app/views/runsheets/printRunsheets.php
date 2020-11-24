<?php
function getDriverTasks($driver, $runsheet_id)
{
    $driver_name = ucwords($driver['name']);
    $task_ids = array();
    $html = "<td>$driver_name</td>";
    $html .= "<td>";
    foreach($driver['tasks'] as $task)
    {
        $task_number = ($task['job_number'] > 0)? "JOB: ".$task['customer']." - ".$task['job_number'] : "ORDER: ".$task['customer']." - ".$task['order_number'];
        $task_number .= (isset($task['client_order_id']) && !empty($task['client_order_id']))? " (".$task['client_order_id'].")" : "";
        $shipto = $task['shipto'];
        $shipto .= (!empty($task['attention']))? " - ATTN: ".$task['attention'] : "";
        $html .= "<div class='border-bottom border-secondary border-bottom-dashed mb-3 pb-3 pl-3'>";
        $html .= "
                <span class='font-weight-bold'>$task_number</span><br>
                <span class='ml-3'>$shipto</span><br>
                <span class='ml-3'>{$task['address']['suburb']}</span>
        ";
        $html .= "</div>";
        $task_ids[] = $task['task_id'];
        $print_text = ($task['printed'] == 0)? "Print Runsheeet" : "Reprint Runsheet";
    }
    //$tid_string = implode(",", $task_ids);
    $tids = htmlspecialchars(json_encode($task_ids), ENT_QUOTES, 'UTF-8');
    $html .= "</td>";
    $html .= "<td>
                <p><button class='btn btn-sm btn-outline-success print-sheet' data-runsheetid='$runsheet_id' data-driverid='{$driver['id']}'>{$print_text}</button></p>
                <!-- p><button class='btn btn-sm btn-outline-danger remove-driver' data-runsheetid='$runsheet_id' data-taskids='$tids'>Remove Driver</button></p>
                <p><button class='btn btn-sm btn-outline-danger remove-tasks' data-runsheetid='$runsheet_id' data-taskids='$tids'>Remove Selected Tasks</button></p>
                <p><button class='btn btn-sm btn-outline-fsg new-driver' data-runsheetid='$runsheet_id' data-taskids='$tids'>Assign New Driver</button></p -->
            </td>";
    return $html;
}
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php if(count($runsheets)):?>
            <?php //echo "<pre>",print_r($runsheets),"</pre>"; //die();?>
            <div class="row">
                <div class="col-12 text-right">
                    <?php if($completed);?>
                        <a class="btn btn-outline-fsg" href="/runsheets/print-runsheets">View All</a>
                    <?php else:?>
                        <a class="btn btn-outline-fsg" href="/runsheets/print-runsheets/complete=off">View Only Not Completed Sheets</a>
                    <?php endif;?>
                </div>
            </div>
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
                <table class="table-striped table-hover" id="finalise_runsheets_table" width="80%">
                    <thead>
                        <tr>
                            <th>Runsheet Day</th>
                            <th>Driver</th>
                            <th>Tasks</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($runsheets as $timestamp => $rs):
                            $rows = count($rs['drivers']);?>
                            <tr>
                                <td rowspan="<?php echo $rows;?>"><?php echo date('D jS M', $timestamp );?></td>
                                <?php echo getDriverTasks($rs['drivers'][0], $rs['runsheet_id']);?>
                            </tr>
                            <?php for($i = 1; $i < $rows; ++$i):?>
                                <tr>
                                    <?php echo getDriverTasks($rs['drivers'][$i], $rs['runsheet_id']);?>
                                </tr>
                            <?php endfor;?>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        <?php else:?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="errorbox">
                        <h2><i class="fas fa-exclamation-triangle"></i> No Runsheets Listed For Finalising</h2>
                    </div>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>