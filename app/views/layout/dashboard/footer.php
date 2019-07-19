
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                common: {
                    init: function(){
                        $('div#order_activity_chart').html("<p class='text-center'><img class='loading' src='/images/preloader.gif' alt='loading...' /><br />Fetching Chart Data</p>");
                        $('div#error_activity_chart').html("<p class='text-center'><img class='loading' src='/images/preloader.gif' alt='loading...' /><br />Fetching Chart Data</p>");
                        google.charts.load('current', {'packages':['corechart']});
                        google.charts.setOnLoadCallback(drawAdminCharts);
                        var params = {
                            from: $('#admin_from_value').val(),
                            to: $('#admin_to_value').val()
                        }

                        function drawAdminCharts()
                        {
                            $.ajax({
                    			url: "/ajaxfunctions/getPickErrors",
                    			dataType:"json",
                    			data: params,
                    			type: 'post',
                                success: function(jsonData)
                                {
                                    console.log(jsonData);
                                    var data = google.visualization.arrayToDataTable(jsonData);
                                    var options = {
                                        title :'Weekly Orders and Errors',
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
                                        vAxes: {
                                            0: {
                                                title: 'Total Orders',
                                                viewWindow: {
                                                    min: 0
                                                }
                                            },
                                            1: {
                                                title: 'Pick Errors',
                                                viewWindow: {
                                                    min: 0,
                                                    max: 20
                                                }
                                            }
                                        },
                                        legend: {
                                            position: 'top'
                                        },
                                        height: 450,
                                        series: {
                                			0:{type: "line", targetAxisIndex:0},
                                            1:{type: "line", targetAxisIndex:1}
                                		},
                                    };

                                    var chart = new google.visualization.ComboChart(document.getElementById('error_activity_chart'));
                                    /*
                                    google.visualization.events.addListener(chart, 'ready', function () {
                                        var imgUri = chart.getImageURI();


                                        var form = document.createElement('form');
                                        form.setAttribute("method", "post");
                                        form.setAttribute("action", "/make-pickerror-pdf");
                                        //form.setAttribute("action", "/misc-functions/make-packslips-pdf.php");
                                        form.setAttribute("target", "_blank");
                                        var hiddenField = document.createElement("input");
                                        hiddenField.setAttribute("type", "hidden");
                                        hiddenField.setAttribute("name", "chart_data");
                                        hiddenField.setAttribute("value", imgUri);
                                        var hiddenField2 = document.createElement("input");
                                        hiddenField2.setAttribute("type", "hidden");
                                        hiddenField2.setAttribute("name", "client_id");
                                        hiddenField2.setAttribute("value", $('#client_id').val());
                                        var hiddenField3 = document.createElement("input");
                                        hiddenField3.setAttribute("type", "hidden");
                                        hiddenField3.setAttribute("name", "from");
                                        hiddenField3.setAttribute("value", $('#from').val());
                                        var hiddenField4 = document.createElement("input");
                                        hiddenField4.setAttribute("type", "hidden");
                                        hiddenField4.setAttribute("name", "to");
                                        hiddenField4.setAttribute("value", $('#to').val());
                                        form.appendChild(hiddenField);
                                        form.appendChild(hiddenField2);
                                        form.appendChild(hiddenField3);
                                        form.appendChild(hiddenField4);
                                        var submitButton = document.createElement("input");
                                        submitButton.setAttribute("type", "submit");
                                        submitButton.setAttribute("value", "Print PDF");
                                        submitButton.setAttribute("class", "button");
                                        form.appendChild(submitButton);
                                        document.getElementById('the_chart').appendChild(form);



                                    });
                                    */
                                    chart.draw(data, options);
                                }
                            });

                            $.ajax({
                    			url: "/ajaxfunctions/getClientActivity",
                    			dataType:"json",
                    			data: params,
                    			type: 'post',
                                success: function(jsonData)
                                {
                                    var data = google.visualization.arrayToDataTable(jsonData);
                                    var options = {
                                        title :'Daily Orders by Client',
                                        titleTextStyle: {
                                            fontSize: 21,
                                            bold: false,
                                            fontColor: "#333"
                                        },
                                        hAxis: {
                                            title: 'Day',
                                            gridlines: {
                                                count: 10
                                            }
                                        },
                                        vAxis: {
                                            title: 'Total Orders',
                                            viewWindow: {
                                                min: 0
                                            }
                                        },
                                        legend: {
                                            position: 'top'
                                        },
                                        height: 450
                                    };

                                    var chart = new google.visualization.ComboChart(document.getElementById('order_activity_chart'));
                                    chart.draw(data, options);
                                }
                            });
                        }
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
                                				title: 'Week Ending',
                                				showTextEvery: 1,
                                				slantedText:true,
                                				slantedTextAngle:-45
                                			},
                                			vAxes: {
                                				0: {
                                					title: 'Total Orders',
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
                                				0:{type: "line", targetAxisIndex:0}
                                			},
                                            title: "Weekly Orders: Last Three Months",
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
                'solar admin': {
                    init: function(){
                        actions.common.init();
                        var maxHeight = 0;
                        $("div.inventory-panel").each(function(){
                            if ($(this).height() > maxHeight) { maxHeight = $(this).height(); }
                        });
                        $("div.inventory-panel").height(maxHeight);
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