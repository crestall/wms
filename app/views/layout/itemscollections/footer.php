
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                common: {
                    init: function(){

                    }
                },
                'record-collection':{
                    init: function()
                    {
                        //console.log("Record Collection");
                        autoCompleter.addressAutoComplete($('#address'));
                        autoCompleter.addressAutoComplete($('#puaddress'), "pu");
                        autoCompleter.suburbAutoComplete($('#suburb'));
                        autoCompleter.suburbAutoComplete($('#pusuburb', "pu"));
                        $('form#add-sales-rep').submit(function(){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Processing form...</h2></div>' });
                            }
                        });
                    }
                },
                'view-collections':{
                    init: function()
                    {

                    }
                },
                'update-collection':{
                    init: function(){

                    }
                }
            }

            //run the script for the current page
            actions[config.curPage].init();
        </script>
        <?php Database::closeConnection(); ?>
    </body>
</html>