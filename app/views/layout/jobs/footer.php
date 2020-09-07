
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
                        autoCompleter.addressAutoComplete($('#customer_address', 'customer_'));
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
                            return false;
                        }
                        function changeCallback(event, ui)
                        {
                            if (!ui.item)
                	        {
                                $(event.target).val("");
                                $('input#customer_id').val(0);
                                return false;
                            }
                        }
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