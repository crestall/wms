<?php
$si_string = "";
foreach($order_items as $oi)
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
                            <h2>No Order ID Supplied</h2>
                            <p>No order was supplied to update</p>
                            <p><a href="/orders/view-orders">Please click here to view all orders to choose from</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php elseif(!$order || !count($order)):?>
        <div class="row">
            <div class="col-lg-12">
                <div class="errorbox">
                    <div class="row">
                        <div class="col-lg-2" style="font-size:96px">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="col-lg-6">
                            <h2>No Order Found</h2>
                            <p>No order was found with that ID</p>
                            <p><a href="/orders/view-orders">Please click here to view all orders to choose from</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else:?>
        <div class="row">
            <div class="col-lg-4">
                <a class="btn btn-primary" href="/orders/order-update/order=<?php echo $order_id;?>">Return to Order</a>
            </div>
            <div class="col-lg-4">
                <a class="btn btn-primary" href="/orders/view-orders/client=<?php echo $order['client_id'];?>">View Orders For Client</a>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <h2>Updating Items For Order Number <?php echo $order['order_number'];?></h2>
            </div>
        </div>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <?php echo Form::displayError('general');?>
        <div class="row">
            <form id="items-update" method="post" action="/form/procItemsUpdate">
                <div id="item_selector" class="form-group row">
                    <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Items</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="item_searcher" id="item_searcher" />
                    </div>
                </div>
                <div class="row">
                    <div class="bs-callout bs-callout-primary bs-callout-more col-md-12">
                        <div id="the_items">
                            <?php foreach($order_items as $oi):?>
                                <div class="row form-group">
                                    <div class="item_holder">
                                        <label class="col-md-3 col-form-label"><?php echo $oi['name'];?></label>
                                        <div class='col-md-4'>
                                            <input type="text" class="required number form-control item-group count" name="items[<?php echo $oi['id'];?>][qty]" id="item_<?php echo $oi['id'];?>" data-itemid="<?php echo $oi['id'];?>" value="<?php echo $oi['qty'];?>" />
                                        </div>
                                        <div class='col-md-1 delete-image-holder'>
                                            <!--img class='delete' data-itemid='<?php echo $oi['id'];?>' src='/images/delete.png' title='Remove this item' /-->
                                            <a class="delete" data-itemid="<?php echo $oi['id'];?>" title="remove this item"><i class="fas fa-backspace fa-2x text-danger"></i></a>
                                        </div>
                                        <?php if($oi['palletized'] > 0):?>
                                            <input type='hidden' class='pallet_size' name='pallet_size_<?php echo $oi['id'];?>' id='pallet_size_<?php echo $oi['id'];?>' value='<?php echo $oi['per_pallet'];?>' />
                                            <div class='form-check col-md-2'>
                                                <div class='checkbox checkbox-default'>
                                                    <input class='pallets form-check-input styled' name='pallet_<?php echo $oi['id'];?>' id='pallet_<?php echo $oi['id'];?>' type='checkbox' checked /><label for='pallet_<?php echo $oi['id'];?>' class='form-check-label'><em><small>Whole Pallets(<?php echo $oi['per_pallet'];?>)</small></em></label>
                                                </div>
                                            </div>
                                        <?php endif;?>
                                    </div>
                                </div>
                            <?php endforeach;?>
                        </div>
                    </div>
                    <?php echo Form::displayError('items');?>
                </div>
                <input type="hidden" name="order_id" value="<?php echo $order['id'];?>" />
                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                <input type="hidden" name="selected_items" id="selected_items" value="<?php echo $si_string;?>" />
                <input type="hidden" name="client_id" id="client_id" value="<?php echo $order['client_id'];?>" />
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