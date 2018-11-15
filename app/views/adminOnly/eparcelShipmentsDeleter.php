<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php foreach($clients as $c):?>
        <div class="row">
            <div class="col-md-12">
                <h2>Doing <?php echo $c['client_name'];?></h2>
            </div>
        </div>
        <?php
        $eParcelClass = $c['eparcel_location']."Eparcel";
        $response = $this->controller->{$eParcelClass}->GetShipments(0, 400, array('status' => 'created'));
        $id_string = "";
        $c = 0;
        ?>
        <div class="row">
            <div class="col-md-12">
                <p>Total shipments: <?php echo $response['pagination']['total_number_of_records'];?></p>
            </div>
        </div>
        <?php foreach($response['shipments'] as $a):
            if($c > 235) break;
            $order = $this->controller->order->getOrderByShipmentId($a['shipment_id']);
            if(empty($order)){
                $id_string .= $a['shipment_id'].",";
                ++$c;
            }?>
        <?php endforeach;?>
        <div class="row">
            <div class="col-md-12">
                <p><?php echo $id_string;?></p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <p>Will delete <?php echo $c;?> shipments</p>
            </div>
        </div>
        <?php if($c > 0):
            $dr = $this->controller->{$eParcelClass}->DeleteShipment($id_string); ?>
            <div class="row">
                <div class="col-md-12">
                    <pre>
                        <?php print_r($dr);?>
                    </pre>
                </div>
            </div>
        <?php endif;?>
        <hr/>
    <?php endforeach;?>
    <div class="row">
        <div class="col-md-12">
            <h2>Doing Generic</h2>
        </div>
    </div>
    <?php
    $response = $this->controller->Eparcel->GetShipments(0, 400, array('status' => 'created'));
    $id_string = "";
    $c = 0;
    ?>
    <div class="row">
        <div class="col-md-12">
            <p>Total shipments: <?php echo $response['pagination']['total_number_of_records'];?></p>
        </div>
    </div>
    <?php foreach($response['shipments'] as $a):
        if($c > 235) break;
        $order = $this->controller->order->getOrderByShipmentId($a['shipment_id']);
        if(empty($order)){
            $id_string .= $a['shipment_id'].",";
            ++$c;
        }?>
    <?php endforeach;?>
    <div class="row">
        <div class="col-md-12">
            <p><?php echo $id_string;?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <p>Will delete <?php echo $c;?> shipments</p>
        </div>
    </div>
    <?php if($c > 0):
        $dr = $this->controller->Eparcel->DeleteShipment($id_string); ?>
        <div class="row">
            <div class="col-md-12">
                <pre>
                    <?php print_r($dr);?>
                </pre>
            </div>
        </div>
    <?php endif;?>
    <hr/>
</div>