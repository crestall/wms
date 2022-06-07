<?php
$address = Form::value('address');
$address2 = Form::value('address2');
$suburb = Form::value('suburb');
$state = Form::value('state');
$postcode = Form::value('postcode');
$country = !empty(Form::value('country'))?Form::value('country'):"AU";
$form_disabled = empty(Form::value('submitted'));
$idisp = "none";
if(!empty(Form::value('items')))
    $idisp = "block";
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div id="feedback_holder" style="display:none;"></div>
        <div class="p-3 pb-0 mb-2 rounded-top bg-bd-fsg">
           <div class="row">
                <div class="col">
                    <h3>Courier</h3>
                </div>
           </div>
           <div class="p-3 light-grey mb-3">
               <div class="form-group row">
                    <label class="col-md-3">Name</label>
                    <div class="col-md-4">
                        <select id="courier_id" name="courier_id" class="form-control selectpicker" data-style="btn-outline-secondary">
                            <option value="0">--Select One--</option>
                            <option value="<?php echo $dfe_id;?>">Direct Freight Express</option>
                            <option value="<?php echo $ep_id;?>">Eparcel</option>
                            <option value="<?php echo $epe_id;?>">Eparcel Express</option>
                        </select>
                    </div>
               </div>
           </div>
        </div>
        <div class="p-3 pb-0 mb-2 rounded-top bg-bd-fsg">
            <div class="row mb-0">
                <div class="col-md-4">
                    <h3>Packages</h3>
                </div>
                <div class="col-md-4">
                    <a class="add-package" style="cursor:pointer" title="Add Another Package"><h4><i class="fad fa-plus-square text-success"></i> Add another</a></h4>
                </div>
                <div class="col-md-4">
                    <a id="remove-all-packages" style="cursor:pointer" title="Leave Only One"><h4><i class="fad fa-times-square text-danger"></i> Leave only one</a></h4>
                </div>
            </div>
            <div id="packages_holder">
                <?php include(Config::get('VIEWS_PATH')."layout/page-includes/add_quote_package.php");?>
            </div>
        </div>
        <div class="p-3 pb-0 mb-2 rounded-top bg-bd-fsg">
            <div class="row">
                <div class="col">
                    <h3>Address</h3>
                </div>
           </div>
           <div class="p-3 light-grey mb-3">
               <?php include(Config::get('VIEWS_PATH')."forms/address_auonly.php");?>
           </div>
        </div>
        <div class="p-3 pb-0 mb-2 rounded-top bg-bd-fsg">
            <div class="row">
                <div class="col">
                    <h3>Make Booking</h3>
                </div>
           </div>
           <div class="p-3 light-grey mb-3">
                <div class="form-group row">
                    <div class="col-md-3">
                        <span class="inst">This will submit the details to the courier and book the shipment.<br>It cannot be altered or cancelled afterwards</span>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-outline-fsg" id="submitter" <?php if($form_disabled) echo "disabled";?>>Book Courier</button>
                    </div>
                </div>
           </div>
        </div>
    </div>
</div>