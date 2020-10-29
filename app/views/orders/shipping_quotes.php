<div class="row mb-3">
    <div class="col-12">
        <p class="inst">These prices are GST and Fuel Levee Inclusive.</p>
        <p class="inst">No margin has been added.</p>
        <p class="inst font-weight-bold">Please do not make these prices available to the customer/client.</p>
    </div>
</div>
<div class="row">
    <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
        <div class="card border-secondary h-100 order-card">
            <div class="card-header bg-secondary text-white">
                Eparcel Pricing
            </div>
            <div class="card-body">
                <?php //echo "<pre>",print_r($eparcel_response),"</pre>";?>
                <?php if(isset($eparcel_response['errors'])):?>
                    <div class='errorbox'>
                        <p><?php echo $eparcel_response['errors'][0]['message'];?></p>
                    </div>
                <?php else:
                    $eparcel_charge = "$".number_format($eparcel_response['shipments'][0]['shipment_summary']['total_cost'] * 1.1, 2);?>
                    <div class="row">
                        <label class="col-8">Quoted Price</label>
                        <div class="col-4">
                            <?php echo $eparcel_charge;?>
                        </div>
                    </div>
                <?php endif;?>
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
        <div class="card border-secondary h-100 order-card">
            <div class="card-header bg-secondary text-white">
                Eparcel Express Pricing
            </div>
            <div class="card-body">
                <?php //echo "<pre>",print_r($express_response),"</pre>";?>
                <?php if(isset($express_response['errors'])):?>
                    <div class='errorbox'>
                        <p><?php echo $express_response['errors'][0]['message'];?></p>
                    </div>
                <?php $express_charge = "$".number_format($express_response['shipments'][0]['shipment_summary']['total_cost'] * 1.1, 2);?>
                    <div class="row">
                        <label class="col-8">Quoted Price</label>
                        <div class="col-4">
                            <?php echo $express_charge;?>
                        </div>
                    </div>
                <?php endif;?>
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
        <div class="card border-secondary h-100 order-card">
            <div class="card-header bg-secondary text-white">
                Direct Freight Pricing
            </div>
            <div class="card-body">
                <?php echo "<pre>",print_r($df_response),"</pre>";?>
            </div>
        </div>
    </div>
</div>
<?php if(isset($eparcel_response['errors'])):?>

<?php else:?>

<?php endif;?>
