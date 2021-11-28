<?php
$job_id = (!empty(Form::value('job_id')))? Form::value('job_id'):$job['job_id'];
$strict_dd = (empty(Form::value('job_id')) && $job['strict_dd'] == 0)? false : (!empty(Form::value('job_id')) && $job['strict_dd'] == 1)?  true : ($job['strict_dd'] == 1)? true : false;
$previous_job_id = (!empty(Form::value('previous_job_id')))? Form::value('previous_job_id'):$job['previous_job_id'];
$customer_po_number = (!empty(Form::value('customer_po_number')))? Form::value('customer_po_number'):$job['customer_po_number']; 
$priority = (!empty(Form::value('priority')))? Form::value('priority'):$job['priority'];
$status_id = (!empty(Form::value('status_id')))? Form::value('status_id'):$job['status_id'];
$salesrep_id = (!empty(Form::value('salesrep_id')))? Form::value('salesrep_id'):$job['salesrep_id'];
$date_entered = (!empty(Form::value('date_entered_value')))? Form::value('date_entered_value'): $job['created_date'];
$date_due = (!empty(Form::value('date_due_value')))? Form::value('date_due_value'): $job['due_date'];
$designer = (!empty(Form::value('designer')))? Form::value('designer'):$job['designer'];
$description = (!empty(Form::value('description')))? Form::value('description'):$job['description'];
$notes = (!empty(Form::value('notes')))? Form::value('notes'):$job['notes'];
$delivery_notes = (!empty(Form::value('delivery_notes')))? Form::value('delivery_notes'):$job['delivery_notes'];
$customer_name = (!empty(Form::value('customer_name')))? Form::value('customer_name'):$customer['name'];
$customer_contact = (!empty(Form::value('customer_contact')))? Form::value('customer_contact'):$customer['contact'];
$customer_email = (!empty(Form::value('customer_email')))? Form::value('customer_email'):$customer['email'];
$customer_phone = (!empty(Form::value('customer_phone')))? Form::value('customer_phone'):$customer['phone'];
$customer_address = (!empty(Form::value('customer_address')))? Form::value('customer_address'):$customer['address'];
$customer_address2 = (!empty(Form::value('customer_address2')))? Form::value('customer_address2'):$customer['address_2'];
$customer_suburb = (!empty(Form::value('customer_suburb')))? Form::value('customer_suburb'):$customer['suburb'];
$customer_state = (!empty(Form::value('customer_state')))? Form::value('customer_state'):$customer['state'];
$customer_postcode = (!empty(Form::value('customer_postcode')))? Form::value('customer_postcode'):$customer['postcode'];
$customer_country = (!empty(Form::value('customer_country')))? Form::value('customer_country'):$customer['country'];
$date_ed = (!empty(Form::value('date_ed_value')))? Form::value('date_ed_value') : $job['ed_date'];
$date_ed2 = (!empty(Form::value('date_ed2_value')))? Form::value('date_ed2_value') : $job['ed2_date'];
$date_ed3 = (!empty(Form::value('date_ed3_value')))? Form::value('date_ed3_value') : $job['ed3_date'];
$ship_to    = (empty(Form::value('ship_to')))?  $job['ship_to']      : Form::value('ship_to');
$address    = empty(Form::value('address'))?    $job['address']      : Form::value('address');
$address2   = empty(Form::value('address2'))?   $job['address_2']    : Form::value('address2');
$suburb     = empty(Form::value('suburb'))?     $job['suburb']       : Form::value('suburb');
$state      = empty(Form::value('state'))?      $job['state']        : Form::value('state');
$postcode   = empty(Form::value('postcode'))?   $job['postcode']     : Form::value('postcode');
$country    = empty(Form::value('country'))?    $job['country']      : Form::value('country');
$delivery_instructions = empty(Form::value('delivery_instructions'))? $job['delivery_instructions'] : Form::value('delivery_instructions');
$attention = empty(Form::value('attention'))? $job['attention'] : Form::value('attention');
//Finisher Stuff
$finisher_count = 3;
$f = 0;
$nf = new NumberFormatter("en", NumberFormatter::SPELLOUT);
while($f < $finisher_count)
{
    $fn = ($f > 0)? $f + 1 : "";
    ${'date_ed'.$fn} = (!empty(Form::value('date_ed{$fn}_value')))? Form::value('date_ed{$fn}_value') : $job['ed'.$fn.'_date'];
    if(count(${'finisher'.$fn}))
    {
        ${'finisher'.$fn.'_name'} = ucwords((!empty(Form::value('finisher{$fn}_name')))? Form::value('finisher{$fn}_name'):${'finisher'.$fn}['name']);
        ${'finisher'.$fn.'_contact'} = (!empty(Form::value('finisher{$fn}_contact')))? Form::value('finisher{$fn}_contact'):${'finisher'.$fn}['contact'];
        ${'finisher'.$fn.'_email'} = (!empty(Form::value('finisher{$fn}_email')))? Form::value('finisher{$fn}_email'):${'finisher'.$fn}['email'];
        ${'finisher'.$fn.'_phone'} = (!empty(Form::value('finisher{$fn}_phone')))? Form::value('finisher{$fn}_phone'):${'finisher'.$fn}['phone'];
        ${'finisher'.$fn.'_address'} = (!empty(Form::value('finisher{$fn}_address')))? Form::value('finisher{$fn}_address'):${'finisher'.$fn}['address'];
        ${'finisher'.$fn.'_address2'} = (!empty(Form::value('finisher{$fn}_address2')))? Form::value('finisher{$fn}_address2'):${'finisher'.$fn}['address_2'];
        ${'finisher'.$fn.'_suburb'} = (!empty(Form::value('finisher{$fn}_suburb')))? Form::value('finisher{$fn}_suburb'):${'finisher'.$fn}['suburb'];
        ${'finisher'.$fn.'_state'} = (!empty(Form::value('finisher{$fn}_state')))? Form::value('finisher{$fn}_state'):${'finisher'.$fn}['state'];
        ${'finisher'.$fn.'_postcode'} = (!empty(Form::value('finisher{$fn}_postcode')))? Form::value('finisher{$fn}_postcode'):${'finisher'.$fn}['postcode'];
        ${'finisher'.$fn.'_country'} = (!empty(Form::value('finisher{$fn}_country')))? Form::value('finisher{$fn}_country'):${'finisher'.$fn}['country'];
        ${'finisher'.$fn.'_po'} =(!empty(Form::value('finisher{$fn}_po')))? Form::value('finisher{$fn}_po'): $job['finisher'.$fn.'_po'];
    }
    else
    {
        ${'finisher'.$fn.'_name'} = Form::value('finisher{$fn}_name');
        ${'finisher'.$fn.'_contact'} = Form::value('finisher{$fn}_contact');
        ${'finisher'.$fn.'_email'} = Form::value('finisher{$fn}_email');
        ${'finisher'.$fn.'_phone'} = Form::value('finisher{$fn}_phone');
        ${'finisher'.$fn.'_address'} = Form::value('finisher{$fn}_address');
        ${'finisher'.$fn.'_address2'} =  Form::value('finisher{$fn}_address2');
        ${'finisher'.$fn.'_suburb'} = Form::value('finisher{$fn}_suburb');
        ${'finisher'.$fn.'_state'} = Form::value('finisher{$fn}_state');
        ${'finisher'.$fn.'_postcode'} = Form::value('finisher{$fn}_postcode');
        ${'finisher'.$fn.'_country'} = Form::value('finisher{$fn}_country');
        ${'finisher'.$fn.'_po'} = Form::value('finisher{$fn}_po');
    }
    ++$f;
}
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php echo "JOB<pre>",print_r($job),"</pre>";?>
        <?php echo "CUSTOMER<pre>",print_r($customer),"</pre>";?>
        <?php echo "CUSTOMER CONTACTS<pre>",print_r($customer_contacts),"</pre>";?>
        <?php //echo "finisher<pre>",print_r($finisher),"</pre>";?>
        <?php //echo "SESSION<pre>",print_r($_SESSION),"</pre>";?>
        <div class="row">
