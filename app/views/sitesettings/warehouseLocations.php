<?php
    if($active == 0)
    {
            $link_text = "<a href='/site-settings/warehouse-locations' class='btn btn-outline-secondary'>View Active Sites</a>";
    }
    else
    {
            $link_text = "<a href='/site-settings/warehouse-locations/active=0' class='btn btn-outline-secondary'>View Inactive Sites</a>";
    }
    $i = 1;
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="row">
            <div class="col-lg-12">
                <p class="text-right"><?php echo $link_text;?></p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <h2>Add a New Site</h2>
            </div>
        </div>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <form id="add_location" method="post" action="/form/procAddSite">
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Site</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="name" id="name" value="<?php echo Form::value('name');?>" />
                    <span class="inst">Site names need to be unique</span>
                    <?php echo Form::displayError('name');?>
                </div>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <div class="form-group row">
                <label class="col-md-3 col-form-label">&nbsp;</label>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-outline-secondary">Add Site</button>
                </div>
            </div>
        </form>
        <?php if(count($sites)):?>
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
                        <table width="100%" class="table-striped table-hover" id="view_sites_table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Edit</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($sites as $s): ?>
                                    <tr id="row_<?php echo $s['id'];?>">
                                        <td data-label="Name"><?php echo $s['name'];?></td>
                                        <td data-label="Edit">
                                            <input type="text" class="form-control required" name="name_<?php echo $s['id'];?>" id="name_<?php echo $s['id'];?>" value="<?php echo $s['name'];?>" />
                                            <input type="hidden" name="current_name_<?php echo $s['id'];?>" id="current_name_<?php echo $s['id'];?>" value="<?php echo $s['name'];?>" />
                                        </td>
                                        <td>
                                            <p>
                                                <a class="btn btn-outline-secondary update" data-siteid="<?php echo $s['id'];?>">Update Details</a><span class="label label-success" id="updated_<?php echo $s['id'];?>"></span>
                                            </p>
                                            <?php if($active == 1):?>
                                                <p>
                                                    <a class="btn btn-outline-danger deactivate" data-siteid="<?php echo $s['id'];?>">Deactivate Site</a>
                                                </p>
                                            <?php else:?>
                                                <p>
                                                    <a class="btn btn-outline-warning reactivate" data-siteid="<?php echo $s['id'];?>">Reactivate Site</a>
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
                    <p>No sites listed yet</p>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>