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
            <div class="col-12">
                <div class="bubble bubble-bottom-left">
                    <div class="error-name float-left">
                        <h1>404</h1>
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