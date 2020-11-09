
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                common: {
                    jobsTable: function(){
                        var table = dataTable.init($('table#production_jobs_table'), {
                            /* No ordering applied by DataTables during initialisation */
                            "order": []
                        });
                        table.on( 'draw', function () {
                            console.log( 'Redraw occurred at: '+new Date().getTime() );
                            $('.selectpicker').selectpicker();
                        });
                    },
                    selectAll: function(){
                        $('#select_all').click(function(e){
                            var checked = this.checked;
                             $('.select').each(function(e){
                                this.checked =  checked;
                             })
                        });
                        $('#status_all').change(function(e){
                            var c = $(this).val();
                            $("select.status").each(function(i,e){
                                $(this).val(c).change();
                            });
                        });
                    },
                    doDates: function(){
                        $( "#date_entered" ).datepicker({
                            changeMonth: true,
                            changeYear: true,
                            dateFormat: "dd/mm/yy",
                            onSelect: function(selectedDate) {
                                var d = new Date( selectedDate.replace( /(\d{2})[-/](\d{2})[-/](\d{4})/, "$2/$1/$3") );
                                s = d.valueOf()/1000;
                                $('#date_entered_value').val(s);
                            }
                        });
                        $('#date_entered_calendar').css('cursor', 'pointer').click(function(e){
                            $('input#date_entered').focus();
                        });
                        $( ".runsheet_day" ).datepicker({
                            changeMonth: true,
                            changeYear: true,
                            dateFormat: "dd/mm/yy",
                            onSelect: function(selectedDate) {
                                var d = new Date( selectedDate.replace( /(\d{2})[-/](\d{2})[-/](\d{4})/, "$2/$1/$3") );
                                s = d.valueOf()/1000;
                                var $tr = $(this).closest('tr');
                                var ar = $tr.prop('id').split("_");
                                var job_id = ar[1];
                                //console.log('input: input#runsheet_daydate_value_'+job_id);
                                //console.log('s: '+s);
                                $('input#runsheet_daydate_value_'+job_id).val(s);
                            }
                        });
                        $('.runsheet_calendar').css('cursor', 'pointer').click(function(e){
                            var $tr = $(this).closest('tr');
                            var ar = $tr.prop('id').split("_");
                            var job_id = ar[1];
                            //console.log('Job ID: '+job_id);
                            $('input#runsheet_daydate_'+job_id).focus();
                        });
                        $( "#date_due" ).datepicker({
                            //showButtonPanel: true,
                            //closeText: 'Clear',
                            changeMonth: true,
                            changeYear: true,
                            dateFormat: "dd/mm/yy",
                            onClose: function(selectedDate){
                                //console.log('selecteddate: '+ selectedDate);
                                if(selectedDate == "")
                                {
                                    $('#date_due_value').val('');
                                    $('#date_due').val('');
                                }
                                else
                                {
                                    var d = new Date( selectedDate.replace( /(\d{2})[-/](\d{2})[-/](\d{4})/, "$2/$1/$3") );
                                    s = d.valueOf()/1000;
                                    $('#date_due_value').val(s);
                                }
                            }
                        });
                        $('#date_due_calendar').css('cursor', 'pointer').click(function(e){
                            $('input#date_due').focus();
                        });
                        $( "#date_ed" ).datepicker({
                            changeMonth: true,
                            changeYear: true,
                            dateFormat: "dd/mm/yy",
                            onClose: function(selectedDate){
                                //console.log('selecteddate: '+ selectedDate);
                                if(selectedDate == "")
                                {
                                    $('#date_ed_value').val('');
                                    $('#date_ded').val('');
                                }
                                else
                                {
                                    var d = new Date( selectedDate.replace( /(\d{2})[-/](\d{2})[-/](\d{4})/, "$2/$1/$3") );
                                    s = d.valueOf()/1000;
                                    $('#date_ed_value').val(s);
                                }
                            }
                        });
                        $('#date_ed_calendar').css('cursor', 'pointer').click(function(e){
                            $('input#date_ed').focus();
                        });
                    },
                    autoComplete: function(){
                        autoCompleter.addressAutoComplete($('#customer_address'), 'customer_');
                        $("input#customer_name").each(function(i,e){
                            if($(this).data('ui-autocomplete') != undefined)
                            {
                                $(this).autocomplete( "destroy" );
                            }
                            autoCompleter.productionJobCustomerAutoComplete($(this), selectCustomerCallback, changeCustomerCallback);
                        });
                        function selectCustomerCallback(event, ui)
                        {
                            $('input#customer_contact').val(ui.item.contact);
                            $('input#customer_email').val(ui.item.email);
                            $('input#customer_phone').val(ui.item.phone);
                            $('input#customer_id').val(ui.item.customer_id);
                            $('input#customer_address').val(ui.item.address);
                            $('input#customer_address2').val(ui.item.address_2);
                            $('input#customer_suburb').val(ui.item.suburb);
                            $('input#customer_state').val(ui.item.state);
                            $('input#customer_country').val(ui.item.country);
                            $('input#customer_postcode').val(ui.item.postcode);
                            if($('#send_to_customer').prop('checked'))
                            {
                                $('input#address').val(ui.item.address).valid();
                                $('input#address2').val(ui.item.address_2);
                                $('input#suburb').val(ui.item.suburb).valid();
                                $('input#state').val(ui.item.state).valid();
                                $('input#country').val(ui.item.country).valid();
                                $('input#postcode').val(ui.item.postcode).valid();
                            }
                            else
                            {
                                $('input#address').val('');
                                $('input#address2').val('');
                                $('input#suburb').val('');
                                $('input#state').val('');
                                $('input#country').val('');
                                $('input#postcode').val('');
                            }
                            return false;
                        }
                        function changeCustomerCallback(event, ui)
                        {
                            if (!ui.item)
                	        {
                                $('input#customer_id').val(0);
                                $('input.customer').each(function(element, index){
                                    $(this).val("");
                                })
                                return false;
                            }
                        }
                        autoCompleter.addressAutoComplete($('#address'));
                        autoCompleter.suburbAutoComplete($('#suburb'));
                        autoCompleter.addressAutoComplete($('#finisher_address'), 'finisher_');
                        autoCompleter.suburbAutoComplete($('#finisher_suburb'), 'finisher_');
                        $("input#finisher_name").each(function(i,e){
                            if($(this).data('ui-autocomplete') != undefined)
                            {
                                $(this).autocomplete( "destroy" );
                            }
                            autoCompleter.productionJobFinisherAutoComplete($(this), selectFinisherCallback, changeFinisherCallback);
                        });
                        function selectFinisherCallback(event, ui)
                        {
                            $('input#finisher_contact').val(ui.item.contact);
                            $('input#finisher_email').val(ui.item.email);
                            $('input#finisher_phone').val(ui.item.phone);
                            $('input#finisher_id').val(ui.item.finisher_id);
                            $('input#finisher_address').val(ui.item.address);
                            $('input#finisher_address2').val(ui.item.address_2);
                            $('input#finisher_suburb').val(ui.item.suburb);
                            $('input#finisher_state').val(ui.item.state);
                            $('input#finisher_country').val(ui.item.country);
                            $('input#finisher_postcode').val(ui.item.postcode);
                            if($('#send_to_finisher').prop('checked'))
                            {
                                $('input#address').val(ui.item.address).valid();
                                $('input#address2').val(ui.item.address_2);
                                $('input#suburb').val(ui.item.suburb).valid();
                                $('input#state').val(ui.item.state).valid();
                                $('input#country').val(ui.item.country).valid();
                                $('input#postcode').val(ui.item.postcode).valid();
                            }
                            else
                            {
                                $('input#address').val('');
                                $('input#address2').val('');
                                $('input#suburb').val('');
                                $('input#state').val('');
                                $('input#country').val('');
                                $('input#postcode').val('');
                            }
                            return false;
                        }
                        function changeFinisherCallback(event, ui)
                        {
                            if (!ui.item)
                	        {
                                $('input#finisher_id').val(0);
                                $('input.finisher').each(function(element, index){
                                    $(this).val("");
                                })
                                return false;
                            }
                        }
                        autoCompleter.addressAutoComplete($('#finisher2_address'), 'finisher2_');
                        autoCompleter.suburbAutoComplete($('#finisher2_suburb'), 'finisher2_');
                        $("input#finisher2_name").each(function(i,e){
                            if($(this).data('ui-autocomplete') != undefined)
                            {
                                $(this).autocomplete( "destroy" );
                            }
                            autoCompleter.productionJobFinisherAutoComplete($(this), selectFinisher2Callback, changeFinisher2Callback);
                        });
                        function selectFinisher2Callback(event, ui)
                        {
                            $('input#finisher2_contact').val(ui.item.contact);
                            $('input#finisher2_email').val(ui.item.email);
                            $('input#finisher2_phone').val(ui.item.phone);
                            $('input#finisher2_id').val(ui.item.finisher_id);
                            $('input#finisher2_address').val(ui.item.address);
                            $('input#finisher2_address2').val(ui.item.address_2);
                            $('input#finisher2_suburb').val(ui.item.suburb);
                            $('input#finisher2_state').val(ui.item.state);
                            $('input#finisher2_country').val(ui.item.country);
                            $('input#finisher2_postcode').val(ui.item.postcode);
                            if($('#send_to_finisher2').prop('checked'))
                            {
                                $('input#address').val(ui.item.address).valid();
                                $('input#address2').val(ui.item.address_2);
                                $('input#suburb').val(ui.item.suburb).valid();
                                $('input#state').val(ui.item.state).valid();
                                $('input#country').val(ui.item.country).valid();
                                $('input#postcode').val(ui.item.postcode).valid();
                            }
                            else
                            {
                                $('input#address').val('');
                                $('input#address2').val('');
                                $('input#suburb').val('');
                                $('input#state').val('');
                                $('input#country').val('');
                                $('input#postcode').val('');
                            }
                            return false;
                        }
                        function changeFinisher2Callback(event, ui)
                        {
                            if (!ui.item)
                	        {
                                $('input#finisher2_id').val(0);
                                $('input.finisher2').each(function(element, index){
                                    $(this).val("");
                                })
                                return false;
                            }
                        }
                        autoCompleter.addressAutoComplete($('#finisher3_address'), 'finisher3_');
                        autoCompleter.suburbAutoComplete($('#finisher3_suburb'), 'finisher3_');
                        $("input#finisher3_name").each(function(i,e){
                            if($(this).data('ui-autocomplete') != undefined)
                            {
                                $(this).autocomplete( "destroy" );
                            }
                            autoCompleter.productionJobFinisherAutoComplete($(this), selectFinisher3Callback, changeFinisher3Callback);
                        });
                        function selectFinisher3Callback(event, ui)
                        {
                            $('input#finisher3_contact').val(ui.item.contact);
                            $('input#finisher3_email').val(ui.item.email);
                            $('input#finisher3_phone').val(ui.item.phone);
                            $('input#finisher3_id').val(ui.item.finisher_id);
                            $('input#finisher3_address').val(ui.item.address);
                            $('input#finisher3_address2').val(ui.item.address_2);
                            $('input#finisher3_suburb').val(ui.item.suburb);
                            $('input#finisher3_state').val(ui.item.state);
                            $('input#finisher3_country').val(ui.item.country);
                            $('input#finisher3_postcode').val(ui.item.postcode);
                            if($('#send_to_finisher3').prop('checked'))
                            {
                                $('input#address').val(ui.item.address).valid();
                                $('input#address2').val(ui.item.address_2);
                                $('input#suburb').val(ui.item.suburb).valid();
                                $('input#state').val(ui.item.state).valid();
                                $('input#country').val(ui.item.country).valid();
                                $('input#postcode').val(ui.item.postcode).valid();
                            }
                            else
                            {
                                $('input#address').val('');
                                $('input#address2').val('');
                                $('input#suburb').val('');
                                $('input#state').val('');
                                $('input#country').val('');
                                $('input#postcode').val('');
                            }
                            return false;
                        }
                        function changeFinisher3Callback(event, ui)
                        {
                            if (!ui.item)
                	        {
                                $('input#finisher3_id').val(0);
                                $('input.finisher3').each(function(element, index){
                                    $(this).val("");
                                })
                                return false;
                            }
                        }
                    }
                },
                'add-job':{
                    init: function(){
                        actions.common.autoComplete();
                        actions.common.doDates();
                        $("form#add_production_job").submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Adding the Job...</h2></div>' });
                            }
                        });
                        $('select#status, #state, #postcode, #suburb, #country').change(function(e){
                            $(this).valid();
                        });
                        var $checkboxes = $("input.send_to_address");
                        $checkboxes.click(function(){
                            console.log('click');
                            $checkboxes.not(this).prop('checked', false);
                        });
                    }
                },
                'view-jobs':{
                    init: function(){
                        actions.common.jobsTable();
                        actions.common.selectAll();
                        actions.common.doDates();
                        //update job status
                        $('button#status').click(function(e){
                            if(!$('input.select:checked').length)
                            {
                                swal({
                                    title: "No Jobs Selected",
                                    text: "Please select at least one job to update its status",
                                    icon: "error"
                                });
                            }
                            else
                            {
                                swal({
                                    title: "Update the status?",
                                    text: "This can only be undone by changing it back",
                                    icon: "warning",
                                    buttons: true,
                                    dangerMode: true
                                }).then( function(changeStatus) {
                                    if(changeStatus)
                                    {
                                        var ids = [];
                                        $('input.select').each(function(i,e){
                                            if($(this).prop('checked') )
                                            {
                                                var job_id = $(this).data('jobid');
                                                var status_id = $('select#status_'+job_id).val();
                                                var ent = {
                                                    jobid: job_id,
                                                    statusid: status_id
                                                }
                                                ids.push(ent);
                                            }
                                        });
                                        $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Updating Status...</h1></div>' });
                                        var data = {jobids: ids};
                                        $.post('/ajaxfunctions/update-job-status', data, function(d){
                                            location.reload();
                                        });

                                    }
                                });
                            }
                        });
                        //add to driver runsheet
                        $('button#runsheet').click(function(e){
                            if(!$('input.select:checked').length)
                            {
                                swal({
                                    title: "No Jobs Selected",
                                    text: "Please select at least one job to add to the runsheet",
                                    icon: "error"
                                });
                            }
                            else
                            {
                                var rs_count = $('input.select:checked').length
                                swal({
                                    title: "Add "+rs_count+" orders to the runsheet?",
                                    text: "This will add the selected orders to the day's runsheet\n\nor create a new runsheet if one doe not exist",
                                    icon: "warning",
                                    buttons: true,
                                    dangerMode: true
                                }).then( function(addToSheet) {
                                    if(addToSheet)
                                    {
                                        var runsheet_days = [];
                                        $('input.select').each(function(i,e){
                                            var job_id = $(this).data('jobid');
                                            var daydate_value = $('input#runsheet_daydate_value_'+job_id).val();
                                            if($(this).prop('checked') )
                                            {
                                                var ent = {
                                                    'timestamp' : daydate_value,
                                                    'job_id'    : job_id
                                                }
                                                runsheet_days.push(ent);
                                            }
                                        });
                                        //console.log(runsheet_days);
                                        /**/
                                        $.ajax({
                                            url: '/ajaxfunctions/add-job-runsheets',
                                            method: 'post',
                                            data: {
                                                runsheets: runsheet_days
                                            },
                                            dataType: 'json',
                                            beforeSend: function(){
                                                $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Creating/Editing Runsheet...</h1></div>' });
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
                        $('button.remove-from-runsheet').click(function(e){
                            var job_id = $(this).data('jobid');
                            var runsheet_id = $(this).data('runsheetid');
                            swal({
                                    title: "Really remove this job from the runsheet?",
                                    text: "This cannot be undone",
                                    icon: "warning",
                                    buttons: true,
                                    dangerMode: true
                                }).then( function(removeFromSheet) {
                                    if(removeFromSheet)
                                    {
                                        //console.log('job id: '+job_id);
                                        //console.log('runsheet id: '+runsheet_id);
                                        $.ajax({
                                            url: '/ajaxfunctions/remove-job-from-runsheet',
                                            method: 'post',
                                            data: {
                                                job_id: job_id,
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
                        })
                        //end add to driver runsheet
                    }
                },
                'update-job':{
                    init: function(){
                        actions.common.doDates();
                        actions.common.autoComplete();
                        $('button#job_details_update_submitter').click(function(e){
                            $('form#job_details_update').submit();
                        });
                        $('button#customer_details_update_submitter').click(function(e){
                            $('form#customer_details_update').submit();
                        });
                        $('button#finisher_details_update_submitter').click(function(e){
                            $('form#finisher_details_update').submit();
                        });
                        $('form#job_details_update, form#customer_details_update, form#finisher_details_update').submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Updating Details...</h2></div>' });
                            }
                        });
                        $('select#status').change(function(e){
                            $(this).valid();
                        });
                    }
                },
                'job-search':{
                    init: function(){
                        datePicker.betweenDates();
                        $('form#job_order_search').submit(function(e){
                            $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Searching For Jobs...</h2></div>' });
                        });
                    }
                },
                'job-search-results':{
                    init: function(){
                        actions['job-search'].init();
                        actions.common.jobsTable();
                        actions.common.selectAll();
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