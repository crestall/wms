<?php
$address = Form::value('address');
$address2 = Form::value('address2');
$suburb = Form::value('suburb');
$state = Form::value('state');
$postcode = Form::value('postcode');
$country = Form::value('country');
$categories = (is_array(Form::value('categories')))? Form::value('categories') : array();
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <?php //var_dump(Form::value('categories'));?>
        <form id="add_production_finisher" method="post" action="/form/procAddProductionFinisher">
            <div class="form-group row">
                <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Name</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="name" id="name" value="<?php echo Form::value('name');?>" />
                    <?php echo Form::displayError('name');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3">Category</label>
                <div class="col-md-4">
                    <select id="category" name="categories[]" class="form-control selectpicker" data-style="btn-outline-secondary" data-live-search="true" data-actions-box="true" multiple title="Choose all that are relevent..."><?php echo $this->controller->finishercategories->getMultiSelectFinisherCategories($categories);?></select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3">Phone</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="phone" id="phone" value="<?php echo Form::value('phone');?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3">Email</label>
                <div class="col-md-4">
                    <input type="text" class="form-control email" name="email" id="email" value="<?php echo Form::value('email');?>" />
                    <?php echo Form::displayError('email');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3">Website</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="website" id="website" value="<?php echo Form::value('website');?>" />
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
                    <?php //echo "<pre>",print_r(Form::value('contacts')),"</pre>";//die(); ?>
                    <?php if(!empty(Form::value('contacts'))):
                        foreach(Form::value('contacts') as $i => $d)
                        {
                            include(Config::get('VIEWS_PATH')."layout/page-includes/add_production_contact.php");
                        }
                    else:
                       include(Config::get('VIEWS_PATH')."layout/page-includes/add_production_contact.php");
                    endif;?>
                </div>
            </div>
            <div class="p-3 pb-0 mb-2 rounded-top mid-grey">
                <div class="row mb-0">
                    <div class="col-md-4">
                        <h4>Address Details</h4>
                    </div>
                </div>
                <div class="p-3 light-grey mb-3">
                    <?php include(Config::get('VIEWS_PATH')."forms/address_nr.php");?>
                </div>
            </div>
            <div class="form-group row">
                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                <div class="col-md-4 offset-md-3">
                    <button type="submit" class="btn btn-outline-secondary">Add This Finisher</button>
                </div>
            </div>
        </form>
    </div>
</div>