<table id="manage_deliveries_table" class="table-striped table-hover" style="width:98%">
    <thead>
        <tr>
            <th data-priority="2">Deliver To</th>
            <th data-priority="10001">Delivery Reference</th>
            <th data-priority="2">Requested Date/Time<br>Delivery Window</th>
            <th>Items</th>
            <th>Status</th>
            <th data-priority="1">
                Select
                <div class="checkbox checkbox-default">
                    <input id="select_all" class="styled" type="checkbox">
                    <label for="select_all"><em><small>(all)</small></em></label>
                </div>
            </th>
            <th data-priority="1"></th>
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
                <td><?php echo ucwords($d['status']);?></td>
                <td class="chkbox">
                    <div class="checkbox checkbox-default">
                        <input <?php //if($errors) echo "disabled";?> type="checkbox" class="select styled" data-deliveryid='<?php echo $d['id'];?>' name="select_<?php echo $d['id'];?>" id="select_<?php echo $d['id'];?>" data-clientid="<?php echo $d['client_id'];?>" />
                        <label for="select_<?php echo $d['id'];?>"></label>
                    </div>
                </td>
                <td>
                    <p><a class="btn btn-sm btn-outline-fsg" href="/deliveries/manage-delivery/delivery=<?php echo $d['id'];?>">Manage</a></p>
                </td>
            </tr>
        <?php endforeach;?>
    </tbody>
</table>