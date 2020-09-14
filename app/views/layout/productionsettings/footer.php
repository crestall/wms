
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                common: {
                    init: function(){

                    }
                },
                'job-status':{
                    init: function(){
                        $("form#add-job-status").submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Adding Job Status...</h2></div>' });
                            }
                            else
                            {
                                return false;
                            }
                        });
                        $("form.edit-job-status").submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Editing Job Status...</h2></div>' });
                            }
                            else
                            {
                                return false;
                            }
                        });
                        $('input.default_checkbox').click(function() {
                            $('input.default_checkbox').not(this).prop("checked", false);
                        });
                        $('.color-picker').spectrum({
                            type: "component",
                            showInput: "true"
                        });
                    }
                },
                'job-csv-import':{
                    init: function(){
                        $('form#bulk_production_add').submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Uploading and Processing Jobs...</h2></div>' });
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