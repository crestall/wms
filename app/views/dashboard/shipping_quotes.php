<div class="page-wrapper">
    <div class="row">
        <div class="col-md-12">
            <h2>Calculated Charges</h2>
            <!--span class="inst">(these charges do not include markup)</span><br/-->
            <span class="inst">(these charges are GST inclusive)</span>
            <?php //echo "<pre>",print_r($eparcel_details),"</pre>";?>
        </div>
    </div>
    <?php if($express):?>
        <div class="row errorbox">
            <label class="col-md-3 col-form-label">&nbsp;</label>
            <div class="col-md-9">
                Express Post Required
            </div>
        </div>
    <?php endif;?>
    <div class="row">
        <label class="col-md-8 col-form-label">eParcel</label>
        <div class="col-md-4">
            <?php echo $eparcel_charge;?>
        </div>
    </div>
    <div class="row">
        <label class="col-md-8 col-form-label">eParcel Express</label>
        <div class="col-md-4">
            <?php echo $eparcel_express_charge;?>
        </div>
    </div>
    <div id="3pltruck_holder"></div>
    <hr/>
    <div class="row">
        <div class="col-md-12">
            <h2>Delivery Details</h2>
        </div>
    </div>
    <div class="row">
        <label class="col-md-3 col-form-label">Client</label>
        <div class="col-md-9">
            <?php echo $client_name;?>
        </div>
    </div>
    <div class="row">
        <label class="col-md-3 col-form-label">Deliver To</label>
        <div class="col-md-9">
            <?php echo $ship_to;?>
        </div>
    </div>
    <div class="row">
        <label class="col-md-3 col-form-label">&nbsp;</label>
        <div class="col-md-9">
            <?php echo $od['suburb'];?>
        </div>
    </div>
    <div class="row">
        <label class="col-md-3 col-form-label">&nbsp;</label>
        <div class="col-md-9">
            <?php echo $od['postcode'];?>
        </div>
    </div>
    <div class="row">
        <label class="col-md-3 col-form-label">&nbsp;</label>
        <div class="col-md-9">
            <?php echo $od['state'];?>
        </div>
    </div>
    <div class="row">
        <label class="col-md-3 col-form-label">&nbsp;</label>
        <div class="col-md-9">
            <?php echo $od['country'];?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <h3>Items</h3>
        </div>
    </div>
    <?php foreach($items as $i):?>
        <div class="row">
            <label class="col-md-9 col-form-label"><?php echo $i['name'];?></label>
            <div class="col-md-3">
                <?php echo $i['qty'];?>
            </div>
        </div>
    <?php endforeach;?>
    <input type="hidden" name="destination" id="destination" value="<?php echo $address_string;?>" />
</div>