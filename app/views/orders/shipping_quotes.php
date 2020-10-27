<div class="row mb-2">
    <p class="inst">These prices are GST and Fuel Levee Inclusive.</p>
    <p class="inst">No margin has been added.</p>
    <p class="inst font-weight-bold">Please do not make these prices available to the customer/client.</p>
</div>
<div class="row">
    <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
        <div class="card border-secondary h-100 order-card">
            <div class="card-header bg-secondary text-white">
                Eparcel Pricing
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
        <div class="card border-secondary h-100 order-card">
            <div class="card-header bg-secondary text-white">
                Eparcel Express Pricing
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
        <div class="card border-secondary h-100 order-card">
            <div class="card-header bg-secondary text-white">
                Direct Freight Pricing
            </div>
        </div>
    </div>
</div>
<?php if(isset($eparcel_response['errors'])):?>

<?php else:?>

<?php endif;?>

<?php echo "<pre>",print_r($eparcel_response),"</pre>";?>