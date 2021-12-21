<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="row">
            <div class="col-lg-12">
                <h3>Record Container Details Here</h3>
            </div>
        </div>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <div class="row">
            <div class="col">
                <form id="container_unloading" method="post" action="/form/procContainerUnload">
                    <div class='form-group row'>
                        <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Client</label>
                        <div class="col-md-4">
                            <select id="client_id" name="client_id" class="form-control selectpicker" data-style="btn-outline-secondary" data-live-search="true"><option value="0">--Select One--</option><?php echo $this->controller->client->getSelectClients(Form::value('client_id'));?></select>
                            <?php echo Form::displayError('client_id');?>
                        </div>
                    </div>
                    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/choose_date.php");?>
                    <div class='form-group row'>
                        <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Container Size</label>
                        <div class="col-md-4">
                            <select id="container_size" name="container_size" class="form-control selectpicker" data-style="btn-outline-secondary">
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
                            <select id="load_type" name="load_type" class="form-control selectpicker" data-style="btn-outline-secondary">
                                <option value="0">--Select One--</option>
                                <option <?php if(Form::value('load_type') == 'Loose') echo 'selected';?>>Loose</option>
                                <option <?php if(Form::value('load_type') == 'Palletised') echo 'selected';?>>Palletised</option>
                            </select>
                            <?php echo Form::displayError('load_type');?>
                        </div>
                    </div>
                    <div class="form-group row custom-control custom-checkbox custom-control-right">
                        <input class="custom-control-input" type="checkbox" id="repalletising" name="repalletising" <?php if(!empty(Form::value('repalletising'))) echo "checked";?> />
                        <label class="custom-control-label col-md-3" for="repalletising">Required Repalletising</label>
                    </div>
                    <div class="form-group row custom-control custom-checkbox custom-control-right">
                        <input class="custom-control-input" type="checkbox" id="disposal" name="disposal" <?php if(!empty(Form::value('disposal'))) echo "checked";?> />
                        <label class="custom-control-label col-md-3" for="disposal">Required Pallet Disposal</label>
                    </div>
                    <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                    <input type="hidden" name="date_value" id="date_value" value="<?php echo Form::value('date_value');?>" />
                    <div class="form-group row">
                        <div class="col-md-4 offset-md-3">
                            <button type="submit" class="btn btn-primary">Record Details</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>