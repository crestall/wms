<?php if(!isset($date_filter)) $date_filter = "Ordered";?>
<div class="row form-group">
    <label class="col-md-3 col-form-label">Filter By Date <?php echo $date_filter;?></label>
    <div class="col-md-1">
        <label>From</label>
    </div>
    <div class="col-md-2">
        <div class="input-group">
            <input type="text" class="form-control" name="date_from" id="date_from" value="<?php echo date("d/m/Y", $from);;?>" />
            <span class="input-group-addon"><i class="far fa-calendar-alt"></i></span>
        </div>
    </div>
    <div class="col-md-1">
        <label>To</label>
    </div>
    <div class="col-md-2">
        <div class="input-group">
            <input type="text" class="form-control" name="date_to" id="date_to" value="<?php echo date("d/m/Y", $to);;?>" />
            <span class="input-group-addon"><i class="far fa-calendar-alt"></i></span>
        </div>
    </div>
    <div class="col-md-3">
        <button id="change_dates" class="btn btn-small btn-primary">Change Dates</button>
    </div>
    <input type="hidden" id="date_from_value" name="date_from_value" value="<?php echo $from;?>" />
    <input type="hidden" id="date_to_value" name="date_to_value" value="<?php echo $to;?>" />
</div>