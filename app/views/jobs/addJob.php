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
                            <div class="form-group row">
                                <label class="col-md-4"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Job Id</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control required" name="job_id" id="job_id" value="<?php echo Form::value('job_id');?>" />
                                    <?php echo Form::displayError('job_id');?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4">Previous Job Id</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="previous_job_id" id="previous_job_id" value="<?php echo Form::value('previous_job_id');?>" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4">Customer PO Number</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="customer_po_number" id="customer_po_number" value="<?php echo Form::value('customer_po_number');?>" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4">Priority</label>
                                <div class="col-md-8">
                                    <select id="priority" class="form-control selectpicker" name="priority" data-style="btn-outline-secondary"><option value="0">-- Select One --</option><?php echo Utility::getPrioritySelect(Form::value('priority'));?></select>
                                    <?php echo Form::displayError('priority');?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Status</label>
                                <div class="col-md-8">
                                    <select id="status_id" class="form-control selectpicker" name="status_id" data-style="btn-outline-secondary"><option value="0">-- Select One --</option><?php echo $this->controller->jobstatus->getSelectJobStatus(Form::value('status_id'));?></select>
                                    <?php echo Form::displayError('status_id');?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> FSG Contact</label>
                                <div class="col-md-8">
                                    <select id="salesrep_id" class="form-control selectpicker" name="salesrep_id" data-style="btn-outline-secondary"><option value="0">-- Select One --</option><?php echo $this->controller->salesrep->getSelectSalesReps(Form::value('salesrep_id'));?></select>
                                    <?php echo Form::displayError('salesrep_id');?>
                                </div>
                            </div>
                            <div class="row form-group">
                                <label class="col-md-4 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Date Entered</label>
                                <div class="col-md-4">
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
                                <label class="col-md-4 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Due Date</label>
                                <div class="col-md-4">
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
                                <label class="custom-control-label col-md-4" for="strict_dd">Strict Due Date</label>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4">Designer</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="designer" id="designer" value="<?php echo Form::value('designer');?>" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Description</label>
                                <div class="col-md-8">
                                    <textarea name="description" id="description" class="form-control required" rows="4"><?php echo Form::value('description');?></textarea>
                                    <?php echo Form::displayError('description');?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4">Notes/Comments</label>
                                <div class="col-md-8">
                                    <textarea name="notes" id="notes" class="form-control" rows="3"><?php echo Form::value('notes');?></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4">Delivery Notes/Comments</label>
                                <div class="col-md-8">
                                    <textarea name="delivery_notes" id="delivery_notes" class="form-control" rows="3"><?php echo Form::value('delivery_notes');?></textarea>
                                </div>
                            </div>
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
                            <div class="form-group row mb-3">
                                <label class="col-md-4"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Customer Name</label>
                                <div class="col-md-8">
                                    <input type="text" class="required form-control" name="customer_name" id="customer_name" value="<?php echo Form::value('customer_name');?>" />
                                    <input type="hidden" name="customer_id" id="customer_id" value="0" />
                                    <?php echo Form::displayError('customer_name');?>
                                </div>
                            </div>
                            <div class="form-group row custom-control custom-checkbox custom-control-right">
                                <input class="custom-control-input send_to_address" type="checkbox" id="send_to_customer" name="send_to_customer" />
                                <label class="custom-control-label col-md-4" for="send_to_customer">Send Job To Customer</label>
                            </div>
                            <div class="p-3 pb-0 mb-2 rounded-top mid-grey">
                                <div class="form-group row">
                                    <h4 class="col-md-8">Contact Details</h4>
                                </div>
                                <div class="form-group row" id="contact_chooser">
                                    <input type="hidden" id="customer_contact_id" name="customer_contact_id" value="0" >
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4 mb-3">Contact Name</label>
                                    <div class="col-md-8 mb-3">
                                        <input type="text" class="form-control customer_contact" name="customer_contact_name" id="customer_contact_name" value="<?php echo Form::value('customer_contact_name');?>" >
                                    </div>
                                    <label class="col-md-4 mb-3">Contact Role</label>
                                    <div class="col-md-8 mb-3">
                                        <input type="text" class="form-control customer_contact" name="customer_contact_role" id="customer_contact_role" value="<?php echo Form::value('customer_contact_role');?>" >
                                    </div>
                                    <label class="col-md-4 mb-3">Contact Email</label>
                                    <div class="col-md-8 mb-3">
                                        <input type="text" class="form-control customer_contact email" name="customer_contact_email" id="customer_contact_email" value="<?php echo Form::value('customer_contact_email');?>" >
                                        <?php echo Form::displayError('customer_contact_email');?>
                                    </div>
                                    <label class="col-md-4 mb-3">Contact Phone</label>
                                    <div class="col-md-8 mb-3">
                                        <input type="text" class="form-control customer_contact" name="customer_contact_phone" id="customer_contact_phone" value="<?php echo Form::value('customer_contact_phonel');?>" >
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">Business Email</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control email" name="customer_email" id="customer_email" value="<?php echo Form::value('customer_email');?>" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">Business Phone</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="customer_phone" id="customer_phone" value="<?php echo Form::value('customer_email');?>" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">Business Website</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="customer_website" id="customer_website" value="<?php echo Form::value('customer_website');?>" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col text-right">
                                    <a  id="customer_address_toggle" class="btn btn-outline-secondary" data-toggle="collapse" href="#customer_address_holder" role="button" aria-expanded="<?php echo $customer_aria_expanded;?>" aria-controls="customer_address_holder"> </a>
                                </div>
                            </div>
                            <div id="customer_address_holder" class="<?php echo $customer_collapse;?> mt-3">
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label">Address Line 1</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control customer" name="customer_address" id="customer_address" value="<?php echo Form::value('customer_address');?>" />
                                        <?php echo Form::displayError('customer_address');?>
                                    </div>
                                    <div class="col-md-4 checkbox checkbox-default">
                                        <input class="form-check-input styled" type="checkbox" id="ignore_customer_address_error" name="ignore_customer_address_error" />
                                        <label for="ignore_customer_address_error">No need for a number</label>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label">Address Line 2</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control customer" name="customer_address2" id="customer_address2" value="<?php echo Form::value('customer_address2');?>" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label">Suburb/Town</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control customer" name="customer_suburb" id="customer_suburb" value="<?php echo Form::value('customer_suburb');?>" />
                                        <?php echo Form::displayError('customer_suburb');?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label">State</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control customer" name="customer_state" id="customer_state" value="<?php echo Form::value('customer_state');?>" />
                                        <span class="inst">for AU addresses use VIC, NSW, QLD, ACT, TAS, WA, SA, NT only</span>
                                        <?php echo Form::displayError('customer_state');?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label">Postcode</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control customer" name="customer_postcode" id="customer_postcode" value="<?php echo Form::value('customer_postcode');?>" />
                                        <?php echo Form::displayError('customer_postcode');?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label">Country</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control customer" name="customer_country" id="customer_country" value="<?php echo Form::value('customer_country');?>" />
                                        <span class="inst">use the 2 letter ISO code</span>
                                        <?php echo Form::displayError('customer_country');?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
