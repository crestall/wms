<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php if(count($jobs)):?>
            <div id="waiting" class="row">
                <div class="col-lg-12 text-center">
                    <h2>Drawing Table..</h2>
                    <p>May take a few moments</p>
                    <img class='loading' src='/images/preloader.gif' alt='loading...' />
                </div>
            </div>
            <div class="row" id="table_holder" style="display:none">
                <div class="col-12">
                    <table class="table-striped table-hover" id="production_jobs_table">
                        <thead>
                            <tr>
                                <th>Job Number</th>
                                <th>Related Job</th>
                                <th>Client</th>
                                <th>Description</th>
                                <th>Notes</th>
                                <th>Status</th>
                                <th>Sales Rep</th>
                                <th>Supplier</th>
                                <th>Date Entered</th>
                                <th>Due Date</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
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