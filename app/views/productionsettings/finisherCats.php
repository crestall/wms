<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <?php echo Form::displayError('general');?>
        <form id="add-finisher-category"  method="post" enctype="multipart/form-data" action="/form/procFinisherCategoryAdd">
            <div class="row">
                <div class="col-lg-12">
                    <h3>Add Finisher Category</h3>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Name</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="name" id="name" value="<?php echo Form::value('name');?>" />
                    <?php echo Form::displayError('name');?>
                </div>
                <span class="inst">Names <span class="font-weight-bold">must</span> be unique</span>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <div class="form-group row">
                <label class="col-md-3 col-form-label">&nbsp;</label>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-outline-secondary">Add Category</button>
                </div>
            </div>
        </form>
        <div class="row">
            <div class="col-lg-12">
                <h2>Currently Available Categories</h2>
            </div>
        </div>
        <?php if(count($cats)):?>
            <?php foreach($cats as $c):?>
                <form class="edit-category mb-3 p-3 border rounded" action="/form/procFinisherCategoryEdit" method="post" id="category_<?php echo $c['id'];?>">
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label class="col-form-label">Name</label>
                            <input type="text" class="form-control required cat_name" name="name" id="name_<?php echo $c['id'];?>" value="<?php echo ucwords($c['name']);?>" />
                            <?php echo Form::displayError("name_{$c['id']}");?>
                        </div>
                        <div class="col-md-1">
                            <label class="col-form-label" for="active_<?php echo $c['id'];?>">Active</label>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="active_<?php echo $c['id'];?>" name="active" <?php if($c['active'] > 0) echo "checked";?> />
                                <label class="custom-control-label" for="active_<?php echo $c['id'];?>"></label>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <label class="col-form-label">&nbsp;</label>
                            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                            <input type="hidden" name="line_id" value="<?php echo $c['id'];?>" />
                            <input type="hidden" name= "currentname" id="currentname_<?php echo $c['id'];?>" value="<?php echo $c['name'];?>" />
                            <button type="submit" class="btn btn-sm btn-outline-secondary">Update</button>
                        </div>
                    </div>
                </form>
            <?php endforeach;?>
        <?php else:?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="errorbox">
                        <h2><i class="fas fa-exclamation-triangle"></i> No Categories Listed</h2>
                        <p>You will need to add some first</p>
                    </div>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>