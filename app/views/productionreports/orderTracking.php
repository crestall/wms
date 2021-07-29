<?php
$entered_by = $this->controller->user->getUserName( $order['entered_by'] );
if(empty($entered_by))
{
    $entered_by = "Automatically Imported";
}
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php if($order_id == 0):?>
            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/no_order_id.php");?>
        <?php elseif(empty($order)):?>
            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/no_order_found.php");?>
        <?php else:?>
            <div class="row">
                <div class="col-sm-12 col-md-6 mb-3">
                    <div class="card border-secondary h-100 order-card">
                        <div class="card-header bg-secondary text-white">
                            Order Details
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <label class="col-4">Client Order Number</label>
                                <div class="col-8"><?php echo $order['client_order_id'];?></div>
                            </div>
                            <div class="row">
                                <label class="col-4">Deliver To</label>
                                <div class="col-8"><?php echo $order['ship_to'];?></div>
                            </div>
                            <div class="row">
                                <label class="col-4">Company</label>
                                <div class="col-8"><?php echo $order['company_name'];?></div>
                            </div>
                            <div class="row">
                                <label class="col-4">Contact Phone</label>
                                <div class="col-8"><?php echo $order['contact_phone'];?></div>
                            </div>
                            <div class="row">
                                <label class="col-4">Tracking Email</label>
                                <div class="col-8"><?php echo $order['tracking_email'];?></div>
                            </div>
                            <div class="row">
                                <label class="col-4">Delivery Instructions</label>
                                <div class="col-8"><?php echo $order['instructions'];?></div>
                            </div>
                            <div class="row">
                                <label class="col-4">Use Express</label>
                                <div class="col-8"><?php if($order['eparcel_express'] > 0) echo "Yes"; else echo "No";?></div>
                            </div>
                            <div class="row">
                                <label class="col-4">Signature Required</label>
                                <div class="col-8"><?php if($order['signature_req'] > 0) echo "Yes"; else echo "No";?></div>
                            </div>
                        </div>
                        <div class="card-footer">

                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6 mb-3">
                    <div class="card border-secondary h-100 order-card">
                        <div class="card-header bg-secondary text-white">
                            Tracking Details
                        </div>
                        <div class="card-body">
                            <?php if(!is_null($order['consignment_id'])):?>
                                <?php if($courier == "eParcel" || $courier == "eParcel Express"):?>
                                    <h5 class="card-subtitle mb-3">eParcel (AusPost) Tracking</h5>
                                    <div class="row">
                                        <label class="col-5">Tracking Number</label>
                                        <div class="col-7"><?php echo $order['consignment_id'];?></div>
                                    </div>
                                    <?php if(isset($tracking['tracking_results'][0]['errors'])):?>
                                        <div class="row">
                                            <div class="col">
                                                <div class="errorbox">
                                                    <h2>There was an error collecting the tracking data</h2>
                                                    <p><?php echo $tracking['tracking_results'][0]['errors'][0]['code'].": ".$tracking['tracking_results'][0]['errors'][0]['message'];?></p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else:?>
                                        <?php foreach($tracking['tracking_results'][0]['trackable_items'][0]['items'][0]['events'] as $event):?>
                                            <div class="row border-bottom border-secondary border-bottom-dashed mb-3">
                                                <label class="col-5">Date</label>
                                                <div class="col-7"><?php echo date("D F j, Y, g:i a", strtotime($event['date']));?></div>
                                                <label class="col-5">Location</label>
                                                <div class="col-7"><?php if(isset($event['location'])) echo $event['location'];?></div>
                                                <label class="col-5">Details</label>
                                                <div class="col-7"><?php echo $event['description'];?></div>
                                            </div>
                                        <?php endforeach;?>
                                    <?php endif;?>
                                <?php elseif($courier == "Direct Freight"):?>
                                    <h5 class="card-subtitle mb-3">Direct Freight Tracking</h5>
                                    <div class="row">
                                        <label class="col-5">Tracking Number</label>
                                        <div class="col-7"><?php echo $order['consignment_id'];?></div>
                                    </div>
                                    <?php if($tracking['ResponseCode'] != 300):?>
                                        <div class="row">
                                            <div class="col errorbox">
                                                <h5 class="card-subtitle mb-3">There Has Been An Error</h5>
                                                <div class="ml-4"><?php echo $tracking['ResponseMessage'];?></div>
                                            </div>
                                        </div>
                                    <?php elseif($tracking['TrackingResults'][0]['ResponseCode'] != 300):?>
                                        <div class="row">
                                            <div class="col errorbox">
                                                <h5 class="card-subtitle mb-3">There Has Been An Error</h5>
                                                <div class="ml-4"><?php echo $tracking['TrackingResults'][0]['ResponseMessage'];?></div>
                                            </div>
                                        </div>
                                    <?php else:?>
                                        <div class="row border-bottom border-secondary mb-3">
                                            <label class="col-5">Expected Delivery</label>
                                            <div class="col-7"><?php echo date("D F j, Y", strtotime($tracking['TrackingResults'][0]['EtaDate']));?></div>
                                        </div>
                                        <?php foreach($tracking['TrackingResults'][0]['ConsignmentTrackingDetails'] as $event):?>
                                            <div class="row border-bottom border-secondary border-bottom-dashed mb-3">
                                                <label class="col-5">Date</label>
                                                <div class="col-7"><?php echo date("D F j, Y, g:i a", strtotime($event['Date']));?></div>
                                                <label class="col-5">Location</label>
                                                <div class="col-7"><?php if(isset($event['Location'])) echo $event['Location'];?></div>
                                                <label class="col-5">Status</label>
                                                <div class="col-7"><?php echo $event['Status'];?></div>
                                            </div>
                                        <?php endforeach;?>
                                    <?php endif;?>
                                <?php else:?>
                                    <div class="row">
                                        <div class="col">
                                            <div class="feedbackbox">
                                                <h5 class="card-subtitle">Local Courier Used</h5>
                                                <div class="ml-4">
                                                    <p>There is no tracking information available for this courier</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif;?>
                            <?php else:?>
                                <div class="row">
                                    <div class="col">
                                        <div class="errorbox">
                                            <h2>No Consignment For Order</h2>
                                            <p>The order does not have a consignment ID yet</p>
                                            <p>Maybe it has not been dispatched yet</p> 
                                        </div>
                                    </div>
                                </div>
                            <?php endif;?>
                        </div>
                        <div class="card-footer">

                        </div>
                    </div>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>