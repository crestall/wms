<?php
echo "<p>FROM: ".date("Y-m-d H:i:s", $from)."</p>";
echo "<p>TO: ".date("Y-m-d H:i:s", $to)."</p>";
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <input type="hidden" name="client_id" id="client_id" value="<?php echo $client_id;?>" />
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="row form-group">
            <label class="col-md-3">Select a Client</label>
            <div class="col-md-4">
                <p><select id="client_selector" class="form-control selectpicker"  data-style="btn-outline-secondary"><option value="0">Select</option><?php echo $this->controller->client->getSelectPPClients($client_id);?></select></p>
            </div>
        </div>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/between_dates.php");?>
        <?php echo "SPACES<pre>",print_r($spaces),"</pre>"; //die();?>
    </div>
</div>