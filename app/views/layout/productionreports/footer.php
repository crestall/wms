
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                common: {
                    init: function(){

                    }
                },
                'warehouse-orders': {
                    init: function(){
                        $('#client_selector,  #status_selector').change(function(e){
                            $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h1>Collecting data...</h1></div>' });
                            var href = '/production-reports/warehouse-orders';
                            if($('#client_selector').val() != 0)
                                href += "/client="+$('#client_selector').val();
                            if($('#status_selector').val() != -1)
                                href += "/status="+$('#status_selector').val();
                            href += "/from="+$('#date_from_value').val();
                            href += "/to="+$('#date_to_value').val(); 
                            window.location.href = href;
                        });
                        datePicker.betweenDates();
                        $('button#change_dates').click(function(e){
                            e.preventDefault();
                            $.blockUI({ message: '<div style="height:120px; padding-top:40px;"><h1>Collecting Orders...</h1></div>' });
                            var from = $('#date_from_value').val();
                            var to = $('#date_to_value').val();
                            window.location.href = "/orders/client-orders/from="+from+"/to="+to;
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