<div id="page-wrapper">
    <input type="hidden" id="client_id" value="<?php echo $client_id;?>" />
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="row">
        <div class="col-lg-12">
            <h2>Expected Shipments for <?php echo $client_name;?></h2>
        </div>
    </div>
    <?php if(!count($shipments)):?>
    <div class="row">
        <div class="col-lg-12">
            <div class="errorbox">
                <h2><i class="far fa-times-circle"></i> No Expected Shipments Found</h2>
                <p>We are not currently expecting any shipments for <?php echo $client_name;?></p>
            </div>
        </div>
    </div>
    <?php else:?>
    <div id="waiting" class="row">
        <div class="col-lg-12 text-center">
            <h2>Drawing Table..</h2>
            <p>May take a few moments</p>
            <img class='loading' src='/images/preloader.gif' alt='loading...' />
        </div>
    </div>
    <div class="row" id="table_holder" style="display:none">
        <div class="col-md-12">
            <table width="100%" class="table-striped table-hover" id="expected_shipments_table">
                <thead>
                    <tr>
                        <th>Shipment Number</th>
                        <th>Items</th>
                        <th>ETA</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($shipments as $s):
                        $is = $this->controller->shipment->getShipmentItemsString($s['id']);
                        $de = $s['date_expected'] > 0? date("d/m/Y", $s['date_expected']) : "Not Known";?>
                        <tr>
                            <td data-label="Shipment Number"><?php echo $s['shipment_number'];?></td>
                            <td data-label="Items"><?php echo $is;?></td>
                            <td data-label="ETA" class="number"><?php echo $de;?></td>
                        </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif;?>
</div>