<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="row mb-3">
            <label class="col-md-3">Select a Client</label>
            <div class="col-md-4">
                <select id="client_selector" class="form-control selectpicker" data-style="btn-outline-secondary" data-live-search="true"><option value="0">Select</option><?php echo $this->controller->client->getSelectClients($client_id);?></select>
            </div>
        </div>
        <?php if($client_id > 0):?>
            <div class="row">
                <div class="col-lg-12">
                    <h2>Collection Items for <?php echo $client_name;?></h2>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-md-3">Select a Product</label>
                <div class="col-md-4">
                    <select id="product_selector" class="form-control selectpicker" data-style="btn-outline-secondary" data-live-search="true"><option value="0">Select</option><?php echo $this->controller->item->getSelectCollectionItemsByClient($client_id,$item_id);?></select>
                </div>
            </div>
            <?php if($item_id > 0):?>
                <?php if(!$this->controller->item->isCollection($item_id)):?>
                    <div class="row">
                        <div class="col-12">
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
                        <div class="col">
                            <form id="collection_edit" action="/form/procCollectionEdit" method="post">
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
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">Add Item</label>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="item_searcher" id="item_searcher" />
                                    </div>
                                </div>
                                <div id="item_selector" class="p-3 pb-0 mb-2 rounded-top mid-grey">
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <h4>Items in this Collection</h4>
                                        </div>
                                        <div class="col-md-4 text-right">
                                            <a id="remove-all-items" style="cursor:pointer" title="Remove All Items"><h4><i class="fad fa-times-square text-danger"></i> Remove all</a></h4>
                                        </div>
                                    </div>
                                    <div id="the_items">
                                        <?php foreach($items as $i):?>
                                                <div class='row item_holder mb-3'>
                                                    <label class="col-md-7"><?php echo $i['name']." (".$i['sku'].")";?></label>
                                                    <div class="col-md-1">
                                                        <input type="text" class="form-control required number" name="items[<?php echo $i['linked_item_id'];?>][qty]"  value="<?php echo $i['number'];?>" />
                                                    </div>
                                                    <div class='col-md-2 delete-image-holder'>
                                                        <a class='delete' data-itemid="<?php echo $i['linked_item_id'];?>" title='remove this item'><i class='fad fa-times-square text-danger'></i> <span class="inst">Remove</span></a>
                                                    </div>
                                                </div>
                                        <?php endforeach;?>
                                        <?php echo Form::displayError('items');?>
                                    </div>
                                </div>
                                <input type="hidden" name="selected_items" id="selected_items" value="<?php echo $sis;?>" />
                                <input type="hidden" name="item_id" id="item_id" value="<?php echo $item_id;?>" />
                                <input type="hidden" name="client_id" id="client_id" value="<?php echo $client_id;?>" />
                                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">&nbsp;</label>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif;?>
            <?php endif;?>
        <?php endif;?>
    </div>
</div>