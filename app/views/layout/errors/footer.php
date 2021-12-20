
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                common: {
                    init: function(){
                        $('button#report_error').click(function(e){
                            console.log('click');
                            $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h1>Reporting Error...</h1></div>' });
                            var data = {
                                url: window.location.href,
                                error_type: $('#error_type').val()
                            }
                            $.post('/ajaxfunctions/reportErrorPage', data, function(d){
                                $.unblockUI();
                                if(d.error)
                                {
                                    $('div#feedback')
                                        .removeClass()
                                        .addClass('errorbox')
                                        .html(d.feedback)
                                        .slideDown()
                                }
                                else
                                {
                                    $('div#feedback')
                                        .removeClass()
                                        .addClass('feedbackbox')
                                        .html(d.feedback)
                                        .slideDown()
                                }
                            });
                        })
                    }
                },
                'error-400': {
                    init: function(){

                    }
                },
                'error-401': {
                    init: function(){

                    }
                },
                'error-403': {
                    init: function(){

                    }
                },
                'error-404': {
                    init: function(){

                    }
                },
                'error-500': {
                    init: function(){

                    }
                },
                'errors':{
                    init:function(){

                    }
                }
            }
            //run the script for the current page
            actions[config.curPage].init();
            actions.common.init();
        </script>
        <?php Database::closeConnection(); ?>
    </body>
</html>