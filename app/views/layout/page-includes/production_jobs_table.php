<table class="table-striped table-hover" id="production_jobs_table">
    <thead>
        <tr>
            <th>Job Number</th>
            <th>Related Job</th>
            <th>Client</th>
            <th>Description</th>
            <th>Notes</th>
            <!--th>Status</th-->
            <th nowrap>Status<br /><select id="status_all" class="selectpicker" data-style="btn-outline-secondary btn-sm" data-width="fit"><option value="0">--Select One--</option><?php echo $this->controller->jobstatus->getSelectJobStatus(false, 1, true);?></select>&nbsp;<em><small>(all)</small></em></th>
            <th>FSG Contact</th>
            <th>Finisher(s)</th>
            <th nowrap>
                Select
                <div class="checkbox checkbox-default">
                    <input id="select_all" class="styled" type="checkbox">
                    <label for="select_all"><em><small>(all)</small></em></label>
                </div>
            </th>
            <th>Runsheet Day</th>
            <th>Date Entered</th>
            <th>Due Date</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($jobs as $job):?>
            <tr id="tr_<?php echo $job['id'];?>">
                <td data-label="Job Number" class="number">
                    <?php if($user_role == "production_admin"):?>
                        <a href="/jobs/update-job/job=<?php echo $job['id'];?>"><?php echo $job['job_id'];?></a>
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
                </td>
                <td data-label="Description"><?php echo $job['description'];?></td>
                <td data-label="Notes"><?php echo $job['notes'];?></td>
                <td data-label="Status"
                <?php if(!empty($job['status_colour'])):?>
                    style="background-color:<?php echo $job['status_colour'];?>; color:<?php echo $job['status_text_colour'];?>"
                <?php endif;?>
                ><select class="selectpicker status" id="status_<?php echo $job['id'];?>" data-style="btn-outline-secondary btn-sm" data-width="fit"><option value="0">--Select One--</option><?php echo $this->controller->jobstatus->getSelectJobStatus($job['status_id']);?></select></td>
                <td data-label="FSG Contact"><?php echo ucwords($job['salesrep_name']);?></td>
                <td data-label="Finisher(s)">
                    <?php for($f = 1; $f <= 3; $f++):
                        $tf = ($f == 1)? "": $f;
                        if(!(empty($job['finisher'.$tf.'_name']))):?>
                            <p class="border-bottom border-secondary border-bottom-dashed mb-3">
                                <?php if($user_role == "production_admin"):?>
                                    <a href="/finishers/edit-finisher/finisher=<?php echo $job['finisher'.$tf.'_id'];?>"><?php echo ucwords($job['finisher'.$tf.'_name']);?></a>
                                <?php else:?>
                                    <?php echo ucwords($job['finisher'.$tf.'_name']);?>
                                <?php endif;?>
                            </p>
                        <?php endif;?>
                    <?php endfor;?>
                </td>
                <td data-label="Select" class="chkbox">
                    <div class="checkbox checkbox-default">
                        <input type="checkbox" class="select styled" data-jobid='<?php echo $job['id'];?>' name="select_<?php echo $job['id'];?>" id="select_<?php echo $job['id'];?>" />
                        <label for="select_<?php echo $job['id'];?>"></label>
                    </div>
                </td>
                <td data-label="Runsheet Day">
                    <?php if($job['printed'] > 0):?>
                        <p>This Job is already on a printed runsheet</p>
                    <?php else:
                        if($job['runsheet_id'] > 0):?>
                            <p>This Job is on the runsheet for <strong><?php echo date('l jS \of F',$job['runsheet_day']);?></strong></p>
                            <p class="text-center"><button class="btn btn-outline-danger remove-from-runsheet" data-jobid="<?php echo $job['id'];?>" data-runsheetid="<?php echo $job['runsheet_id'];?>">Remove It</button></p>
                        <?php else:
                            $date = strtotime("today");?>
                            <div class="input-group">
                                <input type="text" class="form-control runsheet_day" name="runsheet_daydate_<?php echo $job['id'];?>" id="runsheet_daydate_<?php echo $job['id'];?>" value="<?php echo date('d/m/Y',$date);?>" />
                                <input type="hidden" name="runsheet_daydate_value_<?php echo $job['id'];?>" id="runsheet_daydate_value_<?php echo $job['id'];?>" value="<?php echo $date;?>" />
                                <div class="input-group-append">
                                    <span id="runsheet_daydate_calendar_<?php echo $job['id'];?>" class="input-group-text runsheet_calendar"><i class="fad fa-calendar-alt"></i></span>
                                </div>
                            </div>
                        <?php endif;?>

                    <?php endif;?>
                </td>
                <td data-label="Date Entered"><?php echo date("d/m/Y", $job['created_date']);?></td>
                <td data-label="Due Date"><?php if($job['due_date'] > 0) echo date("d/m/Y", $job['due_date']);?></td>
            </tr>
        <?php endforeach;?>
    </tbody>
</table>