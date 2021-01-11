<?php
$i = (isset($i))? $i : 0;
$this_finisher = $i + 1;
$f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
?>
<div class="p-3 light-grey mb-3 afinisher">
    <div class="form-group row">
        <h4>Finisher <?php echo ucwords($f->format($this_finisher));?>'s Details</h4>
    </div>
    <div class="form-group row">
        <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Finisher Name</label>
        <div class="col-md-4">
            <input type="text" class="form-control required" name="finishers[<?php echo $i;?>][name]">
            <?php echo Form::displayError('finishername_'.$i);?>
        </div>
    </div>
</div>