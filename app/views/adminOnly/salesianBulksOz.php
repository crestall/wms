<?php
//echo "<pre>",print_r($_SESSION),"</pre>";
//echo "<p>DOC_ROOT : ".DOC_ROOT."</p>";

/*
sments
[0]     => name
[1]     => company
[2]     => address1
[3]     => address2
[4]     => address3
[5]     => suburb
[6]     => state
[7]     => postcode
[8]     => width cm
[9]     => lenght cm
[10]    => height cm
[11]    => weight kg
[12]    => magazine count
[13]    => empty
*/
$line = 1;
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php"); ?>
        <div class="row">
            <div class="col-md-12">
                <h2>Importing Shipments</h2>
            </div>
        </div>
        <?php foreach ($sments as $s):?>
            <div class="row">
                <div class="col-md-12">
                    <p>Checking address for <?php echo $s[0]." on line $line</p>";?>
                </div>
            </div>
        <?php endforeach;?>
        <div class="row">
            <div class="col-md-12">
                <?php echo "<pre>",print_r($sments),"</pre>";?>
            </div>
        </div>
    </div>
</div>