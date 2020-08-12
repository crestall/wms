<div id="item_selector" class="p-3 pb-0 mb-2 rounded-top mid-grey" style="display:<?php echo $idisp;?>">
    <div class="row mb-0">
        <div class="col-md-4">
            <h4>Line Items</h4>
        </div>
        <div class="col-md-4">
            <a class="add-item" style="cursor:pointer" title="Add Another Item"><h4><i class="fad fa-plus-square text-success"></i> Add another</a></h4>
        </div>
        <div class="col-md-4">
            <a id="remove-all-items" style="cursor:pointer" title="Remove All Items"><h4><i class="fad fa-times-square text-danger"></i> Remove all</a></h4>
        </div>
    </div>
    <div id="items_holder" class="p-3 light-grey">
        <?php if(Form::$num_errors > 0 && is_array(Form::value('items'))):
            //echo "<pre>",print_r(Form::value('items')),"</pre>";die();
            echo Form::displayError('items');
            foreach(Form::value('items') as $ind => $ita):?>
                <div class="row item_holder">
                    <div class='col-md-1 delete-image-holder'>
                        <a class='delete' title='remove this item'><i class='fad fa-times-square text-danger'></i><span class="inst">Remove</span></a>
                    </div>
                    <div class="col-md-6">
                        <p><input type="text" class="form-control item-searcher" name="items[<?php echo $ind;?>][name]" placeholder="Item Name" value="<?php echo $ita['name'];?>" /></p>
                    </div>
                    <div class="col-md-2 qty-holder">
                        <?php if(isset($ita['whole_pallet']) && $ita['whole_pallet']):?>
                            <input type='hidden' name='items[<?php echo $ind;?>][whole_pallet]' value='1' />
                            <select class='form-control selectpicker pallet_qty' data-style='btn-outline-secondary' name='items[<?php echo $ind;?>][qty]'>
                                <option value='0'>Quantity</option>
                                <?php echo $this->controller->item->getSelectLocationAvailableCounts($ita['id'], $ita['qty']);?>
                            </select>
                        <?php else:?>
                            <input type='text' class='form-control number item_qty' name='items[<?php echo $ind;?>][qty]' placeholder='Qty' value="<?php echo $ita['qty'];?>" />
                        <?php endif;?>
                    </div>
                    <div class="col-md-3 qty-location"></div>
                    <input type="hidden" name="items[<?php echo $ind;?>][id]" class="item_id" value="<?php echo $ita['id'];?>"  />
                </div>
            <?php endforeach;?>
        <?php else:?>
            <div class="row item_holder">
                <div class='col-md-1 delete-image-holder'>
                    <a class='delete' title='remove this item' style="display:none;"><i class='fad fa-times-square text-danger'></i><span class="inst">Remove</span></a>
                </div>
                <div class="col-md-6">
                    <p><input type="text" class="form-control item-searcher" name="items[0][name]" placeholder="Item Name" /></p>
                </div>
                <div class="col-md-2 qty-holder">

                </div>
                <div class="col-md-3 qty-location"></div>
                <input type="hidden" name="items[0][id]" class="item_id"  />
            </div>
        <?php endif;?>
    </div>
</div>