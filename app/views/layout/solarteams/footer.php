
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                common: {
                    init: function(){

                    }
                },
                'add-solar-team':{
                    init: function()
                    {
                        $('form#add-solar-team').submit(function(){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Processing form...</h2></div>' });
                            }
                        });
                    }
                },
                'edit-solar-team':{
                    init: function()
                    {
                        $('form#edit-solar-team').submit(function(){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Processing form...</h2></div>' });
                            }
                        });
                    }
                },
                'view-reps':{
                    init: function(){
                        //console.log('correct');
                        $('table#view_reps_table').filterTable({
                            inputSelector: '#table_searcher'
                        });

                        $('table#view_reps_table').stickyTableHeaders();
                    }
                },
                'shipto-reps' :{
                    init: function(){
                        $("select#client_selector").change(function(e){
                            if($(this).val() > 0)
                            {
                                window.location.href = "/sales-reps/ship-to-reps/client="+$(this).val();
                            }
                        });
                    }
                }
            }

            //run the script for the current page
            actions[config.curPage].init();
        </script>
        <?php Database::closeConnection(); ?>
    </body>
</html>