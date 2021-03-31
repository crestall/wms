<?php
 //echo "<pre>",print_r($job),"</pre>";
?>
<form id="jobs-add-production-delivery-note" method="post" action="/form/procAddProductionDeliveryNote">
    <div class="form-group row">
        <div class="col-md-12">
            <textarea name="delivery_notes" id="delivery_notes" class="form-control" rows="8"><?php echo $note; ?></textarea>
        </div>
    </div>
    <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
    <input type="hidden" name="job_id" value="<?php echo $job_id; ?>" />
    <input type="hidden" name="job_no" value="<?php echo $job_no; ?>" />
</form>