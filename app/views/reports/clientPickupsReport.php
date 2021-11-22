<?php

?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <input type="hidden" name="client_id" id="client_id" value="<?php echo $client_id;?>" />
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php if($client_id > 0):?>
            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/between_dates.php");?>
            <?php if(count($pickups)):?>
                <?php //echo "<pre>",print_r($pickups),"</pre>"; //die();?>
                <div id="waiting" class="row">
                    <div class="col-lg-12 text-center">
                        <h2>Drawing Table..</h2>
                        <p>May take a few moments</p>
                        <img class='loading' src='/images/preloader.gif' alt='loading...' />
                    </div>
                </div>
                <div id="table_holder" style="display:none">
                    <div class="row mb-3">
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                            <input type="search" class="form-control" id="table_searcher" placeholder="Search Table" />
                        </div>
                        <div class="col-xl-9 col-lg-8 col-md-6 col-sm-6 text-right">
                            <button id="csv_download" class="btn btn-outline-success"><i class="far fa-file-alt"></i>&nbsp;Download As CSV</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <table id="pickup_report_table" class="table-striped table-hover" style="width:98%">
                                <thead>
                                	<tr>
                                        <th data-priority="10001">Delivery Number/<br>Booked By</th>
                                        <th data-priority="1">Date Requested</th>
                                        <th data-priority="1">Date Completed</th>
                                    	<th data-priority="4">Pickup Address</th>
                                        <th>Items</th>
                                        <th>Urgency</th>
                                        <th data-priority="3">Vehicle</th>
                                        <th data-priority="2">Charge Level</th>
                                        <th data-priority="1">Pickup Charge</th>
                                        <th data-priority="1">Repallatize Charge</th>
                                        <th data-priority="1">Rewrap Charge</th>
                                        <th data-priority="2">GST</th>
                                        <th data-priority="1">Total Charge</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($pickups as $d):
                                        $address_string = "<p class='text-bold'>".$d['client_name']."</p>";
                                        if(!empty($d['address'])) $address_string .= "<br>".$d['address'];
                                        if(!empty($d['address_2'])) $address_string .= "<br>".$d['address_2'];
                                        if(!empty($d['suburb'])) $address_string .= "<br>".$d['suburb'];
                                        if(!empty($d['state'])) $address_string .= "<br>".$d['state'];
                                        if(!empty($d['country'])) $address_string .= "<br>".$d['country'];
                                        if(!empty($d['postcode'])) $address_string .= "<br>".$d['postcode'];
                                        $items = explode("~",$d['items']);
                                        ?>
                                        <tr id="<?php echo $d['id'];?>">
                                            <td>
                                                <p>Pickup Number:<?php echo $d['pickup_number'];?></p>
                                                <p>Requested By:<?php echo $d['requested_by_name'];?></p>
                                            </td>
                                            <td>
                                                <?php echo date('D d/m/Y - g:i A', $d['date_entered']);?>
                                            </td>
                                            <td>
                                                <?php echo date('D d/m/Y - g:i A', $d['date_completed']);?>
                                            </td>
                                            <td><?php echo $address_string;?></td>
                                            <td>
                                                <div class="item_list border-bottom border-secondary border-bottom-dashed mb-3 ">
                                                    <?php foreach($items as $i):
                                                        list($item_id, $item_name, $item_sku, $pallet_count) = explode("|",$i);?>
                                                        <p><span class="iname"><?php echo $item_name."(".$item_sku.")";?>:</span> <span class="font-weight-bold"><?php echo $pallet_count;?> Pallet(s)</span></p>
                                                    <?php endforeach;?>
                                                </div>
                                            </td>
                                            <td><?php echo $d['pickup_window'];?></td>
                                            <td><?php echo ucwords($d['vehicle_type']);?></td>
                                            <td><?php echo ucwords($d['charge_level']);?></td>
                                            <td class="number"><i class="far fa-dollar-sign"></i><?php echo number_format($d['shipping_charge'], 2, '.', ','); ?></td>
                                            <td class="number"><i class="far fa-dollar-sign"></i><?php echo number_format($d['repalletize_charge'], 2, '.', ','); ?></td>
                                            <td class="number"><i class="far fa-dollar-sign"></i><?php echo number_format($d['rewrap_charge'], 2, '.', ','); ?></td>
                                            <td class="number"><i class="far fa-dollar-sign"></i><?php echo number_format($d['gst'], 2, '.', ','); ?></td>
                                            <td class="number"><i class="far fa-dollar-sign"></i><?php echo number_format($d['total_charge'], 2, '.', ','); ?></td>
                                        </tr>
                                    <?php endforeach;?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php else:?>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="errorbox">
                            <h2>No Pickups Listed</h2>
                            <p>There are no pickups listed as being completed between <?php echo date("d/m/Y", $from);?> and <?php echo date("d/m/Y", $to);?> for <?php echo $client_name;?></p>
                            <p>If you believe this is an error, please let Solly know</p>
                            <p>Alternatively, use the date selectors above to change the date range or choose another client</p>
                        </div>
                    </div>
                </div>
            <?php endif;?>
        <?php endif;?>
    </div>
</div>