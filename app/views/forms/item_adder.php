<div id="item_selector" class="form-group row">
                <div class="bs-callout bs-callout-primary bs-callout-more col-md-11">
                    <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Line Items</label>
                    <div class="col-md-9" id="items_holder">
                        <?php if(is_array(Form::value('items'))):?>
                            <?php foreach(Form::value('items') as $i => $item):
                                $qty = (isset($item['qty']))? $item['qty']: "";
                                $pallet_qty = (isset($item['pallet_qty']))? $item['pallet_qty']: "";?>
                                <div class="row item_holder">
                                    <?php if($i == 0):?>
                                        <div class="col-sm-1 add-image-holder">
                                            <a class="add" style="cursor:pointer" title="Add Another Item">
                                                <i class="fas fa-plus-circle fa-2x text-success"></i>
                                            </a>
                                        </div>
                                    <?php else:?>
                                        <div class="col-sm-1 delete-image-holder">
                                            <a class="delete" style="cursor:pointer" title="Remove This Item">
                                                <i class="fas fa-times-circle fa-2x text-danger"></i>
                                            </a>
                                        </div>
                                    <?php endif;?>
                                    <div class="col-sm-4">
                                        <p><input type="text" class="form-control item-searcher" name="items[<?php echo $i;?>][name]" placeholder="Item Name" value="<?php echo $item['name'];?>" /></p>
                                    </div>
                                    <div class="col-sm-4 qty-holder">
                                    <?php if($this->controller->item->isPalletItem($item['id'])):
                                        $select_values = $this->controller->item->getPalletCountSelect($item['id']);
                                        //var_dump($select_values);?>
                                        <div class='col-sm-4'><input type='text' class='form-control number item_qty' name='items[<?php echo $i;?>][qty]' placeholder='Qty' value="<?php echo $qty;?>" /></div>
                                        <div class='col-sm-8'><select class='form-control selectpicker pallet_qty' name='items[<?php echo $i;?>][pallet_qty]'><option value='0'>Whole Pallet Qty</option>
                                        <?php foreach($select_values as $sv):
                                            if($sv['available'] == 0) continue;;?>
                                            <option <?php if($sv['available'] == $pallet_qty) echo "selected";?>><?php echo $sv['available'];?></option>
                                        <?php endforeach;?>
                                        </select>
                                        </div>
                                    <?php else:?>
                                        <input type='text' class='form-control number item_qty' name='items[<?php echo $i;?>][qty]' placeholder='Qty' value="<?php echo $qty;?>"  />
                                    <?php endif;?>
                                    </div>
                                    <div class="col-sm-5 qty-location"></div>
                                    <input type="hidden" name="items[<?php echo $i;?>][id]" class="item_id" value="<?php echo $item['id'];?>" />
                                </div>
                            <?php endforeach;?>
                        <?php else:?>
                            <div class="row item_holder">
                                <div class="col-sm-1 add-image-holder">
                                    <a class="add" style="cursor:pointer" title="Add Another Item">
                                        <i class="fas fa-plus-circle fa-2x text-success"></i>
                                    </a>
                                </div>
                                <div class="col-sm-4">
                                    <p><input type="text" class="form-control item-searcher" name="items[0][name]" placeholder="Item Name" /></p>
                                </div>
                                <div class="col-sm-4 qty-holder">

                                </div>
                                <div class="col-sm-3 qty-location"></div>
                                <input type="hidden" name="items[0][id]" class="item_id"  />
                            </div>
                        <?php endif;?>
                    </div>
                    <?php echo Form::displayError('items');?>
                </div>
            </div>