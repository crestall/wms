<?php
//echo "<pre>",print_r($express_response),"</pre>";die();
$eparcel_express_charge = $eparcel_charge = 0;
if(!isset($express_response['errors']))
{
        $eparcel_express_charge = "$".number_format($express_response['shipments'][0]['shipment_summary']['total_cost_ex_gst'],2);

}
else
{
    ?>
    <div class="row mb-3">
        <div class="col-12 errorbox">
            <h2>There has been an eParcel API error</h2>
            <p>
                CODE: <?php echo $eparcel_response['errors'][0]['code']?>
            </p>
            <p>
                NAME: <?php echo $eparcel_response['errors'][0]['name']?>
            </p>
            <p>
                MESSAGE: <?php echo $eparcel_response['errors'][0]['message']?>
            </p>
        </div>
    </div>
    <?php
    //die();
}
if(!isset($eparcel_response['errors']))
{
    if($eparcel_response['shipments'][0]['items'][0]['product_id'] == '3D85')
        $eparcel_charge = "$".number_format($eparcel_response['shipments'][0]['shipment_summary']['total_cost_ex_gst'],2);

    /*foreach($eparcel_response['shipments'][0]['shipment_summary'] as $pt)
    {
        if($pt['product_id'] == '3D85') //parcelpost
            $eparcel_charge = "$".number_format($pt['calculated_price_ex_gst'],2);
        elseif($pt['product_id'] == '3J85') //expresspost
            $eparcel_express_charge = "$".number_format($pt['calculated_price_ex_gst'],2);
    }
    */
}
else
{
    ?>
    <div class="row mb-3">
        <div class="col-12 errorbox">
            <h2>There has been an eParcel API error</h2>
            <p>
                CODE: <?php echo $eparcel_response['errors'][0]['code']?>
            </p>
            <p>
                NAME: <?php echo $eparcel_response['errors'][0]['name']?>
            </p>
            <p>
                MESSAGE: <?php echo $eparcel_response['errors'][0]['message']?>
            </p>
        </div>
    </div>
    <?php
    //die();
}
?>
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
                <?php if(isset($eparcel_response['items'][0]['errors'])) :
                        //die("create an error box");
                        $e_string = "<div class='errorbox'><ul>";
                        if(!is_array($eparcel_response['items'][0]['errors']))
                           $errs[0] = $eparcel_response['items'][0]['errors'];
                        else
                           $errs = $eparcel_response['items'][0]['errors'];
                        foreach($errs as $err)
                            $e_string .= "<li>".$err['message']."</li>";
                        $e_string .= "</ul></div>";?>
                    <div class='errorbox'>
                        <p><?php echo $e_string;?></p>
                    </div>
                <?php else:
                    //$eparcel_charge = 23.5;//"$".number_format($eparcel_response['shipments'][0]['shipment_summary']['total_cost'], 2);?>
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
                <?php if(isset($eparcel_response['items'][0]['errors'])) :
                        //die("create an error box");
                        $e_string = "<div class='errorbox'><ul>";
                        if(!is_array($eparcel_response['items'][0]['errors']))
                           $errs[0] = $eparcel_response['items'][0]['errors'];
                        else
                           $errs = $eparcel_response['items'][0]['errors'];
                        foreach($errs as $err)
                            $e_string .= "<li>".$err['message']."</li>";
                        $e_string .= "</ul></div>";?>
                    <div class='errorbox'>
                        <p><?php echo $e_string;?></p>
                    </div>
                <?php else:
                    //$eparcel_charge = 23.5;//"$".number_format($eparcel_response['shipments'][0]['shipment_summary']['total_cost'], 2);?>
                    <div class="row">
                        <label class="col-8">Quoted Price</label>
                        <div class="col-4 text-right">
                            <?php echo $eparcel_express_charge;?>
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
                        <p><?php echo $df_response['ResponseMessage'];?></p>
                    </div>
                <?php endif;?>
            </div>
        </div>
    </div>
</div>
<?php if(isset($eparcel_response['errors'])):?>

<?php else:?>

<?php endif;?>
