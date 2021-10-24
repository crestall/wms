<input type="hidden" id="client_id" value="<?php echo $client_id; ?>" />
<input type="hidden" id="from_value" value="<?php echo strtotime('last friday', strtotime('-3 months'));?>" />
<input type="hidden" id="to_value" value="<?php echo strtotime('last saturday', strtotime('tomorrow'));?>" />
<div class="col-md-12 text-center">
    <h2>Quick Links</h2>
</div>
<div class="card-deck indexpagedeck">
    <?php if( Session::isDeliveryClientUser() ):?>
        <div class="card indexpagecard">
            <div class="card-header">
                <h4>Book A Delivery</h4>
            </div>
            <div class="card-body text-center">
            	<a class="btn btn-lg btn-outline-fsg" href="/deliveries/book-delivery"><i class="fad fa-shipping-fast fa-3x fa-flip-horizontal" style="vertical-align: middle;"></i>&nbsp;<span style="font-size:larger">Go!</span></a>
            </div>
        </div>
    <?php else:?>
        <div class="card indexpagecard">
            <div class="card-header">
                <h4>Create An Order</h4>
            </div>
            <div class="card-body text-center">
            	<a class="btn btn-lg btn-outline-fsg" href="/orders/add-order"><i class="fad fa-shipping-fast fa-3x fa-flip-horizontal" style="vertical-align: middle;"></i>&nbsp;<span style="font-size:larger">Go!</span></a>
            </div>
        </div>
        <div class="card indexpagecard">
            <div class="card-header">
                <h4>View Orders</h4>
            </div>
            <div class="card-body text-center">
            	<a class="btn btn-lg btn-outline-fsg" href="/orders/client-orders"><i class="fad fa-th-list fa-3x" style="vertical-align: middle;"></i>&nbsp;<span style="font-size:larger">Look!</span></a>
            </div>
        </div>
        <div class="card indexpagecard">
            <div class="card-header">
                <h4>View Inventory</h4>
            </div>
            <div class="card-body text-center">
            	<a class="btn btn-lg btn-outline-fsg" href="/inventory/client-inventory"><i class="fad fa-inventory fa-3x" style="vertical-align: middle;"></i>&nbsp;<span style="font-size:larger">Check!</span></a>
            </div>
        </div>
        <div class="card indexpagecard">
            <div class="card-header">
                <h4>Dispatch Reports</h4>
            </div>
            <div class="card-body text-center">
            	<a class="btn btn-lg btn-outline-fsg" href="/reports/dispatch-report"><i class="fad fa-file-spreadsheet fa-3x" style="vertical-align: middle;"></i>&nbsp;<span style="font-size:larger">Read!</span></a>
            </div>
        </div>
    <?php endif;?>
</div>
<div id="orders_chart" class="pb-3"></div>
<div class="col-md-12 text-right">
    <button class="btn btn-sm btn-outline-fsg" style="display:none" id="chart_button_2"></button>
</div>
<div id="products_chart" class="pb-3"></div>