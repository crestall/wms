<div id="page-wrapper">
    <input type="hidden" id="client_id" value="<?php echo $client_id;?>" />
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
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
                <button id="csv_download" class="btn btn-success"><i class="far fa-file-alt"></i>&nbsp;Download As CSV</button>
            </p>
        </div>
    </div>
    <div class="row" id="table_holder" style="display:none">
        <?php if(count($products)):?>
            <div class="col-md-12">
                <table class="table-striped table-hover" id="solar-inventory-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>SKU</th>
                            <th>Supplier</th>
                            <th>Owner</th>
                            <th>On Hand</th>
                            <th>Allocated</th>
                            <th>Under Quality Control</th>
                            <th>Available</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($products as $p):
                            $onhand = $this->controller->item->getStockOnHand($p['id']);
                            $allocated = $this->controller->item->getAllocatedStock($p['id'], $this->controller->order->fulfilled_id);
                            $underqc = $this->controller->item->getStockUnderQC($p['id']);
                            $available = $onhand - $allocated - $underqc;
                            $owner = ($p['solar_type_id'] > 0)?$this->controller->solarordertype->getSolarOrderType($p['solar_type_id']): "";
                            ?>
                            <tr>
                                <td data-label="Name"><?php echo $p['name'];?></td>
                                <td data-label="SKU"><?php echo $p['sku'];?></td>
                                <td data-label="Supplier"><?php echo $p['supplier'];?></td>
                                <td data-label="Owner"><?php echo $owner;?></td>
                                <td data-label="On Hand" class="number"><?php echo $onhand;?></td>
                                <td data-label="Allocated" class='number'><?php echo $allocated;?></td>
                                <td data-label="Under Quality Control" class="number"><?php echo $underqc;?></td>
                                <td data-label="Available" class="available number"><?php echo $available;?></td>
                            </tr>
                        <?php endforeach;?>
                    </tbody>

                </table>
            </div>
        <?php else:?>
            <div class="col-lg-12">
                <div class="errorbox">
                    <p>No solar products currently listed</p>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>