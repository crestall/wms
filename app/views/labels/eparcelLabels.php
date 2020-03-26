<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="row">
        <div class="col-lg-3">
            <p><a href="/orders/view-orders/client=<?php echo $client_id;?>" class="btn btn-primary">View Orders For Client</a></p>
        </div>
        <?php if($order_id > 0):?>
            <div class="col-lg-3">
                <p><a href="/orders/order-update/order=<?php echo $order_id;?>" class="btn btn-primary">View This Order</a></p>
            </div>
        <?php endif;?>
    </div>
    <?php if($error):?>
        <div class="row">
            <div class="col-lg-12">
                <div class="errorbox">
                    <h2><i class="far fa-times-circle"></i>There Was An Error Generating Labels</h2>
                    <?php echo $error_string;?>
                </div>
            </div>
        </div>
    <?php else:?>
        <?php foreach($request_ids as $request_id):?>
            <div class="row">
                <div class="col-lg-12">
                    <?php $response = $this->controller->{$eParcelClass}->GetLabel($request_id);
                    while ($response['labels'][0]['status'] == "PENDING")
                    {
                        $response = $this->controller->{$eParcelClass}->GetLabel($request_id);
                        //echo "<hr/>";
                        //echo "<p>".$response['labels'][0]['status']."</p>";
                    }
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <?php if(isset($response['errors']) || $response['labels'][0]['status'] == "ERROR"):?>
                        <div class="errorbox">
                            <h2><i class="far fa-times-circle"></i>There Was An Error Generating Labels</h2>
                            <?php echo "<pre>",print_r($response),"</pre>";?>
                        </div>
                    <?php else:?>
                        <div class='feedbackbox'>
                            <h2><i class="far fa-check-circle"></i>Labels Have Been Generated</h2>
                            <p>Click the button to download them</p>
                            <p><a href="<?php echo $response['labels'][0]['url'];?>" class="btn btn-primary" target="_blank">Download</a></p>
                        </div>
                    <?php endif;?>
                </div>
            </div>
            <?php /*echo "<pre>",print_r($response['labels']),"</pre>";?>
            <?php //echo "<pre>",print_r($order_ids),"</pre>";?>
            <?php foreach($order_ids as $oi):?>
                <?php echo "<p>".$this->controller->order->getOrderDetail($oi)['ship_to']."</p>";?>
            <?php endforeach;?>
            <hr/>
            <?php foreach($response['labels'][0]['shipment_ids'] as $si):?>
                <?php echo "<p>".$this->controller->order->getOrderByShipmentId($si)['ship_to']."</p>";?>
            <?php endforeach;*/?>
        <?php endforeach;?>
    <?php endif;?>
</div>