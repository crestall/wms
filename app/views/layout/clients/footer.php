
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                common: {
                    init: function(){
                        $('#ufa').click(function(e) {
                            if($(this).is(':checked'))
                            {
                                var e = $('#billing_email').val();
                                $('#sales_email').val(e).valid();
                                $('#inventory_email').val(e).valid();
                                $('#deliveries_email').val(e).valid();
                                var c = $('#contact_name').val();
                                $('#sales_contact').val(c);
                                $('#inventory_contact').val(c);
                                $('#deliveries_contact').val(c);
                                //$(this).closest('form').validate();
                            }
                            else
                            {
                                $('#sales_email').val('').valid();
                                $('#inventory_email').val('').valid();
                                $('#deliveries_email').val('').valid();
                                $('#sales_contact').val('');
                                $('#inventory_contact').val('');
                                $('#deliveries_contact').val('');
                            }
                        });

                        autoCompleter.addressAutoComplete($('#address'));
                        autoCompleter.suburbAutoComplete($('#suburb'));
                    }
                },
                'edit-client': {
                    init: function()
                    {
                        actions.common.init();
                        $('form#client_edit').submit(function(){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Processing form...</h2></div>' });
                            }
                        });
                    }
                },
                'add-client': {
                    init: function()
                    {
                        actions.common.init();
                        $('form#client_add').submit(function(){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Processing form...</h2></div>' });
                            }
                        });
                    }
                },
                'view-clients': {
                    init: function()
                    {

                    }
                }
            }

            //run the script for the current page
            actions[config.curPage].init();
        </script>
        <?php Database::closeConnection(); ?>
    </body>
</html>