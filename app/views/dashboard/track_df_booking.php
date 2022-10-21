<div class="page-wrapper">
    <?php //echo "<pre>",print_r($tr),"</pre>"; die();?>
    <div class="p-3 pb-0 mb-2 rounded-top form-section-holder">
        <div class="row">
            <div class="col">
                <h3>Tracking Details</h3>
            </div>
        </div>
        <?php if($tr['ResponseCode'] != 300):?>
            <div class="p-3 errorbox mb-3">
                <div class="row">
                    <div class="col">
                        <h4>There Has Been An error</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <?php echo $tr['ResponseMessage'];?>
                    </div>
                </div>
            </div>
        <?php else:
            $atr = $tr['TrackingResults'][0];
            if($atr['ResponseCode'] != 300):?>
                <div class="p-3 errorbox mb-3">
                    <div class="row">
                        <div class="col">
                            <h4>There Has Been An error</h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <?php echo $atr['ResponseCode'].": ".$atr['ResponseMessage'];?>
                        </div>
                    </div>
                </div>
            <?php else:?>
                <div class="p-3 light-grey mb-3">
                    <div class="row">
                        <label class="col-4">Tracking Number</label>
                        <div class="col-8"><?php echo $atr['Connote'];?></div>
                    </div>
                    <div class="row">
                        <label class="col-4">Expected Delivery</label>
                        <div class="col-8"><?php echo date("D F j, Y", strtotime($atr['EtaDate']));?></div>
                    </div>
                </div>
                <?php foreach(array_reverse( $atr['ConsignmentTrackingDetails'] ) as $event)://Direc Freight puts them in newest first?>
                    <div class="p-3 light-grey mb-3">
                        <div class="row">
                            <label class="col-4">Date</label>
                            <div class="col-8"><?php echo date("D F j, Y, g:i a", strtotime($event['Date']));?></div>
                        </div>
                        <div class="row">
                            <label class="col-4">Location</label>
                            <div class="col-8"><?php if(isset($event['Location'])) echo $event['Location'];?></div>
                        </div>
                        <div class="row">
                            <label class="col-4">Packages/Pallets</label>
                            <div class="col-8"><?php if(isset($event['Location'])) echo $event['Items'];?></div>
                        </div>
                        <div class="row">
                            <label class="col-4">Status</label>
                            <div class="col-8"><?php if(isset($event['Location'])) echo $event['Status'];?></div>
                        </div>
                    </div>
                <?php endforeach;?>
            <?php endif;?>
        <?php endif;?>
    </div>
</div>