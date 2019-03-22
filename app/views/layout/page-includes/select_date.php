<?php if(!isset($date_filter)) $date_filter = "Date";?>
<div class="row form-group">
    <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> <?php echo $date_filter;?></label>
    <div class="col-md-2">
        <div class="input-group">
            <input type="text" class="required form-control" name="date" id="date" value="<?php echo date('d/m/Y', $date);?>" />
            <span class="input-group-addon"><i class="far fa-calendar-alt"></i></span>
        </div>
    </div>
    <input type="hidden" name="date_value" id="date_value" value="<?php echo $date;?>" />
</div>