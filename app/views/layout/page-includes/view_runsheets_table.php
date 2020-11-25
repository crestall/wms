<?php
function getDriverTasks($driver, $runsheet_id)
{
    $driver_name = (empty($driver['name']))? "Not Assigned" : ucwords($driver['name']);
    $all_driver_tasks_completed = true;
    $is_printed = true;
    $task_ids = array();
    $html = "<td style='vertical-align:middle'>$driver_name</td>";
    $html .= "<td>";
    foreach($driver['tasks'] as $task)
    {
        $can_be_completed = true;
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
        if( $task['printed'] == 0 )
            $is_printed = false;
        if($task['completed'] == 0)
            $all_driver_tasks_completed = false;
    }
    //$tid_string = implode(",", $task_ids);
    $can_be_completed = (!$all_driver_tasks_completed && $is_printed);
    $tids = htmlspecialchars(json_encode($task_ids), ENT_QUOTES, 'UTF-8');
    $html .= "</td>";
    if($driver['id'] == 0)
    {
        $html .= "<td>
                    <a href='/runsheets/prepare-runsheet/runsheet={$runsheet_id}' class='btn btn-sm btn-outline-fsg'>Update Driver<br>and Tasks</a>
                </td>";
    }
    else
    {
        $html .= "<td>
                    <p><button class='btn btn-sm btn-outline-success print-sheet' data-runsheetid='$runsheet_id' data-driverid='{$driver['id']}'>{$print_text}</button></p>
        ";
        if($can_be_completed)
            $html .= "<p><a class='btn btn-sm btn-outline-success' href='/runsheets/finalise-runsheet/runsheet={$runsheet_id}/driver={$driver['id']}'>Finalise</a></p>";
        $html .= "</td>";
    }

    return $html;
}
?>
<table class="table-striped table-hover datatable-multi-row" id="view_runsheets_table" width="80%">
    <thead>
        <tr>
            <th>Runsheet Day</th>
            <th>Created</th>
            <th>Last Update</th>
            <th>Driver</th>
            <th>Tasks</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($runsheets as $timestamp => $rs):
            //$cb = $db->queryValue('users', array('id' => $rs['created_by']), 'name');
            $cs = date('d/m/Y', $rs['created_date'] )."<br>by ".ucwords($rs['created_by']);
            if($rs['updated_date'] != $rs['created_date'])
            {
                $lu = date('d/m/Y', $rs['updated_date'])."<br>by ".ucwords($rs['updated_by']);
            }
            else
            {
                $lu = "Never Updated";
            }
            $rows = count($rs['drivers']);?>
            <tr>
                <td data-datatable-multi-row-rowspan="<?php echo $rows;?>" style="vertical-align: middle">
                    <h4><?php echo date('D jS M', $timestamp );?></h4>
                </td>
                <td data-datatable-multi-row-rowspan="<?php echo $rows;?>" style="vertical-align: middle">
                    <?php echo $cs;?>
                </td>
                <td data-datatable-multi-row-rowspan="<?php echo $rows;?>" style="vertical-align: middle">
                    <?php echo $lu;?>
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