<?php

?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <?php //echo "<pre>",print_r($job),"</pre>";?>
        <form id="create_delivery_docket" method="post" action="/pdf/createDeliveryDocket">
            <div class="form-group row">
                <div class="form-group row">
                    <label class="col-md-3">Send As</label>
                    <div class="col-md-4">
                        <select id="sender_id" name="sender_id" class="form-control selectpicker" data-style="btn-outline-secondary"><?php echo $this->controller->deliverydocketsender->getSelectSender(Form::value('sender_id'));?></select>
                        <?php echo Form::displayError('sender_id');?>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>