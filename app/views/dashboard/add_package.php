<div class="page-wrapper">
    <div class="row">
        <form id="orders-add-package" method="post" action="/form/procAddPackages">
            <div class="form-group row">
                <label class="col-md-4 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Width</label>
                <div class="col-md-8">
                    <div class="input-group">
                        <input type="text" class="form-control required number" name="width" id="width" value="<?php echo Form::value('width');?>" />
                        <span class="input-group-addon">cm</span>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-4 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Depth</label>
                <div class="col-md-8">
                    <div class="input-group">
                        <input type="text" class="form-control required number" name="depth" id="depth" value="<?php echo Form::value('depth');?>" />
                        <span class="input-group-addon">cm</span>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-4 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Height</label>
                <div class="col-md-8">
                    <div class="input-group">
                        <input type="text" class="form-control required number" name="height" id="height" value="<?php echo Form::value('height');?>" />
                        <span class="input-group-addon">cm</span>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-4 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Weight</label>
                <div class="col-md-8">
                    <div class="input-group">
                        <input type="text" class="form-control required number" name="weight" id="weight" value="<?php echo Form::value('weight');?>" />
                        <span class="input-group-addon">Kg</span>
                    </div>
                </div>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <input type="hidden" name="order_id" value="<?php echo $order_ids;?>" />
            <div class="form-group row">
                <label class="col-md-4 col-form-label">&nbsp;</label>
                <div class="form-check">
                    <div class="col-md-1 checkbox checkbox-default">
                        <input class="form-check-input styled" type="checkbox" id="pallet" name="pallet" <?php if(!empty(Form::value('pallet'))) echo 'checked';?> />
                        <label for="pallet"></label>
                    </div>
                    <label class="form-check-label col-md-7" for="pallet">Pallet</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-4 col-form-label">&nbsp;</label>
                <div class="col-md-8">
                    <button type="submit" class="btn btn-primary">Add Package</button>
                </div>
            </div>
        </form>
    </div>
</div>