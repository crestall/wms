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
            <div class="row">
                <div class="col">
                    <div class="errorbox">
                        <div class="row">
                            <div class="col-4 text-right">
                                <i class="fad fa-exclamation-triangle fa-6x"></i>
                            </div>
                            <div class="col-8">
                                <h2>No ID Supplied</h2>
                                <p>No order id was supplied, so an order could not be found</p>
                                <p>Please <a href="/orders/client-orders">click here</a> to go back to the list of orders to select one to track.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php elseif(empty($order)):?>
            <div class="row">
                <div class="col">
                    <div class="errorbox">
                        <div class="row">
                            <div class="col-4 text-right">
                                <i class="fad fa-exclamation-triangle fa-6x"></i>
                            </div>
                            <div class="col-8">
                                <h2>No Order Found</h2>
                                <p>No order was found with that ID</p>
                                <p>Please <a href="/orders/client-orders">click here</a> to go back to the list of orders to select one to track.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
                                    <?php if(isset($tracking['tracking_results'][0]['errors'])):?>
                                        <div class="row">
                                            <div class="col">
                                                <div class="errorbox">
                                                    <h2>There was an error collecting the tracking data</h2>
                                                    <p><?php echo $tracking['tracking_results'][0]['errors'][0]['code'].": ".$tracking['tracking_results'][0]['errors'][0]['message'];?></p>
                                                    <p>Please <a href="/orders/client-orders">click here</a> to go back to the list of orders.</p>
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
                                <?php endif;?>
                            <?php else:?>
                                <div class="row">
                                    <div class="col">
                                        <div class="errorbox">
                                            <h2>No Consignment For Order</h2>
                                            <p>The order does not have a consignment ID yet</p>
                                            <p>Maybe it has not been dispatched yet</p>
                                            <p>Please <a href="/orders/client-orders">click here</a> to go back to the list of orders to select one to track.</p>
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

            <div class="row">
                <div class="col-lg-12">
                    <h2>Tracking Details</h2>
                </div>
            </div>
            <?php if(!is_null($order['consignment_id'])):?>
                <?php if($courier == "eParcel" || $courier == "eParcel Express"):?>
                    <?php if(isset($tracking['tracking_results'][0]['errors'])):?>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="errorbox">
                                    <h2>There was an error collecting the tracking data</h2>
                                    <p><?php echo $tracking['tracking_results'][0]['errors'][0]['code'].": ".$tracking['tracking_results'][0]['errors'][0]['message'];?></p>
                                    <p>Please <a href="/orders/client-orders">click here</a> to go back to the list of orders.</p>
                                </div>
                            </div>
                        </div>
                    <?php else:?>
                        <div class="row">
                            <div class="col-lg-12">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Detals</th>
                                            <th>Date &amp; Time</th>
                                            <th>Location</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($tracking['tracking_results'][0]['trackable_items'][0]['items'][0]['events'] as $event):?>
                                            <tr>
                                                <td><?php echo $event['description'];?></td>
                                                <td><?php echo date("D F j, Y, g:i a", strtotime($event['date']));?></td>
                                                <td><?php if(isset($event['location'])) echo $event['location'];?></td>
                                            </tr>
                                        <?php endforeach;?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif;?>
                <?php elseif($courier == "Hunters" || $courier == "HuntersPLU"):?>
                    <?php if(isset($tracking['errorCode'])): ?>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="errorbox">
                                    <h2>There was an error collecting the tracking data</h2>
                                    <p><?php echo $tracking['errorCode'].": ".$tracking['errorMessage'];?></p>
                                    <p>Please <a href="/orders/client-orders">click here</a> to go back to the list of orders.</p>
                                </div>
                            </div>
                        </div>
                    <?php elseif(!count($tracking['events'])):?>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="feedbackbox">
                                    <h2>Order Still Processing</h2>
                                    <p>No tracking events registered for that order</p>
                                    <p>Please check back later when the details have been updated</p>
                                    <p>Please <a href="/orders/client-orders">click here</a> to go back to the list of orders.</p>
                                </div>
                            </div>
                        </div>
                    <?php else:?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Event</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($tracking['events'] as $e):?>
                                    <tr>
                                        <td><?php echo $e['when'];?></td>
                                        <td><?php echo $e['what'];?></td>
                                        <td><?php echo $e['info'];?></td>
                                    </tr>
                                <?php endforeach;?>
                            </tbody>
                        </table>
                    <?php endif;?>
                <?php else:?>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="feedbackbox">
                                <h2>Local Courier Used</h2>
                                <p>There is no tracking information available for this courier</p>
                            </div>
                        </div>
                    </div>
                <?php endif;?>
            <?php else:?>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="errorbox">
                            <h2>No Consignment For Order</h2>
                            <p>The order does not have a consignment ID yet</p>
                            <p>Maybe it has not been dispatched yet</p>
                            <p>Please <a href="/orders/client-orders">click here</a> to go back to the list of orders to select one to track.</p>
                        </div>
                    </div>
                </div>
            <?php endif;?>
        <?php endif;?>
    </div>
</div>