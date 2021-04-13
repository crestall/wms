<?php
  $today = strtotime('today');
  //echo "<p>User Role: $user_role</p>";
  //echo "<pre>",print_r($jobs),"</pre>";
  $can_do_runsheets = false;
  echo "<p>USERROLE: $user_role</p>";
?>
<table class="table-striped table-hover" id="production_jobs_table" width="100%">
    <thead>
        <tr>
            <th data-priority="10001" nowwrap>Priority</th>
            <th data-priority="1">Job Number</th>
        </tr>
    </thead>
</table>