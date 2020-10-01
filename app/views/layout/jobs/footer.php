
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                common: {
                    jobsTable: function(){
                        dataTable.init($('table#production_jobs_table'), {
                            /* No ordering applied by DataTables during initialisation */
                            "order": []
                        });
                    },
                    selectAll: function(){
                        $('#select_all').click(function(e){
                            var checked = this.checked;
                             $('.select').each(function(e){
                                this.checked =  checked;
                             })
                        });
                        $('select#driver_all').change(function(e){
                           	var c = $(this).val();
                            $("select.driver").each(function(i,e){
                                if(!$(this).prop('disabled'))
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
                        $('select#status').change(function(e){
                            $(this).valid();
                        });
                    }
                },
                'view-jobs':{
                    init: function(){
                        actions.common.jobsTable();
                        actions.common.selectAll();
                        //add to driver runsheet
                        $('button.driver-runsheet').click(function(e){
                            var rs_count = $('input.select:checked').length
                            swal({
                                title: "Add "+rc_count+" orders to the driver runsheet?",
                                text: "This will add the selected orders to the driver's runsheet\n\nor create a new runsheet if one doe not exist",
                                icon: "warning",
                                buttons: true,
                                dangerMode: true
                            }).then( function(willFulfill) {
                                /*var ids = [];
                                $('input.select').each(function(i,e){
                                    var order_id = $(this).data('orderid');
                                    console.log('order_id: '+ order_id);
                                    if($(this).prop('checked') && ( $('select#courier_'+order_id).val() == config.eParcelId || $('select#courier_'+order_id).val() == config.eParcelExpressId ))
                                    {
                                        ids.push(order_id);
                                    }
                                });
                                $.ajax({
                                    url: '/ajaxfunctions/fulfill-order',
                                    method: 'post',
                                    data: {
                                        order_ids: ids,
                                        courier_id: config.eParcelId
                                    },
                                    dataType: 'json',
                                    beforeSend: function(){
                                        $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Fulfilling Orders...</h1></div>' });
                                    },
                                    success: function(d){
                                        if(d.error)
                                        {
                                            $.unblockUI();
                                            alert('error');
                                        }
                                        else
                                        {
                                            location.reload();
                                        }
                                    },
                                    error: function(jqXHR, textStatus, errorThrown){
                                        $.unblockUI();
                                        document.open();
                                        document.write(jqXHR.responseText);
                                        document.close();
                                    }
                                });*/
                            });
                        });
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