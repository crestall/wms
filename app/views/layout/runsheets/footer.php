
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                'common':{
                    generateURL: function(url)
                    {
                        //var url = base;
                        if($('#date_from_value').val())
                            url += "/from="+$('#date_from_value').val();
                        if($('#date_to_value').val())
                            url += "/to="+$('#date_to_value').val();
                        if($('#client_selector').val() > 0)
                            url += "/client="+$('#client_selector').val();
                        if($('#customer_selector').val() > 0)
                            url += "/customer="+$('#customer_selector').val();
                        if($('#driver_selector').val() > 0)
                            url += "/driver="+$('#driver_selector').val();
                        window.location.href = url;
                    }
                },
                'completed-runsheets':{
                    init: function(){
                        datePicker.betweenDates();
                        $('button#change_dates').click(function(e){
                            e.preventDefault();
                            $.blockUI({ message: '<div style="height:120px; padding-top:40px;"><h1>Collecting Runsheets...</h1></div>' });
                            actions['common']['generateURL']('/runsheets/completed-runsheets');
                        });
                        $('select#client_selector').change(function(e){
                            $.blockUI({ message: '<div style="height:120px; padding-top:40px;"><h1>Collecting Runsheets...</h1></div>' });
                            actions['common']['generateURL']('/runsheets/completed-runsheets');
                        });
                        $('select#driver_selector').change(function(e){
                            $.blockUI({ message: '<div style="height:120px; padding-top:40px;"><h1>Collecting Runsheets...</h1></div>' });
                            actions['common']['generateURL']('/runsheets/completed-runsheets');
                        });
                        $('select#customer_selector').change(function(e){
                            $.blockUI({ message: '<div style="height:120px; padding-top:40px;"><h1>Collecting Runsheets...</h1></div>' });
                            actions['common']['generateURL']('/runsheets/completed-runsheets');
                        });
                    }
                },
                'prepare-runsheets':{
                    init: function(){
                        dataTable.init($('table#runsheets_table'), {
                            /* No ordering applied by DataTables during initialisation */
                            "order": []
                        });
                    }
                },
                'prepare-runsheet':{
                    init: function(){
                        //console.log('init');
                        $('.task').change(function(){
                            var taskid = $(this).data('taskid');
                            //console.log('taskid: '+taskid);
                            $('div#task_'+taskid+'_address_holder').toggle('blind', 500);
                            $('input#task_'+taskid+'_shipto, input#task_'+taskid+'_address, input#task_'+taskid+'_suburb, input#task_'+taskid+'_postcode').toggleClass('required');
                        });
                        $('.task').each(function(i,e){
                            if(!$(this).prop('checked'))
                            {
                                $(this).change();
                            }
                        });
                        //Address Auto Completers
                        $('input.address_ac').each(function(i,e){
                            var task_id = $(this).prop('id').split("_")[1];
                            //console.log('ac task id: '+task_id);
                            autoCompleter.addressAutoComplete($(this), 'task_'+task_id+'_');
                        });
                        $('input.suburb_ac').each(function(i,e){
                            var task_id = $(this).prop('id').split("_")[1];
                            //console.log('sac task id: '+task_id);
                            autoCompleter.suburbAutoComplete($(this), 'task_'+task_id+'_');
                        });
                        $('input.address_ac, input.suburb_ac, input.postcode_ac').each(function(i,e){
                            $(this).change(function(e){
                                $(this).valid();
                            })
                        });
                        $('form#prepare_runsheet').submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1 style="margin-left: 20px;margin-right: 20px;">Saving Runsheet Data...</h1></div>' });
                            }
                        })
                    }
                },
                'finalise-runsheet':{
                    init:function(){

                    }
                },
                'finalise-runsheets':{
                    init:function(){
                        $('button.complete-tasks').click(function(e){
                            return;
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
                            if(!task_ids.length)
                            {
                                swal({
                                    title: "No Tasks Selected",
                                    text: "No tasks have been selected to complete",
                                    icon: "error"
                                });
                            }
                            else
                            {
                                swal({
                                    title: "Really complete task(s)?",
                                    text: "This cannot be undone",
                                    icon: "warning",
                                    buttons: true,
                                    dangerMode: true
                                }).then( function(completeTask) {
                                    if(completeTask)
                                    {
                                        $.ajax({
                                            url: '/ajaxfunctions/complete-runsheet-tasks',
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
                },
                'view-runsheets':{
                    init:function(){
                        console.log('init');
                        $('button.print-sheet').each(function(i,e){
                            $(this).click(function(e){
                                var runsheet_id = $(this).data('runsheetid');
                                var driver_id = $(this).data('driverid');
                                console.log('runsheet_id: '+runsheet_id);
                                console.log('driver_id: '+driver_id);

                                var form = document.createElement('form');
                                form.setAttribute("method", "post");
                                form.setAttribute("action", "/pdf/printRunsheet");
                                //form.setAttribute("action", "/misc-functions/make-packslips-pdf.php");
                                form.setAttribute("target", "runsheetformresult");
                                var hiddenField = document.createElement("input");
                                hiddenField.setAttribute("type", "hidden");
                                hiddenField.setAttribute("name", "runsheet_id");
                                hiddenField.setAttribute("value", runsheet_id);
                                form.appendChild(hiddenField);
                                var hiddenField2 = document.createElement("input");
                                hiddenField2.setAttribute("type", "hidden");
                                hiddenField2.setAttribute("name", "driver_id");
                                hiddenField2.setAttribute("value", driver_id);
                                form.appendChild(hiddenField2);
                                document.body.appendChild(form);
                                window.open('','runsheetformresult');
                                form.submit();
                            });
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