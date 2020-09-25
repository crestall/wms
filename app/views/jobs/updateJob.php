<?php
$job_id = (!empty(Form::value('job_id')))? Form::value('job_id'):$job['job_id'];
$previous_job_id = (!empty(Form::value('previous_job_id')))? Form::value('previous_job_id'):$job['previous_job_id'];
$status_id = (!empty(Form::value('status_id')))? Form::value('status_id'):$job['status_id'];
$salesrep_id = (!empty(Form::value('salesrep_id')))? Form::value('salesrep_id'):$job['salesrep_id'];
$date_entered = (!empty(Form::value('date_entered_value')))? Form::value('date_entered_value'): $job['created_date'];
$date_due = (!empty(Form::value('date_due_value')))? Form::value('date_due_value'): $job['due_date'];
$designer = (!empty(Form::value('designer')))? Form::value('designer'):$job['designer'];
$description = (!empty(Form::value('description')))? Form::value('description'):$job['description'];
$notes = (!empty(Form::value('notes')))? Form::value('notes'):$job['notes'];
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
if(count($finisher))
{
    //echo "<pre>",print_r($finisher),"</pre>"; //die();
    $finisher_name = ucwords((!empty(Form::value('finisher_name')))? Form::value('finisher_name'):$finisher['name']);
    $finisher_contact = (!empty(Form::value('finisher_contact')))? Form::value('finisher_contact'):$finisher['contact'];
    $finisher_email = (!empty(Form::value('finisher_email')))? Form::value('finisher_email'):$finisher['email'];
    $finisher_phone = (!empty(Form::value('finisher_phone')))? Form::value('finisher_phone'):$finisher['phone'];
    $finisher_address = (!empty(Form::value('finisher_address')))? Form::value('finisher_address'):$finisher['address'];
    $finisher_address2 = (!empty(Form::value('finisher_address2')))? Form::value('finisher_address2'):$finisher['address_2'];
    $finisher_suburb = (!empty(Form::value('finisher_suburb')))? Form::value('finisher_suburb'):$finisher['suburb'];
    $finisher_state = (!empty(Form::value('finisher_state')))? Form::value('finisher_state'):$finisher['state'];
    $finisher_postcode = (!empty(Form::value('finisher_postcode')))? Form::value('finisher_postcode'):$finisher['postcode'];
    $finisher_country = (!empty(Form::value('finisher_country')))? Form::value('finisher_country'):$finisher['country'];

}
else
{
    $finisher_name = Form::value('finisher_name');
    $finisher_contact = Form::value('finisher_contact');
    $finisher_email = Form::value('finisher_email');
    $finisher_phone = Form::value('finisher_phone');
    $finisher_address = Form::value('finisher_address');
    $finisher_address2 =  Form::value('finisher_address2');
    $finisher_suburb = Form::value('finisher_suburb');
    $finisher_state = Form::value('finisher_state');
    $finisher_postcode = Form::value('finisher_postcode');
    $finisher_country = Form::value('finisher_country');
}
if(count($finisher2))
{
    $finisher2_name = ucwords((!empty(Form::value('finisher2_name')))? Form::value('finisher2_name'):$finisher2['name']);
    $finisher2_contact = (!empty(Form::value('finisher2_contact')))? Form::value('finisher2_contact'):$finisher2['contact'];
    $finisher2_email = (!empty(Form::value('finisher2_email')))? Form::value('finisher2_email'):$finisher2['email'];
    $finisher2_phone = (!empty(Form::value('finisher2_phone')))? Form::value('finisher2_phone'):$finisher2['phone'];
    $finisher2_address = (!empty(Form::value('finisher2_address')))? Form::value('finisher2_address'):$finisher2['address'];
    $finisher2_address2 = (!empty(Form::value('finisher2_address2')))? Form::value('finisher2_address2'):$finisher2['address_2'];
    $finisher2_suburb = (!empty(Form::value('finisher2_suburb')))? Form::value('finisher2_suburb'):$finisher2['suburb'];
    $finisher2_state = (!empty(Form::value('finisher2_state')))? Form::value('finisher2_state'):$finisher2['state'];
    $finisher2_postcode = (!empty(Form::value('finisher2_postcode')))? Form::value('finisher2_postcode'):$finisher2['postcode'];
    $finisher2_country = (!empty(Form::value('finisher2_country')))? Form::value('finisher2_country'):$finisher2['country'];
}
else
{
    $finisher2_name = Form::value('finisher2_name');
    $finisher2_contact = Form::value('finisher2_contact');
    $finisher2_email = Form::value('finisher2_email');
    $finisher2_phone = Form::value('finisher2_phone');
    $finisher2_address = Form::value('finisher2_address');
    $finisher2_address2 =  Form::value('finisher2_address2');
    $finisher2_suburb = Form::value('finisher2_suburb');
    $finisher2_state = Form::value('finisher2_state');
    $finisher2_postcode = Form::value('finisher2_postcode');
    $finisher2_country = Form::value('finisher2_country');
}

