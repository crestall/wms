<table class="table-striped table-hover" id="production_jobs_table">
    <thead>
        <tr>
            <th>Job Number</th>
            <th>Related Job</th>
            <th>Client</th>
            <th>Description</th>
            <th>Notes</th>
            <th>Status</th>
            <th>Sales Rep</th>
            <th>Supplier</th>
            <th>Date Entered</th>
            <th>Due Date</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($jobs as $job):?>
            <tr>
                <td data-label="Job Number" class="number">
                    <a href="/jobs/update-job/job=<?php echo $job['id'];?>"><?php echo $job['job_id'];?></a><br>
                    <span class="inst">Click to update details</span>
                </td>
                <td data-label="Related Job" class="number"><?php echo $job['previous_job_id'];?></td>
                <td data-label="Client">
                    <span style="font-size: larger">
                        <?php if($user_role == "production_admin"):?>
                            <a href="/customers/edit-customer/customer=<?php echo $job['customer_id'];?>"><?php echo $job['customer_name'];?></a>
                        <?php else:?>
                            <?php echo $job['customer_name'];?>
                        <?php endif;?>
                    </span>
                    <div class="contact_details">
                        <div class='row'>
                            <label class='col-4 font-weight-bold'>Contact</label>
                            <div class='col-8'>
                                <?php echo $job['customer_contact'];?>
                            </div>
                        </div>
                        <div class='row'>
                            <label class='col-4 font-weight-bold'>Email</label>
                            <div class='col-8'>
                                <?php echo $job['customer_email'];?>
                            </div>
                        </div>
                        <div class='row'>
                            <label class='col-4 font-weight-bold'>Phone</label>
                            <div class='col-8'>
                                <?php echo $job['customer_phone'];?>
                            </div>
                        </div>
                    </div>
                </td>
                <td data-label="Description"><?php echo $job['description'];?></td>
                <td data-label="Notes"><?php echo $job['notes'];?></td>
                <td data-label="Status"
                <?php if(!empty($job['status_colour'])):?>
                    style="background-color:<?php echo $job['status_colour'];?>; color:<?php echo $job['status_text_colour'];?>"
                <?php endif;?>
                ><?php echo ucwords($job['status']);?></td>
                <td data-label="Sales Rep"><?php echo ucwords($job['salesrep_name']);?></td>
                <td data-label="Supplier">
                    <span style="font-size: larger">
                        <?php if($user_role == "production_admin"):?>
                            <a href="/suppliers/edit-supplier/supplier=<?php echo $job['supplier_id'];?>"><?php echo ucwords($job['supplier_name']);;?></a>
                        <?php else:?>
                            <?php echo ucwords($job['supplier_name']);?>
                        <?php endif;?>
                    </span>
                    <div class="contact_details">
                        <div class='row'>
                            <label class='col-4 font-weight-bold'>Contact</label>
                            <div class='col-8'>
                                <?php echo $job['supplier_contact'];?>
                            </div>
                        </div>
                        <div class='row'>
                            <label class='col-4 font-weight-bold'>Email</label>
                            <div class='col-8'>
                                <?php echo $job['supplier_email'];?>
                            </div>
                        </div>
                        <div class='row'>
                            <label class='col-4 font-weight-bold'>Phone</label>
                            <div class='col-8'>
                                <?php echo $job['supplier_phone'];?>
                            </div>
                        </div>
                    </div>
                </td>
                <td data-label="Date Entered"><?php echo date("d/m/Y", $job['created_date']);?></td>
                <td data-label="Due Date"><?php echo date("d/m/Y", $job['due_date']);?></td>
            </tr>
        <?php endforeach;?>
    </tbody>
</table>