
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                'common':{

                },
                'item-searcher':function(){
                    var client_id = $('#client_id').val();
                    var selected_items = $('#selected_items').val();
                    $("input#item_searcher").autocomplete({
                        source: function(req, response){
                            var url = "/ajaxfunctions/getDeliveryItems/?item="+req.term+"&clientid="+client_id+"&exclude="+selected_items;
                            //console.log(url);
                        	$.getJSON(url, function(data){
                        		response(data);
                        	});
                        },
                        select: function(event, ui) {
                            var item_count = ($("div.item_holder").length) - 1;
                            var html = "<div id='item_holder_"+item_count+" class='item_holder p-3 pb-0 mb-2 rounded-top mid-grey>";
                            html += "<h3 class='text-center'>"+ui.item.value+"</h3>";
                            html += "</div>";

                            $('div#items_holder').append(html);
                        },
                        change: function (event, ui) {
                            if (!ui.item)
                	        {
                                $(event.target).val("");
                                return false;
                            }
                        },
                        minLength: 2
                    });
                },
                'book-delivery':{
                    init: function(){
                        actions['item-searcher']();
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