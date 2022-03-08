<?php
    $i = 1;
    $link_text = "";
    if($site > 0)
    {
        if($active == 0)
        {
                $link_text = "<a href='/site-settings/locations/site=$site' class='btn btn-outline-secondary'>View Active Locations</a>";
        }
        else
        {
                $link_text = "<a href='/site-settings/locations/site=$site/active=0' class='btn btn-outline-secondary'>View Inactive Locations</a>";
        }
    }
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php if($site > 0):?>
            <div class="row">
                <div class="col-lg-12">
                    <p class="text-right"><?php echo $link_text;?></p>
                </div>
            </div>
        <?php endif;?>
        <div class="row">
            <div class="col-lg-12">
                <h2>Add a New Location</h2>
            </div>
        </div>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <form id="add_location" method="post" action="/form/procAddLocation">
            <div class="form-group row">
                <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Site</label>
                <div class="col-md-4">
                    <select id="site_id" name="site_id" class="form-control selectpicker" data-style="btn-outline-secondary" data-live-search="true" required><?php echo $this->controller->site->getSelectSites($site);?></select>
                    <?php echo Form::displayError('site_id');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Location</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="location" id="location" value="<?php echo Form::value('location');?>" />
                    <span class="inst">Location names need to be unique</span>
                    <?php echo Form::displayError('location');?>
                </div>
            </div>
            <div class="form-group row custom-control custom-checkbox custom-control-right">
                <input class="custom-control-input" type="checkbox" id="multisku" name="multisku" <?php if(!empty(Form::value('multisku'))) echo 'checked';?> />
                <label class="custom-control-label col-md-3" for="multisku">Multiple SKUs</label>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <div class="form-group row">
                <label class="col-md-3 col-form-label">&nbsp;</label>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-outline-secondary">Add Location</button>
                </div>
            </div>
        </form>
        <div class="row">
            <div class="col-lg-12">
                <h2>Manage Existing Locations</h2>
            </div>
        </div>
        <div class="row mb-3">
            <label class="col-md-3">Select a Site</label>
            <div class="col-md-4">
                <select id="site_selector" class="form-control selectpicker" data-style="btn-outline-secondary" data-live-search="true"><option value="0">Select</option><?php echo $this->controller->site->getSelectSites($site);?></select>
            </div>
        </div>
        <?php if($site > 0):?>
            <?php if(count($locations)):?>
                <div id="waiting" class="row">
                    <div class="col-lg-12 text-center">
                        <h2>Drawing Table..</h2>
                        <p>May take a few moments</p>
                        <img class='loading' src='/images/preloader.gif' alt='loading...' />
                    </div>
                </div>
                <div id="table_holder" style="display:none">
                    <div class="row" id="tablefeedback" style="display: none"></div>
                    <div class="row">
                        <div class="col-lg-12">
                            <table width="100%" class="table-striped table-hover" id="view_locations_table">
                                <thead>
                                    <tr>
                                        <th>Location</th>
                                        <th>Edit</th>
                                        <th>Site</th>
                                        <th>Multi SKU</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($locations as $l): ?>
                                        <tr id="row_<?php echo $l['id'];?>">
                                            <td data-label="Location"><?php echo $l['location'];?></td>
                                            <td data-label="Edit">
                                                <input type="text" class="form-control required" name="location_<?php echo $l['id'];?>" id="location_<?php echo $l['id'];?>" value="<?php echo $l['location'];?>" />
                                                <input type="hidden" name="current_location_<?php echo $l['id'];?>" id="current_location_<?php echo $l['id'];?>" value="<?php echo $l['location'];?>" />
                                            </td>
                                            <td data-label="Multi SKU">
                                                <div class="form-group row custom-control custom-checkbox custom-control-right">
                                                    <input class="custom-control-input" type="checkbox" id="multisku_<?php echo $l['id'];?>" name="multisku_<?php echo $l['id'];?>" <?php if($l['multi_sku'] > 0) echo 'checked';?> />
                                                    <label class="custom-control-label col-md-3" for="multisku_<?php echo $l['id'];?>"></label>
                                                </div>
                                            </td>
                                            <td>
                                                <select name="site_<?php echo $l['id'];?>" class="selectpicker site_id" data-style="btn-outline-secondary btn-sm" data-width="fit" id="site_<?php echo $l['id'];?>"><?php echo $this->controller->site->getSelectSites($l['site_id']) ;?></select></p>
                                            </td>
                                            <td>
                                                <p>
                                                    <a class="btn btn-outline-secondary update" data-locationid="<?php echo $l['id'];?>">Update Details</a><span class="label label-success" id="updated_<?php echo $l['id'];?>"></span>
                                                </p>
                                                <?php if($active == 1):?>
                                                    <p>
                                                        <a class="btn btn-outline-danger deactivate" data-locationid="<?php echo $l['id'];?>">Deactivate Location</a>
                                                    </p>
                                                <?php else:?>
                                                    <p>
                                                        <a class="btn btn-outline-warning reactivate" data-locationid="<?php echo $l['id'];?>">Reactivate Location</a>
                                                    </p>
                                                <?php endif;?>
                                            </td>
                                        </tr>
                                    <?php endforeach;?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php else:?>
                <div class="col-lg-12">
                    <div class="errorbox">
                        <p>No locations listed yet</p>
                    </div>
                </div>
            <?php endif;?>
        <?php endif;?>
    </div>
</div>