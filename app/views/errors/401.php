<?php
$quotes = array(
    'Detective David Mills - Se7en' => "What's on the page?!",
    'Phil Connors - Groundhog Day'  => "Well, what if there is no webpage? There wasn't one today.",
    'Kermit the Frog - The Muppet Movie'    => "Someday we'll find it...your login details"
);
$credit = array_rand($quotes);
$quote = $quotes[$credit];
?>
<div id="page-wrapper">
    <input type="hidden" name="error_type" id="error_type" value="401">
    <input type="hidden" name="loaded" id="loaded" value="<?php echo time();?>">
    <div id="page_container" class="container-xxl">
        <div class="row">
            <div class="bubble bubble-bottom-left col-10 offset-1">
                <div class="row">
                    <div class="error-name col-4">
                        <h1>401</h1>
                        <h2>You are not authenticated</h2>
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
                <p>Your Login credentials could not be verified</p>
                <p>Please login with valid credentials</p>
                <!--p class="text-muted">If you wish us to adjust your priviledges, <a href="/contact/contact-us" class="btn btn-small btn-outline-fsg">Contact Us</button> to let us know</p>
                <p class="text-muted">If you wish to report this error, please include the URL (shown in the address bar of your browser) and time of the error</p-->
                <!--p><a href="/dashboard" class="btn btn-sm btn-outline-fsg">Back to home</a></p-->
            </div>
        </div>
        <div class="row">
            <div class='col'>
                <div id="feedback" style="display:none;"></div>
            </div>
        </div>
    </div>
</div>