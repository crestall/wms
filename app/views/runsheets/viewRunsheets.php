<?php
function getDriverTasks($driver, $runsheet_id)
{
    $driver_name = ucwords($driver['name']);
    $task_ids = array();
    $html = "<td>$driver_name</td>";
    $html .= "<td>";
    foreach($driver['tasks'] as $task)
    {
        $task_number = ($task['job_number'] > 0)? "JOB: ".$task['job_number'] : "ORDER: ".$task['order_number'];
        $html .= "<div class='border-bottom border-secondary border-bottom-dashed mb-3 pb-3 pl-3'>";
        $html .= "  <div class='checkbox checkbox-default'>
                        <input type='checkbox' class='task-select runsheetid_$runsheet_id styled' data-taskid='{$task['task_id']}' id='select_{$task['task_id']}' checked />
                        <label for='select_{$task['task_id']}'>$task_number</label>
                    </div>";
        $html .= "</div>";
        $task_ids[] = $task['task_id'];
    }
    //$tid_string = implode(",", $task_ids);
    $tids = htmlspecialchars(json_encode($task_ids), ENT_QUOTES, 'UTF-8');
    $html .= "</td>";
    $html .= "<td>
                <p><button class='btn btn-sm btn-outline-success print-sheet' data-runsheetid='$runsheet_id' data-taskids='$tids'>Print Runsheet</button></p>
                <p><button class='btn btn-sm btn-outline-danger remove-driver' data-runsheetid='$runsheet_id' data-taskids='$tids'>Remove Driver</button></p>
                <p><button class='btn btn-sm btn-outline-danger remove-tasks' data-runsheetid='$runsheet_id' data-taskids='$tids'>Remove Selected Tasks</button></p>
                <p><button class='btn btn-sm btn-outline-fsg new-driver' data-runsheetid='$runsheet_id' data-taskids='$tids'>Assign New Driver</button></p>
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