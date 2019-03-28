<div id="page-wrapper">
    <input type="hidden" name="client_id" id="client_id" value="<?php echo $client_id;?>" />
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="row form-group">
        <label class="col-md-3">Select a Client</label>
        <div class="col-md-4">
            <p><select id="client_selector" class="form-control selectpicker"><option value="0">Select</option><?php echo $this->controller->client->getSelectClients($client_id);?></select></p>
        </div>
    </div>
    <?php if($client_id > 0):?>
        <?php if(count($products)):?>
            <?php //echo "<pre>",print_r($products),"</pre>";?>
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
                <div class="col-lg-12">
                    <table id="inventory_report_table" class="table-striped table-hover">
                        <thead>
                        	<tr>
                                <th>Name</th>
                                <th>SKU</th>
                                <th>Total On Hand</th>
                                <th>Currently Allocated</th>
                                <th>Under Quality Controll</th>
                                <th>Total Available</th>
                                <th>Locations</th>
                        	</tr>
                        </thead>
                        <tbody>
                            <?php foreach($products as $p):
                                $available = $p['onhand'] - $p['qc_count'] - $p['allocated'];
                                $ls = "";
                                foreach($p['locations'] as $l)
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
                                    $ls .= "<br/>";
                                }
                                $ls = rtrim($ls, "<br/>");
                                ?>
                                <tr>
                                    <td data-label="Name"><?php echo $p['name'];?></td>
                                    <td data-label="SKU"><?php echo $p['sku'];?></td>
                                    <td data-label="Total On Hand" class="number"><?php echo $p['onhand'];?></td>
                                    <td data-label="Currently Allocated" class="number"><?php echo $p['allocated'];?></td>
                                    <td data-label="Under Quality Control" class="number"><?php echo $p['qc_count'];?></td>
                                    <td data-label="Total Available" class="number"><?php echo $available;?></td>
                                    <td data-label="Locations"><?php echo $ls;?></td>
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
                        <h2>No Products Listed</h2>
                        <p></p>
                        <p>If you believe this is an error, please let Solly know</p>
                    </div>
                </div>
            </div>
        <?php endif;?>
    <?php endif;?>
</div>
<div id="block_message"></div>