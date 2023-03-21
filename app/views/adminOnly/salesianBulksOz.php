<?php
//echo "<pre>",print_r($_SESSION),"</pre>";
//echo "<p>DOC_ROOT : ".DOC_ROOT."</p>";

/*
sments
[0]     => name
[1]     => company
[2]     => address1
[3]     => address2
[4]     => address3
[5]     => suburb
[6]     => state
[7]     => postcode
[8]     => width cm
[9]     => lenght cm
[10]    => height cm
[11]    => weight kg
[12]    => magazine count
[13]    => empty
*/
$line = 1;
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php"); ?>
        <div class="row">
            <div class="col-md-12">
                <h1>Importing Shipments</h1>
            </div>
        </div>
        <?php foreach ($sments as $s):
            $name = $s[0];
            $suburb = $s[5];
            $w = $s[8];
            $l = $s[9];
            $h = $s[10];
            $kg = $s[11];
            $state = $s[6];
            $postcode = $s[7];
            $aResponse = $this->controller->Eparcel->ValidateSuburb($suburb, $state, str_pad($postcode,4,'0',STR_PAD_LEFT)); ?>
            <div class="row">
                <div class="col-md-12">
                    <?php echo "<h2>Doing order for $name on line $line</h2>";?>
                    <?php echo "<h3>Checking Address</h3>";?>
                </div>
            </div>
            <?php
            $error_string = "";
            if(isset($aResponse['errors']))
            {
                foreach($aResponse['errors'] as $e)
                {
                    $error_string .= $e['message']." ";
                }
            }
            elseif($aResponse['found'] === false)
            {
                $error_string .= "Postcode does not match suburb or state";
            }
            if(strlen($error_string)):
                ?>
                <div class="row">
                    <div class="col-md-12">
                        <?php echo "<p>$error_string";?>
                    </div>
                </div>
            <?php else:
                //get the prices
                //eParcel
                $eparcel_shipment = array(
                    'from'  =>    array(
                        'suburb'    => 'BAYSWATER',
                        'state'     => 'VIC',
                        'postcode'  => 3153
                    ),
                    'to'    =>    array(
                        'suburb'    => $suburb,
                        'state'     => $state,
                        'postcode'  => $postcode
                    ),
                    'items' =>    array(
                        "product_id"    => '3D85',
                        "length"        => $l,
                        "height"        => $h,
                        "width"         => $w,
                        "weight"        => $kg
                    )
                );
                $eparcel_shipments['shipments'][0]  = $eparcel_shipment;

                $eparcel_response = $this->controller->Eparcel->GetQuote($eparcel_shipments);

                //Direct Freight
                $direct_freight_shipment = array(
                    'ConsignmentId'     => $line,
                    'CustomerReference' => 'Salesians',
                    'IsDangerousGoods'  => false,
                    'ConsignmentList'   => array(
                        array(
                            'ReceiverDetails'   => array(
                                'ReceiverName'          => $name,
                                'ReceiverContactName'   => $name,
                                'AddressLine1'          => $s[2],
                                'Suburb'                => $suburb,
                                'State'                 => $state,
                                'Postcode'              => $postcode,
                                'IsAuthorityToLeave'    => false
                            ),
                            'ConsignmentLineItems'  => array(
                                array(
                                    "RateType"  => "ITEM",
                                    "Items"     => 1,
                                    "Kgs"       => ceil( $kg ),
                                    "Length"    => $l,
                                    "Width"     => $w,
                                    "Height"    => $h
                                )
                            )
                        )
                    )
                );
                $direct_freight_shipment['ConsignmentList']['ReceiverDetails']['AddressLine2'] = (!empty($s[3])) ? $s[3] : "";

                //$df_response = $this->controller->directfreight->createConsignment($direct_freight_shipment);
                $df_r = $this->controller->directfreight->getQuote($direct_freight_shipment);
                $df_response = json_decode($df_r,true);
                //echo "<pre>",print_r($df_response),"</pre>";
                $df_charge =  ($df_response['TotalFreightCharge'] + $df_response['FuelLevyCharge']) * 1.1;
            ?>
                <div class="row">
                    <div class="col-md-12">
                        eParcel Price: <strong><?php echo "$".number_format($eparcel_response['shipments'][0]['shipment_summary']['total_cost'],2,'.',',');?></strong>
                    </div>
                    <div class="col-md-12">
                        Direct Freight Price: <strong><?php echo "$".number_format($df_charge,2,'.',',');?></strong>
                    </div>
                    <div class="col-md-12">
                        <?php
                        if( $df_charge > 0 && $df_charge < $eparcel_response['shipments'][0]['shipment_summary']['total_cost'] )
                        {
                           //$consignment_list['ConsignmentList'][] = $direct_freight_shipment;
                            //echo "<pre>",print_r($direct_freight_shipment),"</pre>";die();
                            $response = $this->controller->directfreight->createConsignment($direct_freight_shipment);
                            echo "<pre>",print_r($response),"</pre>";
                            echo "<p>==============================================================================</p>";
                        }
                        else
                            echo "Choose eParcel";
                        ?>
                    </div>
                </div>
            <?php endif;?>
            <div class="row">
                <div class="col-md-12">
                    <p>====================================================================================</p>
                </div>
            </div>
        <?php ++$line; endforeach;?>
    </div>
</div>