<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="row">
        <div class="col-lg-12">
            <div class="form-group">
                <div class="col-lg-2">&nbsp;</div>
                <label class="col-lg-2">Select a Product</label>
                <div class="col-lg-4">
                    <p><select id="product_selector" class="form-control selectpicker" data-live-search="true"><option value="0">Select</option><?php echo $this->controller->item->getSelectPackItems($item_id);?></select></p>
                </div>
            </div>
        </div>
    </div>
    <?php if($item_id > 0):?>
        <?php if(empty($items)):?>
            <div class="row">
                <div class="col-lg12">
                    <div class="errorbox">
                        <h2>Not a pack item</h2>
                        <p>Sorry this is not listed as a pack item</p>
                        <p>The details for this item can be edited <a href="/products/edit-product/product=<?php echo $item_id;?>">at this link</a></p>
                    </div>
                </div>
            </div>
        <?php else:
            $add_to_location = (empty(Form::value('add_to_location')))? $make_to_location : Form::value('add_to_location');?>
            <hr/>
            <div class="row">
                <div class="col-lg-12">
                    <h2>Make Packs</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <?php if(isset($_SESSION['makefeedback'])) :?>
                       <div class='feedbackbox'><i class="far fa-check-circle"></i> <?php echo Session::getAndDestroy('makefeedback');?></div>
                    <?php endif; ?>
                    <?php if(isset($_SESSION['makeerrorfeedback'])) :?>
                       <div class='errorbox'><i class="far fa-times-circle"></i> <?php echo Session::getAndDestroy('makeerrorfeedback');?></div>
                    <?php endif; ?>
                    <p class="text-info">fields marked <sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> are required</p>
                </div>
            </div>
            <?php echo Form::displayError('makegeneral');?>
            <div class='row'>
                <form id="make_pack_items" method="post" action="/form/procMakePacks">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Number to Make</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control required number" name="make_count" id="make_count" value="<?php echo Form::value('make_count');?>" />
                            <?php echo Form::displayError('make_count');?>
                        </div>
                    </div>
                    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/location_selector.php");?>
                    <div class="row">
                        <div class="col-lg-12">
                            <h3>Pick items from the following locations</h3>
                        </div>
                    </div>
                    <?php foreach($items as $i):?>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label"><?php echo $i['name']." (".$i['sku'].")";?></label>
                            <div class="col-md-4">
                                <select  name="location[<?php echo $i['linked_item_id'];?>]" class="form-control selectpicker item_location" data-live-search="true"><option value="0">--Select One--</option><?php echo $this->controller->location->getSelectItemInLocations($i['linked_item_id'], $i['preferred_pick_location_id']);?></select>
                                <?php echo Form::displayError('add_to_location');?>
                            </div>
                        </div>
                    <?php endforeach;?>
                    <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                    <input type="hidden" name="add_product_id" value="<?php echo $item_id; ?>" />
                    <input type="hidden" name="client_id" value="<?php echo $client_id; ?>" />
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">&nbsp;</label>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">Make Them</button>
                        </div>
                    </div>
                </form>
            </div>
            <?php if($available_packs > 0):?>
                <hr/>
                <div class="row">
                    <div class="col-lg-12">
                        <h2>Break Packs</h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <p>There are currently <strong><?php echo $available_packs;;?> pack<?php echo $s;?></strong> available for breaking</p>
                        <p>You cannot break more than that</p>
                        <input type="hidden" id="available_packs" value="<?php echo $available_packs;?>" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <?php if(isset($_SESSION['breakfeedback'])) :?>
                           <div class='feedbackbox'><i class="far fa-check-circle"></i> <?php echo Session::getAndDestroy('breakfeedback');?></div>
                        <?php endif; ?>
                        <?php if(isset($_SESSION['breakerrorfeedback'])) :?>
                           <div class='errorbox'><i class="far fa-times-circle"></i> <?php echo Session::getAndDestroy('breakerrorfeedback');?></div>
                        <?php endif; ?>
                        <p class="text-info">fields marked <sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> are required</p>
                    </div>
                </div>
                <?php echo Form::displayError('breakgeneral');?>
                <div class="row">
                    <form id="break_pack_items" method="post" action="/form/procBreakPacks">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Number to Break</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control required number" name="break_count" id="break_count" value="<?php echo Form::value('break_count');?>" />
                                <?php echo Form::displayError('break_count');?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <h3>Return items to the following locations</h3>
                            </div>
                        </div>
                        <?php foreach($items as $i):?>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label"><?php echo $i['name']." (".$i['sku'].")";?></label>
                                <div class="col-md-4">
                                    <select  name="location[<?php echo $i['linked_item_id'];?>]" class="form-control selectpicker item_location" data-live-search="true"><option value="0">--Select One--</option><?php echo $this->controller->location->getSelectItemInLocations($i['linked_item_id'], $i['preferred_pick_location_id'], true);?></select>
                                    <?php echo Form::displayError('add_to_location');?>
                                </div>
                            </div>
                        <?php endforeach;?>
                        <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                        <input type="hidden" name="break_product_id" value="<?php echo $item_id; ?>" />
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">&nbsp;</label>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary">Break Them</button>
                            </div>
                        </div>
                    </form>
                </div>
            <?php endif;?>
        <?php endif;?>
    <?php endif;?>
</div>