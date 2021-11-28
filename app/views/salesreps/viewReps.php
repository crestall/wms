<?php
$can_edit_contact  = Permission::canEditContact($user_role);
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="row">
            <div class="col-lg-12">
                <?php if($active == 1):?>
                    <p class="text-right"><a class="btn btn-outline-secondary" href="/fsg-contacts/view-contacts/active=0">View Inactive Contacts</a></p>
                <?php else:?>
                    <p class="text-right"><a class="btn btn-outline-secondary" href="/fsg-contacts/view-contacts">View Active Contacts</a></p>
                <?php endif;?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <?php if(isset($_SESSION['feedback'])) :?>
                   <div class='feedbackbox'><i class="far fa-check-circle"></i> <?php echo Session::getAndDestroy('feedback');?></div>
                <?php endif; ?>
                <?php if(isset($_SESSION['errorfeedback'])) :?>
                   <div class='errorbox'><i class="far fa-times-circle"></i> <?php echo Session::getAndDestroy('errorfeedback');?></div>
                <?php endif; ?>
            </div>
        </div>
        <?php if(count($reps)):?>
            <div class="row">
                <div class="col-md-6 col-sm-9">
                    <div class="form-group row">
                        <label class="col-3">Search</label>
                        <div class="col-9">
                            <input type="text" class="form-control" id="table_searcher" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <table width="100%" class="table-striped table-hover" id="view_reps_table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Comments</th>
                            <?php if($can_edit_contact):?>
                                <th></th>
                            <?php endif;?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($reps as $rep):?>
                            <tr>
                                <td data-label="Name"><?php echo $rep['name'];?></td>
                                <td data-label="Phone" class="number"><?php echo $rep['phone'];?></td>
                                <td data-label="Email"><a href="mailto:<?php echo $rep['email'];?>"><?php echo $rep['email'];?></a></td>
                                <td data-label="Comments"><?php echo nl2br($rep['comments']);?></td>
                                <?php if($can_edit_contact):?>
                                    <td><a href="/fsg-contacts/edit-contact/contact=<?php echo $rep['id'];?>">Edit Details</a></td>
                                <?php endif;?>
                            </tr>
                        <?php endforeach;?>
                    </tbody>

                </table>
            </div>
        <?php else:?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="errorbox">
                        <h2><i class="fas fa-exclamation-triangle"></i> No Reps Listed</h2>
                        <p>There are no <?php if($active == 1) echo "active"; else echo "inactive";?> sales reps listed in the system at this time</p>
                        <p><a href="/fsg-contacts/add-contact">Click here to add one</a></p>
                    </div>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>