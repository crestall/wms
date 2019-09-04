<div id="page-wrapper">
    <input type="hidden" id="client_id" value="<?php echo $client_id;?>" />
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="row">
        <div class="col-lg-12">
            <div class="form-group">
                <div class="col-lg-2">&nbsp;</div>
                <label class="col-lg-2">Select a Product</label>
                <div class="col-lg-4">
                    <p><select id="product_selector" class="form-control selectpicker" data-live-search="true"><option value="0">Select</option><?php echo $this->controller->item->getSelectCollectionItems($item_id);?></select></p>
                </div>
            </div>
        </div>
    </div>
    <?php if($item_id > 0):?>
        <?php if(!$this->controller->item->isCollection($item_id)):?>
            <div class="row">
                <div class="col-lg12">
                    <div class="errorbox">
                        <h2>Not a Collection</h2>
                        <p>Sorry this is not listed as a collection</p>
                        <p>The details for this item can be edited <a href="/products/edit-product/product=<?php echo $item_id;?>">at this link</a></p>
                    </div>
                </div>
            </div>
        <?php else:?>
            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
            <div class="row">
                <form id="collection_edit" action="/form/procCollectionEdit" method="post">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Add Item</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="item_searcher" id="item_searcher" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-7">
                            <h3>Listed Items</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-7">
                            <p>A total of <?php echo count($items);?> items listed</p>
                        </div>
                    </div>
                    <div class="bs-callout bs-callout-primary bs-callout-more">
                        <div id="the_items">
                            <?php foreach($items as $i):?>
                                <div class="form-group row">
                                    <div class='item_holder'>
                                        <label class="col-md-5 col-form-label"><?php echo $i['name']." (".$i['sku'].")";?></label>
                                        <div class="col-md-2">
                                            <input type="text" class="form-control required number" name="items[<?php echo $i['linked_item_id'];?>][qty]"  value="<?php echo $i['number'];?>" />
                                        </div>
                                        <div class='col-md-1 delete-image-holder'>
                                            <a class="delete" data-itemid="<?php echo $i['linked_item_id'];?>" title="remove this item"><i class="fas fa-backspace fa-2x text-danger"></i></a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach;?>
                            <?php echo Form::displayError('items');?>
                        </div>
                    </div>
                    <input type="hidden" name="selected_items" id="selected_items" value="<?php echo $sis;?>" />
                    <input type="hidden" name="item_id" id="item_id" value="<?php echo $item_id;?>" />
                    <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">&nbsp;</label>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        <?php endif;?>
    <?php endif;?>
</div>