<?php

?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="row">
        <div class="col-lg-12">
            <?php if($active == 1):?>
                <p class="text-right"><a class="btn btn-warning" href="/solar-teams/view-teams/active=0">View Inactive Teams</a></p>
            <?php else:?>
                <p class="text-right"><a class="btn btn-primary" href="/solar-teams/view-teams">View Active Reps</a></p>
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
    <?php if(count($teams)):?>
        <div class="row">
            <div class="col-lg-3">
                <div class="form-group">
                    <label>Search</label>
                    <input type="text" class="form-control" id="table_searcher" />
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <table width="100%" class="table-striped table-hover" id="view_teams_table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Comments</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($teams as $team): ?>
                        <tr>
                            <td data-label="Name"><?php echo $team['name'];?></td>
                            <td data-label="Comments"><?php echo nl2br($team['comments']);?></td>
                            <td><a href="/solar-teams/edit-team/team=<?php echo $team['id'];?>">Edit Details</a></td>
                        </tr>
                    <?php endforeach;?>
                </tbody>

            </table>
        </div>
    <?php else:?>
        <div class="row">
            <div class="col-lg-12">
                <div class="errorbox">
                    <h2><i class="fas fa-exclamation-triangle"></i> No Teams Listed</h2>
                    <p>There are no <?php if($active == 1) echo "active"; else echo "inactive";?> solar teams listed in the system at this time</p>
                    <p><a href="/solar-teams/add-team">Click here to add one</a></p>
                </div>
            </div>
        </div>
    <?php endif;?>
</div>