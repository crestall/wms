<?php

?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
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
                        Choose the pallets you would like to have delivered by clicking the corresponding tickbox - at least one <span class="font-italic font-weight-bold">must</span> be selected, but you can choose multiple
                    </li>
                    <li class="list-group-item">
                        To add more items, start typing the new name, SKU, or product ID in the text field again
                    </li>
                </ul>
            </div>
        </div>
        <form id="book_delivery" method="post" action="/form/procBookDelivery">
            <div class="row">
                <div class="col-sm-12 col-md-6 mb-3" id="itemslist">
                    <div class="card h-100 border-secondary order-card">
                        <div class="card-header bg-secondary text-white">
                            Items
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6 mb-3" id="deliverydetails">
                    <div class="card h-100 border-secondary order-card">
                        <div class="card-header bg-secondary text-white">
                            Delivery Details
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>