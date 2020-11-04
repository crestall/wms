<?php
$quotes = array(
    'C3PO - Starwars'           => 'The hyperdrive motivator has been damaged. It is impossible to view this webpage',
    'Jim Lovell - Apollo 13'    => 'Houston, we have a problem',
    'Tyler Durdon - Fight Club' => 'Welcome to this page. The first rule of this page is: You do not talk about this page.'
);
$credit = array_rand($quotes);
$quote = $quotes[$credit];
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <div class="row">
            <div class="bubble bubble-bottom-left col-10 offset-1">
                <div class="row">
                    <div class="error-name col-4">
                        <h1>500</h1>
                        <h2>Internal Error</h2>
                    </div>
                    <div class="error-quote col-6 offset-1">
                        <?php echo $quote;?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row error-foot mt-4">
            <div class="offset-1 col-3 quoter">
                <?php echo $credit;?>
            </div>
            <div class="col-8">
                <p>Oops, we are sorry but our system encountered an internal error</p>
                <p>This means the error is in our coding and you have not done anything wrong</p>
                <p class="text-muted">If you wish to report this error, please include the URL (shown in the address bar) and time of the error</p>
                <p><a href="/dashboard" class="btn btn-sm btn-danger">Back to home</a></p>
            </div>
        </div>
    </div>
</div>