<table id="manage_deliveries_table" class="table-striped table-hover" style="width:98%">
    <thead>
        <tr>
            <th data-priority="10001">Delivery Number</th>
            <th data-priority="2">Deliver To</th>
            <th data-priority="10001">Delivery Reference</th>
            <th data-priority="2">Requested Date/Time<br>Delivery Window</th>
            <th>Items</th>
            <th>Status</th>
            <th data-priority="3">Vehicle Type</th>
            <!--th data-priority="1">
                Select
                <div class="checkbox checkbox-default">
                    <input id="select_all" class="styled" type="checkbox">
                    <label for="select_all"><em><small>(all)</small></em></label>
                </div>
            </th-->
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
                <td><?php echo $d['delivery_number'];?></td>
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
                            list($item_id, $item_name, $item_sku, $item_qty, $location_id, $line_id) = explode("|",$i);?>
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
                <td>
                    <select name="vehicle_type" class="selectpicker vehicle_type" data-deliveryid='<?php echo $d['id'];?>' data-style="btn-outline-secondary btn-sm" data-width="fit" id="vehicletype_<?php echo $d['id'];?>"><option value="0">--Select One--</option><?php echo Utility::getVehicleTypeSelect($d['vehicle_type']);?></select>
                </td>
                <!--td class="chkbox">
                    <div class="checkbox checkbox-default">
                        <input <?php //if($errors) echo "disabled";?> type="checkbox" class="select styled" data-deliveryid='<?php echo $d['id'];?>' name="select_<?php echo $d['id'];?>" id="select_<?php echo $d['id'];?>" data-clientid="<?php echo $d['client_id'];?>" />
                        <label for="select_<?php echo $d['id'];?>"></label>
                    </div>
                </td-->
                <td class="middle">
                    <a class="btn btn-block btn-outline-secondary print_slip" role="button" target="_blank" href="/pdf/printDeliveryPickslip/delivery=<?php echo $d['id'];?>">Print Pickslip</a>
                    <a class="btn btn-block btn-outline-secondary print_docket" id="print_docket_<?php echo $d['id'];?>" role="button" target="_blank" href="/pdf/printDeliveryDocket/delivery=<?php echo $d['id'];?>/vehicle=<?php echo $d['vehicle_type'];?>">Print Delivery Docket</a>
                    <button class="btn btn-block btn-outline-primary adjust_allocation my-2" data-deliveryid="<?php echo $d['id'];?>">Adjust Allocations</button>
                    <div class="border-bottom border-secondary border-bottom-dashed my-2"></div>
                    <button data-deliveryid="<?php echo $d['id'];?>" class="btn btn-block btn-outline-danger delivery_deletion">Delete This Delivery</button>
                    <div class="border-bottom border-secondary border-bottom-dashed my-2"></div>
                    <button <?php if($d['status_id'] < $this->controller->delivery->vehicleassigned_id) echo "disabled";?> id="delivery_completed_<?php echo $d['id'];?>" class="btn btn-block btn-outline-success delivery_completed" data-deliveryid="<?php echo $d['id'];?>" data-clientid="<?php echo $d['client_id'];?>">Mark As Complete</button>
                </td>
            </tr>
        <?php endforeach;?>
    </tbody>
</table>