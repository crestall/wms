
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
                                    url += "/to="+$('#date_to_value').val();
                                $.blockUI({ message: '<div style="height:120px; padding-top:40px;"><h2>Calculating Charges...</h2></div>' });
                                window.location.href = url;
                            }
                        });
                        datePicker.betweenDates();
                        $('button#change_dates').click(function(e){
                            e.preventDefault();
                            $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Calculating Charges...</h1></div>' });
                            var from = $('#date_from_value').val();
                            var to = $('#date_to_value').val();
                            var client_id = $('#client_id').val();
                            var url = '/financials/delivery-client-charges/client='+client_id+"/from="+from+"/to="+to;
                            window.location.href = url;
                        });
                        /* */
                        var dt_options = {
                            "paging": false,
                            "order": [],
                            "dom" : '<<"row"<"col-lg-4"><"col-lg-6">><"row">t>',
                            "mark": true,
                            "columnDefs":[
                                {"width" : "15%", "targets": [1,2,3]}
                            ],
                            "initComplete": function( settings, json ) {
                            }
                        };
                        dataTable.init($('table.financials'), dt_options);
                        //var dtable = dataTable.init($('table#delivery_client_charges'), dt_options );
                        //var gtable = dataTable.init($('table#general_client_charges'), dt_options );

                        /*
                        $('#table_searcher').on( 'keyup', function () {
                            dtable.search( this.value ).draw();
                        } );
                        */
                    }
                }
            }
            //run the script for the current page
            actions[config.curPage].init();
        </script>
        <?php Database::closeConnection(); ?>
    </body>
</html>