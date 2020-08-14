<?php
    $link_text = (!$active)? "<a href='/clients/view-clients' class='btn btn-outline-fsg'>View Active Clients</a>" : "<a href='/clients/view-clients/active=0' class='btn btn-outline-fsg'>View Inactive Clients</a>";
    $i = 1;
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="row">
            <div class="col-lg-12">
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

                <div class="col-lg-12" id="table_holder" style="display:none">
                    <table id="client_list_table" class="table-striped table-hover">
                        <thead>
                        	<tr>
                                <th></th>
                                <th>Client Name</th>
                                <th>Contact Name</th>
                                <th>Contact Email</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($clients as $c):?>
                        	<tr>
                                <td><?php echo $i;?></td>
                                <td data-label="Client Name"><img src="/images/client_logos/tn_<?php echo $c['logo'];?>" alt="client logo" class="img-thumbnail" /> <?php echo $c['client_name'];?></td>
                                <td data-label="Contact Name"><?php echo $c['contact_name'];?></td>
                                <td data-label="Contact Email"><?php echo $c['billing_email'];?></td>
                                <td>
                                    <p><a class="btn btn-outline-secondary" href="/clients/edit-client/client=<?php echo $c['id'];?>" >Edit Details</a></p>
                                </td>
                            </tr>
                        <?php ++$i; endforeach;?>
                        </tbody>
                    </table>
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