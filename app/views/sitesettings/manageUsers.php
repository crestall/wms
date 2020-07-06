<?php

?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="row">
        <div class="col-lg-4"><p><a href="/user/add-user" class="btn btn-primary">Add New User</a></p></div>
        <div class="col-lg-4"><p><a href="/site-settings/manage-users/active=1" class="btn btn-success">View Only Active Users</a></p></div>
        <div class="col-lg-4"><p><a href="/site-settings/manage-users/active=0" class="btn btn-danger">View Only Inactive Users</a></p></div>
        <div class="col-lg-4"></div>
    </div>
    <?php foreach($user_roles as $ur):
        if(!$this->controller->user->canManageRole($ur['id']))
            continue;
        $name = ucwords($ur['name']);?>
        <div class="row">
            <div class="col-lg-12">
                <h2><?php echo $name;?> Users</h2>
                
            </div>
        </div>
    <?php endforeach;?>
</div>