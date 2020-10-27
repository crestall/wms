<?php
$suburb = Form::value('suburb');
$state = Form::value('state');
$postcode = Form::value('postcode');
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div id="feedback_holder" style="display:none;"></div>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <?php echo Form::displayError('general');?>
        <form id="get_quotes" method="post" action="/form/procGetQuotes">
            <div class="col-12 mb-2">
                <p class="inst">Weights are for individual boxes, not a total</p>
            </div>
            <div class="form-group row">
                <label class="col-sm-2"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Suburb/Town</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control required" name="suburb" id="suburb" value="<?php echo $suburb;?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> State</label>
                <div class="col-sm-4">
                    <select id="state" name="state" class="form-control selectpicker" data-style="btn-outline-secondary" required><option value="0">--Select One--</option><?php echo Utility::getStateSelect($state);?></select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Postcode</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control required" name="postcode" id="postcode" value="<?php echo $postcode;?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-1 mb-3">Width</label>
                <div class="col-sm-3 mb-3">
                    <div class="input-group">
                        <input type="text" class="form-control number" data-rule-positiveNumber="true" name="item[][width]" id="carton_width" value="<?php echo Form::value('carton_width');?>" />
                        <div class="input-group-append">
                            <span class="input-group-text">cm</span>
                        </div>
                    </div>
                </div>
                <label class="col-sm-1 mb-3">Length</label>
                <div class="col-sm-3 mb-3">
                    <div class="input-group">
                        <input type="text" class="form-control number" data-rule-positiveNumber="true" name="item[][length]" id="carton_length" value="<?php echo Form::value('carton_length');?>" />
                        <div class="input-group-append">
                            <span class="input-group-text">cm</span>
                        </div>
                    </div>
                </div>
                <label class="col-sm-1 mb-3">Height</label>
                <div class="col-sm-3 mb-3">
                    <div class="input-group">
                        <input type="text" class="form-control number" data-rule-positiveNumber="true" name="item[][carton_height]" id="carton_height" value="<?php echo Form::value('carton_length');?>" />
                        <div class="input-group-append">
                            <span class="input-group-text">cm</span>
                        </div>
                    </div>
                </div>
                <label class="col-sm-1 mb-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Weight</label>
                <div class="col-sm-3 mb-3">
                    <div class="input-group">
                        <input type="text" class="form-control required number" data-rule-positiveNumber="true" name="item[][weight]" id="carton_weight" value="<?php echo Form::value('carton_weight');?>" />
                        <div class="input-group-append">
                            <span class="input-group-text">kg</span>
                        </div>
                    </div>
                </div>
                <label class="col-sm-1 mb-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Count</label>
                <div class="col-sm-3 mb-3">
                    <input type="text" class="form-control required digits" data-rule-positiveNumber="true" name="item[][count]" id="carton_count" value="1" />
                </div>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <div class="form-group row">
                <div class="col-sm-4 offset-sm-6">
                    <button type="submit" class="btn btn-outline-secondary">Get Prices</button>
                </div>
            </div>
        </form>
    </div>
</div>