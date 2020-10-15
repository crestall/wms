<?php echo "<pre>",print_r($runsheets),"</pre>";?>
<table class="table-striped table-hover" id="runsheets_table">
    <thead>
        <tr>
            <th>Runsheet Day</th>
            <th>Created</th>
            <th>Last Updated</th>
            <th>Jobs</th>
            <th>Orders</th>
            <!--th nowrap>
                Select
                <div class="checkbox checkbox-default">
                    <input id="select_all" class="styled" type="checkbox">
                    <label for="select_all"><em><small>(all)</small></em></label>
                </div>
            </th-->
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($runsheets as $timestamp => $rs):
            $cb = $db->queryValue('users', array('id' => $rs['created_by']), 'name');
            $cs = date('d/m/Y', $rs['created_date'] )."<br>by ".$cb;
            if($rs['updated_date'] != $rs['created_date'])
            {
                $ub = $db->queryValue('users', array('id' => $rs['updated_by']), 'name');
                $lu = date('d/m/Y', $rs['updated_date'])."<br>by ".$ub;
            }
            else
            {
                $lu = "Never Updated";
            }
            ?>
            <tr id="tr_<?php echo $rs['runsheet_id'];?>">
                <td data-label="Runsheet Day">
                    <?php echo date('D jS M', $timestamp );?>
                </td>
                <td data-label="Created">
                    <?php echo $cs;?>
                </td>
                <td data-label="Last Update">
                    <?php echo $lu;?>
                </td>
                <td data-label="Jobs">
                    <?php foreach($rs['jobs'] as $job):?>
                        <div class="runsheet_job_list border-bottom border-secondary border-bottom-dashed mb-3 ">
                            <p>
                                <span class="font-weight-bold"><?php echo $job['job_number'];?></span> - <?php echo $job['customer'];?><br>
                                <?php if(!empty($job['driver_name'])) echo $job['driver_name']." - "; echo $job['suburb']?>
                            </p>
                        </div>
                    <?php endforeach;?>
                </td>
                <td data-label="Orders"></td>
                <!--td data-label="Select" class="chkbox">
                    <div class="checkbox checkbox-default">
                        <input type="checkbox" class="select styled" data-runsheetid='<?php echo $rs['runsheet_id'];?>' id="select_<?php echo $rs['runsheet_id'];?>" />
                        <label for="select_<?php echo $rs['runsheet_id'];?>"></label>
                    </div>
                </td-->
                <td>
                    <a href="/runsheets/print-runsheet/runsheet=<?php echo $rs['runsheet_id'];?>" class="btn btn-sm btn-outline-fsg">Update and Print</a>
                </td>
            </tr>
        <?php endforeach;?>
    </tbody>
</table>