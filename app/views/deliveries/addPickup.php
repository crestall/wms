<?php

?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="row form-group">
            <label class="col-md-3">Select a Client</label>
            <div class="col-md-4">
                <p><select id="client_selector" class="form-control selectpicker"  data-style="btn-outline-secondary"><option value="0">Select</option><?php echo $this->controller->client->getSelectDeliveryClients($client_id);?></select></p>
            </div>
        </div>
        <?php if($client_id > 0)
            include(Config::get('VIEWS_PATH')."forms/addPickup.php");
        ?>
    </div>
</div>