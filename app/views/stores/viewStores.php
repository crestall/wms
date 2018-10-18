<?php

?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="row">
        <div class="col-lg-12">
            <?php if($active == 1):?>
                <p class="text-right"><a class="btn btn-warning" href="/stores/view-stores/active=0">View Inactive Stores</a></p>
            <?php else:?>
                <p class="text-right"><a class="btn btn-primary" href="/stores/view-stores">View Active Stores</a></p>
            <?php endif;?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <?php if(isset($_SESSION['feedback'])) :?>
               <div class='feedbackbox'><i class="far fa-check-circle"></i> <?php echo Session::getAndDestroy('feedback');?></div>
            <?php endif; ?>
            <?php if(isset($_SESSION['errorfeedback'])) :?>
               <div class='errorbox'><i class="far fa-times-circle"></i> <?php echo Session::getAndDestroy('errorfeedback');?></div>
            <?php endif; ?>
        </div>
    </div>
    <?php if(count($stores)):?>
        <div class="row">
            <div class="col-lg-3">
                <div class="form-group">
                    <label>Search</label>
                    <input type="text" class="form-control" id="table_searcher" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <table width="100%" class="table-striped table-hover" id="view_stores_table">
                    <thead>
                        <tr>
                            <th>Chain</th>
                            <th>Name</th>
                            <th>Store Number</th>
                            <th>Contact Name</th>
                            <th>Contact Email</th>
                            <th>Address</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($stores as $store):
                            $chain_name = $this->controller->storechain->getChainName($store['chain_id']);
                            $address = Utility::formatAddressWeb($store);?>
                            <tr>
                                <td data-lable="Chain"><?php echo $chain_name;?></td>
                                <td data-label="Name"><?php echo $store['name'];?></td>
                                <td data-label="Store Number"><?php echo $store['store_number'];?></td>
                                <td data-label="Contact Name"><?php echo $store['contact_name'];?></td>
                                <td data-label="Contact Email"><?php echo $store['contact_email'];?></td>
                                <td data-label="Address"><?php echo $address;?></td>
                                <td><a href="/stores/edit-store/store=<?php echo $store['id'];?>">Edit Details</a></td>
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
                    <h2><i class="fas fa-exclamation-triangle"></i> No Stores Listed</h2>
                    <p>There are no <?php if($active == 1) echo "active"; else echo "inactive";?> stores listed in the system at this time</p>
                </div>
            </div>
        </div>
    <?php endif;?>
</div>