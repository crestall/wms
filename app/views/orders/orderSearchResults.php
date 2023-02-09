<?php
$c = 0;
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php echo $form;?>
        <div class="row">
            <label class="col">&nbsp;</label>
            <div class="col m-3">
                <a href="/orders/order-search" class="btn btn-primary">Reset Form</a>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <h2>Search Results</h2>
            </div>
            <div class="col">
                <div class="form-group">
                    <label>Live Filter</label>
                    <input type="search" class="form-control" id="table_searcher" />
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col">
                <?php if($count > 0):?>
                    <div class="feedback_box">
                        <h2>Found <?php echo $count;?> Orders</h2>
                        <p>They will display below</p>
                    </div>
                    <div id="waiting" class="row">
                        <div class="col-lg-12 text-center">
                            <h2>Drawing Table..</h2>
                            <p>May take a few moments</p>
                            <img class='loading' src='/images/preloader.gif' alt='loading...' />
                        </div>
                    </div>
                    <div class="row" id="table_holder" style="display:none">
                        <div class="col-xl-12">
                            <table class="table-striped table-hover" id="search_orders_table" style="width: 95%;margin: auto">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Order No</th>
                                        <th>Client Order ID</th>
                                        <th>Client</th>
                                        <th>Ship To</th>
                                        <th>Item Count</th>
                                        <th>Items</th>
                                        <th>Date Ordered</th>
                                        <th>Date Dispatched</th>
                                        <th>Status</th>
                                        <th>Courier</th>
                                        <th>Consignment Number</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($orders as $o):
                                        ++$c;
                                        $ad = array(
                                            'address'   =>  $o['address'],
                                            'address_2' =>  $o['address_2'],
                                            'suburb'    =>  $o['suburb'],
                                            'state'     =>  $o['state'],
                                            'postcode'  =>  $o['postcode'],
                                            'country'   =>  $o['country']
                                        );
                                        $address = Utility::formatAddressWeb($ad);
                                        $client_name = $this->controller->client->getClientName($o['client_id']);
                                        $courier = $this->controller->courier->getCourierName($o['courier_id']);
                                        //$link = ( $o['store_order'] == 1 )? "/big-bottle-store-orders/{$o['xero_invoiceno']}":"/orders/order-update/order={$o['id']}";
                                        $link = "/orders/order-update/order={$o['id']}";
                                        $charge = "$".number_format($o['total_cost'], 2);
                                        $products = $this->controller->order->getItemsForOrder($o['id']);
                                        $items = "";
                                        $num_items = 0;
                                        foreach($products as $p)
                                        {
                                            $items .= $p['name']." (".$p['qty']."),<br/>";
                                            $num_items += $p['qty'];
                                        }
                                        $items = rtrim($items, ",<br/>");
                                        $dd = (empty($o['date_fulfilled']))? "" : date('d-m-Y', $o['date_fulfilled']);?>
                                        <tr>
                                            <td class="number"><?php echo $c;?></td>
                                            <td class="number" data-label="Order No"><a href="<?php echo $link;?>"><?php echo str_pad($o['order_number'],8,'0',STR_PAD_LEFT);?></a></td>
                                            <td class="number" data-label="Client Order No"><?php echo $o['client_order_id'];?></td>
                                            <td nowrap data-label="Client Name"><?php echo $client_name;?></td>
                                            <td nowrap data-label="Ship To"><?php echo $o['ship_to']."<br/>".$address;?></td>
                                            <td data-label="Item Count"><?php echo $num_items;?></td>
                                            <td data-label="Items"><?php echo $items;?></td>
                                            <td data-label="Date Ordered" class="nowrap"><?php echo date('d-m-Y', $o['date_ordered']);?></td>
                                            <td data-label="Date Dispatched" class='nowrap'><?php echo $dd;?></td>
                                            <td data-label="Status"><?php echo $this->controller->order->getStatusName($o['status_id']);?></td>
                                            <td data-label="Courier"><?php echo $courier;?></td>
                                            <td data-label="Consignment Number"><?php echo $o['consignment_id'];?></td>
                                        </tr>
                                    <?php endforeach;?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php else:?>
                    <div class="errorbox">
                        <h2>No Orders Found</h2>
                        <p>No orders were found when searching against "<strong><?php echo $term;?></strong>"</p>
                        <p>Maybe remove some filters?</p>
                    </div>
                <?php endif;?>
            </div>
        </div>
    </div>
</div>