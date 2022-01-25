
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                common: {
                    init: function(){

                    }
                },
                'delivery-client-charges':{
                    init: function(){
                        $('select#client_selector').change(function(e){
                            if($(this).val() > 0)
                            {
                                var from = $('#date_from_value').val();
                                var to = $('#date_to_value').val();
                                var client_id = $(this).val();
                                var url = '/financials/delivery-client-charges/client='+client_id;
                                if($('#date_from_value').val())
                                    url += "/from="+$('#date_from_value').val();
                                if($('#date_to_value').val())
                                    url += "/from="+$('#date_to_value').val();
                                $.blockUI({ message: '<div style="height:120px; padding-top:40px;"><h2>CCalculating Charges...</h2></div>' });
                                window.location.href = url;
                            }
                        });
                    }
                }
            }
            //run the script for the current page
            actions[config.curPage].init();
        </script>
        <?php Database::closeConnection(); ?>
    </body>
</html>