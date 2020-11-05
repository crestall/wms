<?php
$quotes = array(
    'Detective David Mills - Se7en'     => "What's on the page?!",
    'Bill Lumbergh - Office Space'      => "Yeah... I'm gonna need you to go ahead and find another page.",
    'Nathan R. Jessup - A Few Good Men' => "The page? You can't handle the page!"
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
                        <h1>403</h1>
                        <h2>Forbidden!</h2>
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
                <p>You do not have the required permissions to access this page</p>
                <p class="text-muted"><em>Please contact us if you would like to adjust your access priviledges</em></p>
                <!--p><a href="/dashboard" class="btn btn-sm btn-danger">Back to home</a></p-->
            </div>
        </div>
    </div>
</div>