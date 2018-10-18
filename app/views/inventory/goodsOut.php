<div id="page-wrapper">
    <input type="hidden" id="client_id" value="<?php echo $client_id;?>" />
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="row">
        <form id="goodsout" method="post" action="/form/procGoodsOut">
            <div class="form-group row">
                <label class="col-form-label col-md-3">Select a Client</label>
                <div class="col-md-4">
                    <p><select id="client_selector" name="client_id" class="form-control selectpicker"><option value="0">Select</option><?php echo $this->controller->client->getSelectClients($client_id);?></select></p>
                </div>
            </div>
            <?php if($client_id > 0):?>
                <div class="form-group row">
                    <label class="col-form-label col-md-3">&nbsp;</label>
                    <div class="col-md-9">
                        <h2>Goods Out For <?php echo $client_name;?></h2>
                    </div>
                </div>
                <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
                <div class="form-group row">
                    <label class="col-form-label col-md-3">Pallet Count</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control counter number" name="pallet_count" id="pallet_count" placeholder="One of these is required" value="<?php echo Form::value('pallet_count');?>" />
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-3">Carton Count</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control counter number" name="carton_count" id="carton_count" placeholder="One of these is required" value="<?php echo Form::value('carton_count');?>" />
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-3">Satchel Count</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control counter number" name="satchel_count" id="satchel_count" placeholder="One of these is required" value="<?php echo Form::value('satchel_count');?>" />
                        <?php echo Form::displayError('counter');?>
                    </div>
                </div>
                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">&nbsp;</label>
                    <div class="col-md-4">
                        <button type="submit" id="submit_button" class="btn btn-primary">Submit Form</button>
                    </div>
                </div>
            <?php endif;?>
        </form>
    </div>
</div>