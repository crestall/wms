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
                <p></p>
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
                                <th>Locations</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach($products as $item_id => $details):
                                $available = $details['onhand'] - $details['allocated'] - $details['qc_count'];
                                $ls = "";
                                foreach($details['locations'] as $lid => $l)
                                {
                                    $atm = $l['onhand'] - $l['allocated'] - $l['qc_count'];
                                    $ls .= $l['name']." ( can move ".$atm.")&nbsp;<input type='text' name='number_from_'".$lid." id='number_from_'".$lid." value='".$atm."' class='number' />";
                                    $ls .= "<br/>";
                                }
                                $ls = rtrim($ls, "<br/>");
                                ?>
                                <tr>
                                    <td data-label="Name"><a href="/products/edit-product/product=<?php echo $item_id;?>"><?php echo $details['name'];?></a></td>
                                    <td data-label="SKU"><?php echo $details['sku'];?></td>
                                    <td data-label="Locations" class="text-nowrap">
                                        <?php echo $ls;?>
                                    </td>
                                    <td>
                                        <?php if($details['pack_item'] > 0):?>
                                            <p><a class="btn btn-primary" href="/inventory/pack-items-manage/product=<?php echo $item_id;?>">Manage Pack Item</a></p>
                                        <?php else: ?>
                                            <p><a class="btn btn-primary" href="/inventory/add-subtract-stock/product=<?php echo $item_id;?>">Add/Subtract Stock</a></p>
                                        <?php endif;?>

                                        <p><a class="btn btn-primary" href="/inventory/move-stock/product=<?php echo $item_id;?>">Move Stock</a></p>
                                        <p><a class="btn btn-primary" href="/inventory/quality-control/product=<?php echo $item_id;?>">Quality Control</a>  </p>
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