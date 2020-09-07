
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                common: {
                    init: function(){

                    }
                },
                'add-job':{
                    init: function(){
                        autoCompleter.addressAutoComplete($('#customer_address'), 'customer_');
                        $("input#customer_name").each(function(i,e){
                            if($(this).data('ui-autocomplete') != undefined)
                            {
                                $(this).autocomplete( "destroy" );
                            }
                            autoCompleter.productionJobCustomerAutoComplete($(this), selectCallback, changeCallback);
                        });
                        function selectCallback(event, ui)
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
                        function changeCallback(event, ui)
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
                        $("form#add_production_job").submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Adding the Job...</h2></div>' });
                            }
                        });
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
                        $('i.fa-calendar-alt').css('cursor', 'pointer').click(function(e){
                            $(this).closest('div.input-group').find('input.form-control').focus();
                        });
                    }
                },
                'view-jobs':{
                    init: function(){

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