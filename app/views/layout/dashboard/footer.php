
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                common: {
                    init: function(){
                        
                        $('a#toggle_orders, a#client_activity, a#toggle_inventory, a#toggle_pickups, a#toggle_storeorders, a#toggle_solarorders, a#toggle_solarinstalls, a#toggle_solarservice').click(function(e){
                            $(this).toggleClass('hiding');
                        });

                    }
                },
                admin: {
                    init: function(){
                        actions.common.init();
                        var maxHeight = 0;
                        $("div.order-panel").each(function(){
                            if ($(this).height() > maxHeight) { maxHeight = $(this).height(); }
                        });
                        $("div.order-panel").height(maxHeight);
                    }
                },
                client: {
                    init: function(){
                        $('div#products_chart').html("<p class='text-center'><img class='loading' src='/images/preloader.gif' alt='loading...' /><br />Fetching Chart Data</p>");
                        $('div#orders_chart').html("<p class='text-center'><img class='loading' src='/images/preloader.gif' alt='loading...' /><br />Fetching Chart Data</p>");
                        google.charts.load('current', {'packages':['corechart']});
                        google.charts.setOnLoadCallback(drawClientCharts);
                        var params = {
                            client_id: $('#client_id').val(),
                            from: $('#from_value').val(),
                            to: $('#to_value').val()
                        }

                        function drawClientCharts()
                    	{
                    		$.ajax({
                    			url: "/ajaxfunctions/getOrderTrends",
                    			dataType:"json",
                    			data: params,
                    			type: 'post',
                                success: function(jsonData)
                                {
                                    //var jData =  $.parseJSON(jsonData);
                            		var data = google.visualization.arrayToDataTable(jsonData);
                                    var num_orders = jsonData.length - 1;
                            		if(num_orders > 0)
                            		{
                                		var options = {
                                			hAxis: {
                                				title: 'Week Beginning',
                                				showTextEvery: 1,
                                				slantedText:true,
                                				slantedTextAngle:-45
                                			},
                                			vAxes: {
                                				0: {
                                					title: 'Order Count',
                                					viewWindow: {
                                						min: 0
                                					}
                                				}
                                			},
                                			legend: {
                                				position: 'top'
                                			},
                                			height: 450,
                                			series: {
                                				0:{type: "line", targetAxisIndex:0} ,
                                                1:{type: "line", targetAxisIndex:0}
                                			},
                                            title: "Weekly Orders: Totals/Averages Last Three Months",
                                            titleTextStyle: {
                            					fontSize: 20,
                            					color: '##5F5F5E;',
                            					bold: false,
                            					italic: false,
                            					marginBottom: 20
                                            },
                                		};

                                		var chart = new google.visualization.LineChart(document.getElementById('orders_chart'));
                                		chart.draw(data, options);
                                    }
                                    else
                                    {
                                        $('div#orders_chart').html("<div class='errorbox'><h2>No Orders Placed</h2><p>There have been no orders fulfilled in the last three months</p></div>");
                                    }
                                }
                    		});
                            $.ajax({
                    			url: "/ajaxfunctions/getTopProducts",
                    			dataType:"json",
                    			data: params,
                    			type: 'post',
                                success: function(jData2)
                                {
                                    var data2 = google.visualization.arrayToDataTable(jData2);
                                    var num_products = jData2.length - 1;
                            		if(num_products > 0)
                            		{
                                		var options2 = {
                                			hAxis: {
                                				title: 'Product',
                                				showTextEvery: 1,
                                				slantedText:true,
                                				slantedTextAngle:-45
                                			},
                                			vAxes: {
                                				0: {
                                					title: 'Total Ordered',
                                					viewWindow: {
                                						min: 0
                                					}
                                				}
                                			},
                                			legend: {
                                				position: 'top'
                                			},
                                			height: 450,
                                            title: "Top "+num_products+" Ordered Items: Last Three Months",
                                            titleTextStyle: {
                            					fontSize: 20,
                            					color: '##5F5F5E;',
                            					bold: false,
                            					italic: false,
                            					marginBottom: 20
                                            },
                                		};

                                		var chart2 = new google.visualization.ColumnChart(document.getElementById('products_chart'));
                                		chart2.draw(data2, options2);
                                    }
                                    else
                                    {
                                        $('div#products_chart').html("");
                                    }
                                }
                    		});
                    	}
                    }
                },
                warehouse: {
                    init: function(){
                        actions.common.init();
                        var maxHeight = 0;
                        $("div.inventory-panel").each(function(){
                            if ($(this).height() > maxHeight) { maxHeight = $(this).height(); }
                        });
                        $("div.inventory-panel").height(maxHeight);
                    }
                },
                'solar_admin': {
                    init: function(){
                        //actions.common.init();
                        var maxHeight = 0;
                        $("div.inventory-panel").each(function(){
                            if ($(this).height() > maxHeight) { maxHeight = $(this).height(); }
                        });
                        $("div.inventory-panel").height(maxHeight);

                        $('a#toggle_solarorders, a#toggle_solarinstalls, a#toggle_solarservice, a#solar_activity').click(function(e){
                            $(this).toggleClass('hiding');
                        });

                        $('div#solar_install_chart').html("<p class='text-center'><img class='loading' src='/images/preloader.gif' alt='loading...' /><br />Fetching Chart Data</p>");
                        $('div#solar_service_chart').html("<p class='text-center'><img class='loading' src='/images/preloader.gif' alt='loading...' /><br />Fetching Chart Data</p>");

                        google.charts.load('current', {'packages':['corechart']});
                        google.charts.setOnLoadCallback(drawSolarCharts);
                        var params = {
                            from: $('#solar_admin_from_value').val(),
                            to: $('#solar_admin_to_value').val()
                        }

                        function drawSolarCharts()
                        {
                            $.ajax({
                    			url: "/ajaxfunctions/getSolarInstalls",
                    			dataType:"json",
                    			data: params,
                    			type: 'post',
                                success: function(jsonData)
                                {
                                    console.log(jsonData);
                                    var data = google.visualization.arrayToDataTable(jsonData);
                                    var options = {
                                        title :'Total Weekly Installs',
                                        titleTextStyle: {
                                            fontSize: 21,
                                            bold: false,
                                            fontColor: "#333"
                                        },
                                        hAxis: {
                                            title: 'Week Ending',
                                            //showTextEvery: 1,
                            				slantedText:true,
                            			  	slantedTextAngle:-45
                                        },
                                        vAxis: {
                                            title: 'Total Orders'
                                        },
                                        legend: {
                                            position: 'top'
                                        },
                                        height: 450,
                                        isStacked: true
                                    };

                                    var chart = new google.visualization.ColumnChart(document.getElementById('solar_install_chart'));

                                    chart.draw(data, options);
                                }
                            });

                            $.ajax({
                    			url: "/ajaxfunctions/getSolarServiceJobs",
                    			dataType:"json",
                    			data: params,
                    			type: 'post',
                                success: function(jsonData)
                                {
                                    console.log(jsonData);
                                    var data = google.visualization.arrayToDataTable(jsonData);
                                    var options = {
                                        title :'Total Weekly Service Jobs',
                                        titleTextStyle: {
                                            fontSize: 21,
                                            bold: false,
                                            fontColor: "#333"
                                        },
                                        hAxis: {
                                            title: 'Week Ending',
                                            //showTextEvery: 1,
                            				slantedText:true,
                            			  	slantedTextAngle:-45
                                        },
                                        vAxis: {
                                            title: 'Total Jobs'
                                        },
                                        legend: {
                                            position: 'top'
                                        },
                                        height: 450,
                                        isStacked: true
                                    };

                                    var chart = new google.visualization.ColumnChart(document.getElementById('solar_service_chart'));

                                    chart.draw(data, options);
                                }
                            });

                        }
                    }
                },
                'dashboard':{
                    init: function(){
                        actions['<?php echo $user_role;?>'].init();
                    }

                },
                'comingsoon':{
                    init: function(){
                        
                    }
                }
            }
            //run the script for the current page
            actions[config.curPage].init();
        </script>
        <?php Database::closeConnection(); ?>
    </body>
</html>