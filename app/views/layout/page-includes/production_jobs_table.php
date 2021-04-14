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
            <th data-priority="2" style="max-width: 250px;">Description</th>
        </tr>
    </thead>
</table>