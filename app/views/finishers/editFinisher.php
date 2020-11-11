<?php
$name       = empty(Form::value('name'))?       $finisher['name']         : Form::value('name');
$contact    = empty(Form::value('contact'))?    $finisher['contact']      : Form::value('contact');
$email      = empty(Form::value('email'))?      $finisher['email']        : Form::value('email');
$phone      = empty(Form::value('phone'))?      $finisher['phone']        : Form::value('phone');
$address    = empty(Form::value('address'))?    $finisher['address']      : Form::value('address');
$address2   = empty(Form::value('address2'))?   $finisher['address_2']    : Form::value('address2');
$suburb     = empty(Form::value('suburb'))?     $finisher['suburb']       : Form::value('suburb');
$state      = empty(Form::value('state'))?      $finisher['state']        : Form::value('state');
$postcode   = empty(Form::value('postcode'))?   $finisher['postcode']     : Form::value('postcode');
$country    = empty(Form::value('country'))?    $finisher['country']      : Form::value('country');
$website    = empty(Form::value('website'))?    $finisher['website']      : Form::value('website');
$cat_ids    = empty(Form::value('categories'))? $cat_ids                  : Form::value('categories');
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <?php //var_dump($categories);?>
        <form id="edit_production_finisher" method="post" action="/form/procEditProductionFinisher">
            <div class="form-group row">
                <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Name</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="name" id="name" value="<?php echo $name;?>" />
                    <?php echo Form::displayError('name');?>
                </div>
            </div>
            <div class="form-group row custom-control custom-checkbox custom-control-right">
                        <input class="custom-control-input" type="checkbox" id="active" name="active" <?php if($finisher['active'] > 0) echo "checked";?> />
                        <label class="custom-control-label col-md-3" for="active">Active</label>
                    </div>
            <div class="form-group row">
                <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Contact</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="contact" id="contact" value="<?php echo $contact;?>" />
                    <?php echo Form::displayError('contact');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3">Category</label>
                <div class="col-md-4">
                    <select id="category" name="categories[]" class="form-control selectpicker" data-style="btn-outline-secondary" data-live-search="true" data-actions-box="true" multiple title="Choose all that are relevent..."><?php echo $this->controller->finishercategories->getMultiSelectFinisherCategories($cat_ids);?></select>
                </div>
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
            <?php include(Config::get('VIEWS_PATH')."forms/address_nr.php");?>
            <div class="form-group row">
                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                <input type="hidden" name="finisher_id" value="<?php echo $finisher_id;?>" />
                <div class="col-md-4 offset-md-3">
                    <button type="submit" class="btn btn-outline-secondary">Update Details</button>
                </div>
            </div>
        </form>
    </div>
</div>