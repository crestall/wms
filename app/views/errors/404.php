<?php
$quotes = array(
    'Ben Obi-Wan Kenobi - Star Wars'            => "This is not the webpage you are looking for",
    'Dorothy - The Wizard of Oz'                => "I've got a feeling we're not in Kansas anymore.",
    'Warden Norton - The Shawshank Redemption'  => "Lord! It's a miracle! Webpage up and vanished like a fart in the wind!",
    'Dr. Emmet Brown - Back To The Future'      => "Webpages? Where we're going, we don't need webpages.",
    "Gandalf - The Fellowship of the Ring"      => "Always remember, Frodo, the page is trying to get back to its master. It wants to be found.",
    "Spoon Boy - The Matrix"                    => "There is no page.",
    "Jules Winnfield - Pulp Fiction"            => "It's the one that says 'Page Not Found'.",
    "Blond Thug - The Big Lebowski"             => "Where's the page, Lebowski?<br>Where's the page?",
    "The Narrator - Fight Club"                 => "I am Jack's missing page"
);
$credit = array_rand($quotes);
$quote = $quotes[$credit];
?>
<div id="page-wrapper">
    <input type="hidden" name="error_type" id="error_type" value="404">
    <input type="hidden" name="loaded" id="loaded" value="<?php echo time();?>"> 
    <div id="page_container" class="container-xxl">
        <div class="row">
            <div class="bubble bubble-bottom-left col-10 offset-1">
                <div class="row">
                    <div class="error-name col-4">
                        <h1>404</h1>
                        <h2>Page Not Found</h2>
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
                <p>That page was not found here</p>
                <p>We have probably moved it somewhere else and didn't update all the links</p>
                <p class="text-muted">Please use the menu above to find where it might have gone</p>
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