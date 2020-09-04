
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                common: {
                    init: function(){
                        autoCompleter.addressAutoComplete($('#address'));
                        autoCompleter.suburbAutoComplete($('#suburb'));
                    }
                },
                'add-supplier':{
                    init: function(){
                        actions.common.init();
                        $('form#add_production_supplier').submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Adding Supplier...</h2></div>' });
                            }
                        });
                    }
                },
                'edit-supplier':{
                    init: function(){
                        actions.common.init();
                        $('form#edit_production_supplier').submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Updating Details...</h2></div>' });
                            }
                        });
                    }
                },
                'view-suppliers':{
                    init: function(){
                        dataTable.init($('table#supplier_list_table'), {
                            "order": []
                        } );
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