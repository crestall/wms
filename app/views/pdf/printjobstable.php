<?php

?>
<table width="100%">
    <thead>
        <tr>
            <th>Job Number</th>
            <th>Client</th>
            <th>Description</th>
            <th>Notes</th>
            <th>FSG Contact</th>
            <th>Finisher(s)</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($jobs as $job): ?>
            <tr>
                <td>
                    <?php echo $job['job_id'];?>
                    <?php if(!empty($job['previous_job_id'])):?>
                        <p class="border-top border-secondary border-top-dashed pt-3">
                            Previous Jobs<br>
                            <?php echo $job['previous_job_id'];?>
                        </p>
                    <?php endif;?>
                </td>
                <td><?php echo $job['customer_name'];?></td>
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
            </tr>
        <?php endforeach;?>
    </tbody>
</table>