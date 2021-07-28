<?php

?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="border border-secondary p-3 m-3 rounded bg-light">
            <h3>Filter These Orders</h3>
            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/between_dates.php");?>
            <div class="row">
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                    <div class="form-group">
                        <label>Filter By Client</label>
                        <select id="client_selector" class="form-control selectpicker" data-style="btn-outline-secondary"><option value="0">All Production Clients</option><?php echo $this->controller->client->getSelectProductionClients($client_id);?></select>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                    <div class="form-group">
                        <label>Filter By Status</label>
                        <select id="status_selector" class="form-control selectpicker" data-style="btn-outline-secondary"><option value="0">All Status</option><?php echo $this->controller->order->getSelectStatuses($status_id);?></select>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                    <div class="form-group">
                        <label>Search</label>
                        <input type="text" class="form-control" id="table_searcher" />
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <a href="/production-reports/warehouse-orders" class="w-100 btn btn-outline-danger" >Remove Filters</a>
                    </div>
                </div>
            </div>
        </div>
        <?php if(count($orders)):?>
            <?php echo "<pre>",print_r($orders),"</pre>";?>
            <div id="waiting" class="row">
                <div class="col-lg-12 text-center">
                    <h2>Drawing Table..</h2>
                    <p>May take a few moments</p>
                    <img class='loading' src='/images/preloader.gif' alt='loading...' />
                </div>
            </div>
            <div class="row" id="table_holder" style="display:none">
                <div class="col-xl-12">
                    <table class="table-striped table-hover" id="production_orders_table" style="width: 95%;margin: auto">
                        <thead>
                            <tr>
                                <th data-priority="1">Order No</th>
                                <th>Status</th>
                                <th data-priority="1">Date Ordered</th>
                                <th data-priority="1">Date Fulfilled</th>
                                <th data-priority="2">Deliver To</th>
                                <th data-priority="2">Items</th>
                                <th data-priority="1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($orders as $o):
                                $fullfillment = ($o['date_fulfilled'] > 0)? date('d-m-Y', $o['date_ordered']) : "Not Yet Fulfilled";
                                if(!empty($o['company_name']))
                                {
                                    $ship_to = $o['company_name']."<br>Attn:".$o['ship_to'];
                                }
                                else
                                {
                                    $ship_to = $o['ship_to'];
                                }
                                $address = $this->controller->address->getAddressStringForOrder($o['id']);
            				    $order_status = $this->controller->order->getStatusName($o['status_id']);
                                $item_count = $this->controller->order->getItemCountForOrder($o['id']);
                                $ifo = $this->controller->order->getItemsForOrder($o['id']);
                                ?>
                                <tr id="tr_<?php echo $o['id'];?>">
                                    <td class="filterable number">
                                        <?php echo str_pad($o['order_number'],8,'0',STR_PAD_LEFT);?>
                                    </td>
                                    <td class="filterable" >
                                        <?php echo $order_status;?>
                                    </td>
                                    <td nowrap><?php echo date('d-m-Y', $o['date_ordered']);?></td>
                                    <td nowrap><?php echo $fulfillment;?></td>
                                    <td class="filterable">
                	                    <p class='font-weight-bold'><?php echo $ship_to;?></p>
                                        <p><?php echo $address;?></p>
                                    </td>
                                    <td>
                                        <div class="item_list border-bottom border-secondary border-bottom-dashed mb-3 ">
                                            <?php foreach($ifo as $i):?>
                                                <p><span class="iname"><?php echo $i['name'];?>:</span><span class="icount"><?php echo $i['qty'];?></span><span class="ilocation">(<?php echo $i['location'];?>)</span></p>
                                            <?php endforeach;?>
                                        </div>
                                        <div class="item_total text-right">
                                            Total Items: <?php echo $item_count;?>
                                        </div>
                                    </td>
                                    <td></td>
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
                        <h2><i class="fas fa-exclamation-triangle"></i> No Orders Listed</h2>
                        <p>You may need to remove some filters or change the date range</p>
                    </div>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>