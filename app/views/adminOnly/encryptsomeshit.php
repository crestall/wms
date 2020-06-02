<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="row">
        <div class="col-md-12">
            <form method="post" action="/ajaxfunctions/encryptSomeShit">
                <div class="row form-group">
                    <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup>String to Encrypt</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control required" name="string" id="string" value="<?php echo Form::value('string');?>" />
                        <?php echo Form::displayError('string');?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">&nbsp;</label>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">Generate Encrypted String</button>
                    </div>
                </div>
            </form>
            <div class="row">
                <div id="encrpted_string_holder"></div>
            </div>
        </div>
    </div>
</div>
<div id="block_message"></div>