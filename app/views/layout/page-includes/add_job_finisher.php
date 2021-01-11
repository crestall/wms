<?php
$i = (isset($i))? $i : 0;
$this_finisher = $i + 1;
$f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
?>
<div class="p-3 light-grey mb-3 afinisher">
    <div class="form-group row">
    <h4>Finisher <?php echo ucwords($f->format($i));?>'s Details</h4>
    </div>
</div>