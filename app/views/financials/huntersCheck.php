<?php
  $line = $c = 1;
?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
    <div class="row">
        <div class="col-md-12">
            <form id="hunters_invoice_check" method="post" enctype="multipart/form-data" action="/financials/proc-hunters-check">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Invoice CSV File</label>
                    <div class="col-md-4">
                        <input type="file" name="csv_file" id="csv_file" />
                        <?php echo Form::displayError('csv_file');?>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="form-check">
                        <label class="form-check-label col-md-3" for="header_row">My CSV has a header row</label>
                        <div class="col-md-4 checkbox checkbox-default">
                            <input class="form-check-input styled" type="checkbox" id="header_row" name="header_row" checked />
                            <label for="header_row"></label>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">&nbsp;</label>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">Check Invoice</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php if($show_table):?>
        <?php //echo "<pre>",print_r($csv_array),"</pre>";?>
        <div class="row">
            <div class="col-md-12">
                <h2>Invoice Check Result</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table width="100%" class="table-striped table-hover" id="client_orders_table" style="width:100%">
                    <thead>
                        <tr>
                            <th></th>
                            <th>3PL Order Number</th>
                            <th>Hunters Job No</th>
                            <th>Reference 1</th>
                            <th>Consignment ID</th>
                            <th>3PL Listed Charge</th>
                            <th>Hunters Service Type</th>
                            <th>Hunters Charge</th>
                            <th>Hunters plus GST</th>
                            <th>Hunters plus GST<br/>and Fuel</th>
                            <th>Hunters plus GST<br/>and Fuel and Markup</th>
                            <th nowrap>
                                <button class="btn btn-success btn-sm" id="charge_update">Update Selected<br />Charges</button>
                                <div class="checkbox checkbox-default">
                                    <input id="select_all" class="styled" type="checkbox">
                                    <label for="select_all"><strong>Select</strong></label>&nbsp;<em><small>(all)</small></em>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($csv_array as $row):
                            /*
                            [0] => Date
                            [1] => Job No
                            [2] => Driver
                            [3] => Reference
                            [4] => Consignment ID
                            [5] => From Suburb
                            [6] => From Postcode
                            [7] => To Name
                            [8] => To Suburb
                            [9] => To Postcode
                            [10] => Empty
                            [11] => Service
                            [12] => Items
                            [13] => Weight
                            [14] => Charge Code
                            [15] => Price
                            */
                            if($skip_first)
                            {
                                $skip_first = false;
                                continue;
                            }
                            if(empty($row[1]))
                            {
                                continue;
                            }
                            if(empty($row[4]))
                            {
                                continue;
                            }
                            $order = $this->controller->order->getOrderByConId($row[4]);
                            //echo "<pre>",print_r($order),"</pre>";
                            $hc = str_replace("$",'', $row[15]);
                            $hcgst = round(floatval($hc * 1.1), 2);
                            $hcfuel = round(floatval($hc * 1.1 * Config::get('HUNTERS_FUEL_SURCHARGE')), 2);
                            $hcmarkup = round(floatval($hc * 1.1 * Config::get('HUNTERS_FUEL_SURCHARGE') * 1.3), 2);
                            $rowclass = ($order['total_cost'] == $hcmarkup)? "" : "class='order_error error'";?>
                            <tr <?php echo $rowclass;?>>
                                <td class="number"><?php echo $c;?></td>
                                <td data-label="Order Number"><?php echo $order['order_number'];?></td>
                                <td data-label="Hunters Job No"><?php echo $row[1];?></td>
                                <td data-label="Reference 1"><?php echo $row[3];?></td>
                                <td data-label="Consignment ID"><?php echo $row[4];?></td>
                                <td data-label="3PL Listed Charge"><?php echo $order['total_cost'];?></td>
                                <td data-label="Hunters Service Type"><?php echo $row[11];?></td>
                                <td data-label="Hunters Charge"><?php echo $hc;?></td>
                                <td data-label="Hunters Plus GST"><?php echo $hcgst;?></td>
                                <td data-label="Hunters Plus GST and Fuel"><?php echo $hcfuel;?></td>
                                <td data-label="Hunters plus GST and Fuel and Markup"><?php echo $hcmarkup;?></td>
                                <td class="no-label">
                                    <div class="checkbox checkbox-default">
                                        <input class="update styled" type="checkbox" data-hunterscharge="<?php echo $hcmarkup;?>" data-orderid="<?php echo $order['id'];?>" id="charge_<?php echo $order['id'];?>" name="charge_<?php echo $order['id'];?>" />
                                        <label for="charge_<?php echo $order['id'];?>"></label>
                                    </div>
                                </td>
                            </tr>
                        <?php ++$line; ++$c; endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif;?>
</div>