?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php //echo "JOB<pre>",print_r($job),"</pre>";?>
        <?php //echo "CUSTOMER<pre>",print_r($customer),"</pre>";?>
        <?php //echo "finisher<pre>",print_r($finisher),"</pre>";?>
        <div class="row">
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
                                    <input type="text" class="form-control required number" name="job_id" id="job_id" value="<?php echo $job_id;?>" />
                                    <?php echo Form::displayError('job_id');?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4">Related Job Id</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control number" name="previous_job_id" id="previous_job_id" value="<?php echo $previous_job_id;?>" />
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
                            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                            <input type="hidden" name="id" value="<?php echo $job['id'];?>" >
                        </form>
                    </div>
                    <div class="card-footer text-right">
                        <button id="job_details_update_submitter" class="btn btn-outline-secondary">Save Changes</button>
                    </div>
                </div>
            </div>
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
            <div class="col-sm-12 col-md-6 mb-3">
                <div class="card h-100 border-secondary order-card" id="finisherdetails">
                    <div class="card-header bg-secondary text-white">
                        Finisher One Details
                    </div>
                    <div class="card-body">
                        <?php if(isset($_SESSION['jobfinisherdetailsfeedback'])) :?>
                            <div class='feedbackbox'><?php echo Session::getAndDestroy('jobfinisherdetailsfeedback');?></div>
                        <?php endif; ?>
                        <?php if(isset($_SESSION['jobfinisherdetailserrorfeedback'])) :?>
                            <div class='errorbox'><?php echo Session::getAndDestroy('jobfinisherdetailserrorfeedback');?></div>
                        <?php endif; ?>
                        <form id="finisher_details_update" method="post" action="/form/procJobfinisherUpdate">
                            <div class="row form-group">
                                <label class="col-md-4 col-form-label">Expected Delivery Date</label>
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="date_ed" id="date_ed" value="<?php if(!empty($date_ed)) echo date('d/m/Y', $date_ed);?>" />
                                        <div class="input-group-append">
                                            <span id="date_ed_calendar" class="input-group-text"><i class="fad fa-calendar-alt"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="date_ed_value" id="date_ed_value" value="<?php echo $date_ed;?>" />
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4">Finisher Name</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="finisher_name" id="finisher_name" value="<?php echo $finisher_name;?>" />
                                    <input type="hidden" name="finisher_id" id="finisher_id" value="<?php echo $job['finisher_id'];?>" />
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-4">Contact</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control finisher" name="finisher_contact" id="finisher_contact" value="<?php echo $finisher_contact;?>" />
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-4">Email</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control finisher email" name="finisher_email" id="finisher_email" value="<?php echo $finisher_email;?>" />
                                    <?php echo Form::displayError('finisher_email');?>
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-4">Phone</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control finisher" name="finisher_phone" id="finisher_phone" value="<?php echo $finisher_phone;?>" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">Address Line 1</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control finisher" name="finisher_address" id="finisher_address" value="<?php echo $finisher_address;?>" /><br>
                                    <div class="checkbox checkbox-default" style="margin-left:20px;margin-top:-25px">
                                        <input class="form-check-input styled" type="checkbox" id="ignore_finisher_address_error" name="ignore_finisher_address_error" />
                                        <label for="ignore_finisher_address_error"><span class="inst">No need for a number</span></label>
                                    </div>
                                    <?php echo Form::displayError('finisher_address');?>
                                </div>

                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">Address Line 2</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control finisher" name="finisher_address2" id="finisher_address2" value="<?php echo $finisher_address2;?>" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">Suburb/Town</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control finisher" name="finisher_suburb" id="finisher_suburb" value="<?php echo $finisher_suburb;?>" />
                                    <?php echo Form::displayError('finisher_suburb');?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">State</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control finisher" name="finisher_state" id="finisher_state" value="<?php echo $finisher_state;?>" />
                                    <span class="inst">for AU addresses use VIC, NSW, QLD, ACT, TAS, WA, SA, NT only</span>
                                    <?php echo Form::displayError('finisher_state');?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">Postcode</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control finisher" name="finisher_postcode" id="finisher_postcode" value="<?php echo $finisher_postcode;?>" />
                                    <?php echo Form::displayError('finisher_postcode');?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">Country</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control finisher" name="finisher_country" id="finisher_country" value="<?php echo $finisher_country;?>" />
                                    <span class="inst">use the 2 letter ISO code</span>
                                    <?php echo Form::displayError('finisher_country');?>
                                </div>
                            </div>
                            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                            <input type="hidden" name="id" value="<?php echo $job['id'];?>" >
                        </form>
                    </div>
                    <div class="card-footer text-right">
                        <button id="finisher_details_update_submitter" class="btn btn-outline-secondary">Save Changes</button>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 mb-3">
                <div class="card h-100 border-secondary order-card" id="finisher2details">
                    <div class="card-header bg-secondary text-white">
                        Finisher Two Details
                    </div>
                    <div class="card-body">
                        <?php if(isset($_SESSION['jobfinisher2detailsfeedback'])) :?>
                            <div class='feedbackbox'><?php echo Session::getAndDestroy('jobfinisher2detailsfeedback');?></div>
                        <?php endif; ?>
                        <?php if(isset($_SESSION['jobfinisher2detailserrorfeedback'])) :?>
                            <div class='errorbox'><?php echo Session::getAndDestroy('jobfinisher2detailserrorfeedback');?></div>
                        <?php endif; ?>
                        <form id="finisher2_details_update" method="post" action="/form/procJobfinisherUpdate">
                            <div class="row form-group">
                                <label class="col-md-4 col-form-label">Expected Delivery Date</label>
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="date_ed" id="date_ed" value="<?php if(!empty($date_ed)) echo date('d/m/Y', $date_ed);?>" />
                                        <div class="input-group-append">
                                            <span id="date_ed_calendar" class="input-group-text"><i class="fad fa-calendar-alt"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="date_ed_value" id="date_ed_value" value="<?php echo $date_ed;?>" />
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4">Finisher Name</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="finisher2_name" id="finisher2_name" value="<?php echo $finisher2_name;?>" />
                                    <input type="hidden" name="finisher2_id" id="finisher2_id" value="<?php echo $job['finisher2_id'];?>" />
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-4">Contact</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control finisher2" name="finisher2_contact" id="finisher2_contact" value="<?php echo $finisher2_contact;?>" />
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-4">Email</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control finisher2 email" name="finisher2_email" id="finisher2_email" value="<?php echo $finisher2_email;?>" />
                                    <?php echo Form::displayError('finisher2_email');?>
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-4">Phone</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control finisher2" name="finisher2_phone" id="finisher2_phone" value="<?php echo $finisher2_phone;?>" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">Address Line 1</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control finisher2" name="finisher2_address" id="finisher2_address" value="<?php echo $finisher2_address;?>" /><br>
                                    <div class="checkbox checkbox-default" style="margin-left:20px;margin-top:-25px">
                                        <input class="form-check-input styled" type="checkbox" id="ignore_finisher2_address_error" name="ignore_finisher2_address_error" />
                                        <label for="ignore_finisher2_address_error"><span class="inst">No need for a number</span></label>
                                    </div>
                                    <?php echo Form::displayError('finisher2_address');?>
                                </div>

                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">Address Line 2</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control finisher2" name="finisher2_address2" id="finisher2_address2" value="<?php echo $finisher2_address2;?>" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">Suburb/Town</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control finisher2" name="finisher2_suburb" id="finisher2_suburb" value="<?php echo $finisher2_suburb;?>" />
                                    <?php echo Form::displayError('finisher2_suburb');?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">State</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control finisher2" name="finisher2_state" id="finisher2_state" value="<?php echo $finisher2_state;?>" />
                                    <span class="inst">for AU addresses use VIC, NSW, QLD, ACT, TAS, WA, SA, NT only</span>
                                    <?php echo Form::displayError('finisher2_state');?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">Postcode</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control finisher2" name="finisher2_postcode" id="finisher2_postcode" value="<?php echo $finisher2_postcode;?>" />
                                    <?php echo Form::displayError('finisher2_postcode');?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">Country</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control finisher2" name="finisher2_country" id="finisher2_country" value="<?php $finisher2_country;?>" />
                                    <span class="inst">use the 2 letter ISO code</span>
                                    <?php echo Form::displayError('finisher2_country');?>
                                </div>
                            </div>
                            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                            <input type="hidden" name="id" value="<?php echo $job['id'];?>" >
                        </form>
                    </div>
                    <div class="card-footer text-right">
                        <button id="finisher2_details_update_submitter" class="btn btn-outline-secondary">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>