<?php
$quotes = array(
    'Chief Brody - Jaws'                                => "You're gonna need a bigger computer",
    'Ted Striker & Rumack - Flying High (Airplane!)'    => "'Surely you can't be serious!'<br>'I am serious. And don't call me Shirley.'",
    'Tyler Durdon - Fight Club'                         => 'Welcome to this page. The first rule of this page is: You do not talk about this page.'
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
                        <h1>400</h1>
                        <h2>Bad Request</h2>
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
                <p>The request resulted in an error and we could not render that page</p>
                <p>You probably will need to log in again, as an important cookie may have expired</p>
                <p>You could just try to refresh the page and see if that works</p>
                <p class="text-muted">If you wish to report this error, please include the URL (shown in the address bar of your browser) and time of the error</p>
                <!--p><a href="/dashboard" class="btn btn-sm btn-danger">Back to home</a></p-->
            </div>
        </div>
    </div>
</div>