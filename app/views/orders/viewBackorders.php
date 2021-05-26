<?php

?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="row">
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="form-group">
                    <label>Filter By Client</label>
                    <select id="client_selector" class="form-control selectpicker" data-style="btn-outline-secondary"><option value="0">All Clients</option><?php echo $this->controller->client->getSelectClients($client_id);?></select>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="form-group">
                    <label>Search</label>
                    <input type="text" class="form-control" id="table_searcher" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <?php if(isset($_SESSION['feedback'])) :?>
                   <div class='feedbackbox'><?php echo Session::getAndDestroy('feedback');?></div>
                <?php endif; ?>
                <?php if(isset($_SESSION['errorfeedback'])) :?>
                   <div class='errorbox'><?php echo Session::getAndDestroy('errorfeedback');?></div>
                <?php endif; ?>
            </div>
        </div>
        <?php if(count($orders)):?>
            <div id="waiting" class="row">
                <div class="col-lg-12 text-center">
                    <h2>Drawing Table..</h2>
                    <p>May take a few moments</p>
                    <img class='loading' src='/images/preloader.gif' alt='loading...' />
                </div>
            </div>
            <div class="row" id="table_holder" style="display:none">
                <?php //echo "<pre>",print_r($orders),"</pre>";?>
                <div class="col-xl-12">
                    <table class="table-striped table-hover" id="back_orders_table">
                        <thead>
                            <tr>
                                <th>WMS Number</th>
                                <th>Client</th>
                                <th>Client Order Number</th>
                                <th>Date Ordered</th>
                                <th>Ship To</th>
                                <th>Backordered Items</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($orders as $bo):
                                $client_name = $this->controller->client->getClientName($bo['client_id']);
                                if(empty($bo['ship_to']))
                                {
                                    $ship_to = "";
                                }
                                else
                                {
                                    $ship_to = "<p class='font-weight-bold'>".$bo['ship_to']."</p>";
                                }
                				$ship_to .= $this->controller->address->getAddressStringForOrder($bo['id']);
                                //$boifo = $this->controller->order->getBackorderItemsForOrder($bo['id']);
                                $can_fulfill = true;
                                $item_count = $this->controller->order->getItemCountForOrder($bo['id']);
                                $ifo = $this->controller->order->getItemsForOrder($bo['id']);
                                ?>
                                <tr>
                                    <td class="filterable number" data-label="Order Number">
                                        <a href="/inventory/receive-pod-stock/order=<?php echo $bo['id'];?>"><?php echo $bo['order_number'];?></a>
                                    </td>
                                    <td data-label="Client Name"><?php echo $client_name;?></td>
                                    <td class="filterable number" data-label="Client Order Number"><?php echo $bo['client_order_id'];?></td>
                                    <td data-label="Date Ordered" nowrap><?php echo date('d-m-Y', $bo['date_ordered']);?></td>
                                    <td data-label="Ship To" class="filterable"><?php echo $ship_to;?></td>
                                    <td data-label="Items">
                                        <?php foreach($ifo as $i):
                                            $available = (empty($i['location_qty']))? 0 : $i['location_qty'];?>
                                            <div class="item_list border-bottom border-secondary border-bottom-dashed mb-3 ">
                                                <p><span class="iname"><?php echo $i['name'];?></span><br>
                                                <span class="icount font-weight-bold">Required: <?php echo $i['qty'];?></span>
                                                <?php if(!empty($i['pod_id'])):?>
                                                    <br><span class="bo_pod font-weight-bold">Awaiting: <?php echo $i['pod_id'];?></span>
                                                <?php endif;?>
                                                </p>
                                                <div class="item_total text-right font-weight-bold <?php if($available < $i['qty']) echo "text-danger";?>">
                                                    Available: <?php echo $available;?>
                                                </div>
                                            </div>
                                        <?php endforeach;?>
                                        <div class="item_total text-right">
                                            Total Items: <?php echo $item_count;?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if($user_role == "admin" || $user_role == "super admin"):?>
                                            <p>
                                                <a class="btn btn-sm btn-block btn-outline-danger cancel-order" data-orderid="<?php echo $bo['id'];?>"><i class="fas fa-ban"></i> Cancel This Order</a>
                                                <span class="inst">This will make all items in this order available again</span>
                                            </p>
                                        <?php endif;?>
                                    </td>
                                </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else:?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="errorbox">
                        <h2><i class="fas fa-exclamation-triangle"></i> No Backorders Listed</h2>
                        <p>No Backorders are currently listed in the system</p>
                    </div>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>
