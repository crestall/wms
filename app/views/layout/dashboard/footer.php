
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                common: {
                    init: function(){
                        /************
                        Google chart redraw on window size change
                        ************/
                        //create trigger to resizeEnd event
                        $(window).resize(function() {
                            if(this.resizeTO) clearTimeout(this.resizeTO);
                            this.resizeTO = setTimeout(function() {
                                $(this).trigger('resizeEnd');
                            }, 500);
                        });
                        $('a.btn-outline-order, a.btn-outline-delivery, a.btn-outline-pickup, a.btn-outline-backorder')
                            .click(function(e){
                                $(this).removeClass().addClass("btn btn-clicked-inactive");
                                $(this).html("<i class='fad fa-circle-notch fa-spin'></i> Collecting Info");
                            });
                    },
                    loadProductionCharts: function(){
                        $('div#job_activity_chart').html("<p class='text-center'><img class='loading' src='/images/preloader.gif' alt='loading...' /><br />Fetching Chart Data</p>");
                        google.charts.load('current', {'packages':['corechart']});
                        google.charts.setOnLoadCallback(drawProductionCharts);
                        function drawProductionCharts()
                        {
                            var data = [];
                            var options = [];
                            var num_jobs = 0;
                            $.ajax({
                    			url: "/ajaxfunctions/getWeeklyProductionJobTrends",
                    			dataType:"json",
                    			type: 'post',
                                success: function(jsonData)
                                {
                                    //var jData =  $.parseJSON(jsonData);
                            		data[0] = google.visualization.arrayToDataTable(jsonData);
                                    num_jobs = jsonData.length - 1;
                                    nextProductionAjaxCall();
                                }
                            });
                            function nextProductionAjaxCall(){
                                $.ajax({
                        			url: "/ajaxfunctions/getDailyProductionJobTrends",
                        			dataType:"json",
                        			type: 'post',
                                    success: function(jsonData)
                                    {
                                        //var jData =  $.parseJSON(jsonData);
                                		data[1] = google.visualization.arrayToDataTable(jsonData);
                                        productionAjaxDone();
                                    }
                                });
                            }
                            function productionAjaxDone(){
                                //console.log('num_orders: '+num_orders);
                                //console.log('data: '+data);
                                if(num_jobs > 0)
                                {
                                    options[0] = {
                            		    animation:{
                            		        duration: 1000,
                                            easing: 'out',
                                        },
                            			hAxis: {
                            				title: 'Week Beginning',
                            				showTextEvery: 1,
                            				slantedText:true,
                            				slantedTextAngle:-45
                            			},
                            			vAxes: {
                            				0: {
                            					title: 'Job Count',
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
                            				0:{type: "bars", targetAxisIndex:0, color: "052f95"} ,
                                            1:{type: "line", targetAxisIndex:0}
                            			},
                                        title: "Weekly Jobs: Totals/Averages Last Two Months",
                                        titleTextStyle: {
                        					fontSize: 20,
                        					color: '##5F5F5E;',
                        					bold: false,
                        					italic: false,
                        					marginBottom: 20
                                        },
                            		};
                                    options[1] = {
                            		    animation:{
                            		        duration: 1000,
                                            easing: 'out',
                                        },
                            			hAxis: {
                            				title: 'Day',
                            				slantedText:true,
                            				slantedTextAngle:-45
                            			},
                            			vAxes: {
                            				0: {
                            					title: 'Job Count',
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
                            				0:{type: "bars", targetAxisIndex:0, color: "052f95"} ,
                                            1:{type: "line", targetAxisIndex:0}
                            			},
                                        title: "Daily Jobs: Totals/Averages Last 30 Days",
                                        titleTextStyle: {
                        					fontSize: 20,
                        					color: '##5F5F5E;',
                        					bold: false,
                        					italic: false,
                        					marginBottom: 20
                                        },
                            		};
                                    var chart = new google.visualization.LineChart(document.getElementById('job_activity_chart'));
                                    var button = document.getElementById('chart_button_1');
                                    var current = 0;
                                    function drawChart(){
                                        // Disabling the button while the chart is drawing.
                                        button.disabled = true;
                                        button.style.display = "none";
                                        google.visualization.events.addListener(chart, 'ready',
                                                function() {
                                                    button.disabled = false;
                                                    button.textContent = 'Switch to ' + (current ? 'Weekly' : 'Daily');
                                                    button.style.display = "inline";
                                                });

                                        chart.draw(data[current], options[current]);
                                    }
                                    drawChart();
                                    button.onclick = function() {
                                        current = 1 - current;
                                        drawChart();
                                    }
                                    //redraw chart when window resize is completed
                                    $(window).on('resizeEnd', function() {
                                        drawChart();
                                    });
                                }
                                else
                                {
                                    $('div#job_activity_chart').html("<div class='errorbox'><h2>No Jobs Created</h2><p>There have been no jobs created in the last six months</p></div>");
                                }
                            }
                        }
                    },
                    loadWarehouseCharts: function(){
                        $('div#order_activity_chart').html("<p class='text-center'><img class='loading' src='/images/preloader.gif' alt='loading...' /><br />Fetching Chart Data</p>");
                        google.charts.load('current', {'packages':['corechart']});
                        google.charts.setOnLoadCallback(drawAdminCharts);
                        var params = {
                            from: $('#admin_from_value').val(),
                            to: $('#admin_to_value').val()
                        }
                        function drawAdminCharts()
                        {
                            var data = [];
                            var num_activities = 0
                            var drawChart;
                            $.ajax({
                    			url: "/ajaxfunctions/getAdminWeeklyClientActivity",
                    			dataType:"json",
                    			data: params,
                    			type: 'post',
                                success: function(jsonData)
                                {
                                    //console.log(jsonData);
                                    for (var key in jsonData) {
                                        if (key == 0) { continue; }
                                        num_activities += jsonData[key][1];
                                        num_activities += jsonData[key][2];
                                        num_activities += jsonData[key][3];
                                    };
                            		data[0] = google.visualization.arrayToDataTable(jsonData);
                                    nextAjaxCall();
                                }
                            });
                            function nextAjaxCall(){
                                $.ajax({
                        			url: "/ajaxfunctions/getAdminDailyClientActivity",
                        			dataType:"json",
                        			data: params,
                        			type: 'post',
                                    success: function(jsonData)
                                    {
                                        //console.log(jsonData);
                                        for (var key in jsonData) {
                                            if (key == 0) { continue; }
                                            num_activities += jsonData[key][1];
                                            num_activities += jsonData[key][2];
                                            num_activities += jsonData[key][3];
                                        };
                                		data[1] = google.visualization.arrayToDataTable(jsonData);
                                        ajaxDone();
                                    }
                                });
                            }
                            function ajaxDone(){
                                //console.log('num_orders: '+num_orders);
                                //console.log(data); return;
                                var options = {};
                                if(num_activities > 0)
                                {
                                    options[0] = {
                            		    animation:{
                            		        duration: 1000,
                                            easing: 'out',
                                        },
                            			hAxis: {
                            				title: 'Week Beginning',
                            				showTextEvery: 1,
                            				slantedText:true,
                            				slantedTextAngle:-45
                            			},
                            			vAxes: {
                            				0: {
                            					title: 'Activity Count',
                            					viewWindow: {
                            						min: 0
                            					}
                            				}
                            			},
                            			legend: {
                            				position: 'top'
                            			},
                            			height: 450,
                                        isStacked: true,
                                        seriesType: "bars",
                            			series: {
                            				3:{type: "line", targetAxisIndex:0, color: "052f95"}
                            			},
                                        title: "Weekly Activity: Totals/Averages Last Two Months",
                                        titleTextStyle: {
                        					fontSize: 20,
                        					color: '##5F5F5E;',
                        					bold: false,
                        					italic: false,
                        					marginBottom: 20
                                        },
                            		};
                                    options[1] = {
                            		    animation:{
                            		        duration: 1000,
                                            easing: 'out',
                                        },
                            			hAxis: {
                            				title: 'Day',
                            				slantedText:true,
                            				slantedTextAngle:-45
                            			},
                            			vAxes: {
                            				0: {
                            					title: 'Activity Count',
                            					viewWindow: {
                            						min: 0
                            					}
                            				}
                            			},
                            			legend: {
                            				position: 'top'
                            			},
                            			height: 450,
                                        isStacked: true,
                                        seriesType: "bars",
                            			series: {
                            				3:{type: "line", targetAxisIndex:0, color: "052f95"}
                            			},
                                        title: "Daily Activity: Totals/Averages Last 30 Days",
                                        titleTextStyle: {
                        					fontSize: 20,
                        					color: '##5F5F5E;',
                        					bold: false,
                        					italic: false,
                        					marginBottom: 20
                                        },
                            		};
                                    var chart = new google.visualization.ComboChart(document.getElementById('order_activity_chart'));
                                    var button = document.getElementById('chart_button_1');
                                    var current = 0;
                                    function drawChart(){
                                        // Disabling the button while the chart is drawing.
                                        button.disabled = true;
                                        button.style.display = "none";
                                        google.visualization.events.addListener(chart, 'ready',
                                                function() {
                                                    button.disabled = false;
                                                    button.textContent = 'Switch to ' + (current ? 'Weekly' : 'Daily');
                                                    button.style.display = "inline";
                                                });

                                        chart.draw(data[current], options[current]);
                                    }
                                    drawChart();
                                    button.onclick = function() {
                                        current = 1 - current;
                                        drawChart();
                                    }
                                    //redraw chart when window resize is completed
                                    $(window).on('resizeEnd', function() {
                                        drawChart();
                                    });
                                }
                                else
                                {
                                    $('div#order_activity_chart').html("<div class='errorbox'><h2>No Orders Placed</h2><p>There have been no orders fulfilled in the last three months</p></div>");
                                }
                            }
                        }
                    }
                },
                client: {
                    init: function(){
                        actions.common.init();
                        $('div#products_chart').html("<p class='text-center'><img class='loading' src='/images/preloader.gif' alt='loading...' /><br />Fetching Chart Data</p>");
                        $('div#orders_chart').html("<p class='text-center'><img class='loading' src='/images/preloader.gif' alt='loading...' /><br />Fetching Chart Data</p>");
                        google.charts.load('current', {'packages':['corechart']});
                        if(config.isDeliveryClient)
                            google.charts.setOnLoadCallback(drawDeliveryClientCharts);
                        else
                            google.charts.setOnLoadCallback(drawClientCharts);
                        var params = {
                            client_id: $('#client_id').val(),
                            from: $('#from_value').val(),
                            to: $('#to_value').val()
                        }

                        function drawDeliveryClientCharts()
                        {
                            var num_pickups = 0;
                            var num_deliveries = 0;

                            $.ajax({
                                url: "/ajaxfunctions/getWeeklyDeliveryCountsForChart",
                                dataType: "json",
                                data: {
                                    client_id: $('#client_id').val()
                                },
                                type: "post",
                                success: function(jsonData)
                                {
                                    //num_deliveries = jsonData.length - 1;
                                    for (var key in jsonData) {
                                        if (key == 0) { continue; }
                                        num_deliveries += jsonData[key][1];
                                    };
                                    if(num_deliveries > 0)
                                    {
                                        var data = google.visualization.arrayToDataTable(jsonData);
                                        var options = {
                                		    animation:{
                                		        duration: 1000,
                                                easing: 'out',
                                            },
                                			hAxis: {
                                				title: 'Week Beginning',
                                				showTextEvery: 1,
                                				slantedText:true,
                                				slantedTextAngle:-45
                                			},
                                			vAxes: {
                                				0: {
                                					title: 'Delivery Count',
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
                                				0:{type: "bars", targetAxisIndex:0, color: "052f95"} ,
                                                1:{type: "line", targetAxisIndex:0}
                                			},
                                            title: "Weekly Delivery Totals/Averages Last Two Months",
                                            titleTextStyle: {
                            					fontSize: 20,
                            					color: '#0640c6;',
                            					bold: false,
                            					italic: false,
                            					marginBottom: 20
                                            }
                                		};
                                        var chart = new google.visualization.ColumnChart(document.getElementById('orders_chart'));
                                        chart.draw(data, options);
                                        //redraw chart when window resize is completed
                                        $(window).on('resizeEnd', function() {
                                            chart.draw(data, options);
                                        });
                                    }
                                    else
                                    {
                                        $('div#orders_chart').html("<div class='errorbox'><h2>No Deliveries Booked</h2><p>There have been no deliveries booked in the last three months</p></div>");
                                    }
                                }
                            });

                            //$('div#orders_chart').html("<div class='errorbox'><h2>No Deliveries Booked</h2><p>There have been no deliveries booked in the last three months</p></div>");
                            //$('div#products_chart').html("<div class='errorbox'><h2>No Pickups Booked</h2><p>There have been no pickups booked in the last three months</p></div>");

                            $.ajax({
                                url: "/ajaxfunctions/getWeeklyPickupCountsForChart",
                                dataType: "json",
                                data: {
                                    client_id: $('#client_id').val()
                                },
                                type: "post",
                                success: function(jsonData)
                                {
                                    for (var key in jsonData) {
                                        if (key == 0) { continue; }
                                        num_pickups += jsonData[key][1];
                                    };
                                    if(num_pickups > 0)
                                    {
                                        var data = google.visualization.arrayToDataTable(jsonData);
                                        var options = {
                                		    animation:{
                                		        duration: 1000,
                                                easing: 'out',
                                            },
                                			hAxis: {
                                				title: 'Week Beginning',
                                				showTextEvery: 1,
                                				slantedText:true,
                                				slantedTextAngle:-45
                                			},
                                			vAxes: {
                                				0: {
                                					title: 'Pickup Count',
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
                                				0:{type: "bars", targetAxisIndex:0, color: "466a99"} ,
                                                1:{type: "line", targetAxisIndex:0}
                                			},
                                            title: "Weekly Pickup Totals/Averages Last Two Months",
                                            titleTextStyle: {
                            					fontSize: 20,
                            					color: '##5F5F5E;',
                            					bold: false,
                            					italic: false,
                            					marginBottom: 20
                                            }
                                		};
                                        var chart = new google.visualization.ColumnChart(document.getElementById('products_chart'));
                                        chart.draw(data, options);
                                        //redraw chart when window resize is completed
                                        $(window).on('resizeEnd', function() {
                                            chart.draw(data, options);
                                        });
                                    }
                                    else
                                    {
                                        $('div#products_chart').html("<div class='errorbox'><h2>No Pickups Booked</h2><p>There have been no pickups booked in the last three months</p></div>");
                                    }
                                }
                            });

                            //$('div#orders_chart').html("<div class='errorbox'><h2>No Deliveries Booked</h2><p>There have been no deliveries booked in the last three months</p></div>");
                            //$('div#products_chart').html("<div class='errorbox'><h2>No Pickups Booked</h2><p>There have been no pickups booked in the last three months</p></div>");
                        }

                        function drawClientCharts()
                    	{
                            var data = [];
                            var options = [];
                            var num_orders = 0;
                            $.ajax({
                    			url: "/ajaxfunctions/getWeeklyOrderTrends",
                    			dataType:"json",
                    			data: params,
                    			type: 'post',
                                success: function(jsonData)
                                {
                                    //var jData =  $.parseJSON(jsonData);
                            		data[0] = google.visualization.arrayToDataTable(jsonData);
                                    for (var key in jsonData) {
                                        if (key == 0) { continue; }
                                        num_orders += jsonData[key][1]; 
                                    };
                            		data[1] = google.visualization.arrayToDataTable(jsonData);
                                    nextAjaxCall();
                                }
                            });
                            function nextAjaxCall(){
                                $.ajax({
                        			url: "/ajaxfunctions/getDailyOrderTrends",
                        			dataType:"json",
                        			data: params,
                        			type: 'post',
                                    success: function(jsonData)
                                    {
                                        //var jData =  $.parseJSON(jsonData);
                                		data[1] = google.visualization.arrayToDataTable(jsonData);
                                        ajaxDone();
                                    }
                                });
                            }
                            function ajaxDone(){
                                console.log('num_orders: '+num_orders);
                                //console.log('data: '+data);
                                if(num_orders > 0)
                                {
                                    options[0] = {
                            		    animation:{
                            		        duration: 1000,
                                            easing: 'out',
                                        },
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
                            				0:{type: "bars", targetAxisIndex:0, color: "052f95"} ,
                                            1:{type: "line", targetAxisIndex:0}
                            			},
                                        title: "Weekly Orders: Totals/Averages Last Two Months",
                                        titleTextStyle: {
                        					fontSize: 20,
                        					color: '##5F5F5E;',
                        					bold: false,
                        					italic: false,
                        					marginBottom: 20
                                        },
                            		};
                                    options[1] = {
                            		    animation:{
                            		        duration: 1000,
                                            easing: 'out',
                                        },
                            			hAxis: {
                            				title: 'Day',
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
                            				0:{type: "bars", targetAxisIndex:0, color: "052f95"} ,
                                            1:{type: "line", targetAxisIndex:0}
                            			},
                                        title: "Daily Orders: Totals/Averages Last 30 Days",
                                        titleTextStyle: {
                        					fontSize: 20,
                        					color: '##5F5F5E;',
                        					bold: false,
                        					italic: false,
                        					marginBottom: 20
                                        },
                            		};
                                    var chart = new google.visualization.LineChart(document.getElementById('orders_chart'));
                                    var button = document.getElementById('chart_button_2');
                                    var current = 0;
                                    function drawChart(){
                                        // Disabling the button while the chart is drawing.
                                        button.disabled = true;
                                        button.style.display = "none";
                                        google.visualization.events.addListener(chart, 'ready',
                                                function() {
                                                    button.disabled = false;
                                                    button.textContent = 'Switch to ' + (current ? 'Weekly' : 'Daily');
                                                    button.style.display = "inline";
                                                });

                                        chart.draw(data[current], options[current]);
                                    }
                                    drawChart();
                                    button.onclick = function() {
                                        current = 1 - current;
                                        drawChart();
                                    }
                                    //redraw chart when window resize is completed
                                    $(window).on('resizeEnd', function() {
                                        drawChart();
                                    });
                                }
                                else
                                {
                                    $('div#orders_chart').html("<div class='errorbox'><h2>No Orders Placed</h2><p>There have been no orders fulfilled in the last three months</p></div>");
                                }
                            }
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
                                		    animation:{
                                		        "startup": true,
                                		        duration: 1000,
                                                easing: 'out',
                                            },
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
                                            series: {
                                				0:{type: "bars", targetAxisIndex:0, color: "052f95"}
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
                                        function drawChart(){
                                            chart2.draw(data2, options2);
                                        }
                                        drawChart();
                                        //redraw chart when window resize is completed
                                        $(window).on('resizeEnd', function() {
                                            drawChart();
                                        });
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
                admin: {
                    init: function(){
                        actions.common.init();
                        actions.common.loadWarehouseCharts();
                    }
                },
                production_admin: {
                    init: function(){
                        actions.common.init();
                        actions.common.loadProductionCharts();
                    }
                },
                production: {
                    init: function(){
                        actions.common.init();
                        actions.common.loadProductionCharts();
                    }
                },
                production_sales: {
                    init: function(){
                        actions.common.init();
                        actions.common.loadProductionCharts();
                    }
                },
                production_sales_admin: {
                    init: function(){
                        actions.common.init();
                        actions.common.loadProductionCharts();
                    }
                },
                warehouse: {
                    init: function(){
                        actions.common.init();
                        actions.common.loadWarehouseCharts();
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