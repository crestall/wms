<table class="table-striped table-hover" id="production_jobs_table">
    <thead>
        <tr>
            <th>Job Number</th>
            <th>Related Job</th>
            <th>Client</th>
            <th>Description</th>
            <th>Notes</th>
            <th>Status</th>
            <th>FSG Contact</th>
            <th>Finisher</th>
            <th nowrap>
                Select
                <div class="checkbox checkbox-default">
                    <input id="select_all" class="styled" type="checkbox">
                    <label for="select_all"><em><small>(all)</small></em></label>
                </div>
            </th>
            <th nowrap>Courier<br /><select id="driver_all" class="selectpicker" data-style="btn-outline-secondary" data-width="fit"><option value="0">--Select One--</option><?php echo $this->controller->driver->getSelectDrivers();?></select>&nbsp;<em><small>(all)</small></em></th>
            <th>Date Entered</th>
            <th>Due Date</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($jobs as $job):?>
            <tr>
                <td data-label="Job Number" class="number">
                    <?php if($user_role == "production_admin"):?>
                        <a href="/jobs/update-job/job=<?php echo $job['id'];?>"><?php echo $job['job_id'];?></a><br>
                        <span class="inst">Click to update details</span>
                    <?php else:?>
                        <?php echo $job['job_id'];?>
                    <?php endif;?>
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
                <td data-label="FSG Contact"><?php echo ucwords($job['salesrep_name']);?></td>
                <td data-label="Finisher">
                    <span style="font-size: larger">
                        <?php if($user_role == "production_admin"):?>
                            <a href="/finishers/edit-finisher/finisher=<?php echo $job['finisher_id'];?>"><?php echo ucwords($job['finisher_name']);;?></a>
                        <?php else:?>
                            <?php echo ucwords($job['finisher_name']);?>
                        <?php endif;?>
                    </span>
                    <div class="contact_details">
                        <div class='row'>
                            <label class='col-4 font-weight-bold'>Contact</label>
                            <div class='col-8'>
                                <?php echo $job['finisher_contact'];?>
                            </div>
                        </div>
                        <div class='row'>
                            <label class='col-4 font-weight-bold'>Email</label>
                            <div class='col-8'>
                                <?php echo $job['finisher_email'];?>
                            </div>
                        </div>
                        <div class='row'>
                            <label class='col-4 font-weight-bold'>Phone</label>
                            <div class='col-8'>
                                <?php echo $job['finisher_phone'];?>
                            </div>
                        </div>
                    </div>
                </td>
                <td data-label="Select" class="chkbox">
                    <div class="checkbox checkbox-default">
                        <input type="checkbox" class="select styled" data-jobid='<?php echo $job['id'];?>' name="select_<?php echo $job['id'];?>" id="select_<?php echo $job['id'];?>" />
                        <label for="select_<?php echo $job['id'];?>"></label>
                    </div>
                </td>
                <td data-label="Driver" nowrap>
	                <p><select name="driver" class="selectpicker driver" data-style="btn-outline-secondary btn-sm" data-width="fit" id="driver_<?php echo $job['id'];?>" <?php if($job['driver_id'] > 0 ) echo "disabled";?>><option value="0">--Select One--</option><?php echo $this->controller->driver->getSelectDrivers($job['driver_id']);?></select></p>
                    <?php if( $job['driver_id'] > 0): ?>
                        <p><a class="btn btn-outline-danger remove_driver" data-jobid="<?php echo $job['id'];?>">Remove From Driver's Runsheet</a></p>
                    <?php endif;?>
                </td>
                <td data-label="Date Entered"><?php echo date("d/m/Y", $job['created_date']);?></td>
                <td data-label="Due Date"><?php if($job['due_date'] > 0) echo date("d/m/Y", $job['due_date']);?></td>
            </tr>
        <?php endforeach;?>
    </tbody>
</table>