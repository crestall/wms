<?php
$term       = (empty(Form::value('term')))? $term : Form::value('term');
$client_id  = (empty(Form::value('client_id')))? $client_id : Form::value('client_id');
$status_id  = (empty(Form::value('status_id')))? $status_id : Form::value('status_id');
$urgency_id  = (empty(Form::value('urgency_id')))? $urgency_id : Form::value('urgency_id');
$date_from_value  = (empty(Form::value('date_from_value')))? $date_from_value : Form::value('date_from_value');
$date_from = ($date_from_value > 0)? date("d/m/Y", $date_from_value) : "";
$date_to_value  = (empty(Form::value('date_to_value')))? $date_to_value : Form::value('date_to_value');
$date_to = ($date_to_value > 0)? date("d/m/Y", $date_to_value) : "";
$pickups = Form::value('pickups');
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php echo $form;?>
        <?php if(count($pickups)):?>
            <?php echo "<pre>",print_r($pickups),"</pre>";?>
        <?php else:?>
            <div class="errorbox">
                <h2>No Pickups Found</h2>
                <?php if(!empty($term)):?>
                    <p>No Pickups were found when searching against "<strong><?php echo $term;?></strong>"</p>
                <?php endif;?>
                <p>Maybe remove some filters?</p>
            </div>
        <?php endif;?>
    </div>
</div>