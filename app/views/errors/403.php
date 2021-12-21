<?php
$quotes = array(
    'Detective David Mills - Se7en'                     => "What's on the page?!",
    'Bill Lumbergh - Office Space'                      => "Yeah... I'm gonna need you to go ahead and find another page.",
    'Daniel Kaffee & Nathan R. Jessup - A Few Good Men' => "'I want the page!'<br>'You can't handle the page!'"
);
$credit = array_rand($quotes);
$quote = $quotes[$credit];
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <input type="hidden" name="error_type" id="error_type" value="403">
    <input type="hidden" name="loaded" id="loaded" value="<?php echo time();?>">
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
                <p class="text-muted"><em>Please <a href="/contact/contact-us">contact us</a> if you would like to adjust your access priviledges</em></p>
            </div>
        </div>
    </div>
</div>