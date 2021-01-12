<?php
$i = (isset($i))? $i : 0;
$this_finisher = $i + 1;
$f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
?>
<div id="finisher_<?php echo $i;?>" class="p-3 light-grey mb-3 afinisher">
    <div class="form-group row">
        <h4 class="col-md-4 finisher_title">Finisher <?php echo ucwords($f->format($this_finisher));?>'s Details</h4>
        <div class="col-md-4">
            <h5><a data-finisher="<?php echo $i;?>" class="remove-finisher" style="cursor:pointer" title="Remove Finisher"><i class="fad fa-times-square text-danger"></i> Remove This Finisher</a></h5>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Finisher Name</label>
        <div class="col-md-4">
            <input type="text" class="form-control finisher_name required" name="finishers[<?php echo $i;?>][name]">
            <span class="inst">
                Start typing a name and choose a finisher from the list<br>
                Only finishers already in the system can be chosen here<br>
                <a href="/finishers/add-finisher" target="_blank" title="opens in new window">Click here to add a new finisher <i class="fal fa-external-link"></i></a>
            </span>
            <?php echo Form::displayError('finishername_'.$i);?>
        </div>
    </div>
    <div class="this_finisher_details">
        <div class="form-group row">
            <label class="col-md-4">Purchase Order Number</label>
            <div class="col-md-4">
                <input type="text" class="form-control finisher_po" name="finishers[<?php echo $i;?>][purchase_order]">
            </div>
        </div>
        <div class="row form-group">
            <label class="col-md-4 col-form-label">Expected Delivery Date</label>
            <div class="col-md-3">
                <div class="input-group">
                    <input type="text" class="form-control finisher_ed_date_value" name="finishers[<?php echo $i;?>][ed_date]">
                    <div class="input-group-append">
                        <span class="input-group-text"><i class="fad fa-calendar-alt"></i></span>
                    </div>
                </div>
            </div>
            <input type="hidden" name="date_ed<?php echo $fn;?>_value" id="date_ed<?php echo $fn;?>_value" value="<?php echo Form::value('date_ed'.$fn.'_value');?>" />
        </div>
    </div>
</div>