<!------------------------------------------------------------------------------------------------------------------------------------------->
<!-------------------------------------------------     Job Details     --------------------------------------------------------------------->
<!------------------------------------------------------------------------------------------------------------------------------------------->
            <div class="col-sm-12 col-md-6 mb-3" id="jobdetails">
                <div class="card h-100 border-secondary order-card">
                    <div class="card-header bg-secondary text-white">
                        Job Details
                    </div>
                    <div class="card-body">
                        <?php if(isset($_SESSION['jobdetailsfeedback'])) :?>
                           <div class='feedbackbox'><?php echo Session::getAndDestroy('jobdetailsfeedback');?></div>
                        <?php endif; ?>
                        <?php if(isset($_SESSION['jobdetailserrorfeedback'])) :?>
                           <div class='errorbox'><?php echo Session::getAndDestroy('jobdetailserrorfeedback');?></div>
                        <?php endif; ?>
                        <form id="job_details_update" method="post" action="/form/procJobDetailsUpdate">
                            <div class="form-group row">
                                <label class="col-md-4"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Job Id</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control required" name="job_id" id="job_id" value="<?php echo $job_id;?>" />
                                    <input type="hidden" name="current_jobid" id="current_jobid" value="<?php echo $job_id;?>" >
                                    <?php echo Form::displayError('job_id');?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4">Related Job Id</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="previous_job_id" id="previous_job_id" value="<?php echo $previous_job_id;?>" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4">Customer PO Number</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="customer_po_number" id="customer_po_number" value="<?php echo $customer_po_number;?>" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4">Priority</label>
                                <div class="col-md-8">
                                    <select id="priority" class="form-control selectpicker" name="priority" data-style="btn-outline-secondary"><option value="0">-- Select One --</option><?php echo Utility::getPrioritySelect($priority);?></select>
                                    <?php echo Form::displayError('priority');?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Status</label>
                                <div class="col-md-8">
                                    <select id="status_id" class="form-control selectpicker" name="status_id" data-style="btn-outline-secondary"><option value="0">-- Select One --</option><?php echo $this->controller->jobstatus->getSelectJobStatus($status_id);?></select>
                                    <?php echo Form::displayError('status_id');?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4">FSG Contact</label>
                                <div class="col-md-8">
                                    <select id="salesrep_id" class="form-control selectpicker" name="salesrep_id" data-style="btn-outline-secondary"><option value="0">-- Select One --</option><?php echo $this->controller->salesrep->getSelectSalesReps($salesrep_id);?></select>
                                </div>
                            </div>
                            <div class="row form-group">
                                <label class="col-md-4 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Date Entered</label>
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <input type="text" class="required form-control" name="date_entered" id="date_entered" value="<?php echo date('d/m/Y', $date_entered);?>" />
                                        <div class="input-group-append">
                                            <span id="date_entered_calendar" class="input-group-text"><i class="fad fa-calendar-alt"></i></span>
                                        </div>
                                        <?php echo Form::displayError('date_entered');?>
                                    </div>
                                </div>
                                <input type="hidden" name="date_entered_value" id="date_entered_value" value="<?php echo $date_entered;?>" />
                            </div>
                            <div class="row form-group">
                                <label class="col-md-4 col-form-label">Due Date</label>
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="date_due" id="date_due" value="<?php if(!empty($date_due)) echo date('d/m/Y', $date_due);?>" />
                                        <div class="input-group-append">
                                            <span id="date_due_calendar" class="input-group-text"><i class="fad fa-calendar-alt"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="date_due_value" id="date_due_value" value="<?php echo $date_due;?>" />
                            </div>
                            <div class="form-group row custom-control custom-checkbox custom-control-right">
                                <input class="custom-control-input" type="checkbox" id="strict_dd" name="strict_dd" <?php if($strict_dd) echo "checked";?>  />
                                <label class="custom-control-label col-md-4" for="strict_dd">Strict Due Date</label>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4">Designer</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="designer" id="designer" value="<?php echo $designer;?>" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Description</label>
                                <div class="col-md-8">
                                    <textarea name="description" id="description" class="form-control required" rows="4"><?php echo $description;?></textarea>
                                    <?php echo Form::displayError('description');?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4">Notes/Comments</label>
                                <div class="col-md-8">
                                    <textarea name="notes" id="notes" class="form-control" rows="3"><?php echo $notes;?></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4">Delivery Notes/Comments</label>
                                <div class="col-md-8">
                                    <textarea name="delivery_notes" id="delivery_notes" class="form-control" rows="3"><?php echo $delivery_notes;?></textarea>
                                </div>
                            </div>
                            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                            <input type="hidden" name="id" value="<?php echo $job['id'];?>" >
                        </form>
                    </div>
                    <div class="card-footer text-right">
                        <button id="job_details_update_submitter" class="btn btn-outline-secondary">Save Changes</button>
                    </div>
                </div>
            </div>
