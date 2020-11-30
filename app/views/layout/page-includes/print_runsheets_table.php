<?php
function getDriverTasks($driver, $runsheet_id)
{
    $driver_name = ucwords($driver['name']);
    $task_ids = array();
    $html = "<td>$driver_name</td>";
    $html .= "<td>";
    foreach($driver['tasks'] as $task)
    {
        $task_number = ($task['job_number'] > 0)? "JOB: ".$task['customer']." - ".$task['job_number'] : ($task['order_number'] > 0)? "ORDER: ".$task['customer']." - ".$task['order_number'] : "MISCELLANEOUS TASK";
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
<table class="table-striped table-hover datatable-multi-row datatable-printbuttons" id="finalise_runsheets_table" width="80%">
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
                <td data-datatable-multi-row-rowspan="<?php echo $rows;?>" style="vertical-align: middle">
                    <h4><?php echo date('D jS M', $timestamp );?></h4>
                    <script type="x/template" class="extra-row-content">
                        <?php for($i = 1; $i < $rows; ++$i):?>
                            <tr>
                                <?php echo getDriverTasks($rs['drivers'][$i], $rs['runsheet_id']);?>
                            </tr>
                        <?php endfor; ?>
                    </script>
                </td>
            <?php echo getDriverTasks($rs['drivers'][0], $rs['runsheet_id']); ?>
            </tr>
        <?php endforeach;?>
    </tbody>
</table>