
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                common: {
                    init: function(){

                    }
                },
                'add-sales-rep':{
                    init: function()
                    {
                        autoCompleter.addressAutoComplete($('#address'));
                        autoCompleter.suburbAutoComplete($('#suburb'));
                        $('form#add-sales-rep').submit(function(){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Processing form...</h2></div>' });
                            }
                        });
                    }
                },
                'edit-sales-rep':{
                    init: function()
                    {
                        autoCompleter.addressAutoComplete($('#address'));
                        autoCompleter.suburbAutoComplete($('#suburb'));
                        $('form#edit-sales-rep').submit(function(){
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