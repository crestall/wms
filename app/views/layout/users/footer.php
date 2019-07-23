
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                common: {
                    init: function(){

                    }
                },
                profile:{
                    init: function(){
                        $('form#profile_update').submit(function(){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Processing form...</h2></div>' });
                            }
                            else
                            {
                                return false;
                            }
                        });
                    }
                },
                'add-user':{
                    init: function(){
                        $('select#role_id').change(function(e){
                            if($(this).val() == $('#client_role_id').val())
                            {
                                $('#client_holder').slideDown();
                                $('#client_id').rules('add', 'notNone');
                            }
                            else
                            {
                                $('#client_holder').slideUp();
                                $('#client_id').rules('remove');
                            }
                            $(this).valid();
                        });
                        $('select#role_id').change(function(e){
                            if($(this).val() == $('#solar_role_id').val())
                            {
                                $('#solar_holder').slideDown();
                                $('#solar_team_id').rules('add', 'notNone');
                            }
                            else
                            {
                                $('#solar_holder').slideUp();
                                $('#solar_team_id').rules('remove');
                            }
                            $(this).valid();
                        });
                        $('select#role_id, select#client_id, select#solar_team_id').change(function(e){
                            $(this).valid();
                        });
                    }
                },
                'edit-user-profile':{
                    init: function(){

                    }
                }
            }
            //run the script for the current page
            actions[config.curPage].init();
        </script>
        <?php Database::closeConnection(); ?>
    </body>
</html>