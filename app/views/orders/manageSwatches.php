<?php
  $states = array(
    "VIC",
    "NSW",
    "TAS",
    "ACT",
    "QLD",
    "NT",
    "SA",
    "WA",
  );
  asort($states);
?>
<div id="page-wrapper">
    <input type="hidden" id="posted" value="<?php echo $posted;?>" />
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="row">
        <div class="col-lg-3 text-center">
            <?php if($posted == 0):?>
                <p><button class="btn btn-info" id="show_fulfilled">Show Only Posted Requests</button></p>
            <?php else:?>
                <p><button class="btn btn-primary" id="show_unfulfilled">Show Only Unposted Requests</button></p>
            <?php endif;?>
        </div>
        <div class="col-lg-3 text-center">
            <p><a class="btn btn-primary label-print"><i class="fas fa-tags"></i> Print Labels For Selected</a></p> 
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3">
            <div class="form-group">
                <label>Filter By Client</label>
                <select id="client_selector" class="form-control selectpicker"><option value="0">All Clients</option><?php echo $this->controller->client->getSelectClients($client_id);?></select>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="form-group">
                <label>Filter By State</label>
                <select id="state_selector" class="form-control selectpicker">
                    <option value="0">All States</option>
                    <?php
                    foreach($states as $s)
                    {
                        echo "<option";
                        if($s == $state)
                        {
                            echo " selected";
                        }
                        echo ">$s</option>";
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="form-group">
                <label>Search</label>
                <input type="text" class="form-control" id="table_searcher" />
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <?php if(isset($_SESSION['feedback'])) :?>
               <div class='feedbackbox'><?php echo Session::getAndDestroy('feedback');?></div>
            <?php endif; ?>
            <?php if(isset($_SESSION['errorfeedback'])) :?>
               <div class='errorbox'><?php echo Session::getAndDestroy('errorfeedback');?></div>
            <?php endif; ?>
        </div>
    </div>
    <?php if(count($swatches)):?>
    <div class="row">
        <div class="col-lg-12">
            <table width="100%" class="table-striped table-hover" id="swatches" style="width:100%">
                <thead>
        	    	<tr>
                        <th></th>
        	        	<th>Client</th>
        				<th>Deliver To</th>
        				<th>Delivery Address</th>
        				<th>Date Imported</th>
                        <th nowrap>
                            Select
                            <div class="checkbox checkbox-default">
                                <input id="select_all" class="styled" type="checkbox">
                                <label for="select_all"><em><small>(all)</small></em></label>
                            </div>
                        </th>
        			</tr>
        		</thead>
                <tbody>
                    <?php $c = 0; foreach($swatches as $sw):
                        ++$c;

                        if(empty($sw['name']))
                        {
                            $ship_to = "";
                        }
                        else
                        {
                            $ship_to = $sw['name'];
                        }
        				$client_name = $this->controller->client->getClientName($sw['client_id']);

                        $address_string = $sw['address']."<br/>";
                        if(!empty($sw['address_2']))
                            $address_string .= " ".$sw['address_2']."<br/>";
                        $address_string .= " ".$sw['suburb']."<br/>";
                        $address_string .= " ".$sw['state']."<br/>";
                        $address_string .= " ".$sw['postcode']."<br/>";
                        $address_string .= " ".$sw['country'];
                        /*
                        */
                        ?>
        	        	<tr>
                            <td class="number" data-label="Count"><?php echo $c;?></td>
        					<td data-label="Client Name"><?php echo $client_name;?></td>
        	                <td class="filterable" data-label="Ship To"><?php echo $ship_to;?></td>
        					<td data-label="Delivery Address" class="filterable"><?php echo $address_string;?></td>
        					<td data-label="Date Imported" nowrap><?php echo date('d-m-Y', $sw['date']);?></td>
        					<td data-label="Select" class="chkbox">
                                <div class="checkbox checkbox-default">
                                    <input <?php //if($errors) echo "disabled";?> type="checkbox" class="select styled" data-orderid='<?php echo $sw['id'];?>' name="select_<?php echo $sw['id'];?>" id="select_<?php echo $sw['id'];?>" data-clientid="<?php echo $sw['client_id'];?>" />
                                    <label for="select_<?php echo $sw['id'];?>"></label>
                                </div>
                            </td>
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
                <h2><i class="fas fa-exclamation-triangle"></i> No Orders Listed</h2>
                <p>Either all swatch requests are fulfilled or you need to remove some filters</p>
            </div>
        </div>
    </div>
<?php endif;?>
</div>
