<?php
$si_string = "";
foreach($job_items as $oi)
{
    $si_string .= $oi['id'].",";
}
$si_string = rtrim($si_string, ",");
?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php if($error):?>
        <div class="row">
            <div class="col-lg-12">
                <div class="errorbox">
                    <div class="row">
                        <div class="col-lg-2" style="font-size:96px">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="col-lg-6">
                            <h2>No Job ID Supplied</h2>
                            <p>No job was supplied to update</p>
                            <p><a href="/solar-jobs/view-installs">Please click here to view all installs to choose from</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php elseif(!$job || !count($job)):?>
        <div class="row">
            <div class="col-lg-12">
                <div class="errorbox">
                    <div class="row">
                        <div class="col-lg-2" style="font-size:96px">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="col-lg-6">
                            <h2>No Job Found</h2>
                            <p>No job was found with that ID</p>
                            <p><a href="/solar-jobs/view-installs">Please click here to view all installs to choose from</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else:?>
        <div class="row">
            <div class="col-lg-4">
                <a class="btn btn-primary" href="/solar-jobs/edit-install/type=<?php echo $job['type_id'];?>/id=<?php echo $job_id;?>">Return to Job</a>
            </div>
            <div class="col-lg-4">
                <a class="btn btn-primary" href="/solar-jobs/view-installs/type=<?php echo $job['type_id'];?>">View All Install of this type</a>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <h2>Updating Items For Job Number <?php echo $job['work_order'];?></h2>
            </div>
        </div>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <?php echo Form::displayError('general');?>
        <div class="row">
            <form id="items-update" method="post" action="/form/procSolarItemsUpdate">


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
                <?php foreach($job_items as $i => $item):
                    $qty = !(( ($item['qty'] == $item['location_qty']) && $item['palletized'] > 0))? $item['qty']: "";
                    $pallet_qty = ( ($item['qty'] == $item['location_qty']) && $item['palletized'] > 0)? $item['qty']: "";?>
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
            <?php endif;?>
        </div>
        <?php echo Form::displayError('items');?>
    </div>
</div>






                <input type="hidden" name="order_id" value="<?php echo $job['id'];?>" />
                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                <input type="hidden" name="client_id" id="client_id" value="<?php echo $job['client_id'];?>" />
                <input type="hidden" name="type_id" id="type_id" value="<?php echo $job['type_id'];?>" />
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">&nbsp;</label>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">Update Items</button>
                    </div>
                </div>
            </form>
        </div>
    <?php endif;?>
</div>