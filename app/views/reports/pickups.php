<div id="page-wrapper">
    <input type="hidden" name="client_id" id="client_id" value="<?php echo $client_id;?>" />
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="row form-group">
        <label class="col-md-3">Select a Client</label>
        <div class="col-md-4">
            <p><select id="client_selector" class="form-control selectpicker"><option value="0">Select</option><?php echo $this->controller->client->getSelectClients($client_id);?></select></p>
        </div>
    </div>
    <?php if($client_id > 0):?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/between_dates.php");?>
        <?php if(count($pickups)):?>
            <?php echo "<pre>",print_r($pickups),"</pre>"; die();?>

        <?php else:?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="errorbox">
                        <h2>No Pickups Listed</h2>
                        <p>There are no pickups listed as being booked between <?php echo date("d/m/Y", $from);?> and <?php echo date("d/m/Y", $to);?></p>
                        <p>If you believe this is an error, please let Solly know</p>
                        <p>Alternatively, use the date selectors above to change the date range</p>
                    </div>
                </div>
            </div>
        <?php endif;?>
    <?php endif;?>
</div>
<div id="block_message"></div>