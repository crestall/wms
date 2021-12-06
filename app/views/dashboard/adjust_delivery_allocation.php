<?php
$items = explode("~",$dd['items']);
?>
<div class="page-wrapper">
    <div class="row" id="feedback_holder" style="display:none"></div>
    <div class="row">
        <form id="adjust-allocation" method="post" action="/form/procAdjustAllocations">
            <?php foreach($items as $i): 
                list($item_id, $item_name, $item_sku, $item_qty, $location_id) = explode("|",$i);?>
                <p>Need a location for <?php echo $item_name."( ".$item_id." )";?> with a quantity of <?php echo $item_qty;?></p>
            <?php endforeach;?>
        </form>
    </div>
</div>