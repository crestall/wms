<?php

?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="row">
        <div class="col-lg-12">
            <h2>Add a New Location</h2>
        </div>
    </div>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
    <div class="row">
        <form id="add_location" method="post" action="/form/procAddLocation">
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Location</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="location" id="location" value="<?php echo Form::value('location');?>" />
                    <span class="inst">Location names need to be unique</span>
                    <?php echo Form::displayError('location');?>
                </div>
            </div>
            <div class="form-group row">
                <div class="form-check">
                    <label class="form-check-label col-md-3" for="multisku">Multiple SKUs</label>
                    <div class="col-md-4 checkbox checkbox-default">
                        <input class="form-check-input styled" type="checkbox" id="multisku" name="multisku" />
                        <label for="multisku"></label>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="form-check">
                    <label class="form-check-label col-md-3" for="trays">Tray Location</label>
                    <div class="col-md-4 checkbox checkbox-default">
                        <input class="form-check-input styled" type="checkbox" id="trays" name="trays" />
                        <label for="trays"></label>
                    </div>
                </div>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <div class="form-group row">
                <label class="col-md-3 col-form-label">&nbsp;</label>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Add Location</button>
                </div>
            </div>
        </form>
    </div>
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
                                        <div class="checkbox checkbox-default">
                                            <input class="form-check-input styled" type="checkbox" id="multisku_<?php echo $l['id'];?>" name="multisku_<?php echo $l['id'];?>" <?php if($l['multi_sku'] > 0) echo "checked";?> />
                                            <label for="multisku_<?php echo $l['id'];?>"></label>
                                        </div>
                                    </td>
                                    <td data-label="Trays">
                                        <div class="checkbox checkbox-default">
                                            <input class="form-check-input styled" type="checkbox" id="trays_<?php echo $l['id'];?>" name="trays_<?php echo $l['id'];?>" <?php if($l['tray'] > 0) echo "checked";?> />
                                            <label for="trays_<?php echo $l['id'];?>"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <p>
                                            <a class="btn btn-primary update" data-locationid="<?php echo $l['id'];?>">Update Details</a><span class="label label-success" id="updated_<?php echo $l['id'];?>"></span>
                                        </p>
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