<div class="page-wrapper">
    <div class="row" id="feedback_holder" style="display:none"></div>
        <form id="order-add-package" method="post" action="/form/procAddPackage">
            <div class="form-group row">
                <label class="col-md-4"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Width</label>
                <div class="col-md-2">
                    <div class="input-group">
                        <input type="text" class="form-control required number" name="width" id="width" />
                        <div class="input-group-append">
                            <span class="input-group-text">cm</span>
                        </div>
                    </div>
                </div>
                <label class="col-md-4"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Depth</label>
                <div class="col-md-2">
                    <div class="input-group">
                        <input type="text" class="form-control required number" name="depth" id="depth" />
                        <span class="input-group-append">cm</span>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-4 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Height</label>
                <div class="col-md-2">
                    <div class="input-group">
                        <input type="text" class="form-control required number" name="height" id="height" />
                        <span class="input-group-append">cm</span>
                    </div>
                </div>
                <label class="col-md-4 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Weight</label>
                <div class="col-md-2">
                    <div class="input-group">
                        <input type="text" class="form-control required number" name="weight" id="weight" />
                        <span class="input-group-append">Kg</span>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-4"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> <span id='label_text'>Count</span></label>
                <div class="col-md-2">
                    <input type="text" class="form-control required number" name="count" id="count" value="1" />
                </div>
                <div class="custom-control custom-checkbox mr-sm-2">
                    <input class="custom-control-input" type="checkbox" id="pallet" name="pallet" />
                    <label class="custom-control-label" for="pallet">Pallet(s)?</label>
                </div>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <input type="hidden" name="order_id" value="<?php echo $order_id;?>" />
            <div class="form-group row">
                <label class="col-md-6 col-form-label">&nbsp;</label>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-outline-secondary">Add Package</button>
                </div>
            </div>
        </form>
</div>