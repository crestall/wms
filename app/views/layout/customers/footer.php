
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                common: {
                    init: function(){
                        autoCompleter.addressAutoComplete($('#address'));
                        autoCompleter.suburbAutoComplete($('#suburb'));
                    },
                    addContact: function(){
                        $("a.add-contact").click(function(e){
                            e.preventDefault();
                            var contact_count = $("div#contacts_holder div.acontact").length;
                            //console.log('packages: '+contact_count);
                            var data = {
                                i: contact_count
                            }
                            $.post('/ajaxfunctions/addFinisherContact', data, function(d){
                                $('div#contacts_holder').append(d.html);
                            });
                        });
                        $("a#remove-all-contacts").click(function(e){
                            e.preventDefault();
                            $('div#contacts_holder div.acontact').not(':first').remove();
                        });
                    }
                },
                'add-customer':{
                    init: function(){
                        actions.common.init();
                        actions.common.addContact();
                        $('form#add_production_customer').submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Adding Customer...</h2></div>' });
                            }
                        });
                    }
                },
                'view-customers':{
                    init: function(){
                        dataTable.init($('table#customer_list_table'), {
                            "order": []
                        } );
                    }
                },
                'edit-customer':{
                    init: function(){
                        actions.common.init();
                        $('form#edit_production_customer').submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Updating Customer...</h2></div>' });
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