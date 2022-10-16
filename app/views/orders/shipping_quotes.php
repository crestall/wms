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
                    $eparcel_charge = "$".number_format($eparcel_response['shipments'][0]['shipment_summary']['total_cost'], 2);?>
                    <div class="row">
                        <label class="col-8">Quoted Price</label>
                        <div class="col-4 text-right">
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
                <?php else:
                    $express_charge = "$".number_format($express_response['shipments'][0]['shipment_summary']['total_cost'], 2);?>
                    <div class="row">
                        <label class="col-8">Quoted Price</label>
                        <div class="col-4 text-right">
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
                <?php //echo "<pre>",print_r($df_response),"</pre>"; //die();?>
                <?php if($df_response['ResponseCode'] == 300):
                    $fuel_surcharge = 1 + Utility::getDFFuelLevee($df_response['FuelLevy']);
                    $surcharges = Utility::getDFSurcharges($df_response['df_items']);
                    $surcharges = number_format($surcharges * 1.1 * $fuel_surcharge, 2);
                    $df_charge = number_format($df_response['TotalFreightCharge'] * 1.1 * $fuel_surcharge, 2);
                    $total = $surcharges + $df_charge;?>
                    <div class="row">
                        <label class="col-8">Shipping Price</label>
                        <div class="col-4 text-right">
                            <?php echo "$".$df_charge;?>
                        </div>
                        <label class="col-8">Surcharges</label>
                        <div class="col-4 text-right">
                            <?php echo "$".$surcharges;?>
                        </div>
                        <label class="col-8">Total</label>
                        <div class="col-4 text-right">
                            <?php echo '$'.$total;?>
                        </div>
                    </div>
                <?php else:?>
                    <div class='errorbox'>
                        <p><?php echo df_response['ResponseMessage'];?></p>
                    </div>
                <?php endif;?>
            </div>
        </div>
    </div>
</div>
<?php if(isset($eparcel_response['errors'])):?>

<?php else:?>

<?php endif;?>
