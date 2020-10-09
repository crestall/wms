
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                'view-runsheets':{
                    init: function(){
                        dataTable.init($('table#runsheets_table'), {
                            /* No ordering applied by DataTables during initialisation */
                            "order": []
                        });
                    }
                },
                'print-runsheet':{
                    init: function(){
                        $('button.runsheet').click(function(e){
                            var form = document.createElement('form');
                            form.setAttribute("method", "post");
                            form.setAttribute("action", "/pdf/printRunsheet");
                            form.setAttribute("target", "formresult");
                            document.body.appendChild(form);
                            window.open('','formresult');
                            form.submit();
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