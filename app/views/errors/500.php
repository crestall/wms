<?php
$quotes = array(
    'C3PO - Starwars'           => 'The hyperdrive motivator has been damaged. It is impossible to view this webpage',
    'Jim Lovell - Apollo 13'    => 'Houston, we have a problem',
    'Tyler Durdon - Fight Club' => 'Welcome to this page. The first rule of this page is: You do not talk about this page.'
);
$credit = array_rand($input);
$quote = $quotes[$credit];
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <div class="row">
            <div class="col-12">
                <div class="bubble bubble-bottom-left">
                    <div class="error-name float-left">
                        <h1>500</h1>
                        <h2>ERROR</h2>
                    </div>
                    <div class="error-quote float-right">
                        <?php echo $quote;?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>