
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
                        $('a#customer_address_toggle').toggle(
                            function(){
                                $(this).text('Show Address Details');
                            },
                            function(){
                                $(this).text('Hide Address Details');
                        });
                    }
                },
                'view-jobs':{
                    init: function(){

                    }
                },
                'add-customer':{
                    init: function(){

                    }
                },
                'view-customers':{
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