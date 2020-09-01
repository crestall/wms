<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?> 
        <?php echo Form::displayError('general');?>
        <form id="add-job-status"  method="post" enctype="multipart/form-data" action="/form/procJobStatusAdd">
            <div class="row">
                <div class="col-lg-12">
                    <h3>Add Job Status</h3>
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
                    <button type="submit" class="btn btn-outline-secondary">Add Status</button>
                </div>
            </div>
        </form>
        <div class="row">
            <div class="col-lg-12">
                <h2>Currently Available Status</h2>
            </div>
        </div>
        <?php if(count($status)):?>
            <?php echo "<pre>",print_r($status),"</pre>";?>
            <?php //foreach($couriers as $c):?>
        <?php else:?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="errorbox">
                        <h2><i class="fas fa-exclamation-triangle"></i> No Status Listed</h2>
                        <p>You will need to add some first</p>
                    </div>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>