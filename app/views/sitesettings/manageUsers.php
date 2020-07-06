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
    <div id="waiting" class="row">
        <div class="col-lg-12 text-center">
            <h2>Drawing Table..</h2>
            <p>May take a few moments</p>
            <img class='loading' src='/images/preloader.gif' alt='loading...' />
        </div>
    </div>
    <div class="row" id="table_holder" style="display:none">
        <div class="col-lg-12">
            <table id="user_list_table" class="table-striped table-hover" width="100%">
                <?php foreach($user_roles as $ur):
                    if(!$this->controller->user->canManageRole($ur['id']))
                        continue;
                    $name = ucwords($ur['name']);?>
                    <thead>
                        <tr>
                            <th colspan=5>
                                <h2><?php echo $name;?> Users</h2>
                            </th>
                        </tr>
                        <tr>
                            <th></th>
                            <th>User</th>
                            <th>Client Name</th>
                            <th>Email</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1;
                        foreach($this->controller->user->getAllUsersByRoleID($ur['id'], $active) as $user):?>
                            <tr>
                                <td><?php echo $i;?></td>
                                <td><img src="/images/profile_pictures/<?php echo $user['profile_picture'];?>" alt="profile image" class="img-thumbnail" /> <?php echo $user['name'];?></td>
                                <td><?php echo $this->controller->client->getClientName($user['client_id']);?></td>
                                <td><?php echo $user['email'];?></td>
                            </tr>
                        <?php ++$i; endforeach;?>
                    </tbody>
                <?php endforeach;?>
            </table>
        </div>
    </div>
</div>