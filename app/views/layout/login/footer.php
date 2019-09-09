        <!-- Jquery JavaScript -->
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
        <!-- Validation JavaScript -->
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js" ></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/additional-methods.min.js" ></script>
        <script src="/scripts/form_validators.js" ></script>
        <!-- Bootstrap JavaScript -->
        <script src="/scripts/bootstrap.3.3.4.min.js"></script>
        <!-- Menu JavaScript -->
        <script src="//cdn.jsdelivr.net/npm/metismenu/dist/metisMenu.min.js"></script>
        <!-- DataTables JavaScript -->
        <script language="javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
        <script language="javascript" src="https://cdn.datatables.net/responsive/2.2.1/js/dataTables.responsive.min.js"></script>
        <script src="/scripts/dataTables.bootstrap.min.js"></script>
        <!-- Block UI JavaScript -->
        <script src="/scripts/jquery.blockUI.js"></script>
        <!-- WMS JavaScript -->
        <script src="/scripts/common.js"></script>

        <!-- Assign CSRF Token to JS variable -->
        <?php Config::setJsConfig('csrfToken', Session::generateCsrfToken()); ?>
        <!-- Assign all configuration variables -->
        <script>config = <?php echo json_encode(Config::getJsConfig()); ?>;</script>
        <script>
            $(document).ready(function(e){
                $('button#link-forgot-password').click(function(e){
                    e.preventDefault();
                    $('form#form-login').slideToggle('slow');
                    $('form#form-forgot-password').slideToggle('slow');
                });

                $('button#link-login').click(function(e){
                    e.preventDefault();
                    $('form#form-login').slideToggle('slow');
                    $('form#form-forgot-password').slideToggle('slow');
                });
            });
        </script>
		<?php Database::closeConnection(); ?>
	</body>
</html>
