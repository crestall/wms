<?php
  $today = strtotime('today');
  //echo "<p>User Role: $user_role</p>";
  //echo "<pre>",print_r($jobs),"</pre>";
  $can_do_runsheets = false;
?>
<table class="table-striped table-hover" id="production_jobs_table" width="100%">
    <thead>
        <tr>
            <th data-priority="10001" nowwrap>Priority<br /><select id="priority_all" class="selectpicker" data-style="btn-outline-secondary btn-sm" data-width="fit"><option value="0">--</option><?php echo Utility::getPrioritySelect();?></select>&nbsp;<em><small>(all)</small></em></th>
            
        </tr>
    </thead>
</table>