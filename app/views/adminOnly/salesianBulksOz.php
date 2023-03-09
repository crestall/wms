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
        <?php foreach ($sments as $s):
            $name = $s[0];
            $suburb = $s[5];
            $state = $s[6];
            $postcode = $s[7];
            $aResponse = $this->controller->Eparcel->ValidateSuburb($suburb, $state, str_pad($postcode,4,'0',STR_PAD_LEFT)); ?>
            <div class="row">
                <div class="col-md-12">
                    <?php echo "Checking address for $name on line $line";?>
                </div>
            </div>
            <?php
            $error_string = "";
            if(isset($aResponse['errors']))
            {
                foreach($aResponse['errors'] as $e)
                {
                    $error_string .= $e['message']." ";
                }
            }
            elseif($aResponse['found'] === false)
            {
                $error_string .= "Postcode does not match suburb or state";
            }
            if(strlen($error_string)):
                ?>
                <div class="row">
                    <div class="col-md-12">
                        <?php echo "<p>$error_string";?>
                    </div>
                </div>
            <?php else:?>
                <div class="row">
                    <div class="col-md-12">
                        Address is good
                    </div>
                </div>
            <?php endif;?>
            <div class="row">
                <div class="col-md-12">
                    <p>====================================================================================</p>
                </div>
            </div>
        <?php ++$line; endforeach;?>
    </div>
</div>