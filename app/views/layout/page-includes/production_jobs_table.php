<?php
  $today = strtotime('today');
  //echo "<p>User Role: $user_role</p>";
  //echo "<pre>",print_r($jobs),"</pre>";
  $can_do_runsheets = false;
  
?>
<table class="table-striped table-hover" id="production_jobs_table" width="100%">
    <thead>
        <tr>
            <th data-priority="10001" nowwrap>Priority</th>
            <th data-priority="1">Job Number</th>
            <th data-priority="1">Client</th>
            <th class="no-sort" data-priority="2" style="max-width: 250px;">Description</th>
            <th class="no-sort">Finisher(s)</th>
            <th>FSG Contact</th>
            <?php if($can_change_status):?>
                <th data-priority="2" nowrap>Status<br /><select id="status_all" class="selectpicker" data-style="btn-outline-secondary btn-sm" data-width="fit"><option value="0">--Select One--</option><?php echo $this->controller->jobstatus->getSelectJobStatus(false, 1, true);?></select>&nbsp;<em><small>(all)</small></em></th>
            <?php else:?>
                <th data-priority="2">Status</th>
            <?php endif;?>
            <th>Due Date</th>
            <th class="no-sort" style="max-width: 250px;">Delivery</th>
            <?php if($need_checkbox):?>
                <th data-priority="1" nowrap class="no-sort">
                    Select
                    <div class="checkbox checkbox-default">
                        <input id="select_all" class="styled" type="checkbox">
                        <label for="select_all"><em><small>(all)</small></em></label>
                    </div>
                </th>
            <?php endif;?>
            <?php if($can_do_runsheets):?>
                <th>Runsheet Day</th>
            <?php endif;?>
        </tr>
    </thead>
</table>