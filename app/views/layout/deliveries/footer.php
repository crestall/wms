
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                'common':{

                },
                'item-searcher':function(){
                    $("input#item_searcher").autocomplete({
                        source: function(req, response){
                            var client_id = $('#client_id').val();
                            var selected_items = $('#selected_items').val();
                            var url = "/ajaxfunctions/getDeliveryItems/?item="+req.term+"&clientid="+client_id+"&exclude="+selected_items;
                            //console.log(url);
                        	$.getJSON(url, function(data){
                        		response(data);
                        	});
                        },
                        select: function(event, ui) {
                            var item_count = ($("div.item_holder").length) - 1;
                            var ta = parseInt(ui.item.total_available );
                            var locations = ui.item.locations.split("~");
                            var html = "<div id='item_holder_"+item_count+"' class='item_holder p-3 pb-0 mb-2 rounded-top mid-grey'>";
                            html += "<h5 class='text-center'>"+ui.item.value+"</h5>";
                            html += "<p class='text-center'>Currently "+ta.toLocaleString('en')+" available in total<br>";
                            html += "<label for='select_all_"+ui.item.item_id+"'><em><small>Select All</small></em></label><input style='margin-left:5px' class='select_all' id='select_all_"+ui.item.item_id+"' data-itemid='"+ui.item.item_id+"' type='checkbox'></p>";
                            html += "<div class='row'>";
                            locations.forEach(function (location, ind)
                            {
                                loc_array = location.split("|");
                                html += "<div class='col-5'><label for='location_"+loc_array[1]+"'>Pallet With "+loc_array[0]+"</label></div>";
                                html += "<div class='col-1'><input id='location_"+loc_array[1]+"' class='item_selector select_"+ui.item.item_id+"' name='items["+ui.item.item_id+"]' value='"+loc_array[1]+"' type='checkbox'></div>";
                            });
                            html += "</div>"
                            html += "</div>";

                            $('div#items_holder').append(html);
                            var add_id = $('input#selected_items').val()+","+ui.item.item_id;
                            var new_remove = add_id.replace(/^,|,$/g,'');
                            $('input#selected_items').val(new_remove);
                            $(event.target).val("");
                            $('input.select_all').each(function(index, element){
                                $(this).off('click').click(function(e){
                                    var checked = this.checked;
                                    var item_id = $(this).data("itemid");
                                     $('.select_'+item_id).each(function(e){
                                        this.checked =  checked;
                                        $(this).change();
                                     })
                                });
                            });
                            $('input.item_selector').change(function(ev){
                                if($('input.item_selector:checked').length)
                                    $("button#submitter").attr('disabled', false);
                                else
                                    $("button#submitter").attr('disabled', true);
                            });
                            return false;
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