<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="row form-group">
        <label class="col-md-3">Choose Another Type</label>
        <div class="col-md-4">
            <p><select id="type_selector" class="form-control selectpicker"><option value="0">--Choose One--</option><?php echo $this->controller->solarordertype->getSelectSolarOrderTypes($order_type_id);?></select></p>
        </div>
    </div>
    <?php echo $form;?>
</div>