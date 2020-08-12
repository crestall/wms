<?php
$si_string = "";
foreach($order_items as $oi)
{
    $si_string .= $oi['id'].",";
}
$si_string = rtrim($si_string, ",");
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php if($error):?>
            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/no_order_id.php");?>
        <?php elseif(!$order || !count($order)):?>
            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/no_order_found.php");?>
        <?php else:?>
            <div class="row">
                <div class="col">
                    <a class="btn btn-outline-secondary" href="/orders/order-update/order=<?php echo $order_id;?>">Return to Order</a>
                </div>
                <div class="col text-right">
                    <a class="btn btn-outline-secondary" href="/orders/view-orders/client=<?php echo $order['client_id'];?>">View Orders For Client</a>
                </div>
            </div>
            <div class="row">
                <div class="col-12 mb-3 mt-3">
                    <h2>Updating Items For Order Number <?php echo $order['order_number'];?></h2>
                </div>
            </div>
            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
            <?php echo Form::displayError('general');?>
            <div class="col-12">
                <form id="items-update" method="post" action="/form/procItemsUpdate">
                    <div id="item_selector" class="p-3 pb-0 mb-2 rounded-top mid-grey">
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
                                <?php foreach($order_items as $i => $item):
                                    $qty = !(( ($item['qty'] == $item['location_qty']) && $item['palletized'] > 0))? $item['qty']: "";
                                    $pallet_qty = ( ($item['qty'] == $item['location_qty']) && $item['palletized'] > 0)? $item['qty']: "";?>
                                    <div class="row item_holder">
                                        <div class='col-md-1 delete-image-holder'>
                                            <a class='delete' title='remove this item'><i class='fad fa-times-square text-danger'></i><span class="inst">Remove</span></a>
                                        </div>
                                        <div class="col-md-6">
                                            <p><input type="text" class="form-control item-searcher" name="items[<?php echo $i;?>][name]" placeholder="Item Name" value="<?php echo $item['name'];?>" /></p>
                                        </div>
                                        <div class="col-md-2 qty-holder">
                                            <?php if($this->controller->item->isPalletItem($item['id'])):
                                                $select_values = $this->controller->item->getPalletCountSelect($item['id']);?>
                                                <select class='form-control selectpicker pallet_qty' data-style='btn-outline-secondary' name='items[<?php echo $i;?>]][qty]'><option value='0'>Quantity</option>
                                                <?php foreach($select_values as $sv):
                                                    if($sv['available'] == 0) continue;;?>
                                                    <option <?php if($sv['available'] == $pallet_qty) echo "selected";?>><?php echo $sv['available'];?></option>
                                                <?php endforeach;?>
                                                </select>
                                            <?php else:?>
                                                <input type='text' class='form-control number item_qty' name='items[<?php echo $i;?>][qty]' placeholder='Qty' value="<?php echo $qty;?>"  />
                                            <?php endif;?>
                                        </div>
                                        <div class="col-md-3 qty-location"></div>
                                        <input type="hidden" name="items[<?php echo $i;?>][id]" class="item_id" value="<?php echo $item['id'];?>" />
                                    </div>
                                <?php endforeach;?>
                            <?php endif;?>
                        </div>
                    </div>
                    <input type="hidden" name="order_id" value="<?php echo $order['id'];?>" />
                    <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                    <input type="hidden" name="client_id" id="client_id" value="<?php echo $order['client_id'];?>" />
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">&nbsp;</label>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-outline-fsg">Update Items</button>
                        </div>
                    </div>
                </form>
            </div>
        <?php endif;?>
    </div>
</div>