<!------------------------------------------------------------------------------------------------------------------------------------------->
<!-------------------------------------------------     Delivery Details     ---------------------------------------------------------------->
<!------------------------------------------------------------------------------------------------------------------------------------------->
            <div class="col-sm-12 col-md-6 mb-3">
                <div class="card h-100 border-secondary order-card" id="deliverydetails">
                    <div class="card-header bg-secondary text-white">
                        Delivery Details
                    </div>
                    <div class="card-body">
                        <?php if(isset($_SESSION['jobdeliverydetailsfeedback'])) :?>
                            <div class='feedbackbox'><?php echo Session::getAndDestroy('jobdeliverydetailsfeedback');?></div>
                        <?php endif; ?>
                        <?php if(isset($_SESSION['jobdeliverydetailserrorfeedback'])) :?>
                            <div class='errorbox'><?php echo Session::getAndDestroy('jobdeliverydetailserrorfeedback');?></div>
                        <?php endif; ?>
                        <form id="delivery_details_update" method="post" action="/form/procJobDeliveryUpdate">
                            <?php include(Config::get('VIEWS_PATH')."forms/delivery_destinations.php");?>
                            <div id="delivery_address_holder">
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Deliver To</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control required" name="ship_to" id="ship_to" value="<?php echo $ship_to;?>" />
                                        <?php echo Form::displayError('ship_to');?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label">Attention</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" name="attention" id="attention" value="<?php echo $attention;?>" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label">Delivery Instructions</label>
                                    <div class="col-md-8">
                                        <textarea class="form-control" name="delivery_instructions" id="delivery_instructions" placeholder="Instructions For Driver"><?php echo $delivery_instructions;?></textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Address Line 1</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control required" name="address" id="address" value="<?php echo $address;?>" /><br>
                                        <div class="checkbox checkbox-default" style="margin-left:20px;margin-top:-25px">
                                            <input class="form-check-input styled" type="checkbox" id="ignore_address_error" name="ignore_address_error" <?php if(!empty(Form::value('ignore_address_error'))) echo 'checked';?> />
                                            <label for="ignore_address_error"><span class="inst">No need for a number</span></label>
                                        </div>
                                        <?php echo Form::displayError('address');?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4">Address Line 2</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" name="address2" id="address2" value="<?php echo $address2;?>" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Suburb/Town</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control required" name="suburb" id="suburb" value="<?php echo $suburb;?>" />
                                        <?php echo Form::displayError('suburb');?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4">State</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" name="state" id="state" value="<?php echo $state;?>" />
                                        <span class="inst">for AU addresses use VIC, NSW, QLD, ACT, TAS, WA, SA, NT only</span>
                                        <?php echo Form::displayError('state');?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4 "><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Postcode</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control required" name="postcode" id="postcode" value="<?php echo $postcode;?>" />
                                        <?php echo Form::displayError('postcode');?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Country</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control required" name="country" id="country" value="<?php echo $country;?>" />
                                        <span class="inst">use the 2 letter ISO code</span>
                                        <?php echo Form::displayError('country');?>
                                    </div>
                                </div>
                                <input type="hidden" id="csrf_token" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                                <input type="hidden" name="job_id" id="job_id" value="<?php echo $job['id'];?>" >
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-right">
                        <button id="delivery_details_update_submitter" class="btn btn-outline-secondary">Save Changes</button>
                    </div>
                </div>
            </div>