<!------------------------------------------------------------------------------------------------------------------------------------------->
<!-------------------------------------------------     The Finishers     ---------------------------------------------------------------->
<!------------------------------------------------------------------------------------------------------------------------------------------->
                <div class="col-sm-12 col-md-6 mb-3">
                    <div class="card h-100 border-secondary order-card" id="finishersdetails">
                        <div class="card-header bg-secondary text-white">
                            Finisher(s) Details
                        </div>
                        <div class="card-body">
                            <div class="col">
                                <a class="add-finisher" style="cursor:pointer" title="Add Another Finisher"><h4><i class="fad fa-plus-square text-success"></i> Add A Finisher</a></h4>
                            </div>
                            <div id="finishers_holder"></div>
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
                            <div class="form-group row custom-control custom-checkbox custom-control-right">
                                <input class="custom-control-input send_to_address" type="checkbox" id="held_in_store" name="held_in_store" />
                                <label class="custom-control-label col-md-4" for="held_in_store">Hold Job In Store</label>
                            </div>
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
                                        <textarea class="form-control" name="delivery_instructions" id="delivery_instructions" placeholder="Instructions For Driver"><?php echo Form::value('delivery_instructions');?></textarea>
                                    </div>
                                </div>
                                <?php include(Config::get('VIEWS_PATH')."forms/address_auonly.php");?>
                            </div>
                        </div>
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
            </div>
        </form>
    </div>
</div>