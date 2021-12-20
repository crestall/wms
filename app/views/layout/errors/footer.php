
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                common: {
                    init: function(){

                    }
                },
                'error-400': {
                    init: function(){

                    }
                },
                'error-401': {
                    init: function(){

                    }
                },
                'error-403': {
                    init: function(){

                    }
                },
                'error-404': {
                    init: function(){

                    }
                },
                'error-500': {
                    init: function(){

                    }
                },
                'errors':{
                    init:function(){

                    }
                }
            }
            //run the script for the current page
            actions[config.curPage].init();
            actions.common.init();
        </script>
        <?php Database::closeConnection(); ?>
    </body>
</html>