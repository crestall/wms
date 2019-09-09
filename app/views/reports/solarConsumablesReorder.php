<?php
$c = 0;
?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
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
                <table id="consumables_reorder_table" class="table-striped table-hover">
                    <thead>
                    	<tr>
                    	    <th></th>
                            <th>Name</th>
                            <th>SKU</th>
                            <th>Currently Available</th>
                            <th>Minimum<br/>Reorder Amount</th>
                    	</tr>
                    </thead>
                    <tbody>
                        <?php foreach($products as $p):
                            ++$c;
                            ?>
                            <tr>
                                <td class="number"><?php echo $c;?></td>
                                <td data-label="Name"><?php echo $p['name'];?></td>
                                <td data-label="SKU"><?php echo $p['sku'];?></td>
                                <td data-label="Currently Available" class="number"><?php echo $p['currently_available'];?></td>
                                <td data-label="Minimum Reorder Amount" class="number"><?php echo $p['minimum_reorder_amount'];?></td>
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
                    <h2>No Products Require Reordering</h2>
                    <p></p>
                    <p>If you believe this is an error, please let Solly know</p>
                </div>
            </div>
        </div>
    <?php endif;?>
</div>