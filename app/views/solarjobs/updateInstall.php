<?php

?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php if(!$details || !count($details)):?>
        <div class="row">
            <div class="col-md-12">
                <div class="errorbox">
                    <div class="row">
                        <div class="col-md-2" style="font-size:96px">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="col-md-6">
                            <h2>No Job Found</h2>
                            <p>No job was found with that ID</p>
                            <p><a href="/solar-jobs/view-installs">Please click here to view all installs to choose from</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else:?>
        <div class="row">
            <div class="col-md-12">
                <h2>Updating Job Number <?php echo $details['work_order'];?></h2>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?php if(isset($_SESSION['feedback'])) :?>
                   <div class='feedbackbox'><?php echo Session::getAndDestroy('feedback');?></div>
                <?php endif; ?>
                <?php if(isset($_SESSION['errorfeedback'])) :?>
                   <div class='errorbox'><?php echo Session::getAndDestroy('errorfeedback');?></div>
                <?php endif; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h3>Job Details</h3>
            </div>
        </div>
        <div class="bs-callout bs-callout-primary bs-callout-more">
            <div class="row ">
                <div class="col-md-7">
                    <dl class="dl-horizontal order-details">
                        <dt>Work Order</dt>
                        <dd><?php echo $details['work_order'];?></dd>
                        <dt>Customer Name</dt>
                        <dd><?php echo $details['customer_name'];?></dd>
                        <dt>Address</dt>
                        <dd><?php echo $details['address'];?></dd>
                        <?php if(!empty($details['address_2'])):?>
                            <dt>&nbsp;</dt>
                            <dd><?php echo $details['address_2'];?></dd>
                        <?php endif;?>
                        <dt>&nbsp;</dt>
                        <dd><?php echo $details['suburb'];?></dd>
                        <dt>&nbsp;</dt>
                        <dd><?php echo $details['state'];?></dd>
                        <dt>&nbsp;</dt>
                        <dd><?php echo $details['postcode'];?></dd>
                        <dt>&nbsp;</dt>
                        <dd><?php echo $details['country'];?></dd>
                    </dl>
                </div>
                <div class="col-md-5">
                    <dl class="dl-horizontal order-details">
                        <dt>Install date</dt>
                        <dd><?php echo date("d/m/Y", $details['install_date']);?></dd>
                        <dt>Team</dt>
                        <dd></dd>
                        <dt>Entered By</dt>
                        <dd><?php echo $entered_by;?></dd>
                    </dl>
                </div>
            </div>
            <div class='row'>
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    <a class="btn btn-primary" href="/solar-jobs/update-details/id=<?php echo $id;?>">Update These Details</a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h3>Job Items</h3>
            </div>
        </div>
        <div class="bs-callout bs-callout-primary bs-callout-more">
            <div class="row">
                <div class="col-md-10">
                    <dl class="dl-horizontal order-items">
                        <?php foreach($order_items as $oi):?>
                            <dt><?php echo $oi['name'];?></dt>
                            <dd><?php echo $oi['qty'];?></dd>
                        <?php endforeach;?>
                    </dl>
                </div>
            </div>
            <div class='row'>
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    <a class="btn btn-primary" href="/solar-jobs/items-update/job=<?php echo $id;?>">Update These Items</a>
                </div>
            </div>
        </div>
    <?php endif;?>
</div>