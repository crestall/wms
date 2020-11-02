<?php
$quotes = array(
    'Ben Obi-Wan Kenobi - Star Wars'            => "This is not the webpage you are looking for",
    'Dorothy - The Wizard of Oz'                => "I've got a feeling we're not in Kansas anymore.",
    'Warden Norton - The Shawshank Redemption'  => "Lord! It's a miracle! Webpage up and vanished like a fart in the wind!",
    'Dr. Emmet Brown - Back To The Future'      => "Webpages? Where we're going, we don't need webpages."
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
                        <h1>404</h1>
                        <h2>ERROR</h2>
                    </div>
                    <div class="error-quote col-6">
                        <?php echo $quote;?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row error-foot">
            <div class="offset-1 col-3 quoter">
                <?php echo $credit;?>
            </div>
            <div class="col-8">
                <p>That page was not found here</p>
                <p>We have probably moved it somewhere else and didn't update all the links</p>
                <p class="text-muted">Please use the menu above find where it might have gone</p>
                <p class="text-muted">If you wish to report this error, please include the URL (shown in the address bar) and time of the error</p>
                <p><a href="/dashboard" class="btn btn-info">Back to home</a></p>
            </div>
        </div>
    </div>
</div>