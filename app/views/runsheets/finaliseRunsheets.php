<?php
$db = Database::openConnection();
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php if(count($runsheets)):?>
            <?php echo "<pre>",print_r($runsheets),"/<pre>"; //die();?>
            <div class="row">
                <table class="table-striped table-hover" id="finalise_runsheets_table">
                    <thead>
                        <tr>
                            <th>Runsheet Day</th>
                            <th>Driver</th>
                            <th>Tasks</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($runsheets as $timestamp => $rs):
                            $rows = count($rs['drivers']);?>
                            <tr>
                                <td rowspan="<?php echo $rows;?>"><?php echo date('D jS M', $timestamp );?></td>
                                <td><?php echo ucwords($rs['drivers'][0]['name']);?></td>
                                <td>Arrange the tasks here</td>
                                <td>Actions</td>
                            </tr>
                            <?php for($i = 1; $i < $rows; ++$i):?>
                                <tr>
                                    <td><?php echo ucwords($rs['drivers'][$i]['name']);?></td>
                                    <td>Arrange the tasks here</td>
                                    <td>Actions</td>
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