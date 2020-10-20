
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                'view-runsheets':{
                    init: function(){
                        dataTable.init($('table#runsheets_table'), {
                            /* No ordering applied by DataTables during initialisation */
                            "order": []
                        });
                    }
                },
                'print-runsheet':{
                    init: function(){
                        $('.task').click(function(e){
                            $('#task').valid();
                        });
                        $("form#print_runsheet").submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Creating Runsheet...</h2></div>' });
                                setTimeout(function () {
                                    window.location.reload(1);
                                }, 3000);
                            }
                        });
                    }
                },
                'finalise-runsheets':{
                    init:function(){
                        $('button.remove-tasks').click(function(e){
                            var runsheet_id = $(this).data('runsheetid');
                            var tids = $(this).data('taskids');
                            var task_ids = tids.split(',');
                            console.log('Runsheet ID: '+runsheet_id);
                            console.log('Task IDs: '+task_ids);
                        })
                    }
                }
            }
            //console.log('current page: '+config.curPage);
            //run the script for the current page
            actions[config.curPage].init();
        </script>
        <?php Database::closeConnection(); ?>
    </body>
</html>