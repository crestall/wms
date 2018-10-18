<div id="page-wrapper">
    <input type="hidden" id="client_id" value="<?php echo $client_id;?>" />
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="row">
        <div class="col-lg-12">
            <div class="form-group">
                <div class="col-lg-2">&nbsp;</div>
                <label class="col-lg-2">Select a Client</label>
                <div class="col-lg-4">
                    <select id="client_selector" class="form-control selectpicker"><option value="0">Select</option><?php echo $this->controller->client->getSelectClients($client_id);?></select>
                </div>
            </div>
        </div>
    </div>
    <?php if($client_id > 0):?>
        <div class="row">
            <div class="col-lg-12">
                <h2>Products for <?php echo $client_name;?></h2>
            </div>
        </div>
        <div id="waiting" class="row">
            <div class="col-lg-12 text-center">
                <h2>Drawing Table..</h2>
                <p>May take a few moments</p>
                <img class='loading' src='/images/preloader.gif' alt='loading...' />
            </div>
        </div>
        <div class="row" id="table_holder" style="display:none">
            <?php if(count($products)):?>
                <div class="col-md-12">
                    <table class="table-striped table-hover" id="view_items_table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>SKU</th>
                                <th>Barcode</th>
                                <th>On Hand</th>
                                <th>Allocated</th>
                                <th>Under Quality Control</th>
                                <th>Available</th>
                                <th>Locations</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $item_id = 0;
                            $count = 1;
                            foreach($products as $p):
                                $do_row = false;
                                if($p['item_id'] == $item_id)
                                {
                                    $allocated += $p['allocated'];
                                    $on_hand += $p['qty'];
                                    $qc_count += $p['qc_count'];
                                    $location_string .= $p['location']." (".$p['qty'].")";
                                    if($p['qc_count'] > 0)
                                    {
                                        $location_string .= ", QC(".$p['qc_count'].")";
                                    }
                                    if($p['allocated'] > 0)
                                    {
                                        $location_string .= ", Allocated(".$p['allocated'].")";
                                    }
                                    $location_string .= "<br />";
                                }
                                else
                                {
                                    //Not for first one
                                    if($item_id != 0)
                                    {
                                        $total_allocated = $allocated;
                                        $total_onhand = $on_hand;
                                        $total_qc = $qc_count;
                                        $ls = rtrim($location_string,"<br />");
                                        $iid = $item_id;
                                        $iname = $name;
                                        $isku = $sku;
                                        $ibarcode = $barcode;
                                        $do_row = true;
                                    }
                                    //next item
                                    $allocated = $p['allocated'];
                                    $on_hand = $p['qty'];
                                    $qc_count = $p['qc_count'];
                                    $name = $p['name'];
                                    $sku = $p['sku'];
                                    $barcode = $p['barcode'];
                                    $location_string = "";
                                    if(!empty($p['location']))
                                    {
                                        $location_string .= $p['location']." (".$p['qty'].")";
                                        if($p['qc_count'] > 0)
                                        {
                                            $location_string .= ", QC(".$p['qc_count'].")";
                                        }
                                        if($p['allocated'] > 0)
                                        {
                                            $location_string .= ", Allocated(".$p['allocated'].")";
                                        }
                                        $location_string .= "<br />";
                                    }


                                    $item_id = $p['item_id'];
                                }
                                //draw the last one
                                if($count == count($products))
                                {
                                    $total_allocated = $allocated;
                                    $total_onhand = $on_hand;
                                    $total_qc = $qc_count;
                                    $ls = rtrim($location_string,"<br />");
                                    $iid = $item_id;
                                    $iname = $name;
                                    $isku = $sku;
                                    $ibarcode = $barcode;
                                    $do_row = true;
                                }
                                ++$count;
                                if($do_row):
                                    $available = $total_onhand - $total_allocated - $total_qc;
                                    ?>
                                    <tr>
                                        <td data-label="Name"><a href="/products/edit-product/product=<?php echo $iid;?>"><?php echo $iname;?></a></td>
                                        <td data-label="SKU"><?php echo $isku;?></td>
                                        <td data-label="Barcode" class="number"><?php echo $ibarcode;?></td>
                                        <td data-label="On Hand" class="number"><?php echo $total_onhand;?></td>
                                        <td data-label="Allocated" class='number'><?php echo $total_allocated;?></td>
                                        <td data-label="Under Quality Control" class="number"><?php echo $total_qc;?></td>
                                        <td data-label="Available" class="number"><?php echo $available;?></td>
                                        <td data-label="Locations" class="text-nowrap"><?php echo $ls;?></td>
                                        <td>
                                            <p><a class="btn btn-primary" href="/inventory/add-subtract-stock/product=<?php echo $iid;?>">Add/Subtract Stock</a></p>
                                            <p><a class="btn btn-primary" href="/inventory/move-stock/product=<?php echo $iid;?>">Move Stock</a></p>
                                            <p><a class="btn btn-primary" href="/inventory/quality-control/product=<?php echo $iid;?>">Quality Control</a>  </p>
                                        </td>
                                    </tr>
                                <?php endif;?>
                            <?php endforeach;?>
                        </tbody>

                    </table>
                </div>
            <?php else:?>
                <div class="col-lg-12">
                    <div class="errorbox">
                        <p>No products listed for <?php echo $client_name;?></p>
                    </div>
                </div>
            <?php endif;?>
        </div>
    <?php endif;?>
</div>