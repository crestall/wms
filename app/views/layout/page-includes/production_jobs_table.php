<?php
  $today = strtotime('today');
  //echo "<p>User Role: $user_role</p>";
  //echo "<pre>",print_r($jobs),"</pre>";
  $can_do_runsheets = false;
?>
<table class="table-striped table-hover" id="production_jobs_table" width="100%">
    <thead>
        <tr>
            <th data-priority="10001" nowwrap>Priority<br /><select id="priority_all" class="selectpicker" data-style="btn-outline-secondary btn-sm" data-width="fit"><option value="0">--</option><?php echo Utility::getPrioritySelect();?></select>&nbsp;<em><small>(all)</small></em></th>
            <th data-priority="1">Job Number</th>
            <th data-priority="1">Client</th>
            <th class="no-sort" data-priority="2" style="max-width: 250px;">Description</th>
            <th class="no-sort">Finisher(s)</th>
            <th>FSG Contact</th>
            <?php if($can_change_status):?>
                <th data-priority="2" nowrap>Status<br /><select id="status_all" class="selectpicker" data-style="btn-outline-secondary btn-sm" data-width="fit"><option value="0">--Select One--</option><?php echo $this->controller->jobstatus->getSelectJobStatus(false, 1, true);?></select>&nbsp;<em><small>(all)</small></em></th>
            <?php else:?>
                <th data-priority="2">Status</th>
            <?php endif;?>
            <th>Dispatch Date</th>
            <th class="no-sort" style="max-width: 250px;">Delivery</th>
            <?php if($need_checkbox):?>
                <th data-priority="1" nowrap class="no-sort">
                    Select
                    <div class="checkbox checkbox-default">
                        <input id="select_all" class="styled" type="checkbox">
                        <label for="select_all"><em><small>(all)</small></em></label>
                    </div>
                </th>
            <?php endif;?>
            <?php if($can_do_runsheets):?>
                <th>Runsheet Day</th>
            <?php endif;?>
        </tr>
    </thead>
    <tbody>
        <?php foreach($jobs as $job):
            $add_to_runsheet = true;
            //create finisher array
            $finisher_array = array();
            if(!empty($job['finishers']))
            {
                $fa = explode("~", $job['finishers']);
                foreach($fa as $f)
                {
                    list($a['id'], $a['name'],$a['email'],$a['phone'],$a['address'],$a['address_2'],$a['suburb'],$a['state'],$a['postcode'],$a['country'],$a['contact_id'],$a['contact_name'],$a['contact_email'],$a['contact_phone'], $a['contact_role'],$a['purchase_order'],$a['ed_date_value']) = explode('|', $f);
                    if(!empty($a['id']))
                        $finisher_array[] = $a;
                }
            }
            ?>
            <tr id="tr_<?php echo $job['id'];?>">
                <td data-label="Priority">
                    <select class="selectpicker priority"  id="priority_<?php echo $job['id'];?>" data-ranking="<?php echo ($job['priority'] > 0)? $job['priority'] : "";?>" data-style="btn-outline-secondary btn-sm" data-width="fit"><option value="0">--</option><?php echo Utility::getPrioritySelect($job['priority']);?></select>
                </td>
                <td data-label="Job Number" class="number">
                    <?php if($user_role == "production_admin" ||  $user_role == "production" || $user_role == "production_sales_admin"):?>
                        <a href="/jobs/update-job/job=<?php echo $job['id'];?>"><?php echo $job['job_id'];?></a>
                    <?php else:?>
                        <?php echo $job['job_id'];?>
                    <?php endif;?>
                    <?php if(!empty($job['previous_job_id'])):?>
                        <p class="border-top border-secondary border-top-dashed pt-3">
                            Previous<br>
                            <?php echo $job['previous_job_id'];?>
                        </p>
                    <?php endif;?>
                    <?php echo "<p>Created: ".date("d/m/Y", $job['created_date'])."</p>"; ?>
                </td>
                <td data-label="Client">
                    <span style="font-size: larger">
                        <?php if($user_role == "production_admin"):?>
                            <a href="/customers/edit-customer/customer=<?php echo $job['customer_id'];?>"><?php echo $job['customer_name'];?></a>
                        <?php else:?>
                            <?php echo $job['customer_name'];?>
                        <?php endif;?>
                    </span>
                </td>
                <td data-label="Description">
                    <?php echo $job['description'];?>
                    <?php if(!empty($job['notes'])):?>
                        <div class="notes notes-info mt-3">
                            <h6>Production Notes:</h6>
                            <?php $note = nl2br($job['notes']); echo $note;?>
                        </div>
                    <?php endif;?>
                    <?php if(isset($_SESSION['notefeedback_'.$job['id']])) :?>
                       <div class='feedbackbox'><?php echo Session::getAndDestroy('notefeedback_'.$job['id']);?></div>
                    <?php endif; ?>
                    <?php if(isset($_SESSION['noteerrorfeedback_'.$job['id']])) :?>
                       <div class='errorbox'><?php echo Session::getAndDestroy('noteerrorfeedback_'.$job['id']);?></div>
                    <?php endif; ?>
                    <p class="text-right mt-3"><button class="btn btn-sm btn-outline-fsg production_note" data-jobid="<?php echo $job['id'];?>" data-jobno="<?php echo $job['job_id'];?>">Add Note For Production</button></p>
                </td>
                <td data-label="Finisher(s)">
                    <?php if(!empty($finisher_array)):
                        foreach($finisher_array as $fin):?>
                            <p class="border-bottom border-secondary border-bottom-dashed mb-3">
                                <?php if($user_role == "production_admin"):?>
                                    <a href="/finishers/edit-finisher/finisher=<?php echo $fin['id'];?>"><?php echo ucwords($fin['name']);?></a>
                                <?php else:?>
                                    <?php echo ucwords($fin['name']);?>
                                <?php endif;?>
                            </p>
                        <?php endforeach;
                    endif;?>
                </td>
                <td data-label="FSG Contact"><?php echo ucwords($job['salesrep_name']);?></td>
                <td data-label="Status"
                    <?php if(!empty($job['status_colour'])):?>
                        style="background-color:<?php echo $job['status_colour'];?>; color:<?php echo $job['status_text_colour'];?>"
                    <?php endif;?>
                    ><select class="selectpicker status" <?php if(!$can_change_status) echo "disabled"; ?> id="status_<?php echo $job['id'];?>" data-style="btn-light btn-sm" data-width="fit"><option value="0">--Select One--</option><?php echo $this->controller->jobstatus->getSelectJobStatus($job['status_id']);?></select>
                    <?php if($job['status_change_time'] > 0)
                    {
                        echo "<p>Status Changed: ".date("d/m/Y", $job['status_change_time']);
                        if(!is_null($job['status_change_name']))
                        {
                            echo "<br>By: ".$job['status_change_name'];
                        }
                        echo "</p>";
                    }
                    ?>
                </td>
                <td data-label="Due Date"
                    <?php if( $job['strict_dd'] > 0 && (filter_var($job['due_date'], FILTER_VALIDATE_INT)) && strtolower($job['status']) != "complete" ):?>
                        <?php if( ($job['due_date'] < $today) ):?>
                            style="background-color: #222; color:#FFF"
                        <?php elseif( ($job['due_date'] - $today) <= (24 * 60 * 60)):?>
                            style="background-color: #FF0000; color:#FFF"
                        <?php elseif( ($job['due_date'] - $today) <= (2 * 24 * 60 * 60)):?>
                            style="background-color: #e6e600;"
                        <?php else: ?>
                            style="background-color: #66ff66;"
                        <?php endif;?>
                    <?php elseif(preg_match("/^asap$/i", $job['due_date'])):?>
                        style="background-color: #FF0000; color:#FFF"
                    <?php endif;?>
                >
                    <?php
                    if( $job['due_date'] !== 0 )
                    {
                        if( filter_var($job['due_date'], FILTER_VALIDATE_INT)  )
                            echo date("d/m/Y", $job['due_date']);
                        else
                            echo $job['due_date'];
                    }

                    ?>
                </td>
                <td data-label="Delivery">
                    <?php if(!empty($job['delivery_notes'])):?>
                        <div class="notes notes-warning">
                            <h6>Delivery Notes:</h6>
                            <?php $note = nl2br($job['delivery_notes']); echo $note;?>
                        </div>
                    <?php endif;?>
                    <?php if(isset($_SESSION['deliveryfeedback_'.$job['id']])) :?>
                       <div class='feedbackbox'><?php echo Session::getAndDestroy('deliveryfeedback_'.$job['id']);?></div>
                    <?php endif; ?>
                    <?php if(isset($_SESSION['deliveryerrorfeedback_'.$job['id']])) :?>
                       <div class='errorbox'><?php echo Session::getAndDestroy('deliveryerrorfeedback_'.$job['id']);?></div>
                    <?php endif; ?>
                    <p class="text-right mt-3 mb-3"><button class="btn btn-sm btn-outline-fsg delivery_note" data-jobid="<?php echo $job['id'];?>" data-jobno="<?php echo $job['job_id'];?>">Add Note For Delivery</button></p>
                    <p><a class="btn btn-sm btn-block btn-outline-info delivery_docket" href="/jobs/create-delivery-docket/job=<?php echo $job['id'];?>">Create Delivery Docket</a></p>
                    <!--p class="mt-2"><a class="btn btn-sm btn-block btn-outline-secondary" href="/jobs/create-shipment/job=<?php echo $job['id'];?>">Create Shipment</a></p-->
                </td>
                <?php if($need_checkbox):?>
                    <td data-label="Select" class="chkbox">
                        <div class="checkbox checkbox-default">
                            <input type="checkbox" class="select styled" data-jobid='<?php echo $job['id'];?>' name="select_<?php echo $job['id'];?>" id="select_<?php echo $job['id'];?>" />
                            <label for="select_<?php echo $job['id'];?>"></label>
                        </div>
                    </td>
                <?php endif;?>
                <?php if($can_do_runsheets):?>
                    <td data-label="Runsheet Day">
                        <?php if($job['runsheet_id'] > 0):
                            $add_to_runsheet = false;?>
                            <p>This Job is on the runsheet for <strong><?php echo date('l jS \of F',$job['runsheet_day']);?></strong></p>
                            <?php if($job['runsheet_completed'] == 1):
                                $add_to_runsheet = true;?>
                                <p class="text-center">The runsheet has been completed</p>
                            <?php else:?>
                                <?php if($job['driver_id'] > 0):
                                    $print_text = ($job['printed'] == 0)? "Print Runsheeet" : "Reprint Runsheet";?>
                                    <p class="text-center"><button class="btn btn-outline-danger remove-from-runsheet" data-jobid="<?php echo $job['id'];?>" data-runsheetid="<?php echo $job['runsheet_id'];?>">Remove It</button></p>
                                    <p class="text-center"><button class='btn btn-sm btn-outline-success print-sheet' data-runsheetid='<?php echo $job['runsheet_id'];?>' data-driverid='<?php echo $job['driver_id'];?>'><?php echo $print_text;?></button></p>
                                    <?php if($job['printed'] > 0):?>
                                        <p><a class='btn btn-sm btn-outline-success' href='/runsheets/finalise-runsheet/runsheet=<?php echo $job['runsheet_id'];?>/driver=<?php echo $job['driver_id'];?>'>Finalise Runsheet</a></p>
                                    <?php endif;?>
                                <?php else:?>
                                    <p><a href='/runsheets/prepare-runsheet/runsheet=<?php echo $job['runsheet_id'];?>' class='btn btn-sm btn-outline-fsg'>Update Driver<br>and Tasks</a></p>
                                <?php endif;?>
                            <?php endif;?>
                        <?php endif;?>
                        <?php if($add_to_runsheet):
                            $date = strtotime("today");?>
                            <div class="input-group">
                                <input type="text" class="form-control runsheet_day" name="runsheet_daydate_<?php echo $job['id'];?>" id="runsheet_daydate_<?php echo $job['id'];?>" value="<?php echo date('d/m/Y',$date);?>" />
                                <input type="hidden" name="runsheet_daydate_value_<?php echo $job['id'];?>" id="runsheet_daydate_value_<?php echo $job['id'];?>" value="<?php echo $date;?>" />
                                <div class="input-group-append">
                                    <span id="runsheet_daydate_calendar_<?php echo $job['id'];?>" class="input-group-text runsheet_calendar"><i class="fad fa-calendar-alt"></i></span>
                                </div>
                            </div>
                        <?php endif;?>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach;?>
    </tbody>
</table>