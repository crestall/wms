
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                common: {
                    init: function(){

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
                        autoCompleter.addressAutoComplete($('#supplier_address'), 'supplier_');
                        $("input#supplier_name").each(function(i,e){
                            if($(this).data('ui-autocomplete') != undefined)
                            {
                                $(this).autocomplete( "destroy" );
                            }
                            autoCompleter.productionJobSupplierAutoComplete($(this), selectSupplierCallback, changeSupplierCallback);
                        });
                        function selectSupplierCallback(event, ui)
                        {
                            $('input#supplier_contact').val(ui.item.contact);
                            $('input#supplier_email').val(ui.item.email);
                            $('input#supplier_phone').val(ui.item.phone);
                            $('input#supplier_id').val(ui.item.customer_id);
                            $('input#supplier_address').val(ui.item.address);
                            $('input#supplier_address2').val(ui.item.address_2);
                            $('input#supplier_suburb').val(ui.item.suburb);
                            $('input#supplier_state').val(ui.item.state);
                            $('input#supplier_country').val(ui.item.country);
                            $('input#supplier_postcode').val(ui.item.postcode);
                            return false;
                        }
                        function changeSupplierCallback(event, ui)
                        {
                            if (!ui.item)
                	        {
                                $('input#supplier_id').val(0);
                                $('input.supplier').each(function(element, index){
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
                        dataTable.init($('table#production_jobs_table'), {
                            /* No ordering applied by DataTables during initialisation */
                            "order": []
                        });
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
                        $('button#supplier_details_update_submitter').click(function(e){
                            $('form#supplier_details_update').submit();
                        });
                        $('form#job_details_update, form#customer_details_update, form#supplier_details_update').submit(function(e){
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