<?php
    if($active == 0)
    {
            $link_text = "<a href='/site-settings/locations' class='btn btn-outline-secondary'>View Active Locations</a>";
    }
    else
    {
            $link_text = "<a href='/site-settings/locations/active=0' class='btn btn-outline-secondary'>View Inactive Locations</a>";
    }
    $i = 1;
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="row">
            <div class="col-lg-12">
                <p class="text-right"><?php echo $link_text;?></p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <h2>Add a New Location</h2>
            </div>
        </div>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <form id="add_location" method="post" action="/form/procAddLocation">
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
            <div class="form-group row custom-control custom-checkbox custom-control-right">
                <input class="custom-control-input" type="checkbox" id="trays" name="trays" <?php if(!empty(Form::value('trays'))) echo 'checked';?> />
                <label class="custom-control-label col-md-3" for="trays">Tray Location</label>
            </div>
            <div class="form-group row custom-control custom-checkbox custom-control-right">
                <input class="custom-control-input" type="checkbox" id="oversize" name="oversize" <?php if(!empty(Form::value('oversize'))) echo 'checked';?> />
                <label class="custom-control-label col-md-3" for="oversize">Oversize Location</label>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <div class="form-group row">
                <label class="col-md-3 col-form-label">&nbsp;</label>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-outline-secondary">Add Location</button>
                </div>
            </div>
        </form>
        <div id="waiting" class="row">
            <div class="col-lg-12 text-center">
                <h2>Drawing Table..</h2>
                <p>May take a few moments</p>
                <img class='loading' src='/images/preloader.gif' alt='loading...' />
            </div>
        </div>
        <div id="table_holder" style="display:none">
            <div class="row" id="tablefeedback" style="display: none"></div>
            <?php if(count($locations)):?>
                <div class="row">
                    <div class="col-lg-12">
                        <table width="100%" class="table-striped table-hover" id="view_locations_table">
                            <thead>
                                <tr>
                                    <th>Location</th>
                                    <th>Edit</th>
                                    <th>Multi SKU</th>
                                    <th>Trays</th>
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
                                        <td data-label="Trays">
                                            <div class="form-group row custom-control custom-checkbox custom-control-right">
                                                <input class="custom-control-input" type="checkbox" id="trays_<?php echo $l['id'];?>" name="trays_<?php echo $l['id'];?>" <?php if($l['tray'] > 0) echo "checked";?> />
                                                <label class="custom-control-label col-md-3" for="trays_<?php echo $l['id'];?>"></label>
                                            </div>
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
            <?php else:?>
                <div class="col-lg-12">
                    <div class="errorbox">
                        <p>No locations listed yet</p>
                    </div>
                </div>
            <?php endif;?>
        </div>
    </div>
</div>