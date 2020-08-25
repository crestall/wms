<?php

?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="row">
            <div class="col-lg-3 col-md-6"><p><a href="/user/add-user" class="btn btn-outline-fsg">Add New User</a></p></div>
            <div class="col-lg-3 col-md-6"><p><a href="/site-settings/manage-users/" class="btn btn-outline-success">View Only Active Users</a></p></div>
            <div class="col-lg-3 col-md-6"><p><a href="/site-settings/manage-users/active=0" class="btn btn-outline-danger">View Only Inactive Users</a></p></div>
            <div class="col-lg-3 col-md-6"><p><a href="/site-settings/manage-users/active=-1" class="btn btn-outline-warning">View All Users</a></p></div>
        </div>
    </div>
</div>