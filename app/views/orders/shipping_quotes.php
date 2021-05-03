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
                <?php else:
                    $express_charge = "$".number_format($express_response['shipments'][0]['shipment_summary']['total_cost'], 2);?>
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
                <?php //echo "<pre>",print_r($df_response),"</pre>"; die();?>
                <?php if($df_response['ResponseCode'] == 300):
                    $surcharges = 0;
                    foreach($df_response['df_items'] as $i)
                    {
                        if( $i['Kgs'] > 30 )
                        {
                            $w = $i['Kgs'] - 30;

                            $ws = ( floor($w / 30) + 1) * 5;
                            $ws = ($ws > 25)? 25 : $ws;
                            $ws = $ws * $i['Items'];
                            $surcharges += $ws;
                        }
                        if($i["Length"] + $i['Width'] + $i['Height'] >= 220)
                            $surcharges += 5 * $i['Items'];
                        if( ($i['Length'] >= 150 && $i['Length'] < 200) || ($i['Width'] >= 150 && $i['Width'] < 200) || ($i['Height'] >= 150 && $i['Height'] < 200) )
                            $surcharges += 5 * $i['Items'];
                        elseif( ($i['Length'] >= 200 && $i['Length'] < 299) || ($i['Width'] >= 200 && $i['Width'] < 299) || ($i['Height'] >= 200 && $i['Height'] < 299) )
                            $surcharges += 12 * $i['Items'];
                        elseif( ($i['Length'] >= 300 && $i['Length'] < 399) || ($i['Width'] >= 300 && $i['Width'] < 399) || ($i['Height'] >= 300 && $i['Height'] < 399) )
                            $surcharges += 25 * $i['Items'];
                        elseif( ($i['Length'] >= 400 && $i['Length'] < 499) || ($i['Width'] >= 400 && $i['Width'] < 499) || ($i['Height'] >= 400 && $i['Height'] < 499) )
                            $surcharges += 65 * $i['Items'];
                        elseif( ($i['Length'] >= 500 && $i['Length'] < 599) || ($i['Width'] >= 500 && $i['Width'] < 599) || ($i['Height'] >= 500 && $i['Height'] < 599) )
                            $surcharges += 110 * $i['Items'];
                        elseif( ($i['Length'] >= 600) || ($i['Width'] >= 600) || ($i['Height'] >= 600) )
                            $surcharges += 300 * $i['Items'];
                    }
                    //echo "Surcharges: $surcharges";
                    $surcharges = number_format($surcharges * 1.1 * DF_FUEL_SURCHARGE, 2);
                    $df_charge = number_format($df_response['TotalFreightCharge'] * 1.1 * DF_FUEL_SURCHARGE, 2);
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
