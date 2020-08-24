<div class="row">
    <div class="col">
        <div class="errorbox">
            <div class="row">
                <div class="col-4 text-right">
                    <i class="fad fa-exclamation-triangle fa-6x"></i>
                </div>
                <div class="col-8">
                    <h2>No Order ID Supplied</h2>
                    <p>No order was supplied to update</p>
                    <?php if($user_role != "client"):?>
                        <p>Please <a href="/orders/client-orders">click here</a> to go back to the list of orders to select one to track.</p>
                    <?php else:?>
                        <p><a href="/orders/view-orders">Please click here to view all orders to choose from</a></p>
                    <?php endif;?>

                </div>
            </div>
        </div>
    </div>
</div>
