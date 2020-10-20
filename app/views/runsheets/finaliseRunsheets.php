<?php
function getDriverTasks($driver)
{
    $driver_name = ucwords($driver['name']);
    $html = "<td>$driver_name</td>";
    $html .= "<td>";
    foreach($driver['tasks'] as $task)
    {
        $task_number = ($task['job_number'] > 0)? "JOB: ".$task['job_number'] : "ORDER: ".$task['order_number'];
        $html .= "<div class='border-bottom border-secondary border-bottom-dashed mb-3 pb-3 pl-3'>";
        $html .= $task_number;
        $html .= "</div>";
    }
    $html .= "</td>";
    $html .= "<td>
                <p><button class='btn btn-sm btn-outline-danger remove-tasks'>Remove Selected Tasks</button></p>
                <p><button class='btn btn-sm btn-outline-fsg complete-tasks'>Complete Selected Tasks</button></p>
            </td>";
    return $html;
}
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php if(count($runsheets)):?>
            <?php echo "<pre>",print_r($runsheets),"</pre>"; //die();?>
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
                                <?php echo getDriverTasks($rs['drivers'][0]);?>
                            </tr>
                            <?php for($i = 1; $i < $rows; ++$i):?>
                                <tr>
                                    <?php echo getDriverTasks($rs['drivers'][$i]);?>
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