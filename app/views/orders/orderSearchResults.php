<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php echo $form;?>
    <div class="form-group row">
        <label class="col-md-3 col-form-label">&nbsp;</label>
        <div class="col-md-4">
            <p>
                <a href="/orders/order-search" class="btn btn-primary">Reset Form</a> 
            </p>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <h2>Search Results</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <?php if($count > 0):?>
                <div class="feedbackbox">
                    <h2>Found <?php echo $count;?> order<?php echo $s;?></h2>
                    <p>They are listed below</p>
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
    <?php if($count > 0):
        $c = 0;?>
        <div class="row">
            <div class="col-md-12">
                <table id="search_results_table">
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
                            <th>eParcel Manifest</th>
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
                                <td class="number"><a href="<?php echo $link;?>"><?php echo str_pad($o['order_number'],8,'0',STR_PAD_LEFT);?></a></td>
                                <td class="number"><?php echo $o['client_order_id'];?></td>
                                <td nowrap><?php echo $client_name;?></td>
                                <td nowrap><?php echo $o['ship_to']."<br/>".$address;?></td>
                                <td><?php echo $num_items;?></td>
                                <td><?php echo $items;?></td>
                                <td class="nowrap"><?php echo date('d-m-Y', $o['date_ordered']);?></td>
                                <td class='nowrap'><?php echo $dd;?></td>
                                <td><?php echo $this->controller->order->getStatusName($o['status_id']);?></td>
                                <td><?php echo $courier;?></td>
                                <td><?php echo $o['consignment_id'];?></td>
                                <td><?php echo $o['manifest_id'];?></td>
                            </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif;?>
</div>