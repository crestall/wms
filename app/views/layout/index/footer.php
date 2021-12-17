
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                init: function(){
                    $('a.controller-index-link')
                        .click(function(e){
                            //e.preventDefault();
                            $(this).removeClass().addClass("btn btn-lg btn-clicked-inactive");
                            $(this).html("<i class='fad fa-circle-notch fa-3x fa-spin'></i><br>Loading Page");
                        });
                }
            }
            //console.log('current page: '+config.curPage);
            actions.init();
        </script>
        <?php Database::closeConnection(); ?>
    </body>
</html>