<div class="row">
    <div class="col-lg-12">
        <?php if(isset($_SESSION['feedback'])) :?>
           <div class='feedbackbox'><?php echo Session::getAndDestroy('feedback');?></div>
        <?php endif; ?>
        <?php if(isset($_SESSION['errorfeedback'])) :?>
           <div class='errorbox'><?php echo Session::getAndDestroy('errorfeedback');?></div>
        <?php endif; ?>
        <a name="form_top"></a>
        <?php if(Form::$num_errors > 0) :?>
        	<div class='errorbox'>Sorry, some errors were found with the form. Please correct where shown and re-submit.</div>
        <?php endif ?>
        <p class="text-info">fields marked <sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> are required</p>
    </div>
</div>