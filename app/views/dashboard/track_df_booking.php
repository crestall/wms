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
            <?php else:
                foreach($atr['ConsignmentTrackingDetails'] as $ctd):?>
                    <div class="p-3 light-grey mb-3">
                        <?php echo "<pre>",print_r($ctd),"</pre>";?>
                    </div>
                <?php endforeach;?>
            <?php endif;?>
        <?php endif;?>
    </div>
</div>