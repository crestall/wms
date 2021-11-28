<?php

?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php if(count($runsheets)):?>
            <?php //echo "<pre>",print_r($runsheets),"</pre>"; //die();?>
            <div class="row">
                <div class="col-12">
                    <?php if(isset($_SESSION['feedback'])) :?>
                       <div class='feedbackbox'><?php echo Session::getAndDestroy('feedback');?></div>
                    <?php endif; ?>
                    <?php if(isset($_SESSION['errorfeedback'])) :?>
                       <div class='errorbox'><?php echo Session::getAndDestroy('errorfeedback');?></div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="row">
                <table class="table-striped table-hover" id="finalise_runsheets_table" width="80%">
                    <thead>
                        <tr>
                            <th>Runsheet Day</th>
                            <th>Driver</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($runsheets as $timestamp => $rs):
                            $rows = count($rs['drivers']);?>
                            <tr>
                                <td rowspan="<?php echo $rows;?>" style="vertical-align: middle"><h4><?php echo date('D jS M', $timestamp );?></h4></td>
                                <td style="vertical-align: middle"><?php echo ucwords($rs['drivers'][0]['name']);?></td>
                                <td style="text-align:center;"><a class="btn btn-lg btn-outline-success" href="/runsheets/finalise-runsheet/runsheet=<?php echo $rs['runsheet_id'];?>/driver=<?php echo $rs['drivers'][0]['id'];?>">Finalise</a></td>
                            </tr>
                            <?php for($i = 1; $i < $rows; ++$i):?>
                                <tr>
                                    <td style="vertical-align: middle"><?php echo ucwords($rs['drivers'][$i]['name']);?></td>
                                    <td style="text-align:center;"><a class="btn btn-lg btn-outline-success" href="/runsheets/finalise-runsheet/runsheet=<?php echo $rs['runsheet_id'];?>/driver=<?php echo $rs['drivers'][$i]['id'];?>">Finalise</a></td>
                                </tr>
                            <?php endfor;?>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        <?php else:?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="errorbox">
                        <h2><i class="fas fa-exclamation-triangle"></i> No Runsheets Listed For Finalising</h2>
                    </div>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>