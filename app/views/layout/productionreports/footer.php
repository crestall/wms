
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
                            $.blockUI({ message: '<div style="height:120px; padding-top:40px;"><h1>Collecting Orders...</h1></div>' });
                            actions['warehouse-orders']['doUrl']();
                        });
                        datePicker.betweenDates();
                        $('button#change_dates').click(function(e){
                            e.preventDefault();
                            $.blockUI({ message: '<div style="height:120px; padding-top:40px;"><h1>Collecting Orders...</h1></div>' });
                            actions['warehouse-orders']['doUrl']();
                        });
                        var dt_options = {
                            "columnDefs": [
                                { "orderable": false, "targets": [5,6] },
                                { "searchable": false, "targets": [6]},
                                { "type": 'extract-date', "targets" : [2,3]}
                            ],
                            "paging": false,
                            "order": [],
                            "dom" : '<<"row"<"col-lg-4"><"col-lg-6">><"row">t>',
                            "mark": true
                        }
                        var table = dataTable.init($('table#production_orders_table'), dt_options );
                        $('#table_searcher').on( 'keyup', function () {
                            table.search( this.value ).draw();
                        } );
                    },
                    doUrl: function(){
                        var href = '/production-reports/warehouse-orders';
                        if($('#client_selector').val() != 0)
                            href += "/client="+$('#client_selector').val();
                        if($('#status_selector').val() != 0)
                            href += "/status="+$('#status_selector').val();
                        href += "/from="+$('#date_from_value').val();
                        href += "/to="+$('#date_to_value').val();
                        console.log('href='+href );
                        window.location.href = href;
                    }
                },
                'order-tracking' : {
                    init: function(){

                    }
                },
                'order-detail' : {
                    init: function(){
                        $('button#print').click(function(e){
                        	$("div#print_this").printArea({
                                    //put some options in
                            });;
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