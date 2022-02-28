<?php
    $link_text = (!$active)? "<a href='/clients/view-clients' class='btn btn-outline-fsg'>View Active Clients</a>" : "<a href='/clients/view-clients/active=0' class='btn btn-outline-fsg'>View Inactive Clients</a>";
    $i = 1;
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="row">
            <div class="col">
                <p class="text-right"><?php echo $link_text;?></p>
            </div>
        </div>
        <?php if(count($clients)):?>
            <div id="waiting" class="row">
                <div class="col-lg-12 text-center">
                    <h2>Drawing Table..</h2>
                    <p>May take a few moments</p>
                    <img class='loading' src='/images/preloader.gif' alt='loading...' />
                </div>
            </div>
            <div class="row" id="table_holder" style="display:none">
                <div class="col-12">
                    <table id="client_list_table" class="table-striped table-hover" style="width: 95%;margin: auto">
                        <thead>
                        	<tr>
                                <th data-priority="10002"></th>
                                <th data-priority="1">Client Name</th>
                                <th data-priority="2">Contact Name</th>
                                <th data-priority="2">Contact Email</th>
                                <th data-priority="10001">Locked Orders</th>
                                <th data-priority="10001">Production Client</th>
                                <th data-priority="10001">Pick Pack Client</th>
                                <th data-priority="10001">Delivery Client</th>
                                <th data-priority="1"></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($clients as $c):
                            $logo_path = DOC_ROOT.'/images/client_logos/tn_'.$c['logo'];?>
                        	<tr>
                                <td><?php echo $i;?></td>
                                <td data-label="Client Name">
                                    <?php if(file_exists($logo_path)):?>
                                        <img src="/images/client_logos/tn_<?php echo $c['logo'];?>" alt="client logo" class="img-thumbnail" /><br>
                                    <?php endif;?>
                                    <?php echo $c['client_name'];?>
                                </td>
                                <td data-label="Contact Name"><?php echo $c['contact_name'];?></td>
                                <td data-label="Contact Email"><?php echo $c['billing_email'];?></td>
                                <td data-label="Locked Orders"><?php echo ($c['can_adjust'] > 0)?  "No" : "Yes";?></td>
                                <td data-label="Production Client"><?php echo ($c['production_client'] > 0)?  "Yes" : "No";?></td>
                                <td data-label="Pick Pack Client"><?php echo ($c['pick_pack'] > 0)?  "Yes" : "No";?></td>
                                <td data-label="Delivery Client"><?php echo ($c['delivery_client'] > 0)?  "Yes" : "No";?></td>
                                <td>
                                    <p><a class="btn btn-outline-secondary" href="/clients/edit-client/client=<?php echo $c['id'];?>" >Edit Details</a></p>
                                </td>
                            </tr>
                        <?php ++$i; endforeach;?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else:?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="errorbox">
                        <h2>No Clients Listed</h2>
                    </div>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>