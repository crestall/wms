    <div class="container">
        <div class="row">
			<div class="col-md-4 col-md-offset-4">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Login</h3>
                    </div>
                    <div class="panel-body">
                    <?php $display_form = Session::getAndDestroy('display-form'); ?>
                        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
                        <?php echo Form::displayError('general');?>
                        <form action="/form/procLogin" id="form-login" method="post"
                            <?php if(!empty($display_form)){ echo "class='display-none'"; } ?> >
                            <fieldset>
                                <div class="form-group">
                                    <input type="email" name="email" class="form-control required email" placeholder="* E-mail" autofocus />
                                    <?php echo Form::displayError('email');?>
                                </div>
                                <div class="form-group">
                                    <input type="password" name="password" class="form-control required" placeholder="* Password" />
                                    <?php echo Form::displayError('password');?>
                                </div>
                                <?php if (!empty($redirect)): ?>
                                    <div class="form-group">
                                        <input type="hidden" name="redirect" value="<?php echo $this->encodeHTML($redirect); ?>" />
                                    </div>
                                <?php endif; ?>
                                <div class="form-group">
                                    <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                                </div>
								<div class="form-group form-actions text-right">
                                   <button type="submit" name="submit" value="submit" class="btn btn-success">
										<i class="fa fa-check"></i> Login
									</button>
                                </div>
                                <hr/>
                                <hr/>
                                <div class="form-group">
									Forgot your password? <button id="link-forgot-password" class='btn btn-primary btn-sm'>Restore it</button>
                                </div>
                            </fieldset>
                        </form>
						<form action="/form/procForgotPassword" id="form-forgot-password" method="post"
                            <?php if($display_form !== "forgot-password"){ echo "class='display-none'"; } ?> >
                            <fieldset>
                                <div class="form-group">
                                    <input type="email" name="email" class="form-control required email" placeholder="E-mail" autofocus />
                                    <?php echo Form::displayError('email');?>
                                </div>
								<div class="form-group">
                                    <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                                </div>
								<div class="form-group form-actions text-right">
                                   <button type="submit" name="submit" value="submit" class="btn btn-sm btn-success">
										<i class="fa fa-check"></i> Send
									</button>
                                </div>	
								<div class="form-group">
									Did you remember your password? <button id="link-login" class='btn btn-primary btn-sm'>Login</button>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
	
	
