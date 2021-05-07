<?php

?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php echo "<p>pod_id: $pod_id</p>";?>
        <div class="row mb-3">
            <label class="col-md-3">POD Invoice</label>
            <div class="col-md-4">
                <select id="pod_invoice_selector" class="form-control selectpicker" data-style="btn-outline-secondary" data-live-search="true"><option value="0">Select</option><?php echo $this->controller->order->getPODIdSelect($pod_id);?></select>
            </div>
        </div>
        <?php if(!empty($pod_items)):?>
            <div class="row">
                <div class="col-12">
                    <?php echo "<pre>",print_r($pod_items),"</pre>";?>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>