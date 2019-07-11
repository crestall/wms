                <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-10 text-center">
                                    <h2>Latest Solar Service Jobs</h2>
                                </div>
                                <div class="col-xs-2 text-right">
                                     <a id="toggle_pickups" data-toggle="collapse" href="#new_pickups"><span class="fa arrow huge"></span></a>
                                </div>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="collapse in" id="new_pickups">
                                <div class="row">
                                   <?php foreach($solar_service_jobs as $p):
                                        $s = ($p['pickup_count'] > 1)? "s" : ""; ?>
                                        <div class="col-lg-6">
                                            <div class="panel panel-<?php echo $panel_classes[$c % count($panel_classes)];?>">
                                                
                                            </div>
                                        </div>
                                        <?php if($c % 2 == 0):?>
                                            </div><div class="row">
                                        <?php endif;++$c;?>
                                    <?php endforeach;?>
                                </div>
                            </div>
                        </div>
                    </div>