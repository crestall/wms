<?php if(count($items)):?>
    <?php foreach($items as $i):?>
        <div class="form-group row">
            <div class="form-check">
                <label class="form-check-label col-md-3" for="return_items_<?php echo $i['id'];?>"><?php echo "{$i['name']} ({$i['qty']})";?></label>
                <div class="col-md-4 checkbox checkbox-default">
                    <input class="form-check-input styled return_items" type="checkbox" id="return_items_<?php echo $i['id'];?>" name="item_returns[<?php echo $i['item_id'];?>][id]" />
                    <label for="return_items_<?php echo $i['id'];?>"></label>
                </div>
                <input type='hidden' name='item_returns[<?php echo $i['item_id'];?>][qty]' value='<?php echo $i['qty'];?>' />
            </div>
        </div>
    <?php endforeach;?>
    <input type='hidden' name='order_id' value='<?php echo $items[0]['order_id'];?>' /> 
<?php else:?>
    <div class="row">
        <div class="col-md-12">
            <div class="errorbox">
                <h2><i class='far fa-times-circle'></i>Items Not Found For order</h2>
                <p>Either that consignmnet id does not belong to that client, or the order has not been dispatched as yet</p>
                <p>Please clear the order and retry</p>
            </div>
        </div>
    </div>
<?php endif;?>