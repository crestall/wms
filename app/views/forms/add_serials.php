<?php
echo "<pre>",print_r($items),"</pre>";
?>
<?php foreach($items as $item): ?>
    <div class="form-group row">
        <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> <?php echo $item['name']."(".$item['sku'].")";?></label>
        <div class="col-md-3"><input type="text" name="serial[<?php echo $item;?>][line_id]" class="form-control required" placeholder="Serial Number" /></div>
    </div>
<?php endforeach;?>
