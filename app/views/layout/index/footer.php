
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                init: function(){

                }
            }
            //console.log('current page: '+config.curPage);
            actions.init();
        </script>
        <?php Database::closeConnection(); ?>
    </body>
</html>