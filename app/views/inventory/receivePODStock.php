<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="row mb-3">
            <label class="col-md-3">POD Invoice</label>
            <div class="col-md-4">
                <select id="client_selector" class="form-control selectpicker" data-style="btn-outline-secondary" data-live-search="true"><option value="0">Select</option><?php echo $this->controller->order->getPODIdSelect($pod_id);?></select>
            </div>
        </div>
    </div>
</div>