<!------------------------------------------------------------------------------------------------------------------------------------------->
<!-------------------------------------------------     Customer Details     ---------------------------------------------------------------->
<!------------------------------------------------------------------------------------------------------------------------------------------->
            <div class="col-sm-12 col-md-6 mb-3">
                <div class="card h-100 border-secondary order-card" id="customerdetails">
                    <div class="card-header bg-secondary text-white">
                        Customer Details
                    </div>
                    <div class="card-body">
                        <?php if(isset($_SESSION['jobcustomerdetailsfeedback'])) :?>
                            <div class='feedbackbox'><?php echo Session::getAndDestroy('jobcustomerdetailsfeedback');?></div>
                        <?php endif; ?>
                        <?php if(isset($_SESSION['jobcustomerdetailserrorfeedback'])) :?>
                            <div class='errorbox'><?php echo Session::getAndDestroy('jobcustomerdetailserrorfeedback');?></div>
                        <?php endif; ?>
                        <form id="customer_details_update" method="post" action="/form/procJobCustomerUpdate">
                            <div class="form-group row">
                                <label class="col-md-4"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Customer Name</label>
                                <div class="col-md-8">
                                    <input type="text" class="required form-control" name="customer_name" id="customer_name" value="<?php echo $customer_name;?>" />
                                    <input type="hidden" name="customer_id" id="customer_id" value="<?php echo $customer['id'];?>" />
                                    <?php echo Form::displayError('customer_name');?>
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-4">Contact</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control customer" name="customer_contact" id="customer_contact" value="<?php echo $customer_contact;?>" />
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-4">Email</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control customer email" name="customer_email" id="customer_email" value="<?php echo $customer_email;?>" />
                                    <?php echo Form::displayError('customer_email');?>
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-4">Phone</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control customer" name="customer_phone" id="customer_phone" value="<?php echo $customer_phone;?>" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">Address Line 1</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control customer" name="customer_address" id="customer_address" value="<?php echo $customer_address;?>" /><br>
                                    <div class="checkbox checkbox-default" style="margin-left:20px;margin-top:-25px">
                                        <input class="form-check-input styled" type="checkbox" id="ignore_customer_address_error" name="ignore_customer_address_error" />
                                        <label for="ignore_customer_address_error"><span class="inst">No need for a number</span></label>
                                    </div>
                                    <?php echo Form::displayError('customer_address');?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">Address Line 2</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control customer" name="customer_address2" id="customer_address2" value="<?php echo $customer_address2;?>" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">Suburb/Town</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control customer" name="customer_suburb" id="customer_suburb" value="<?php echo $customer_suburb;?>" />
                                    <?php echo Form::displayError('customer_suburb');?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">State</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control customer" name="customer_state" id="customer_state" value="<?php echo $customer_state;?>" />
                                    <span class="inst">for AU addresses use VIC, NSW, QLD, ACT, TAS, WA, SA, NT only</span>
                                    <?php echo Form::displayError('customer_state');?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">Postcode</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control customer" name="customer_postcode" id="customer_postcode" value="<?php echo $customer_postcode;?>" />
                                    <?php echo Form::displayError('customer_postcode');?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">Country</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control customer" name="customer_country" id="customer_country" value="<?php echo $customer_country;?>" />
                                    <span class="inst">use the 2 letter ISO code</span>
                                    <?php echo Form::displayError('customer_country');?>
                                </div>
                            </div>
                            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                            <input type="hidden" name="id" value="<?php echo $job['id'];?>" >
                        </form>
                    </div>
                    <div class="card-footer text-right">
                        <button id="customer_details_update_submitter" class="btn btn-outline-secondary">Save Changes</button>
                    </div>
                </div>
            </div>
