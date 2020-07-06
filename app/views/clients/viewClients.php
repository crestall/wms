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
            <?php echo "<pre>",print_r($clients),"</pre>";?>
            </div>
        </div>