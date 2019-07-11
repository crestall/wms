                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-10 text-center">
                                <h2>Latest Solar Install Jobs </h2>
                            </div>
                            <div class="col-xs-2 text-right">
                                 <a id="toggle_solarorders" data-toggle="collapse" href="#new_solarorders"><span class="fa arrow huge"></span></a>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="collapse in" id="new_solarorders">
                            <div class="row">
                               <?php foreach($solar_orders as $so):
                                    $s = ($so['order_count'] > 1)? "s" : ""; ?>
                                    <div class="col-lg-6">
                                        <div class="panel panel-<?php echo $panel_classes[$c % count($panel_classes)];?>">
                                            <div class="panel-heading order-panel">
                                                <h3 class="text-center"><?php echo ucwords($so['name']);?></h3>
                                            </div>
                                            <div class="panel-footer">
                                                <div class="row">
                                                    <div class="col-xs-8">
                                                        <div><span class="huge"><?php echo $so['order_count'];?></span> Job<?php echo $s;?></div>
                                                        <div><a class="btn btn-<?php echo $panel_classes[$c % count($panel_classes)];?>" href="/solar-jobs/view-installs/type=<?php echo $so['type_id'];?>">Manage Jobs</a></div>
                                                    </div>
                                                    <div class="col-xs-4">
                                                        <i class="fas fa-tools fa-3x"></i>
                                                    </div>
                                                </div>
                                            </div>
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