<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="row">
        <div class="col-lg-12">
            <h3>Record Container Details Here</h3>
        </div>
    </div>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
    <div class="row">
        <form id="container_unloading" method="post" action="/form/procContainerUnload">
            <div class='form-group row'>
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Client</label>
                <div class="col-md-4">
                    <select id="client_id" name="client_id" class="form-control selectpicker" data-live-search="true"><option value="0">--Select One--</option><?php echo $this->controller->client->getSelectClients(Form::value('client_id'));?></select>
                    <?php echo Form::displayError('client_id');?>
                </div>
            </div>
            <div class="row form-group">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Date Unloaded</label>
                <div class="col-md-2">
                    <div class="input-group">
                        <input type="text" class="required form-control" name="date" id="date" value="<?php echo Form::value('date');?>" />
                        <span class="input-group-addon"><i class="far fa-calendar-alt"></i></span>
                    </div>
                </div>
            </div>
            <div class='form-group row'>
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Container Size</label>
                <div class="col-md-4">
                    <select id="container_size" name="container_size" class="form-control selectpicker">
                        <option value="0">--Select One--</option>
                        <option <?php if(Form::value('container_size') == '20 Foot') echo 'selected';?> >20 Foot</option>
                        <option <?php if(Form::value('container_size') == '40 Foot') echo 'selected';?>>40 Foot</option>
                    </select>
                    <?php echo Form::displayError('container_size');?>
                </div>
            </div>
            <div class='form-group row'>
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Type of Load</label>
                <div class="col-md-4">
                    <select id="load_type" name="load_type" class="form-control selectpicker">
                        <option value="0">--Select One--</option>
                        <option <?php if(Form::value('load_type') == 'Loose') echo 'selected';?>>Loose</option>
                        <option <?php if(Form::value('load_type') == 'Palletised') echo 'selected';?>>Palletised</option>
                    </select>
                    <?php echo Form::displayError('load_type');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Item Count</label>
                <div class="col-md-4">
                    <input type="text" class="form-control number" name="item_count" id="item_count" value="<?php echo Form::value('item_count');?>" <?php if(Form::value('load_type') !== 'Loose') echo 'disabled'; ?> />
                    <?php echo Form::displayError('item_count');?>
                </div>
            </div>
            <div class="form-group row">
                <div class="form-check">
                    <label class="form-check-label col-md-3" for="repalletising">Required Repalletising</label>
                    <div class="col-md-4 checkbox checkbox-default">
                        <input class="form-check-input styled" type="checkbox" id="repalletising" name="repalletising" <?php if(!empty(Form::value('repalletising'))) echo "checked";?> />
                        <label for="repalletising"></label>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="form-check">
                    <label class="form-check-label col-md-3" for="disposal">Required Pallet Disposal</label>
                    <div class="col-md-4 checkbox checkbox-default">
                        <input class="form-check-input styled" type="checkbox" id="disposal" name="disposal" <?php if(!empty(Form::value('disposal'))) echo "checked";?> />
                        <label for="disposal"></label>
                    </div>
                </div>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <input type="hidden" name="date_value" id="date_value" value="<?php echo Form::value('date_value');?>" />
            <div class="form-group row">
                <label class="col-md-3 col-form-label">&nbsp;</label>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Record Details</button>
                </div>
            </div>
        </form>
    </div>
</div>