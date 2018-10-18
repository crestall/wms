<?php
  if($active == 0)
  {
      $link_text = "<a href='/clients/view-clients' class='btn btn-primary'>View Active Clients</a>";
  }
  else
  {
      $link_text = "<a href='/clients/view-clients/active=0' class='btn btn-warning'>View Inactive Clients</a>";
  }
  $i = 1;
?>
        <div id="page-wrapper">
            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
            <div class="row">
                <div class="col-lg-12">
                    <p class="text-right"><?php echo $link_text;?></p>
                </div>
            </div>
            <div class="row">
            <?php foreach($clients as $c):?>
                <div class="col-lg-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-lg-2">
                                    <img src="/images/client_logos/tn_<?php echo $c['logo'];?>" alt="client logo" class="img-thumbnail" />
                                </div>
                                <div class="col-lg-10">
                                    <h2 class="text-center"><?php echo $c['client_name'];?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-8">
                                    <dl class="dl-horizontal client-list">
                                        <dt>Contact Name</dt>
                                        <dd><?php echo $c['contact_name'];?></dd>
                                        <dt>Contact Email</dt>
                                        <dd><?php echo $c['billing_email'];?></dd>
                                    </dl>
                                </div>
                                <div class="col-lg-4">
                                    <p><a href="/clients/edit-client/client=<?php echo $c['id'];?>" >Edit Details</a></p>
                                    <!--p><a href="/products/viewProducts/<?php //echo $c['id'];?>" >View Products</a></p>
                                    <p><a href="/inventory/viewInventory/<?php //echo $c['id'];?>" >View Inventory</a></p-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if($i % 2 == 0):?>
                    </div>
                    <div class="row">
                <?php endif;?>
            <?php ++$i; endforeach;?>
            </div>
        </div>