<!------------------------------------------------------------------------------------------------------------------------------------------->
<!-------------------------------------------------     Finisher Details     ------------------------------------------------------------>
<!------------------------------------------------------------------------------------------------------------------------------------------->
            <?php
           //echo "<pre>",print_r($_SESSION),"</pre>";
            $f = 0;
            while($f < $finisher_count):
                $fn = ($f > 0)? ($f + 1) : "";
                $fwn = ucwords($nf->format($f+1));?>
                <div class="col-sm-12 col-md-6 mb-3">
                    <div class="card h-100 border-secondary order-card" id="finisher<?php echo $fn;?>details">
                        <div class="card-header bg-secondary text-white">
                            Finisher <?php echo $fwn;?> Details
                        </div>
                        <div class="card-body">
                            <?php if(isset($_SESSION['jobfinisher'.$fn.'detailsfeedback'])) :?>
                                <div class='feedbackbox'><?php echo Session::getAndDestroy('jobfinisher'.$fn.'detailsfeedback');?></div>
                            <?php endif; ?>
                            <?php if(isset($_SESSION['jobfinisher'.$fn.'detailserrorfeedback'])) :?>
                                <div class='errorbox'><?php echo Session::getAndDestroy('jobfinisher'.$fn.'detailserrorfeedback');?></div>
                            <?php endif; ?>
                            <form id="finisher<?php echo $fn;?>_details_update" method="post" action="/form/procJobfinisherUpdate">
                                <div class="row form-group">
                                    <label class="col-md-4 col-form-label">Expected Delivery Date</label>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="date_ed<?php echo $fn;?>" id="date_ed<?php echo $fn;?>" value="<?php if(!empty(${'date_ed'.$fn})) echo date('d/m/Y', ${'date_ed'.$fn});?>" />
                                            <div class="input-group-append">
                                                <span id="date_ed<?php echo $fn;?>_calendar" class="input-group-text"><i class="fad fa-calendar-alt"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="date_ed<?php echo $fn;?>_value" id="date_ed<?php echo $fn;?>_value" value="<?php echo ${'date_ed'.$fn};?>" />
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4">Finisher Name</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" name="finisher<?php echo $fn;?>_name" id="finisher<?php echo $fn;?>_name" value="<?php echo ${'finisher'.$fn.'_name'};?>" />
                                        <input type="hidden" name="finisher<?php echo $fn;?>_id" id="finisher<?php echo $fn;?>_id" value="<?php echo $job['finisher'.$fn.'_id'];?>" />
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label class="col-md-4">Purchase Order No.</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" name="finisher<?php echo $fn;?>_po" id="finisher<?php echo $fn;?>_po" value="<?php echo ${'finisher'.$fn.'_po'};?>" />
                                    </div>
                                </div>
                                <div class="form-group row ">
                                    <label class="col-md-4">Contact</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control finisher<?php echo $fn;?>" name="finisher<?php echo $fn;?>_contact" id="finisher<?php echo $fn;?>_contact" value="<?php echo ${'finisher'.$fn.'_contact'};?>" />
                                    </div>
                                </div>
                                <div class="form-group row ">
                                    <label class="col-md-4">Email</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control finisher<?php echo $fn;?> email" name="finisher<?php echo $fn;?>_email" id="finisher<?php echo $fn;?>_email" value="<?php echo ${'finisher'.$fn.'_email'};?>" />
                                        <?php echo Form::displayError('finisher{$fn}_email');?>
                                    </div>
                                </div>
                                <div class="form-group row ">
                                    <label class="col-md-4">Phone</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control finisher<?php echo $fn;?>" name="finisher<?php echo $fn;?>_phone" id="finisher<?php echo $fn;?>_phone" value="<?php echo ${'finisher'.$fn.'_phone'};?>" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label">Address Line 1</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control finisher<?php echo $fn;?>" name="finisher<?php echo $fn;?>_address" id="finisher<?php echo $fn;?>_address" value="<?php echo ${'finisher'.$fn.'_address'};?>" /><br>
                                        <div class="checkbox checkbox-default" style="margin-left:20px;margin-top:-25px">
                                            <input class="form-check-input styled" type="checkbox" id="ignore_finisher<?php echo $fn;?>_address_error" name="ignore_finisher<?php echo $fn;?>_address_error" />
                                            <label for="ignore_finisher<?php echo $fn;?>_address_error"><span class="inst">No need for a number</span></label>
                                        </div>
                                        <?php echo Form::displayError('finisher{$fn}_address');?>
                                    </div>

                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label">Address Line 2</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control finisher<?php echo $fn;?>" name="finisher<?php echo $fn;?>_address2" id="finisher<?php echo $fn;?>_address2" value="<?php echo ${'finisher'.$fn.'_address2'};?>" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label">Suburb/Town</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control finisher<?php echo $fn;?>" name="finisher<?php echo $fn;?>_suburb" id="finisher<?php echo $fn;?>_suburb" value="<?php echo ${'finisher'.$fn.'_suburb'};?>" />
                                        <?php echo Form::displayError('finisher{$fn}_suburb');?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label">State</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control finisher<?php echo $fn;?>" name="finisher<?php echo $fn;?>_state" id="finisher<?php echo $fn;?>_state" value="<?php echo ${'finisher'.$fn.'_state'};?>" />
                                        <span class="inst">for AU addresses use VIC, NSW, QLD, ACT, TAS, WA, SA, NT only</span>
                                        <?php echo Form::displayError('finisher{$fn}_state');?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label">Postcode</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control finisher<?php echo $fn;?>" name="finisher<?php echo $fn;?>_postcode" id="finisher<?php echo $fn;?>_postcode" value="<?php echo ${'finisher'.$fn.'_postcode'};?>" />
                                        <?php echo Form::displayError('finisher{$fn}_postcode');?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label">Country</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control finisher<?php echo $fn;?>" name="finisher<?php echo $fn;?>_country" id="finisher<?php echo $fn;?>_country" value="<?php echo ${'finisher'.$fn.'_country'};?>" />
                                        <span class="inst">use the 2 letter ISO code</span>
                                        <?php echo Form::displayError('finisher{$fn}_country');?>
                                    </div>
                                </div>
                                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                                <input type="hidden" name="job_id" value="<?php echo $job['id'];?>" >
                                <input type="hidden" name="finisher_number" value="<?php echo ($f + 1);?>" >
                            </form>
                        </div>
                        <div class="card-footer text-right">
                            <button id="finisher<?php echo $fn;?>_details_update_submitter" class="btn btn-outline-secondary">Save Changes</button>
                        </div>
                    </div>
                </div>
                <?php ++$f;
            endwhile;?>
        </div>
    </div>
</div>