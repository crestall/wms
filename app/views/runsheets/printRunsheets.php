<?php

?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php if(count($runsheets)):?>
            <div id="waiting" class="row">
                <div class="col-lg-12 text-center">
                    <h2>Drawing Table..</h2>
                    <p>May take a few moments</p>
                    <img class='loading' src='/images/preloader.gif' alt='loading...' />
                </div>
            </div>
            <div class="row mt-4" id="table_holder" style="display:none">
                <div class="col-12 text-right">
                    <?php if($completed):?>
                        <a class="btn btn-outline-fsg" href="/runsheets/print-runsheets">View All Sheets</a>
                    <?php else:?>
                        <a class="btn btn-outline-fsg" href="/runsheets/print-runsheets/complete=off">View Only Not Completed Sheets</a>
                    <?php endif;?>
                </div>
                <div class="col-12">
                    <?php if(isset($_SESSION['feedback'])) :?>
                       <div class='feedbackbox'><?php echo Session::getAndDestroy('feedback');?></div>
                    <?php endif; ?>
                    <?php if(isset($_SESSION['errorfeedback'])) :?>
                       <div class='errorbox'><?php echo Session::getAndDestroy('errorfeedback');?></div>
                    <?php endif; ?>
                </div>
                <div class="col-12">
                    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/print_runsheets_table.php");?>
                </div>
            </div>
        <?php else:?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="errorbox">
                        <h2><i class="fas fa-exclamation-triangle"></i> No Runsheets Listed For Printing</h2>
                    </div>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>