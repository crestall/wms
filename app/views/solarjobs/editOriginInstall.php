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
                            <p><a href="/solar-jobs/view-installs/type=<?php echo $type;?>">Please click here to view all <?php echo $order_type;?> installs to choose from</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else:?>
        <?php echo "<pre>",print_r($details),"</pre>";?>
    <?php endif;?>
</div>