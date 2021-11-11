<?php

?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <input type="hidden" name="client_id" id="client_id" value="<?php echo $client_id;?>" /> 
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
         <div class="row form-group">
            <label class="col-md-3">Select a Client</label>
            <div class="col-md-4">
                <p><select id="client_selector" class="form-control selectpicker"  data-style="btn-outline-secondary"><option value="0">Select</option><?php echo $this->controller->client->getSelectDeliveryClients($client_id);?></select></p>
            </div>
        </div>
        <?php if($client_id > 0):?>
            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/between_dates.php");?>
            <?php if(count($deliveries)):?>
                <?php echo "<pre>",print_r($deliveries),"</pre>"; //die();?>
                <div id="waiting" class="row">
                    <div class="col-lg-12 text-center">
                        <h2>Drawing Table..</h2>
                        <p>May take a few moments</p>
                        <img class='loading' src='/images/preloader.gif' alt='loading...' />
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <p class="text-right">
                            <button id="csv_download" class="btn btn-outline-success"><i class="far fa-file-alt"></i>&nbsp;Download As CSV</button>
                        </p>
                    </div>
                </div>
                <div class="row" id="table_holder" style="display:none">
                    <div class="col-12">
                        <table id="delivery_report_table" class="table-striped table-hover" style="width:98%">
                            <thead>
                            	<tr>
                                    <th data-priority="10001">Delivery Number/<br>Client Reference</th>
                                    <th data-priority="1">Date Entered</th>
                                    <th data-priority="1">Date Fulfilled</th>
                                	<th data-priority="4">Delivered To</th>
                                    <th>Items</th>
                                    <th>Urgency</th>
                                    <th data-priority="3">Vehicle</th>
                                    <th data-priority="2">Charge Level</th>
                                    <th data-priority="1">Charge</th>
                                    <th data-priority="2">GST</th>
                                    <th data-priority="1">Total Charge</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($deliveries as $d):
                                    $address_string = "<p class='text-bold'>".$d['client_name']."</p>";
                                    if(!empty($d['attention'])) $address_string .= $d['attention'];
                                    if(!empty($d['address'])) $address_string .= "<br>".$d['address'];
                                    if(!empty($d['address_2'])) $address_string .= "<br>".$d['address_2'];
                                    if(!empty($d['suburb'])) $address_string .= "<br>".$d['suburb'];
                                    if(!empty($d['state'])) $address_string .= "<br>".$d['state'];
                                    if(!empty($d['country'])) $address_string .= "<br>".$d['country'];
                                    if(!empty($d['postcode'])) $address_string .= "<br>".$d['postcode'];
                                    $items = explode("~",$d['items']);
                                    $pallet_count = 0;
                                    ?>
                                    <tr id="<?php echo $d['id'];?>">
                                        <td>
                                            <p><?php echo $d['delivery_number'];?></p>
                                            <?php if(!empty($d['client_reference'])):?>
                                                <p><?php echo $d['client_reference'];?></p>
                                            <?php endif;?>
                                        </td>
                                        <td>
                                            <?php echo date('D d/m/Y - g:i A', $d['date_entered']);?>
                                        </td>
                                        <td>
                                            <?php echo date('D d/m/Y - g:i A', $d['date_fulfilled']);?>
                                        </td>
                                        <td><?php echo $address_string;?></td>
                                        <td>
                                            <div class="item_list border-bottom border-secondary border-bottom-dashed mb-3 ">
                                                <?php foreach($items as $i):
                                                    ++$pallet_count;
                                                    list($item_id, $item_name, $item_sku, $item_qty, $location_id) = explode("|",$i);?>
                                                    <p><span class="iname"><?php echo $item_name."(".$item_sku.")";?>:</span> <span class="font-weight-bold">Pallet of <?php echo $item_qty;?></span></p>
                                                <?php endforeach;?>
                                            </div>
                                            <div class="item_total text-right">
                                                Total Pallets: <?php echo $pallet_count;?>
                                            </div>
                                        </td>
                                        <td><?php echo $d['delivery_window'];?></td>
                                        <td><?php echo ucwords($d['vehicle_type']);?></td>
                                        <td><?php echo ucwords($d['charge_level']);?></td>
                                        <td class="number"><i class="far fa-dollar-sign"></i><?php echo $d['shipping_charge'];?></td>
                                        <td class="number"><i class="far fa-dollar-sign"></i><?php echo $d['gst'];?></td>
                                        <td class="number"><i class="far fa-dollar-sign"></i><?php echo $d['total_charge'];?></td>
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
                            <h2>No Deliveries Listed</h2>
                            <p>There are no deliveries listed as being completed between <?php echo date("d/m/Y", $from);?> and <?php echo date("d/m/Y", $to);?></p>
                            <p>If you believe this is an error, please let Solly know</p>
                            <p>Alternatively, use the date selectors above to change the date range</p>
                        </div>
                    </div>
                </div>
            <?php endif;?>
        <?php endif;?>
    </div>
</div>