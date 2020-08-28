<?php if(count($items)):?>
    <div class="form-group row">
        <div class="col-md-9 offset-md-2">
            <h2>Items Returned For Order Number <?php echo $order['order_number'];?></h2>
        </div>
    </div>
    <?php foreach($items as $i):?>
        <div class="form-group row">
            <div class="col-md-6">
                <?php echo "{$i['name']}";?>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control number" name="item_returns[<?php echo $i['item_id'];?>][qty]" number data-rule-max="<?php echo $i['qty'];?>" value="<?php echo $i['qty'];?>" />
            </div>
        </div>
    <?php endforeach;?>
    <input type='hidden' name='order_id' value='<?php echo $items[0]['order_id'];?>' /> 
<?php else:?>
    <div class="row">
        <div class="col-md-12">
            <div class="errorbox">
                <h2><i class='far fa-times-circle'></i>Items Not Found For order</h2>
                <p>Either that consignment id does not belong to that client, or the order has not been dispatched as yet</p>
                <p>Please clear the order and retry</p>
            </div>
        </div>
    </div>
<?php endif;?>