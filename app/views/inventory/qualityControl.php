<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="row">
            <div class="col">
                <p><a class="btn btn-outline-fsg" href="/inventory/view-inventory/client=<?php echo $product_info['client_id'];?>">Client Inventory</a></p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?php if(isset($_SESSION['feedback'])) :?>
                   <div class='feedbackbox'><i class="far fa-check-circle"></i> <?php echo Session::getAndDestroy('feedback');?></div>
                <?php endif; ?>
                <?php if(isset($_SESSION['errorfeedback'])) :?>
                   <div class='errorbox'><i class="far fa-times-circle"></i> <?php echo Session::getAndDestroy('errorfeedback');?></div>
                <?php endif; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4 col-md-4 col-lg-4 col-xl-4 mb-3">
                <div class="card h-100 border-secondary">
                    <div class="card-header bg-secondary text-white">
                        Current Locations
                    </div>
                    <div class="card-body">
                        <?php echo $location_string;?>
                    </div>
                </div>
            </div>
            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 mb-3">
                <div class="card h-100 border-secondary">
                    <div class="card-header bg-secondary text-white">
                        Manage Quality Control Status
                    </div>
                    <div class="card-body">
                        <div class="container-fluid">
                            <form id="quality_control" method="post" action="/form/procQualityControl">
                                <div class="form-group row">
                                    <label class="col-md-5 col-form-label">Add To Quality Control</label>
                                    <div class="col-md-7">
                                        <input type="text" class="form-control number" name="qty_add" id="qty_add" value="<?php echo Form::value('qty_add');?>" />
                                        <?php echo Form::displayError('qty_add');?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-5 col-form-label">Location</label>
                                    <div class="col-md-7">
                                        <select id="add_to_location" name="add_to_location" class="form-control selectpicker" data-live-search="true" data-style="btn-outline-secondary"><option value="0">--Select One--</option><?php echo $this->controller->location->getSelectItemInLocations($product_id, Form::value('add_to_location'));?></select>
                                        <?php echo Form::displayError('add_to_location');?>
                                    </div>
                                </div>
                                <?php if($show_remove):?>
                                    <div class="form-group row">
                                        <label class="col-md-5 col-form-label">Remove From Quality Control</label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control number" name="qty_subtract" id="qty_subtract" value="<?php echo Form::value('qty_subtract');?>" />
                                            <?php echo Form::displayError('qty_subtract');?>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-5 col-form-label">Location</label>
                                        <div class="col-md-7">
                                            <select id="subtract_from_location" name="subtract_from_location" class="form-control selectpicker" data-live-search="true" data-style="btn-outline-secondary"><option value="0">--Select One--</option><?php echo $this->controller->location->getSelectQCItemInLocations($product_id, Form::value('subtract_from_location'));?></select>
                                            <?php echo Form::displayError('subtract_from_location');?>
                                        </div>
                                    </div>
                                <?php endif;?>
                                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>" />
                                <input type="hidden" name="product_name" value="<?php echo $product_info['name']; ?>" />
                            </form>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <button id="qc_submitter" class="btn btn-outline-secondary">Update Quality Control Stock</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>