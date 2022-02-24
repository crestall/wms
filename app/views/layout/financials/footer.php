
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                common: {
                    init: function(){

                    },
                    'choose-client' : function(page_name){
                        $('select#client_selector').change(function(e){
                            if($(this).val() > 0)
                            {
                                var from = $('#date_from_value').val();
                                var to = $('#date_to_value').val();
                                var client_id = $(this).val();
                                var url = '/financials/'+page_name+'/client='+client_id;
                                $.blockUI({ message: '<div style="height:120px; padding-top:40px;"><h2>Calculating Charges...</h2></div>' });
                                window.location.href = url;
                            }
                        });
                    },
                    'change-date' : function(page_name){
                        $('button#change_dates').click(function(e){
                            e.preventDefault();
                            $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Calculating Charges...</h1></div>' });
                            var from = $('#date_from_value').val();
                            var to = $('#date_to_value').val();
                            var client_id = $('#client_id').val();
                            var url = '/financials/'+page_name+'/client='+client_id+"/from="+from+"/to="+to;
                            window.location.href = url;
                        });
                    },
                    'data-table' : function(){
                        var dt_options = {
                            "paging": false,
                            "order": [],
                            "dom" : '<<"row"<"col-lg-4"><"col-lg-6">><"row">t>',
                            "processing": true,
                            "columnDefs":[
                                {"width" : "15%", "targets": [1,2,3]}
                            ],
                            "initComplete": function( settings, json ) {
                                //console.log(settings.sTableId);
                                $("div.waiting").each(function(ind,el){
                                	$(this).remove();
                                });
                                $("div.table_holder").each(function(ind,el){
                                	$(this).show();
                                });
                                $('#'+settings.sTableId).css({"width":"98%"});
                            }
                        };
                        dataTable.init($('table.financials'), dt_options);
                        dt_options['columnDefs'] = [
                           {"width" : "22.5%", "targets":[1,2]}
                        ];
                        console.log(dt_options);
                        dataTable.init($('table#delivery_handling_client_charges'), dt_options);
                    }
                },
                'delivery-client-charges':{
                    init: function(){
                        datePicker.betweenDates();
                        actions.common['choose-client']("delivery-client-charges");
                        actions.common['change-date']("delivery-client-charges");
                        actions.common['data-table']();
                    }
                },
                'pickpack-client-charges':{
                    init: function(){
                        datePicker.betweenDates();
                        actions.common['choose-client']("pickpack-client-charges");
                        actions.common['change-date']("pickpack-client-charges");
                        actions.common['data-table']();
                    }
                }
            }
            //run the script for the current page
            actions[config.curPage].init();
        </script>
        <?php Database::closeConnection(); ?>
    </body>
</html>