<?php
$shrink_wrap = (empty(Form::value('shrink_wrap')))? $order['shrink_wrap']:Form::value('shrink_wrap');
$bubble_wrap = (empty(Form::value('bubble_wrap')))? $order['bubble_wrap']:Form::value('bubble_wrap');
$pallets = (empty(Form::value('pallets')))? $order['pallets']:Form::value('pallets');
$satchels = (empty(Form::value('satchels')))? $order['satchels']:Form::value('satchels');
$charge = (empty(Form::value('charge')))? $order['total_cost']:Form::value('charge');
?>
<div class="row">
    <div class="col-md-12">
        <?php if(isset($_SESSION['miscfeedback'])) :?>
           <div class='feedbackbox'><i class="far fa-check-circle"></i> <?php echo Session::getAndDestroy('miscfeedback');?></div>
        <?php endif; ?>
        <?php if(isset($_SESSION['miscerrorfeedback'])) :?>
           <div class='errorbox'><i class="far fa-times-circle"></i> <?php echo Session::getAndDestroy('miscerrorfeedback');?></div>
        <?php endif; ?>
    </div>
</div>
<div class="row">
    <form id="add_to_stock" method="post" action="/form/procAddMiscToOrder">
        <div class="row">
            <div class="col-md-12">
                <h3>Add Miscellaneous Items To Order</h3>
            </div>
        </div>
        <div class="form-group row">
            <div class="form-check">
                <label class="form-check-label col-md-3" for="shrink_wrap">Shrink Wrap</label>
                <div class="col-md-4 checkbox checkbox-default">
                    <input class="form-check-input styled" type="checkbox" id="shrink_wrap" name="shrink_wrap" <?php if(!empty($shrink_wrap)) echo 'checked';?> />
                    <label for="shrink_wrap"></label>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <div class="form-check">
                <label class="form-check-label col-md-3" for="bubble_wrap">Bubble Wrap</label>
                <div class="col-md-4 checkbox checkbox-default">
                    <input class="form-check-input styled" type="checkbox" id="bubble_wrap" name="bubble_wrap" <?php if(!empty($bubble_wrap)) echo 'checked';?> />
                    <label for="bubble_wrap"></label>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 col-form-label">Pallets</label>
            <div class="col-md-4">
                <input type="text" class="form-control number" name="pallets" id="pallets" value="<?php echo $pallets;?>" />
                <?php echo Form::displayError('pallets');?>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 col-form-label">Satchels</label>
            <div class="col-md-4">
                <input type="text" class="form-control number" name="satchels" id="satchels" value="<?php echo $satchels;?>" />
                <?php echo Form::displayError('satchels');?>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 col-form-label">Charge Amount</label>
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">$</span>
                    <input type="text" class="form-control number" name="charge" id="charge" value="<?php echo $charge;?>" />
                </div>
                <?php echo Form::displayError('charge');?>
            </div>
        </div>
        <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>" />
        <div class="form-group row">
            <label class="col-md-3 col-form-label">&nbsp;</label>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary">Add Miscellaneous Items</button>
            </div>
        </div>
    </form>
</div>