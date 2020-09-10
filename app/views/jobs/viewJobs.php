<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php if(count($jobs)):?>
            <div class="row">
                <div class="col-12">
                    <?php echo "<pre>",print_r($jobs),"</pre>";?>
                </div>
            </div>
        <?php else:?>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="errorbox">
                            <h2><i class="fas fa-exclamation-triangle"></i> No Jobs Listed</h2>
                        </div>
                    </div>
                </div>
        <?php endif;?>
    </div>
</div>