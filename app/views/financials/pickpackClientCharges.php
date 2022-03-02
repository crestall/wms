<?php
function getTableHTML($cs)
{
    $html = "
        <thead>
            <tr>
                <th>Service</th>
                <th>Units</th>
                <th>Unit Charge</th>
                <th>Total (Ex GST)</th>
            </tr>
        </thead>
        <tbody>
    ";
    $gc = array_slice($cs, 2);
    foreach($gc as $service => $details):
        list($units, $uc, $tc) = explode("|",$details);
        $html .= "
        <tr>
            <td>".ucwords(str_replace("_", " ", $service))."</td>
            <td class='number'>$units</td>
            <td class='number nowrap'><i class='fas fa-dollar-sign'></i> ".$uc."</td>
            <td class='number nowrap'><i class='fas fa-dollar-sign'></i> ".$tc."</td>
        </tr>
    ";
    $html .= "</tbody>";
    endforeach;
    return $html;
}
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <input type="hidden" name="client_id" id="client_id" value="<?php echo $client_id;?>" />
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="row form-group">
            <label class="col-md-3">Select a Client</label>
            <div class="col-md-4">
                <p><select id="client_selector" class="form-control selectpicker"  data-style="btn-outline-secondary"><option value="0">Select A Client</option><?php echo $this->controller->client->getSelectPPClients($client_id);?></select></p>
            </div>
        </div>
        <?php if($client_id > 0):?>
            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/between_dates.php");?>
            <div class="row">
                <div class="col-md-2 mb-2 offset-md-9">
                    <a class="btn btn-small btn-outline-fsg" href="/financials/pickpack-client-charges">Remove Filters</a>
                </div>
            </div>
            <div class="row">
                <div class="col mb-3">
                    <h2 class="financials-client-name">Charges For <?php echo $client_name;?></h2>
                </div>
            </div>
            <div id="general_charges_holder" class="mt-2 mb-3 p-2 border border-secondary rounded bg-fsg">
                <h3 class="text-center">General Charges</h3>
                <div class="m-2 p-2 border rounded bg-light">
                    <?php if(count($general_charges)):?>
                        <div class="waiting row">
                            <div class="col-lg-12 text-center">
                                <h2>Drawing Table..</h2>
                                <p>May take a few moments</p>
                                <img class='loading' src='/images/preloader.gif' alt='loading...' />
                            </div>
                        </div>
                        <?php //echo "<pre>",print_r($delivery_charges),"</pre>";?>
                        <div class="table_holder row" style="display:none">
                            <div class="col-xl-12">
                                <table class="table-striped table-hover financials" id="general_client_charges">
                                    <?php echo getTableHTML($general_charges);?>
                                </table>
                            </div>
                        </div>
                    <?php else:?>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="errorbox">
                                    <h2><i class="fas fa-exclamation-triangle"></i> No General Charges to Display</h2>
                                    <p>Nothing happened for this client in the selected date range</p>
                                </div>
                            </div>
                        </div>
                    <?php endif;?>
                </div>
            </div>
            <div id="delivery_charges_holder" class="mt-2 mb-3 p-2 border border-secondary rounded bg-fsg">
                <h3 class="text-center">Handling and Delivery Charges</h3>
                <div class="m-2 p-2 border rounded bg-light">
                    <?php if(count($delivery_handling_charges)):?>
                        <div class="waiting row">
                            <div class="col-lg-12 text-center">
                                <h2>Drawing Table..</h2>
                                <p>May take a few moments</p>
                                <img class='loading' src='/images/preloader.gif' alt='loading...' />
                            </div>
                        </div>
                        <?php //echo "<pre>",print_r($delivery_charges),"</pre>";?>
                        <div class="table_holder row" style="display:none">
                            <div class="col-xl-12">
                                <table class="table-striped table-hover" id="delivery_handling_client_charges">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Orders</th>
                                            <th>Total Charge(Ex GST)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $dhc = array_slice($delivery_handling_charges, 2);
                                        foreach($dhc as $service => $details):
                                            list($units, $tc) = explode("|",$details);?>
                                            <tr>
                                                <td><?php echo ucwords(str_replace("_", " ", $service));?></td>
                                                <td class='number'><?php echo $units;?></td>
                                                <td class='number nowrap'><i class='fas fa-dollar-sign'></i> <?php echo $tc;?></td>
                                            </tr>
                                        <?php endforeach;?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php else:?>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="errorbox">
                                    <h2><i class="fas fa-exclamation-triangle"></i> No Delivery or Handling Charges to Display</h2>
                                    <p>Nothing happened for this client in the selected date range</p>
                                </div>
                            </div>
                        </div>
                    <?php endif;?>
                </div>
            </div>
            <div id="general_charges_holder" class="mt-2 mb-3 p-2 border border-secondary rounded bg-fsg">
                <h3 class="text-center">Storage Charges</h3>
                <div class="m-2 p-2 border rounded bg-light">
                    <?php if(count($storage_charges)):?>
                        <div class="waiting row">
                            <div class="col-lg-12 text-center">
                                <h2>Drawing Table..</h2>
                                <p>May take a few moments</p>
                                <img class='loading' src='/images/preloader.gif' alt='loading...' />
                            </div>
                        </div>
                        <?php //echo "<pre>",print_r($delivery_charges),"</pre>";?>
                        <div class="table_holder row" style="display:none">
                            <div class="col-xl-12">
                                <table class="table-striped table-hover financials" id="pp_storage_charges">
                                    <?php echo getTableHTML($storage_charges);?>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    <?php else:?>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="errorbox">
                                    <h2><i class="fas fa-exclamation-triangle"></i> No Storage Charges to Display</h2>
                                    <p>Nothing happened for this client in the selected date range</p>
                                </div>
                            </div>
                        </div>
                    <?php endif;?>
                </div>
            </div>
            <div id="container_unloading_charges_holder" class="mt-2 mb-3 p-2 border border-secondary rounded bg-fsg">
                <h3 class="text-center">Container Unloading Charges</h3>
                <div class="m-2 p-2 border rounded bg-light">
                    <?php if(count($container_unloading_charges)):?>
                        <?php //echo "<pre>",print_r($delivery_charges),"</pre>";?>
                        <div class="waiting row">
                            <div class="col-lg-12 text-center">
                                <h2>Drawing Table..</h2>
                                <p>May take a few moments</p>
                                <img class='loading' src='/images/preloader.gif' alt='loading...' />
                            </div>
                        </div>
                        <div class="table_holder row" style="display:none">
                            <div class="col-xl-12">
                                <table class="table-striped table-hover financials" id="container_unloading_charges_charges">
                                    <?php echo getTableHTML($container_unloading_charges);?>
                                </table>
                            </div>
                        </div>
                    <?php else:?>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="errorbox">
                                    <h2><i class="fas fa-exclamation-triangle"></i> No Container Unloading Charges to Display</h2>
                                    <p>Nothing happened for this client in the selected date range</p>
                                </div>
                            </div>
                        </div>
                    <?php endif;?>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>
<div id="block_message"></div>