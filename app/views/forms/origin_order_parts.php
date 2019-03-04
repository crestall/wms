<?php
$i = 0;
?>

<div id="item_selector" class="form-group row">
    <div class="col-md-9" id="items_holder">
        <?php foreach($parts as $name => $details):
            $part_name = ucwords(str_replace("_", " ", $name));
            ?>
            <div class="row item_holder">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> <?php echo $part_name;?></label>
                <div class="col-md-1"><input type="text" name="items[<?php echo $i;?>][qty]" class="form-control required number" value="<?php echo $details['qty'];?>" /></div>
                <input type="hidden" name="items[<?php echo $i;?>][id]" value="<?php echo $details['id'];?>" />
            </div>
        <?php ++$i; endforeach;?>
        <div class="row item_holder">
            <div class="col-sm-1 add-image-holder">
                <a class="add" style="cursor:pointer" title="Add Another Item">
                    <i class="fas fa-plus-circle fa-2x text-success"></i>
                </a>
            </div>
            <div class="col-sm-4">
                <p><input type="text" class="form-control item-searcher" name="items[<?php echo $i;?>][name]" placeholder="Item Name" /></p>
            </div>
            <div class="col-sm-4 qty-holder">

            </div>
            <div class="col-sm-3 qty-location"></div>
            <input type="hidden" name="items[<?php echo $i;?>][id]" class="item_id"  />
        </div>
    </div>
</div>