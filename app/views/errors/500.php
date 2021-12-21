<?php
$quotes = array(
    'C3PO - Starwars'                           => 'The hyperdrive motivator has been damaged. It is impossible to view this webpage',
    'Jim Lovell - Apollo 13'                    => 'Houston, we have a problem',
    'Captain - Cool Hand Luke'                  => 'What we have here is... failure to communicate.',
    "HAL 9000 - 2001: A Space Odyssey"          => "I'm sorry, Dave. I'm afraid I can't do that.",
    "Howard Beal - Network"                     => "I'm as mad as hell, and I'm not going to take this anymore!",
    "Indiana Jones - Raiders of the Lost Ark"   => "500 Errors! Why'd it have be 500 errors?!",
    "Dr. Ian Malcolm - Jurassic Park"           => "God help us! We're in the hands of engineers!",
    "The Narrator - Fight Club"                 => "I am Jack's broken page",
    "Vizzini - The Princess Bride"              => "A 500 error? INCONCEIVABLE"
);
$credit = array_rand($quotes);
$quote = $quotes[$credit];
?>
<div id="page-wrapper">
    <input type="hidden" name="error_type" id="error_type" value="500">
    <input type="hidden" name="loaded" id="loaded" value="<?php echo time();?>">
    <div id="page_container" class="container-xxl">
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
                <p class="text-muted">If you wish to report this error <button id="report_error" class="btn btn-small btn-outline-fsg">Click Here</button></p>
                <!--p class="text-muted">If you wish to report this error, please include the URL (shown in the address bar of your browser) and time of the error</p-->
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