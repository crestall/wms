<?php
?>
<?php if(!empty($item)): ?>
    <div class="row">
        <div class="col-12">
            <?php echo "<pre>",print_r($item),"</pre>";?>
        </div>
    </div>
<?php else:?>
    <div class="row">
        <div class="col-lg-12">
            <div class='errorbox'><h2><i class="far fa-times-circle"></i> Item Not Found</h2>
                <p>That product was not found in the system</p>
            </div>
        </div>
    </div>
<?php endif;?>