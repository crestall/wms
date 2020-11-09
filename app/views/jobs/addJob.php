<?php
$ship_to = Form::value('ship_to');
$address = Form::value('address');
$address2 = Form::value('address2');
$suburb = Form::value('suburb');
$state = Form::value('state');
$postcode = Form::value('postcode');
$country = (empty(Form::value('country')))? "AU" : Form::value('country');
$date_entered = (empty(Form::value('date_entered_value')))? time() : Form::value('date_entered_value');
$date_due = (empty(Form::value('date_due_value')))? strtotime('+7 days') : Form::value('date_due_value');
$date_ed = (empty(Form::value('date_ed_value')))? "" : date('d/m/Y', Form::value('date_ed_value'));
$date_ed2 = (empty(Form::value('date_ed2_value')))? "" : date('d/m/Y', Form::value('date_ed2_value'));
$date_ed3 = (empty(Form::value('date_ed3_value')))? "" : date('d/m/Y', Form::value('date_ed3_value'));
if(Session::getAndDestroy('show_customer_address'))
{
    $customer_collapse = "collapse show";
    $customer_aria_expanded = "true";
}
else
{
    $customer_collapse = "collapse";
    $customer_aria_expanded = "false";
}
if(Session::getAndDestroy('show_finisher_address'))
{
    $finisher_collapse = "collapse show";
    $finisher_aria_expanded = "true";
}
else
{
    $finisher_collapse = "collapse";
    $finisher_aria_expanded = "false";
}
if(Session::getAndDestroy('show_finisher2_address'))
{
    $finisher2_collapse = "collapse show";
    $finisher2_aria_expanded = "true";
}
else
{
    $finisher2_collapse = "collapse";
    $finisher2_aria_expanded = "false";
}
if(Session::getAndDestroy('show_finisher3_address'))
{
    $finisher3_collapse = "collapse show";
    $finisher3_aria_expanded = "true";
}
else
{
    $finisher3_collapse = "collapse";
    $finisher3_aria_expanded = "false";
}
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <form id="add_production_job" method="post" action="/form/procAddProductionJob">
            <div class="border border-secondary p-3 m-3 rounded bg-light">
                <h3>Job Details</h3>
                <div class="form-group row">
                    <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Job Id</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control required number" name="job_id" id="job_id" value="<?php echo Form::value('job_id');?>" />
                        <?php echo Form::displayError('job_id');?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3">Related Job Id</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control number" name="previous_job_id" id="previous_job_id" value="<?php echo Form::value('previous_job_id');?>" />
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Status</label>
                    <div class="col-md-4">
                        <select id="status_id" class="form-control selectpicker" name="status_id" data-style="btn-outline-secondary"><option value="0">-- Select One --</option><?php echo $this->controller->jobstatus->getSelectJobStatus(Form::value('status_id'));?></select>
                        <?php echo Form::displayError('status_id');?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3">FSG Contact</label>
                    <div class="col-md-4">
                        <select id="salesrep_id" class="form-control selectpicker" name="salesrep_id" data-style="btn-outline-secondary"><option value="0">-- Select One --</option><?php echo $this->controller->salesrep->getSelectSalesReps(Form::value('salesrep_id'));?></select>
                    </div>
                </div>
                <div class="row form-group">
                    <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Date Entered</label>
                    <div class="col-md-3">
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
                    <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Due Date</label>
                    <div class="col-md-3">
                        <div class="input-group">
                            <input type="text" class="form-control" name="date_due" id="date_due" value="<?php echo date('d/m/Y', $date_due);?>" />
                            <div class="input-group-append">
                                <span id="date_due_calendar" class="input-group-text"><i class="fad fa-calendar-alt"></i></span>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="date_due_value" id="date_due_value" value="<?php echo $date_due;?>" />
                </div>
                <div class="form-group row">
                    <label class="col-md-3">Designer</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="designer" id="designer" value="<?php echo Form::value('designer');?>" />
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Description</label>
                    <div class="col-md-4">
                        <textarea name="description" id="description" class="form-control required" rows="4"><?php echo Form::value('description');?></textarea>
                        <?php echo Form::displayError('description');?>
                    </div>
                </div>
                <div class="form-group row">
                        <label class="col-md-3">Notes/Comments</label>
                        <div class="col-md-4">
                            <textarea name="notes" id="notes" class="form-control" rows="3"><?php echo Form::value('notes');?></textarea>
                        </div>
                </div>
            </div>
            <div class="border border-secondary p-3 m-3 rounded bg-light">
                <h3>Customer Details</h3>
                <div class="form-group row mb-3">
                    <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Customer Name</label>
                    <div class="col-md-4">
                        <input type="text" class="required form-control" name="customer_name" id="customer_name" value="<?php echo Form::value('customer_name');?>" />
                        <input type="hidden" name="customer_id" id="customer_id" value="0" />
                        <?php echo Form::displayError('customer_name');?>
                    </div>
                </div>
                <div class="form-group row ">
                    <label class="col-lg-1 col-md-2 mb-md-3">Contact</label>
                    <div class="col-lg-3 col-md-4">
                        <input type="text" class="form-control customer" name="customer_contact" id="customer_contact" value="<?php echo Form::value('customer_contact');?>" />
                    </div>
                    <label class="col-lg-1 col-md-2">Email</label>
                    <div class="col-lg-3 col-md-4">
                        <input type="text" class="form-control customer email" name="customer_email" id="customer_email" value="<?php echo Form::value('customer_email');?>" />
                        <?php echo Form::displayError('customer_email');?>
                    </div>
                    <label class="col-lg-1 col-md-2">Phone</label>
                    <div class="col-lg-3 col-md-4">
                        <input type="text" class="form-control customer" name="customer_phone" id="customer_phone" value="<?php echo Form::value('customer_phone');?>" />
                    </div>
                </div>
                <div class="row">
                    <div class="col text-right">
                        <a  id="customer_address_toggle" class="btn btn-outline-secondary" data-toggle="collapse" href="#customer_address_holder" role="button" aria-expanded="<?php echo $customer_aria_expanded;?>" aria-controls="customer_address_holder"> </a>
                    </div>
                </div>
                <div id="customer_address_holder" class="<?php echo $customer_collapse;?> mt-3">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Address Line 1</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control customer" name="customer_address" id="customer_address" value="<?php echo Form::value('customer_address');?>" />
                            <?php echo Form::displayError('customer_address');?>
                        </div>
                        <div class="col-md-3 checkbox checkbox-default">
                            <input class="form-check-input styled" type="checkbox" id="ignore_customer_address_error" name="ignore_customer_address_error" />
                            <label for="ignore_customer_address_error">No need for a number</label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Address Line 2</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control customer" name="customer_address2" id="customer_address2" value="<?php echo Form::value('customer_address2');?>" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Suburb/Town</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control customer" name="customer_suburb" id="customer_suburb" value="<?php echo Form::value('customer_suburb');?>" />
                            <?php echo Form::displayError('customer_suburb');?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">State</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control customer" name="customer_state" id="customer_state" value="<?php echo Form::value('customer_state');?>" />
                            <span class="inst">for AU addresses use VIC, NSW, QLD, ACT, TAS, WA, SA, NT only</span>
                            <?php echo Form::displayError('customer_state');?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Postcode</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control customer" name="customer_postcode" id="customer_postcode" value="<?php echo Form::value('customer_postcode');?>" />
                            <?php echo Form::displayError('customer_postcode');?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Country</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control customer" name="customer_country" id="customer_country" value="<?php echo Form::value('customer_country');?>" />
                            <span class="inst">use the 2 letter ISO code</span>
                            <?php echo Form::displayError('customer_country');?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="border border-secondary p-3 m-3 rounded bg-light">
                <h3>Delivery Details</h3>
                <div class="form-group row">
                    <div class=" offset-1 col-5 checkbox checkbox-default ">
                        <input class="form-check-input styled send_to_address" type="checkbox" id="send_to_customer" name="send_to_customer" checked />
                        <label for="send_to_customer">Send to Customer</label>
                    </div>
                    <div class="col-6 checkbox checkbox-default">
                        <input class="form-check-input styled send_to_address" type="checkbox" id="send_to_finisher" name="send_to_finisher" />
                        <label for="send_to_finisher">Send to Finisher One</label>
                    </div>
                    <div class="offset-1 col-5 checkbox checkbox-default">
                        <input class="form-check-input styled send_to_address" type="checkbox" id="send_to_finisher2" name="send_to_finisher2" />
                        <label for="send_to_finisher2">Send to Finisher Two</label>
                    </div>
                    <div class="col-md-6 checkbox checkbox-default">
                        <input class="form-check-input styled send_to_address" type="checkbox" id="send_to_finisher3" name="send_to_finisher3" />
                        <label for="send_to_finisher3">Send to Finisher Three</label>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Deliver To</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control required" name="ship_to" id="ship_to" value="<?php echo $ship_to;?>" />
                        <?php echo Form::displayError('ship_to');?>
                    </div>
                </div>
                <?php include(Config::get('VIEWS_PATH')."forms/address_auonly.php");?>
            </div>
            <div class="border border-secondary p-3 m-3 rounded bg-light">
                <h3>Finisher One Details</h3>
                <div class="row form-group">
                    <label class="col-md-3 col-form-label">Expected Delivery Date</label>
                    <div class="col-md-3">
                        <div class="input-group">
                            <input type="text" class="form-control" name="date_ed" id="date_ed" value="<?php echo $date_ed;?>" />
                            <div class="input-group-append">
                                <span id="date_ed_calendar" class="input-group-text"><i class="fad fa-calendar-alt"></i></span>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="date_ed_value" id="date_ed_value" value="<?php echo Form::value('date_ed_value');?>" />
                </div>
                <div class="form-group row mb-3">
                    <label class="col-md-3">Finisher Name</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="finisher_name" id="finisher_name" value="<?php echo Form::value('finisher_name');?>" />
                        <input type="hidden" name="finisher_id" id="finisher_id" value="0" />
                    </div>
                </div>
                <div class="form-group row ">
                    <label class="col-lg-1 col-md-2 mb-md-3">Contact</label>
                    <div class="col-lg-3 col-md-4">
                        <input type="text" class="form-control finisher" name="finisher_contact" id="finisher_contact" value="<?php echo Form::value('finisher_contact');?>" />
                    </div>
                    <label class="col-lg-1 col-md-2">Email</label>
                    <div class="col-lg-3 col-md-4">
                        <input type="text" class="form-control finisher email" name="finisher_email" id="finisher_email" value="<?php echo Form::value('finisher_email');?>" />
                        <?php echo Form::displayError('finisher_email');?>
                    </div>
                    <label class="col-lg-1 col-md-2">Phone</label>
                    <div class="col-lg-3 col-md-4">
                        <input type="text" class="form-control finisher" name="finisher_phone" id="finisher_phone" value="<?php echo Form::value('finisher_phone');?>" />
                    </div>
                </div>
                <div class="row">
                    <div class="col text-right">
                        <a  id="finisher_address_toggle" class="btn btn-outline-secondary" data-toggle="collapse" href="#finisher_address_holder" role="button" aria-expanded="<?php echo $finisher_aria_expanded;?>" aria-controls="finisher_address_holder"> </a>
                    </div>
                </div>
                <div id="finisher_address_holder" class="<?php echo $finisher_collapse;?> mt-3">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Address Line 1</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control finisher" name="finisher_address" id="finisher_address" value="<?php echo Form::value('finisher_address');?>" />
                            <?php echo Form::displayError('finisher_address');?>
                        </div>
                        <div class="col-md-3 checkbox checkbox-default">
                            <input class="form-check-input styled" type="checkbox" id="ignore_finisher_address_error" name="ignore_finisher_address_error" />
                            <label for="ignore_finisher_address_error">No need for a number</label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Address Line 2</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control finisher" name="finisher_address2" id="finisher_address2" value="<?php echo Form::value('finisher_address2');?>" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Suburb/Town</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control finisher" name="finisher_suburb" id="finisher_suburb" value="<?php echo Form::value('finisher_suburb');?>" />
                            <?php echo Form::displayError('finisher_suburb');?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">State</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control finisher" name="finisher_state" id="finisher_state" value="<?php echo Form::value('finisher_state');?>" />
                            <span class="inst">for AU addresses use VIC, NSW, QLD, ACT, TAS, WA, SA, NT only</span>
                            <?php echo Form::displayError('finisher_state');?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Postcode</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control finisher" name="finisher_postcode" id="finisher_postcode" value="<?php echo Form::value('finisher_postcode');?>" />
                            <?php echo Form::displayError('finisher_postcode');?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Country</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control finisher" name="finisher_country" id="finisher_country" value="<?php echo Form::value('finisher_country');?>" />
                            <span class="inst">use the 2 letter ISO code</span>
                            <?php echo Form::displayError('finisher_country');?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="border border-secondary p-3 m-3 rounded bg-light">
                <h3>Finisher Two Details</h3>
                <div class="row form-group">
                    <label class="col-md-3 col-form-label">Expected Delivery Date</label>
                    <div class="col-md-3">
                        <div class="input-group">
                            <input type="text" class="form-control" name="date_ed2" id="date_ed2" value="<?php echo $date_ed2;?>" />
                            <div class="input-group-append">
                                <span id="date_ed2_calendar" class="input-group-text"><i class="fad fa-calendar-alt"></i></span>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="date_ed2_value" id="date_ed2_value" value="<?php echo Form::value('date_ed2_value');?>" />
                </div>
                <div class="form-group row mb-3">
                    <label class="col-md-3">Finisher Name</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="finisher2_name" id="finisher2_name" value="<?php echo Form::value('finisher2_name');?>" />
                        <input type="hidden" name="finisher2_id" id="finisher2_id" value="0" />
                    </div>
                </div>
                <div class="form-group row ">
                    <label class="col-lg-1 col-md-2 mb-md-3">Contact</label>
                    <div class="col-lg-3 col-md-4">
                        <input type="text" class="form-control finisher2" name="finisher2_contact" id="finisher2_contact" value="<?php echo Form::value('finisher2_contact');?>" />
                    </div>
                    <label class="col-lg-1 col-md-2">Email</label>
                    <div class="col-lg-3 col-md-4">
                        <input type="text" class="form-control finisher2 email" name="finisher2_email" id="finisher2_email" value="<?php echo Form::value('finisher2_email');?>" />
                        <?php echo Form::displayError('finisher2_email');?>
                    </div>
                    <label class="col-lg-1 col-md-2">Phone</label>
                    <div class="col-lg-3 col-md-4">
                        <input type="text" class="form-control finisher2" name="finisher2_phone" id="finisher2_phone" value="<?php echo Form::value('finisher2_phone');?>" />
                    </div>
                </div>
                <div class="row">
                    <div class="col text-right">
                        <a  id="finisher2_address_toggle" class="btn btn-outline-secondary" data-toggle="collapse" href="#finisher2_address_holder" role="button" aria-expanded="<?php echo $finisher2_aria_expanded;?>" aria-controls="finisher2_address_holder"> </a>
                    </div>
                </div>
                <div id="finisher2_address_holder" class="<?php echo $finisher2_collapse;?> mt-3">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Address Line 1</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control finisher2" name="finisher2_address" id="finisher2_address" value="<?php echo Form::value('finisher2_address');?>" />
                            <?php echo Form::displayError('finisher2_address');?>
                        </div>
                        <div class="col-md-3 checkbox checkbox-default">
                            <input class="form-check-input styled" type="checkbox" id="ignore_finisher2_address_error" name="ignore_finisher2_address_error" />
                            <label for="ignore_finisher2_address_error">No need for a number</label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Address Line 2</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control finisher2" name="finisher2_address2" id="finisher2_address2" value="<?php echo Form::value('finisher2_address2');?>" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Suburb/Town</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control finisher2" name="finisher2_suburb" id="finisher2_suburb" value="<?php echo Form::value('finisher2_suburb');?>" />
                            <?php echo Form::displayError('finisher2_suburb');?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">State</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control finisher2" name="finisher2_state" id="finisher2_state" value="<?php echo Form::value('finisher2_state');?>" />
                            <span class="inst">for AU addresses use VIC, NSW, QLD, ACT, TAS, WA, SA, NT only</span>
                            <?php echo Form::displayError('finisher2_state');?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Postcode</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control finisher2" name="finisher2_postcode" id="finisher2_postcode" value="<?php echo Form::value('finisher2_postcode');?>" />
                            <?php echo Form::displayError('finisher2_postcode');?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Country</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control finisher2" name="finisher2_country" id="finisher2_country" value="<?php echo Form::value('finisher2_country');?>" />
                            <span class="inst">use the 2 letter ISO code</span>
                            <?php echo Form::displayError('finisher2_country');?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="border border-secondary p-3 m-3 rounded bg-light">
                <h3>Finisher Three Details</h3>
                <div class="row form-group">
                    <label class="col-md-3 col-form-label">Expected Delivery Date</label>
                    <div class="col-md-3">
                        <div class="input-group">
                            <input type="text" class="form-control" name="date_ed3" id="date_ed3" value="<?php echo $date_ed3;?>" />
                            <div class="input-group-append">
                                <span id="date_ed3_calendar" class="input-group-text"><i class="fad fa-calendar-alt"></i></span>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="date_ed3_value" id="date_ed3_value" value="<?php echo Form::value('date_ed3_value');?>" />
                </div>
                <div class="form-group row mb-3">
                    <label class="col-md-3">Finisher Name</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="finisher3_name" id="finisher3_name" value="<?php echo Form::value('finisher3_name');?>" />
                        <input type="hidden" name="finisher3_id" id="finisher3_id" value="0" />
                    </div>
                </div>
                <div class="form-group row ">
                    <label class="col-lg-1 col-md-2 mb-md-3">Contact</label>
                    <div class="col-lg-3 col-md-4">
                        <input type="text" class="form-control finisher3" name="finisher3_contact" id="finisher3_contact" value="<?php echo Form::value('finisher3_contact');?>" />
                    </div>
                    <label class="col-lg-1 col-md-2">Email</label>
                    <div class="col-lg-3 col-md-4">
                        <input type="text" class="form-control finisher3 email" name="finisher3_email" id="finisher3_email" value="<?php echo Form::value('finisher3_email');?>" />
                        <?php echo Form::displayError('finisher3_email');?>
                    </div>
                    <label class="col-lg-1 col-md-2">Phone</label>
                    <div class="col-lg-3 col-md-4">
                        <input type="text" class="form-control finisher3" name="finisher3_phone" id="finisher3_phone" value="<?php echo Form::value('finisher3_phone');?>" />
                    </div>
                </div>
                <div class="row">
                    <div class="col text-right">
                        <a  id="finisher3_address_toggle" class="btn btn-outline-secondary" data-toggle="collapse" href="#finisher3_address_holder" role="button" aria-expanded="<?php echo $finisher3_aria_expanded;?>" aria-controls="finisher3_address_holder"> </a>
                    </div>
                </div>
                <div id="finisher3_address_holder" class="<?php echo $finisher3_collapse;?> mt-3">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Address Line 1</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control finisher3" name="finisher3_address" id="finisher3_address" value="<?php echo Form::value('finisher3_address');?>" />
                            <?php echo Form::displayError('finisher3_address');?>
                        </div>
                        <div class="col-md-3 checkbox checkbox-default">
                            <input class="form-check-input styled" type="checkbox" id="ignore_finisher3_address_error" name="ignore_finisher3_address_error" />
                            <label for="ignore_finisher3_address_error">No need for a number</label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Address Line 2</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control finisher3" name="finisher3_address2" id="finisher3_address2" value="<?php echo Form::value('finisher3_address2');?>" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Suburb/Town</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control finisher3" name="finisher3_suburb" id="finisher3_suburb" value="<?php echo Form::value('finisher3_suburb');?>" />
                            <?php echo Form::displayError('finisher3_suburb');?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">State</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control finisher3" name="finisher3_state" id="finisher3_state" value="<?php echo Form::value('finisher3_state');?>" />
                            <span class="inst">for AU addresses use VIC, NSW, QLD, ACT, TAS, WA, SA, NT only</span>
                            <?php echo Form::displayError('finisher3_state');?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Postcode</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control finisher3" name="finisher3_postcode" id="finisher3_postcode" value="<?php echo Form::value('finisher3_postcode');?>" />
                            <?php echo Form::displayError('finisher3_postcode');?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Country</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control finisher3" name="finisher3_country" id="finisher3_country" value="<?php echo Form::value('finisher3_country');?>" />
                            <span class="inst">use the 2 letter ISO code</span>
                            <?php echo Form::displayError('finisher3_country');?>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <div class="form-group row">
                <div class="col-md-4 offset-6 offset-md-8">
                    <button type="submit" class="btn btn-lg btn-outline-secondary" id="submitter">Add This Job</button>
                </div>
            </div>
        </form>
    </div>
</div>