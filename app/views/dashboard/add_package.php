<div id="page-wrapper">
    <div id="page_container" class="container-md" style="min-height: 0; height: 260px;">
        <form id="orders-add-package" method="post" action="/form/procAddPackages">
            <div class="form-group row">
                <label class="col-md-3 col-sm-6"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Width</label>
                <div class="col-md-3 col-sm-6">
                    <div class="input-group">
                        <input type="text" class="form-control required number" name="width" id="width" />
                        <div class="input-group-append">
                            <span class="input-group-text">cm</span>
                        </div>
                    </div>
                </div>
                <label class="col-md-3 col-sm-6"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Depth</label>
                <div class="col-md-3 col-sm-6">
                    <div class="input-group">
                        <input type="text" class="form-control required number" name="depth" id="depth" />
                        <div class="input-group-append">
                            <span class="input-group-text">cm</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-sm-6"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Height</label>
                <div class="col-md-3 col-sm-6">
                    <div class="input-group">
                        <input type="text" class="form-control required number" name="height" id="height" />
                        <div class="input-group-append">
                            <span class="input-group-text">cm</span>
                        </div>
                    </div>
                </div>
                <label class="col-md-3 col-sm-6"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Weight</label>
                <div class="col-md-3 col-sm-6">
                    <div class="input-group">
                        <input type="text" class="form-control required number" name="weight" id="weight" />
                        <div class="input-group-append">
                            <span class="input-group-text">Kg</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-sm-6"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> <span id='label_text'>Count</span></label>
                <div class="col-md-3 col-sm-4">
                    <input type="text" class="form-control required number" name="count" id="count" value="1" />
                </div>
                <div class="custom-control custom-checkbox col-sm-2">
                    <input class="custom-control-input" type="checkbox" id="pallet" name="pallet" />
                    <label class="custom-control-label" for="pallet">Pallet(s)?</label>
                </div>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <?php foreach($order_ids as $oid):?>
                <input type="hidden" name="order_ids[]" value="<?php echo $oid;?>" />
            <?php endforeach;?>
            <input type="hidden" name="client_id" value="<?php echo $client_id;?>" />
            <!--div class="form-group row">
                <div class="col-md-4 offset-md-3">
                    <button type="submit" class="btn btn-outline-secondary">Add Package</button>
                </div>
            </div-->
        </form>
    </div>
</div>