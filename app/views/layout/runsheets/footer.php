
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
                        $('button.complete-tasks').click(function(e){
                            var runsheet_id = $(this).data('runsheetid');
                            var tids = $(this).data('taskids');
                            var task_ids = [];
                            $.each(tids, function(i, v){
                                if($('input#select_'+v).prop('checked'))
                                {
                                    task_ids.push(v);
                                }
                            })
                            console.log('ids to deal with: '+task_ids);
                        });


                        $('button.remove-tasks').click(function(e){
                            var runsheet_id = $(this).data('runsheetid');
                            var tids = $(this).data('taskids');
                            var task_ids = [];
                            $.each(tids, function(i, v){
                                if($('input#select_'+v).prop('checked'))
                                {
                                    task_ids.push(v);
                                }
                            })
                            //console.log('ids to deal with: '+task_ids);
                            if(!task_ids.length)
                            {
                                swal({
                                    title: "No Tasks Selected",
                                    text: "No tasks have been selected to delete",
                                    icon: "error"
                                });
                            }
                            else
                            {
                                swal({
                                    title: "Really remove task(s) from the runsheet?",
                                    text: "This cannot be undone",
                                    icon: "warning",
                                    buttons: true,
                                    dangerMode: true
                                }).then( function(removeTask) {
                                    if(removeTask)
                                    {
                                        $.ajax({
                                            url: '/ajaxfunctions/remove-tasks-from-runsheet',
                                            method: 'post',
                                            data: {
                                                task_ids: task_ids,
                                                runsheet_id: runsheet_id
                                            },
                                            dataType: 'json',
                                            beforeSend: function(){
                                                $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Removing From Runsheet...</h1></div>' });
                                            },
                                            success: function(d){
                                                if(d.error)
                                                {
                                                    $.unblockUI();
                                                    alert('error');
                                                }
                                                else
                                                {
                                                    location.reload(true);
                                                    //window.location.href = "http://stackoverflow.com";
                                                }
                                            },
                                            error: function(jqXHR, textStatus, errorThrown){
                                                $.unblockUI();
                                                document.open();
                                                document.write(jqXHR.responseText);
                                                document.close();
                                            }
                                        });
                                    }
                                });
                            }
                        });
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