<?php
$name       = empty(Form::value('name'))?       $customer['name']         : Form::value('name');

$email      = empty(Form::value('email'))?      $customer['email']        : Form::value('email');
$phone      = empty(Form::value('phone'))?      $customer['phone']        : Form::value('phone');
$address    = empty(Form::value('address'))?    $customer['address']      : Form::value('address');
$address2   = empty(Form::value('address2'))?   $customer['address_2']    : Form::value('address2');
$suburb     = empty(Form::value('suburb'))?     $customer['suburb']       : Form::value('suburb');
$state      = empty(Form::value('state'))?      $customer['state']        : Form::value('state');
$postcode   = empty(Form::value('postcode'))?   $customer['postcode']     : Form::value('postcode');
$country    = empty(Form::value('country'))?    $customer['country']      : Form::value('country');
$website    = empty(Form::value('website'))?    $customer['website']      : Form::value('website');
//create the contacts array
$contacts   = empty(Form::value('contacts'))?    $customer['contacts']    : Form::value('contacts');
if(!is_array($contacts))
{
    $contact_array = array();
    if(!empty($contacts))
    {
        $ca = explode("|", $contacts);
        foreach($ca as $c)
        {
            list($a['contact_id'], $a['name'],$a['email'],$a['phone'],$a['role']) = explode(',', $c);
            $contact_array[] = $a;
        }
    }
}
else
{
    $contact_array = $contacts;
}
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <form id="edit_production_customer" method="post" action="/form/procEditProductionCustomer">
            <div class="form-group row">
                <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Name</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="name" id="name" value="<?php echo $name;?>" />
                    <?php echo Form::displayError('name');?>
                </div>
            </div>
            <div class="form-group row custom-control custom-checkbox custom-control-right">
                <input class="custom-control-input" type="checkbox" id="active" name="active" <?php if($customer['active'] > 0) echo "checked";?> />
                <label class="custom-control-label col-md-3" for="active">Active</label>
            </div>
            <div class="form-group row">
                <label class="col-md-3">Phone</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="phone" id="phone" value="<?php echo $phone;?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3">Email</label>
                <div class="col-md-4">
                    <input type="text" class="form-control email" name="email" id="email" value="<?php echo $email;?>" />
                    <?php echo Form::displayError('email');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3">Website</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="website" id="website" value="<?php echo $website;?>" />
                </div>
            </div>
            <div class="p-3 pb-0 mb-2 rounded-top mid-grey">
                <div class="row mb-0">
                    <div class="col-md-4">
                        <h4>Contacts</h4>
                    </div>
                    <div class="col-md-4">
                        <a class="add-contact" style="cursor:pointer" title="Add Another Contact"><h4><i class="fad fa-plus-square text-success"></i> Add another</a></h4>
                    </div>
                    <div class="col-md-4">
                        <a id="remove-all-contacts" style="cursor:pointer" title="Leave Only First"><h4><i class="fad fa-times-square text-danger"></i> Leave only one contact</a></h4>
                    </div>
                </div>
                <div id="contacts_holder">
                    <div class="form-group row">
                        <div class="col-12">
                            <span class="inst">At least one contact name is required</span>
                        </div>
                    </div>
                    <?php //echo "<pre>", var_dump($contact_array) ,"</pre>";//die(); ?>
                    <?php
                    if(!empty($contact_array)):
                        foreach($contact_array as $i => $d)
                        {
                            include(Config::get('VIEWS_PATH')."layout/page-includes/add_production_contact.php");
                        }
                    else:
                        include(Config::get('VIEWS_PATH')."layout/page-includes/add_production_contact.php");
                    endif;?>
                </div>
            </div>
            <?php include(Config::get('VIEWS_PATH')."forms/address_nr.php");?>
            <div class="form-group row">
                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                <input type="hidden" name="customer_id" value="<?php echo $customer_id;?>" />
                <div class="col-md-4 offset-md-3">
                    <button type="submit" class="btn btn-outline-secondary">Update Details</button>
                </div>
            </div>
        </form>
    </div>
</div>