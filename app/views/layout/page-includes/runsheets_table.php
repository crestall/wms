<table class="table-striped table-hover" id="runsheets_table">
    <thead>
        <tr>
            <th>Runsheet Day</th>
            <th>Created</th>
            <th>Last Updated</th>
            <th>Jobs</th>
            <th>Orders</th>
            <th nowrap>
                Select
                <div class="checkbox checkbox-default">
                    <input id="select_all" class="styled" type="checkbox">
                    <label for="select_all"><em><small>(all)</small></em></label>
                </div>
            </th>
            <th></th>
        </tr>
        <tr>
            <td colspan="7"><span class="inst">Click on  the runsheet day to edit and print the runsheet</span> </td>
        </tr>
    </thead>
    <tbody>
        <?php foreach($runsheets as $timestamp => $rs):
            $cb = $db->queryValue('users', array('id' => $rs['created_by']), 'name');
            $cs = date('d/M/Y', $rs['created_date'] )."<br>by ".$cb;
            if($rs['updated_date'] != $rs['created_date'])
            {
                $ub = $db->queryValue('users', array('id' => $rs['updated_by']), 'name');
                $lu = date('d/M/Y', $rs['updated_date'])."<br>by ".$ub;
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
                <td data-label="Jobs"></td>
                <td data-label="Orders"></td>
                <td data-label="Select" class="chkbox">
                    <div class="checkbox checkbox-default">
                        <input type="checkbox" class="select styled" data-runsheetid='<?php echo $rs['runsheet_id'];?>' id="select_<?php echo $rs['runsheet_id'];?>" />
                        <label for="select_<?php echo $rs['runsheet_id'];?>"></label>
                    </div>
                </td>
                <td></td>
            </tr>
        <?php endforeach;?>
    </tbody>
</table>