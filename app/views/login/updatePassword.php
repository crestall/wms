

    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-lock"></i> Update Your Password</h3>
                    </div>
                    <div class="panel-body">
                        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
                        <?php echo Form::displayError('general');?>
                        <form action="/form/procUpdatePassword" id="form-update-password" method="post">
                            <fieldset>
                                <div class="form-group">
                                    <input class="form-control required" placeholder="* Password" name="password" type="password" id="password" value="<?php echo Form::value('password');?>" />
                                    <?php echo Form::displayError('password');?>
                                </div>
								<div class="form-group">
                                    <input class="required form-control" placeholder="* Confirm Password" name="confirm_password" type="password" id="confirm_password" value="<?php echo Form::value('confirm_password');?>" />
                                    <?php echo Form::displayError('confirm_password');?>
                                </div>
                                <div class="form-group">
                                    <input type="hidden" name="id" value="<?php echo $this->encodeHTML($this->controller->request->query("id")); ?>" />
                                </div>
                                <div class="form-group">
                                    <input type="hidden" name="token" value="<?php echo $this->encodeHTML($this->controller->request->query("token")); ?>" />
                                </div>
                                <div class="form-group">
                                    <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                                </div>
								<div class="form-group form-actions text-right">
                                    <button type="submit" name="submit" value="submit" class="btn btn-md btn-success">
										<i class="fa fa-check"></i> Update
									</button>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

