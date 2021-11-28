<?php
$db = Database::openConnection();
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php if(count($runsheets)):?>
            <?php //echo "<pre>",print_r($runsheets),"</pre>";?>
            <div id="waiting" class="row">
                <div class="col-lg-12 text-center">
                    <h2>Drawing Table..</h2>
                    <p>May take a few moments</p>
                    <img class='loading' src='/images/preloader.gif' alt='loading...' />
                </div>
            </div>
            <div class="row mt-4" id="table_holder" style="display:none">
                <?php //echo "User Role $user_role";?>
                <!--div class="col-md-4 mb-3 text-center"><a class="btn btn-outline-fsg" href="#">A Type of Runsheet</a></div>
                <div class="col-md-4 mb-3 text-center"><a class="btn btn-outline-fsg" href="#">A Type of Runsheet</a></div>
                <div class="col-md-4 mb-3 text-center"><a class="btn btn-outline-fsg" href="#">A Type of Runsheet</a></div-->
                <div class="col-12">
                    <?php if(isset($_SESSION['feedback'])) :?>
                       <div class='feedbackbox'><?php echo Session::getAndDestroy('feedback');?></div>
                    <?php endif; ?>
                    <?php if(isset($_SESSION['errorfeedback'])) :?>
                       <div class='errorbox'><?php echo Session::getAndDestroy('errorfeedback');?></div>
                    <?php endif; ?>
                </div>
                <div class="col-12">
                    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/runsheets_table.php");?>
                </div>
            </div>
        <?php else:?>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="errorbox">
                            <h2><i class="fas fa-exclamation-triangle"></i> No Open Runsheets Listed</h2>
                            <p></p>
                        </div>
                    </div>
                </div>
        <?php endif;?>
    </div>
</div>