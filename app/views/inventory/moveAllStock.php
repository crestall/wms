<div id="page-wrapper">
    <input type="hidden" id="client_id" value="<?php echo $client_id;?>" />
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="row">
        <div class="col-lg-12">
            <div class="form-group">
                <div class="col-lg-2">&nbsp;</div>
                <label class="col-lg-2">Select a Client</label>
                <div class="col-lg-4">
                    <select id="client_selector" class="form-control selectpicker"><option value="0">Select</option><?php echo $this->controller->client->getSelectClients($client_id);?></select>
                </div>
            </div>
        </div>
    </div>
    <?php if($client_id > 0):?>
        <div class="row">
            <div class="col-lg-12" id="feedback_holder">
                <p></p>
            </div>
        </div>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <div class="col-md-12">
            <form id="move_all_client_stock" method="post" action="/form/procMoveAllClientStock">
                <div class="form-group row">
                    <label class="col-md-5 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup>Move all stock to</label>
                    <div class="col-md-5">
                        <select id="location_selector" class="form-control selectpicker" name="move_to_location">
                            <option value="0">Select</option>
                            <option value="<?php echo $bayswater_receiving_id;?>">Bayswater Receiving</option>
                            <option value="<?php echo $receiving_id;?>">Receiving</option>
                        </select>
                    </div>
                </div>
                    <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                    <input type="hidden" name="client_id" value="<?php echo $client_id; ?>" />
                <div class="form-group row">
                    <label class="col-md-5 col-form-label">&nbsp;</label>
                    <div class="col-md-5">
                        <p><button type="submit" class="btn btn-primary">Move Stock Now</button> </p>
                    </div>
                </div>
                </div>
            </form>
        </div>
    <?php endif;?>
</div>