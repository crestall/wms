<?php
if(!empty(Form::value('shrink_wrap')))
{
    $shrink_check = "checked";
}
elseif(!empty($order['shrink_wrap']))
{
    $shrink_check = "checked";
}
else
{
    $shrink_check = "";
}
if(!empty(Form::value('bubble_wrap')))
{
    $bubble_check = "checked";
}
elseif(!empty($order['bubble_wrap']))
{
    $bubble_check = "checked";
}
else
{
    $bubble_check = "";
}
if(!empty(Form::value('inc_gst')))
{
    $gst_check = "checked";
}
elseif( strtoupper($order['country']) == "AU" )
{
    $gst_check = "checked";
}
else
{
    $gst_check = "checked";
}
$pallets = (empty(Form::value('pallets')))? $order['pallets']:Form::value('pallets');
$satchels = (empty(Form::value('satchels')))? $order['satchels']:Form::value('satchels');
$handling_charge = (empty(Form::value('handling_charge')))? $order['handling_charge']:Form::value('handling_charge');
$postage_charge = (empty(Form::value('postage_charge')))? $order['postage_charge']:Form::value('postage_charge');
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
    <form id="add_miscellaneous" method="post" action="/form/procAddMiscToOrder">
        <div class="form-group row custom-control custom-checkbox custom-control-right">
            <input class="custom-control-input col" type="checkbox" id="shrink_wrap" name="shrink_wrap" <?php echo $shrink_check;?> />
            <label class="custom-control-label col" for="shrink_wrap">Shrink Wrap</label>
        </div>
        <div class="form-group row custom-control custom-checkbox custom-control-right">
            <input class="custom-control-input col" type="checkbox" id="bubble_wrap" name="bubble_wrap" <?php echo $bubble_check;?> />
            <label class="custom-control-label col" for="bubble_wrap">Bubble Wrap</label>
        </div>
        <div class="form-group row custom-control custom-checkbox custom-control-right">
            <input class="custom-control-input col" type="checkbox" id="inc_gst" name="inc_gst" <?php echo $gst_check;?> />
            <label class="custom-control-label col" for="inc_gst">Add GST To Postage</label>
        </div>
        <div class="form-group row">
            <label class="col">Pallets</label>
            <div class="col">
                <input type="text" class="form-control number" name="pallets" id="pallets" value="<?php echo $pallets;?>" />
                <?php echo Form::displayError('pallets');?>
            </div>
        </div>
        <div class="form-group row">
            <label class="col">Satchels</label>
            <div class="col">
                <input type="text" class="form-control number" name="satchels" id="satchels" value="<?php echo $satchels;?>" />
                <?php echo Form::displayError('satchels');?>
            </div>
        </div>
        <div class="form-group row">
            <label class="col">Handling Charge</label>
            <div class="col">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">$</span>
                    </div>
                    <input type="text" class="form-control number" name="handling_charge" id="handling_charge" value="<?php echo $handling_charge;?>" />
                </div>
                <?php echo Form::displayError('handling');?>
            </div>
        </div>
        <div class="form-group row">
            <label class="col">Postage Charge</label>
            <div class="col">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">$</span>
                    </div>
                    <input type="text" class="form-control number" name="postage_charge" id="postage_charge" value="<?php echo $postage_charge;?>" />
                </div>
                <?php echo Form::displayError('postage');?>
            </div>
        </div>
        <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>" />
    </form>
</div>