<?php

?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="row">
        <div class="col-lg-12">
            <?php if($active == 1):?>
                <p class="text-right"><a class="btn btn-warning" href="/sales-reps/view-reps/active=0">View Inactive Reps</a></p>
            <?php else:?>
                <p class="text-right"><a class="btn btn-primary" href="/sales-reps/view-reps">View Active Reps</a></p>
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
            <div class="col-lg-3">
                <div class="form-group">
                    <label>Search</label>
                    <input type="text" class="form-control" id="table_searcher" />
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
                        <th>Client</th>
                        <th>Comments</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($reps as $rep):
                        $client_name = $this->controller->client->getClientName($rep['client_id']);;?>
                        <tr>
                            <td data-label="Name"><?php echo $rep['name'];?></td>
                            <td data-label="Phone" class="number"><?php echo $rep['phone'];?></td>
                            <td data-label="Email"><?php echo $rep['email'];?></td>
                            <td data-label="Client"><?php echo $client_name;?></td>
                            <td data-label="Comments"><?php echo nl2br($rep['comments']);?></td>
                            <td><a href="/sales-reps/edit-sales-rep/rep=<?php echo $rep['id'];?>">Edit Details</a></td>
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
                    <p><a href="/sales-reps/add-sales-rep">Click here to add one</a></p>
                </div>
            </div>
        </div>
    <?php endif;?>
</div>