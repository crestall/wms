<?php

?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <?php echo Form::displayError('general');?>
        <form id="add_bookcover"  method="post" enctype="multipart/form-data" action="/form/procBookCoverAdd">
            <div class="row">
                <div class="col-lg-12">
                    <h3>Add New Cover</h3>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Name</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="name" id="name" value="<?php echo Form::value('name');?>" />
                    <span class="inst">Names need to be unique</span>
                    <?php echo Form::displayError('name');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Quantity</label>
                <div class="col-4">
                    <input type="text" class="form-control required number" name="qty" id="qty" value="<?php echo Form::value('qty');?>" />
                    <?php echo Form::displayError('qty');?>
                </div>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <div class="form-group row">
                <div class="col-md-4 offset-md-3">
                    <button type="submit" class="btn btn-outline-secondary">Add Cover</button>
                </div>
            </div>
        </form>
        <?php if(count($covers)):?>
            <div id="waiting" class="row">
                <div class="col-lg-12 text-center">
                    <h2>Drawing Table..</h2>
                    <p>May take a few moments</p>
                    <img class='loading' src='/images/preloader.gif' alt='loading...' />
                </div>
            </div>
            <div id="table_holder" style="display:none">
                <div class="row" id="tablefeedback" style="display: none"></div>
                    <div class="col-lg-12">
                        <table width="100%" class="table-striped table-hover" id="view_bookcovers_table">
                            <thead>
                                <tr>
                                    <th>Book Name</th>
                                    <th>Edit</th>
                                    <th>Quantity</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($covers as $c): ?>
                                    <tr id="row_<?php echo $c['id'];?>">
                                        <td><?php echo $c['name'];?></td>
                                        <td>
                                            <input type="text" class="form-control required" name="cover_<?php echo $c['id'];?>" id="cover_<?php echo $c['id'];?>" value="<?php echo $c['name'];?>" />
                                            <input type="hidden" name="current_name_<?php echo $c['id'];?>" id="current_location_<?php echo $c['id'];?>" value="<?php echo $c['name'];?>" />
                                        </td>
                                        <td>
                                            <input type="text" class="form-control required number" name="qty_<?php echo $c['id'];?>" id="qty_<?php echo $c['id'];?>" value="<?php echo Form::value('qty');?>" />
                                        </td>
                                        <td>
                                            <p>
                                                <a class="btn btn-outline-secondary update" data-coverid="<?php echo $c['id'];?>">Update Details</a><span class="label label-success" id="updated_<?php echo $c['id'];?>"></span>
                                            </p>
                                        </td>
                                    </tr>
                                <?php endforeach;?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php else:?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="errorbox">
                        <h2><i class="fas fa-exclamation-triangle"></i> No Covers Listed</h2>
                        <p>You will need to add some first</p>
                    </div>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>