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
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-10 text-center">
                                <h2><?php echo $name;?> Users</h2>
                            </div>
                            <div class="col-xs-2 text-right">
                                 <a class="toggle_roles" data-toggle="collapse" href="#role_<?php echo $ur['id'];?>"><span class="fa arrow huge"></span></a>
                                 <!--a id="toggle_orders" data-toggle="collapse" href="#new_orders"><span class="fa arrow huge"></span></a-->
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="collapse" id="role_<?php echo $ur['id'];?>">
                            <div class="row">
                                <?php $i = 1;
                                foreach($this->controller->user->getAllUsersByRoleID($ur['id'], $active) as $user):?>
                                    <div class="col-lg-6">
                                        <div class="panel panel-primary">
                                            <div class="panel-heading">
                                                <div class="row">
                                                    <div class="col-lg-2">
                                                        <img src="/images/profile_pictures/<?php echo $user['profile_picture'];?>" alt="profile image" class="img-thumbnail" />
                                                    </div>
                                                    <div class="col-lg-10">
                                                        <h2 class="text-center"><?php echo $user['name'];?></h2>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="panel-body">
                                                <div class="row">
                                                    <div class="col-lg-8">
                                                        <dl class="dl-horizontal user-list">
                                                            <dt>Client Name</dt>
                                                            <dd><?php echo $this->controller->client->getClientName($user['client_id']);?></dd>
                                                            <dt>Email</dt>
                                                            <dd><?php echo $user['email'];?></dd>
                                                        </dl>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <?php if($user['active'] > 0):?>
                                                            <p><a class="btn btn-danger deactivate" data-userid="<?php echo $user['id'];?>">Deactivate User</a> </p>
                                                        <?php else:?>
                                                            <p><a class="btn btn-success reactivate" data-userid="<?php echo $user['id'];?>">Reactivate User</a> </p>
                                                        <?php endif;?>
                                                        <p><a class="btn btn-primary" href="/user/edit-user-profile/user=<?php echo $user['id'];?>">Edit Profile</a></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if($i % 2 == 0):?>
                                        </div>
                                        <div class="row">
                                    <?php endif;?>
                                    <?php ++$i;
                                endforeach;?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach;?>
</div>