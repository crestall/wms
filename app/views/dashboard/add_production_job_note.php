
<div id="page-wrapper">
    <div id="page_container" class="container-md" style="min-height: 0; height: 260px;">
        <?php //echo "<pre>",print_r($job),"</pre>";?>
        <form id="jobs-add-production-note" method="post" action="/form/procAddProductionNote">
            <div class="form-group row">
                <label class="col-md-4">Notes/Comments</label>
                <div class="col-md-8">
                    <textarea name="notes" id="notes" class="form-control" rows="3"><?php echo $note; ?></textarea>
                </div>
            </div>
        </form>
    </div>
</div>