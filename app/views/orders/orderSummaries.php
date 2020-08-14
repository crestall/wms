<?php
$seven_days = 7*24*60*60;
$older_from = $from - $seven_days;
$newer_from = $from + $seven_days;
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="row mb-3">
            <div class="col-4">
                <a href="/orders/order-summaries/from=<?php echo $older_from;?>" class="btn btn-outline-secondary">Show Older Summaries</a>
            </div>
            <div class="col-4">
                <a href="/orders/order-summaries/from=<?php echo $newer_from;?>" class="btn btn-outline-secondary">Show Newer Summaries</a>
            </div>
            <div class="col-4">
                <a href="/orders/order-summaries/from=<?php echo $newer_from;?>" class="btn btn-outline-secondary">Show Most Recent</a>
            </div>
        </div>
        <?php if(count($summaries)):?>
            <div class="row">
                <div class="col-lg-12">
                    <table id="order_summaries_table" class="table-striped table-hover">
                        <thead>
                        	<tr>
                            	<th>Manifest ID</th>
                				<th>Submitted On</th>
                				<th>Number of Orders</th>
                                <th>Client</th>
                                <th>Printed</th>
                				<th></th>
                			</tr>
                        </thead>
                        <tbody>
                            <?php foreach($summaries as $s):
                                $order_count = $this->controller->order->getOrderCountForSummary($s['id']);
                                $client_name = $this->controller->client->getClientName($s['client_id']);
                				if($order_count > 0):
                                    $printed = ($s['printed'] > 0)? "Yes" : "No";
                					$date_submitted = date('d/m/Y', $s['create_date']);?>
                		            <tr>
                                    	<td data-label="Manifest ID"><?php echo $s['manifest_id'];?></td>
                						<td  data-label="Submitted On"><?php echo $date_submitted;?></td>
                						<td class="number" data-label="Number of Orders"><?php echo $order_count;?></td>
                                        <td data-label="Client"><?php echo $client_name;?></td>
                                        <td class="centre printed" data-label="Printed"><?php echo $printed;?></td>
                                        <td class="nowrap"><a href="/pdf/order-summary/summary=<?php echo $s['id'];?>" class="summary btn btn-outline-fsg" target="_blank">Open Order Summary</a></td>
                					</tr>
                				<?php endif;?>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else:?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="errorbox">
                        <h2>No Order Summaries Found</h2>
                        <p>There are no order summaries in the system</p>
                    </div>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>