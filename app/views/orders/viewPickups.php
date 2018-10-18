<?php
  $c = 0;
?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="row">
        <div class="col-lg-12">
            <h2>Booked In Pickups For <?php echo $client_name;?></h2>
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
    <?php if(count($pickups)):?>
        <?php //echo "<pre>",print_r($pickups),"</pre>";?>
        <div class="row">
            <div class="col-md-12">
                <table width="100%" class="table-striped table-hover" id="pickups_table">
                    <thead>
                        <th></th>
                        <th>Date Booked</th>
                        <th>Pickup Number</th>
                        <th>Client</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Pallets</th>
                        <th>Cartons</th>
                        <th>Entered By</th>
                        <th></th>
                    </thead>
                    <tbody>
                        <?php foreach($pickups as $pu):
                            ++$c;
                            $pickup_address = $this->controller->pickup->getAddressString($pu['id'], "pu");
                            $dropoff_address = $this->controller->pickup->getAddressString($pu['id']);
                            $entered_by = $this->controller->user->getUserName( $pu['entered_by'] );
                            $client = $this->controller->client->getClientName($pu['client_id']);
                            ?>
                            <tr>
                                <td><?php echo $c;?></td>
                                <td data-label="Date Booked"><?php echo date('d/m/Y', $pu['date']);?></td>
                                <td data-label="Pickup Number"><?php echo $pu['pickup_number'];?></td>
                                <td data-label="Client"><?php echo $client;?></td>
                                <td data-label="From"><?php echo $pickup_address;?></td>
                                <td data-label="To"><?php echo $dropoff_address;?></td>
                                <td data-label="Pallets" class="number"><?php echo $pu['pallets'];?></td>
                                <td data-label="Cartons" class="number"><?php echo $pu['cartons'];?></td>
                                <td data-label="Entered By"><?php echo $entered_by;?></td>
                                <td>
                                    <p><a data-pickupid="<?php echo $pu['id'];?>" class="btn btn-primary update-pickup" href="/orders/update-pickup/pickup=<?php echo $pu['id'];?>"><i class="fas fa-pen"></i> Update</a></p>
                                    <p><button data-pickupid="<?php echo $pu['id'];?>" class="btn btn-danger cancel-pickup"><i class="fas fa-ban"></i> Cancel</button></p>
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
                    <h2><i class="fas fa-exclamation-triangle"></i> No Pickups Listed</h2>
                    <p>Either all pickups are fulfilled or you need to remove some filters</p>
                </div>
            </div>
        </div>
    <?php endif;?>
</div>
