<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="row form-group">
        <label class="col-md-3 col-form-label">Filter By Date Ordered</label>
        <div class="col-md-1">
            <label>From</label>
        </div>
        <div class="col-md-2">
            <div class="input-group">
                <input type="text" class="form-control" name="date_from" id="date_from" value="<?php echo date("d/m/Y", $from);;?>" />
                <span class="input-group-addon"><i class="far fa-calendar-alt"></i></span>
            </div>
        </div>
        <div class="col-md-1">
            <label>To</label>
        </div>
        <div class="col-md-2">
            <div class="input-group">
                <input type="text" class="form-control" name="date_to" id="date_to" value="<?php echo date("d/m/Y", $to);;?>" />
                <span class="input-group-addon"><i class="far fa-calendar-alt"></i></span>
            </div>
        </div>
        <div class="col-md-3">
            <button id="change_dates" class="btn btn-small btn-primary">Change Dates</button>
        </div>
        <input type="hidden" id="date_from_value" name="date_from_value" value="<?php echo $from;?>" />
        <input type="hidden" id="date_to_value" name="date_to_value" value="<?php echo $to;?>" />
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
            <div class="col-lg-12">
                <table id="client_orders_table" class="table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Date Ordered</th>
                            <th>Date Fulfilled</th>
                            <th>3PL Order Number</th>
                            <th>Your Order ID</th>
                            <th>Delivery To</th>
                            <th>Address</th>
                            <th>Items</th>
                            <th>Total Items</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($orders as $o):
                            $date_fulfilled = ($o['date_fulfilled'] > 0)? date('d/m/Y', $o['date_fulfilled']): "";
                            $ad = array(
                                'address'   =>  $o['address'],
                                'address_2' =>  $o['address_2'],
                                'suburb'    =>  $o['suburb'],
                                'state'     =>  $o['state'],
                                'postcode'  =>  $o['postcode'],
                                'country'   =>  $o['country']
                            );
                            $address = Utility::formatAddressWeb($ad);
                            $products = $this->controller->order->getItemsForOrder($o['id']);
                            //$products = array();
                            $num_items = 0;
                            $items = "";
                            foreach($products as $p)
                            {
                                $items .= $p['name']." - ".$p['qty']."<br/>";
                                $num_items += $p['qty'];
                            }
                            $items = rtrim($items, "<br/>");

                            ?>
                            <tr>
                                <td data-label="Date Ordered" class="number"><?php echo date("d/m/Y", $o['date_ordered']);?></td>
                                <td data-label="Date Fulfilled" class="number"><?php echo $date_fulfilled;?></td>
                                <td data-label="3PL Order Number" class="number"><?php echo $o['order_number'];?></td>
                                <td data-label="Your Order Id" class="number"><?php echo $o['client_order_id'];?></td>
                                <td data-label="Delivery To"><?php echo $o['ship_to'];;?></td>
                                <td data-label="Delivery Address"><?php echo $address;?></td>
                                <td data-label="Items" class="nowrap"><?php echo $items;?></td>
                                <td data-label="Total Items"><?php echo $num_items;?></td>
                                <td class="nowrap">
                                    <?php if($o['courier_id'] != 4):?>
                                        <p><a class="btn btn-primary btn-sm" href="/orders/order-tracking/order=<?php echo $o['id'];?>">Track Order</a></p>
                                    <?php endif;?>
                                    <p><a class="btn btn-primary btn-sm" href="/orders/order-detail/order=<?php echo $o['id'];?>">View Details</a></p>
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
                    <h2>No Orders Listed</h2>
                    <p>There are no orders listed as being submitted between <?php echo date("d/m/Y", $from);?> and <?php echo date("d/m/Y", $to);?></p>
                    <p>If you believe this is an error, please contact us to let us know</p>
                    <p>Alternatively, use the date selectors above to change the date range</p>
                </div>
            </div>
        </div>
    <?php endif;?>
</div>