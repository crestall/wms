<?php

?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php //echo "<pre>",print_r(Form::$values),"</pre>"; die();?>
        <div class="row">
            <div class="form_instructions col">
                <h3>Instructions</h3>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        Start typing the item name, SKU or product ID in the text field at the top of the "Items" card below
                    </li>
                    <li class="list-group-item">
                        Select the desired item from the drop down list that appears, and a new section will appear showing the pallets we have stored and the count of items on each pallet
                    </li>
                    <li class="list-group-item">
                        Choose the pallets you would like to have delivered by clicking the corresponding tickbox - <span class="font-italic font-weight-bold">at least one must be selected</span>, and you can choose multiple<br>
                        The "Book Delivery" button will be enabled when pallets have been selected
                    </li>
                    <li class="list-group-item">
                        To add more items, start typing the new name, SKU, or product ID in the text field again
                    </li>
                    <li class="list-group-item">
                        You can add a reference to help you find this booking in the future
                    </li>
                </ul>
            </div>
        </div>
        <?php include(Config::get('VIEWS_PATH')."forms/addDelivery.php"); ?>
    </div>
</div>