<?php if(!isset($date_filter)) $date_filter = "Date";?>
<div class="row form-group">
    <label class="col-md-3 col-form-label"><?php echo $date_filter;?></label>
    <div class="col-md-2">
        <div class="input-group">
            <input type="text" class="required form-control" name="date" id="date" value="<?php echo date('d/m/Y', $date);?>" />
            <div class="input-group-append">
                <span class="input-group-text"><i class="fad fa-calendar-alt"></i></span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <button id="change_date" class="btn btn-small btn-outline-secondary">Change Date</button>
    </div>
    <input type="hidden" name="date_value" id="date_value" value="<?php echo $date;?>" />
</div>