<table id="view_deliveries_table" class="table-striped table-hover" style="width:90%">
    <thead>
        <tr>
            <th>Deliver To</th>
            <th data-priority="3">Delivery Reference</th>
            <th>Requested Date/Time<br>Delivery Window</th>
            <th>Items</th>
            <th data-priority="1">Progress</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($deliveries as $d):
            $address_string = "";
            if(!empty($d['address'])) $address_string .= $d['address'];
            if(!empty($d['address_2'])) $address_string .= "<br/>".$d['address_2'];
            if(!empty($d['suburb'])) $address_string .= "<br/>".$d['suburb'];
            if(!empty($d['state'])) $address_string .= "<br/>".$d['state'];
            if(!empty($d['country'])) $address_string .= "<br/>".$d['country'];
            if(!empty($d['postcode'])) $address_string .= "<br/>".$d['postcode'];
            $pc = ceil($d['stage']/$d['total_stages']*100);
            $items = explode("~",$d['items']);
            $pallet_count = 0;
            ?>
            <tr>
                <td>
                    <p class='font-weight-bold'><?php echo $d['attention'];?></p>
                    <p><?php echo $address_string;?></p>
                </td>
                <td><?php if(!empty($d['client_reference'])) echo $d['client_reference'];?></td>
                <td class="bg-<?php echo $d['delivery_window_class'];?> delivery-window">
                    <?php echo date('D d/m/Y - g:i A', $d['date_entered']);?><br>
                    <?php echo ucwords($d['delivery_window']);?>
                </td>
                <td>
                    <div class="item_list border-bottom border-secondary border-bottom-dashed mb-3 ">
                        <?php foreach($items as $i):
                            ++$pallet_count;
                            list($item_id, $item_name, $item_sku, $item_qty) = explode("|",$i);?>
                            <p><span class="iname"><?php echo $item_name."(".$item_sku.")";?>:</span> <span class="font-weight-bold">Pallet of <?php echo $item_qty;?></span></p>
                        <?php endforeach;?>
                    </div>
                    <div class="item_total text-right">
                        Total Pallets: <?php echo $pallet_count;?>
                    </div>
                </td>
                <td>
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-<?php echo $d['status_class'];?>" role="progressbar" aria-valuenow="<?php echo $pc;?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $pc;?>%"></div>
                    </div>
                    <div class="text-center mt-0"><?php echo strtoupper($d['status']);?></div>
                </td>
            </tr>
        <?php endforeach;?>
    </tbody>
</table>