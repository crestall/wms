<?php

?>
<h1>Jobs List Generated on <?php echo date("d/m/Y");?></h1>
<table width="100%">
    <thead>
        <tr>
            <th>Job Number</th>
            <th>Client</th>
            <th>Status</th>
            <th>Description</th>
            <th>Notes</th>
            <th>FSG Contact</th>
            <th>Finisher(s)</th>
            <th>Due Date</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($jobs as $job): ?>
            <tr>
                <td>
                    <?php echo $job['job_id'];?>
                </td>
                <td><?php echo $job['customer_name'];?></td>
                <td><?php echo $job['status'];?></td>
                <td><?php echo $job['description'];?></td>
                <td>
                    <?php if(!empty($job['notes'])):?>
                        <div class="notes notes-info">
                            <h6>Production Notes:</h6>
                            <?php echo $job['notes'];?>
                        </div>
                    <?php endif;?>
                    <?php if(!empty($job['delivery_notes'])):?>
                        <div class="notes notes-warning">
                            <h6>Delivery Notes:</h6>
                            <?php echo $job['delivery_notes'];?>
                        </div>
                    <?php endif;?>
                </td>
                <td><?php echo ucwords($job['salesrep_name']);?></td>
                <td data-label="Finisher(s)">
                    <?php for($f = 1; $f <= 3; $f++):
                        $tf = ($f == 1)? "": $f;
                        if(!(empty($job['finisher'.$tf.'_name']))):?>
                            <p class="border-bottom border-secondary border-bottom-dashed mb-3">
                                <?php echo ucwords($job['finisher'.$tf.'_name']);?>
                            </p>
                        <?php endif;?>
                    <?php endfor;?>
                </td>
                <td>
                    <?php if($job['strict_dd'] > 0):?>
                        <?php if($job['due_date'] > 0) echo date("d/m/Y", $job['due_date']);?>
                    <?php endif;?>
                </td>
            </tr>
        <?php endforeach;?>
    </tbody>
</table>