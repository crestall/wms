<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <input type="hidden" name="client_id" id="client_id" value="<?php echo $client_id;?>" />
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="row form-group">
            <label class="col-md-3">Select a Client</label>
            <div class="col-md-4">
                <p><select id="client_selector" class="form-control selectpicker"  data-style="btn-outline-secondary"><option value="0">Select A Client</option><?php echo $this->controller->client->getSelectDeliveryClients($client_id);?></select></p>
            </div>
        </div>
        <?php if($client_id > 0):?>
            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/between_dates.php");?>
            <?php if(count($delivery_charges)):?>
                <?php echo "<pre>",print_r($delivery_charges),"</pre>";?>
                <div id="waiting" class="row">
                    <div class="col-lg-12 text-center">
                        <h2>Drawing Table..</h2>
                        <p>May take a few moments</p>
                        <img class='loading' src='/images/preloader.gif' alt='loading...' />
                    </div>
                </div>
                <div id="table_holder" style="display:none">
                    <div class="row">
                        <div class="col mb-3">
                            <h2>Charges For <?php echo $delivery_charges['client_name'];?></h2>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-12">
                            <table class="table-striped table-hover" id="delivery_client_charges" style="width: 95%;margin: auto">
                                <thead>
                                    <tr>
                                        <th>Service</th>
                                        <th>Units</th>
                                        <th>Unit Charge</th>
                                        <th>Total (Ex GST)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $dc = array_slice($delivery_charges, 2);
                                    echo "<pre>",print_r($dc),"</pre>";
                                    foreach($dc as $service => $details):?>
                                        <tr>
                                            <td><?php echo ucwords(str_replace("_", " ", $service));?></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    <?php endforeach;?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php else:?>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="errorbox">
                            <h2><i class="fas fa-exclamation-triangle"></i> No Delivery Charges to Display</h2>
                            <p>Nothing happened for this client in the selected date range</p>
                        </div>
                    </div>
                </div>
            <?php endif;?>
        <?php endif;?>
    </div>
</div>
<div id="block_message"></div>