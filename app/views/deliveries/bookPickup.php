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
                        Choose the required item from the list that appears.
                    </li>
                    <li class="list-group-item">
                        You can also choose "Add A New Item" at the top of the list to add a new item to our system. You will need to enter the item's name and a unique product ID
                    </li>
                    <li class="list-group-item">
                        Once you have selected or created the item that needs collecting, please enter the number of pallets you would like collected
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
        <?php include(Config::get('VIEWS_PATH')."forms/addPickup.php");?>
    </div>
</div>