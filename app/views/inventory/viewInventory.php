<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <input type="hidden" id="client_id" value="<?php echo $client_id;?>" />
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="row mb-3">
            <label class="col-md-3">Select a Client</label>
            <div class="col-md-4">
                <select id="client_selector" class="form-control selectpicker" data-style="btn-outline-secondary" data-live-search="true"><option value="0">Select</option><?php echo $this->controller->client->getSelectClients($client_id);?></select>
            </div>
        </div>
        <?php if($client_id > 0):?>
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
                        <table class="table-striped table-hover" id="view_items_table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>SKU</th>
                                    <th>Barcode</th>
                                    <th>Client Product ID</th>
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
                                foreach($products as $item_id => $details):
                                    $available = $details['onhand'] - $details['allocated'] - $details['qc_count'];
                                    $ls = "";
                                    foreach($details['locations'] as $l)
                                    {
                                        $ls .= $l['name']." (".$l['onhand'].")";
                                        if($l['allocated'] > 0)
                                        {
                                            $ls .= " Allocated (".$l['allocated'].")";
                                        }
                                        if($l['qc_count'] > 0)
                                        {
                                            $ls .= " Under Quality Control (".$l['qc_count'].")";
                                        }
                                        if($l['oversize'] == 1)
                                        {
                                            $ls .= " Oversize";
                                        }
                                        $ls .= "<br/>";
                                    }
                                    $ls = rtrim($ls, "<br/>");
                                    $image = "";
                                    if(preg_match('/https?/i', $details['image']))
                                    {
                                        $image = "<br><img src='".$details['image']."' class='img-thumbnail img-fluid'>";
                                    }
                                    elseif(!empty($p['image']))
                                    {
                                        $image = "<br><img src='/images/products/tn_".$details['image']."' class='img-fluid img-thumbnail'>";
                                    }
                                    ?>
                                    <tr>
                                        <td data-label="Name">
                                            <a href="/products/edit-product/product=<?php echo $item_id;?>"><?php echo $details['name'];?></a><?php echo $image;?>
                                        </td>
                                        <td data-label="SKU"><?php echo $details['sku'];?></td>
                                        <td data-label="Barcode" class="number"><?php echo $details['barcode'];?></td>
                                        <td data-label="Client product ID" class="number"><?php echo $details['client_product_id'];?></td>
                                        <td data-label="On Hand" class="number"><?php echo $details['onhand'];?></td>
                                        <td data-label="Allocated" class='number'><?php echo $details['allocated'];?></td>
                                        <td data-label="Under Quality Control" class="number"><?php echo $details['qc_count'];?></td>
                                        <td data-label="Available" class="number"><?php echo $available;?></td>
                                        <td data-label="Locations" class="text-nowrap"><?php echo $ls;?></td>
                                        <td>
                                            <p><a class="btn btn-outline-secondary" href="/inventory/add-subtract-stock/product=<?php echo $item_id;?>">Add/Subtract Stock</a></p>
                                            <p><a class="btn btn-outline-secondary" href="/inventory/move-stock/product=<?php echo $item_id;?>">Move Stock</a></p>
                                            <p><a class="btn btn-outline-secondary" href="/inventory/quality-control/product=<?php echo $item_id;?>">Quality Control</a>  </p>
                                        </td>
                                    </tr>
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
</div>