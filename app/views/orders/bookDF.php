<?php

?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <?php echo Form::displayError('general');?>
        <?php if(isset($_SESSION['dfresponse'])) :
            $response = Session::getAndDestroy('dfresponse');
            if($response['ResponseCode'] != 200):?>
                <div class="row">
                    <div class="col">
                        <div class="errorbox">
                            <div class="row">
                            <div class="col-4 text-right">
                                <i class="fad fa-exclamation-triangle fa-6x"></i>
                            </div>
                            <div class="col-8">
                                <h2>There Has Been An Error Booking The Pickup</h2>
                                <p><?php echo $response['ResponseMessage'];?></p>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            <?php else:?>
                <div class="row">
                    <div class="col">
                        <div class="feedbackbox">
                            <div class="row">
                            <div class="col-4 text-right">
                                <i class="fad fa-check-circle fa-6x"></i>
                            </div>
                            <div class="col-8">
                                <h2>The Pickup Has been Booked</h2>
                                <p><label>Booking Reference:</label><?php echo $response['BookingReferenceNumber'];?></p>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            <?php endif;?>
        <?php endif;?>
        <form id="df_collection" method="post" action="/form/procDFCollection">
            <div class="form-group row">
                <div class="col-12 mb-2">
                    <p class="inst">At least one of cartons or pallets must be filled in.</p>
                    <p class="inst">Use whole numbers only.</p>
                    <p class="inst">Only enter the largest dimension across <span class="font-weight-bold">all</span> cartons or pallets.</p>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-12">
                    <h5>Cartons</h5>
                </div>
            </div>
            <div class="form-group row ml-4">
                <div class="col-9">
                    <div class="form-group row">
                        <label class="col-md-2 mb-3">Count</label>
                        <div class="col-md-4 mb-3">
                            <input type="text" class="form-control number count digits" name="carton_count" id="carton_count" value="<?php echo Form::value('carton_count');?>" />
                        </div>
                        <?php echo Form::displayError('carton_count');?>
                        <label class="col-md-2 mb-3">Width</label>
                        <div class="col-md-4 mb-3">
                            <div class="input-group">
                                <input type="text" class="form-control number digits" name="carton_width" id="carton_width" value="<?php echo Form::value('carton_width');?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">cm</span>
                                </div>
                            </div>
                        </div>
                        <?php echo Form::displayError('carton_width');?>
                        <label class="col-md-2 mb-3">Length</label>
                        <div class="col-md-4 mb-3">
                            <div class="input-group">
                                <input type="text" class="form-control number digits" name="carton_length" id="carton_length" value="<?php echo Form::value('carton_length');?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">cm</span>
                                </div>
                            </div>
                        </div>
                        <?php echo Form::displayError('carton_length');?>
                        <label class="col-md-2 mb-3">Height</label>
                        <div class="col-md-4 mb-3">
                            <div class="input-group">
                                <input type="text" class="form-control number digits" name="carton_height" id="carton_height" value="<?php echo Form::value('carton_length');?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">cm</span>
                                </div>
                            </div>
                        </div>
                        <?php echo Form::displayError('carton_height');?>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-12">
                    <h5>Pallets</h5>
                </div>
            </div>
            <div class="form-group row ml-4">
                <div class="col-9">
                    <div class="form-group row">
                        <label class="col-md-2 mb-3">Count</label>
                        <div class="col-md-4 mb-3">
                            <input type="text" class="form-control number count digits" name="pallet_count" id="pallet_count" value="<?php echo Form::value('pallet_count');?>" />
                        </div>
                        <?php echo Form::displayError('pallet_count');?>
                        <label class="col-md-2 mb-3">Width</label>
                        <div class="col-md-4 mb-3">
                            <div class="input-group">
                                <input type="text" class="form-control number digits" name="pallet_width" id="pallet_width" value="<?php echo Form::value('pallet_width');?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">cm</span>
                                </div>
                            </div>
                        </div>
                        <?php echo Form::displayError('pallet_width');?>
                        <label class="col-md-2 mb-3">Length</label>
                        <div class="col-md-4 mb-3">
                            <div class="input-group">
                                <input type="text" class="form-control number digits" name="pallet_length" id="pallet_length" value="<?php echo Form::value('pallet_length');?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">cm</span>
                                </div>
                            </div>
                        </div>
                        <?php echo Form::displayError('pallet_length');?>
                        <label class="col-md-2 mb-3">Height</label>
                        <div class="col-md-4 mb-3">
                            <div class="input-group">
                                <input type="text" class="form-control number digits" name="pallet_height" id="pallet_height" value="<?php echo Form::value('pallet_length');?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">cm</span>
                                </div>
                            </div>
                        </div>
                        <?php echo Form::displayError('pallet_height');?>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-2">
                    Total Weight
                </label>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control required number digits" name="weight" id="weight" value="<?php echo Form::value('weight');?>" />
                        <div class="input-group-append">
                            <span class="input-group-text">Kg</span>
                        </div>
                    </div>
                    <?php echo Form::displayError('weight');?>
                </div>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <div class="form-group row">
                <div class="col-md-4 offset-md-6">
                    <button type="submit" class="btn btn-outline-secondary">Book Collection</button>
                </div>
            </div>
        </form>
    </div>
</div>