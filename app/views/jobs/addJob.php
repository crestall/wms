<?php
$ship_to = Form::value('ship_to');
$attention = Form::value('attention');
$address = Form::value('address');
$address2 = Form::value('address2');
$suburb = Form::value('suburb');
$state = Form::value('state');
$postcode = Form::value('postcode');
$country = (empty(Form::value('country')))? "AU" : Form::value('country');
$date_entered = (empty(Form::value('date_entered_value')))? time() : Form::value('date_entered_value');
$date_due = (empty(Form::value('date_due_value')))? strtotime('+7 days') : Form::value('date_due_value');
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
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <form id="add_production_job" method="post" action="/form/procAddProductionJob">
<!------------------------------------------------------------------------------------------------------------------------------------------->
<!-------------------------------------------------     Job Details     --------------------------------------------------------------------->
<!------------------------------------------------------------------------------------------------------------------------------------------->
            <div class="border border-secondary p-3 m-3 rounded bg-light">
                <h3>Job Details</h3>
                <div class="form-group row">
                    <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Job Id</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control required" name="job_id" id="job_id" value="<?php echo Form::value('job_id');?>" />
                        <?php echo Form::displayError('job_id');?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3">Previous Job Id</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="previous_job_id" id="previous_job_id" value="<?php echo Form::value('previous_job_id');?>" />
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3">Related Job Id</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="related_job_id" id="related_job_id" value="<?php echo Form::value('related_job_id');?>" />
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3">Priority</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="priority" id="priority" value="<?php echo Form::value('priority');?>" />
                    </div>
                    <?php echo Form::displayError('priority');?>
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
                <div class="form-group row custom-control custom-checkbox custom-control-right">
                    <input class="custom-control-input" type="checkbox" id="strict_dd" name="strict_dd"  />
                    <label class="custom-control-label col-md-3" for="strict_dd">Strict Due Date</label>
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
<!------------------------------------------------------------------------------------------------------------------------------------------->
<!-------------------------------------------------     Customer Details     ---------------------------------------------------------------->
<!------------------------------------------------------------------------------------------------------------------------------------------->
                <h3>Customer Details</h3>
                <div class="form-group row mb-3">
                    <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Customer Name</label>
                    <div class="col-md-4">
                        <input type="text" class="required form-control" name="customer_name" id="customer_name" value="<?php echo Form::value('customer_name');?>" />
                        <input type="hidden" name="customer_id" id="customer_id" value="0" />
                        <?php echo Form::displayError('customer_name');?>
                    </div>
                </div>
                <div class="form-group row custom-control custom-checkbox custom-control-right">
                    <input class="custom-control-input send_to_address" type="checkbox" id="send_to_customer" name="send_to_customer" />
                    <label class="custom-control-label col-md-3" for="send_to_customer">Send Job To Customer</label>
                </div>
                <div class="p-3 pb-0 mb-2 rounded-top mid-grey">
                    <div class="form-group row">
                        <h4 class="col-md-4">Contact Details</h4>
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
<!------------------------------------------------------------------------------------------------------------------------------------------->
<!-------------------------------------------------     The Finishers     ---------------------------------------------------------------->
<!------------------------------------------------------------------------------------------------------------------------------------------->
            <div class="border border-secondary p-3 m-3 rounded bg-light">
                <div class="row mb-0">
                    <div class="col-md-8">
                        <h3>Finisher(s) Details</h3>
                    </div>
                    <div class="col-md-4">
                        <a class="add-finisher" style="cursor:pointer" title="Add Another Finisher"><h4><i class="fad fa-plus-square text-success"></i> Add A Finisher</a></h4>
                    </div>
                </div>
                <div id="finishers_holder"></div>
            </div>
<!------------------------------------------------------------------------------------------------------------------------------------------->
<!-------------------------------------------------     Delivery Details     ---------------------------------------------------------------->
<!------------------------------------------------------------------------------------------------------------------------------------------->
            <div class="border border-secondary p-3 m-3 rounded bg-light">
                <h3>Delivery Details</h3>
                <div class="form-group row custom-control custom-checkbox custom-control-right">
                    <input class="custom-control-input send_to_address" type="checkbox" id="held_in_store" name="held_in_store" />
                    <label class="custom-control-label col-md-3" for="held_in_store">Hold Job In Store</label>
                </div>
                <div id="delivery_address_holder">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Deliver To</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control required" name="ship_to" id="ship_to" value="<?php echo $ship_to;?>" />
                            <?php echo Form::displayError('ship_to');?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Attention</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="attention" id="attention" value="<?php echo $attention;?>" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Delivery Instructions</label>
                        <div class="col-md-4">
                            <textarea class="form-control" name="delivery_instructions" id="delivery_instructions" placeholder="Instructions For Driver"><?php echo Form::value('delivery_instructions');?></textarea>
                        </div>
                    </div>
                    <?php include(Config::get('VIEWS_PATH')."forms/address_auonly.php");?>
                </div>
            </div>
<!------------------------------------------------------------------------------------------------------------------------------------------->
<!-------------------------------------------------     Form Submission     ---------------------------------------------------------------->
<!------------------------------------------------------------------------------------------------------------------------------------------->
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <div class="form-group row">
                <div class="col-md-4 offset-6 offset-md-8">
                    <button type="submit" class="btn btn-lg btn-outline-secondary" id="submitter">Add This Job</button>
                </div>
            </div>
        </form>
    </div>
</div>