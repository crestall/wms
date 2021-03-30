<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <input type="hidden" id="client_id" value="<?php echo $client_id;?>" />
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="row">
            <div class="col-lg-12">
                <h2>Products for <?php echo $client_name;?></h2>
            </div>
        </div>
        <?php if(count($products)):?>
            <div id="waiting" class="row">
                <div class="col-lg-12 text-center">
                    <h2>Drawing Table..</h2>
                    <p>May take a few moments</p>
                    <img class='loading' src='/images/preloader.gif' alt='loading...' />
                </div>
            </div>
            <div class="row" id="table_holder" style="display:none">
                <div class="col-md-12">
                    <table class="table-striped table-hover" id="client_inventory_table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>SKU</th>
                                <th>Details</th>
                                <th>On Hand</th>
                                <th>Allocated</th>
                                <th>Under Quality Control</th>
                                <th>Available</th>
                                <th>Total Bay Usage</th>
                                <th>Warning Level</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($products as $itemid => $p):
                                //$onhand = $this->controller->item->getStockOnHand($p['id']);
                                //$allocated = $this->controller->item->getAllocatedStock($p['id'], $this->controller->order->fulfilled_id);
                                //$underqc = $this->controller->item->getStockUnderQC($p['id']);
                                $available = $p['onhand'] - $p['allocated'] - $p['qc_count'];
                                $image = (!empty($p['image']))? "<img src='/images/products/tn_{$p['image']}' alt='product_image' class='thumbnail' /><br/>":"";
                                $full_bays = $this->controller->item->getBayUsage($itemid);
                                $trays = $this->controller->item->getTrayUsage($itemid);
                                $location_string = ($full_bays > 0)? $full_bays." Full Pallet Bays<br/>" : "";
                                $location_string .= ($trays > 0)? $trays." Tray Spaces (9 per pallet bay)" : "";
                                $location_string = rtrim($location_string, "<br/>");
                                $details = "";
                                if(!empty($p['width'])) $details .= "Width: ".$p['width']."cm<br/>";
                                if(!empty($p['depth'])) $details .= "Depth: ".$p['depth']."cm<br/>";
                                if(!empty($p['height'])) $details .= "Height: ".$p['height']."cm<br/>";
                                if(!empty($p['weight'])) $details .= "Weight: ".$p['weight']."kg";
                                ?>
                                <tr>
                                    <td data-label="Name"><?php echo $image.$p['name'];?></td>
                                    <td data-label="SKU"><?php echo $p['sku'];?></td>
                                    <td data-label="Details"><?php echo $details;?></td>
                                    <td data-label="On Hand" class="number"><?php echo $p['onhand'];?></td>
                                    <td data-label="Allocated" class='number'><?php echo $p['allocated'];?></td>
                                    <td data-label="Under Quality Control" class="number"><?php echo $p['qc_count'];?></td>
                                    <td data-label="Available" class="available number"><?php echo $available;?></td>
                                    <td data-label="Total Bay Usage" class="text-nowrap"><?php echo $location_string;?></td>
                                    <td>
                                        <p><input type="text" class="form-control number ml-auto" id="lowstock_<?php echo $itemid;?>" name="lowstock_<?php echo $itemid;?>" value="<?php echo $p['low_stock_warning'];?>" style="max-width: 80px" /></p>
                    					<p class="text-right"><button class="btn btn-outline-secondary btn-sm update_product" data-productid="<?php echo $itemid;?>">Update</button> </p>
                                        <div class="errorbox" style="display:none;" id="error_<?php echo $itemid;?>">Only input whole, positive numbers please</div>
                                    	<div class="feedbackbox" style="display:none;" id="feedback_<?php echo $itemid;?>">Product warning level updated</div>
                                    </td>
                                </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else:?>
            <div class="col-lg-12">
                <div class="errorbox">
                    <p>No products listed for <?php echo $client_name;?></p>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>