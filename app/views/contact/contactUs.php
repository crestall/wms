<?php
$address = Form::value('address');
$address2 = Form::value('address2');
$suburb = Form::value('suburb');
$state = Form::value('state');
$postcode = Form::value('postcode');
$country = Form::value('country');
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <?php echo Form::displayError('general');?>
        <div class="row">
            <div class="col">
                <form id="contact_us" method="post"  action="/form/procContactUs">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Subject</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control required" name="subject" id="subject" value="<?php echo Form::value('subject');?>" />
                            <?php echo Form::displayError('subject');?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Message</label>
                        <div class="col-md-4">
                            <textarea class="form-control required ckeditor" name="message" id="message"><?php echo Form::value('message');?></textarea>
                        </div>
                    </div>
                    <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                    <input type="hidden" name="the_website" id="the_website" value="" />
                    <input type="hidden" name="loaded" id="loaded" value="<?php echo time();?>" />
                    <div class="form-group row">
                        <div class="col-md-4 offset-md-3">
                            <button type="submit" class="btn btn-outline-secondary">Send Message</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>