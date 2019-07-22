
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                common: {
                    init: function(){

                    },
                    'add-item': function(){
                        $("a.add").click(function(e){
                            e.preventDefault;
                            var item_count = $(":input.item-searcher").length;
                            //console.log('items: '+item_count);
                            var html = "<div class='row item_holder'>"
                            html += "<div class='col-sm-1 delete-image-holder'>";
                            html += "<a class='delete' title='remove this item'><i class='fas fa-times-circle fa-2x text-danger'></i></a>";
                            html += "</div>"; //col-sm-1
                            html += "<div class='col-sm-4'>";
                            html += "<p><input type='text' class='form-control item-searcher' name=items["+item_count+"][name]' placeholder='Item Name' /></p>";
                            html += "</div>"; //col-sm-4
                            html += "<div class='col-sm-4 qty-holder'>";
                            //html += "<input type='text' class='form-control number item_qty' name='items["+item_count+"][qty]' placeholder='Qty' disabled />";
                            html += "</div>"; //col-sm-4
                            html += "<div class='col-sm-3 qty-location'></div>";
                            html += "<input type='hidden' name='items["+item_count+"][id]' class='item_id' />"
                            html += "</div>"; //row
                            $('div#items_holder').append(html).find('input.item-searcher').focus();

                            actions['item-searcher'].init();
                            itemsUpdater.itemDelete();
                            //itemsUpdater.updateValidation();
                        });
                    },
                },
                'order-consumables':{
                    init: function(){
                        actions['item-searcher'].init();
                        actions.common['add-item']();
                        itemsUpdater.itemDelete();
                    }
                },
                'item-searcher':{
                    init: function(){
                        $("input.item-searcher").each(function(i,e){
                            if($(this).data('ui-autocomplete') != undefined)
                            {
                                $(this).autocomplete( "destroy" );
                            }
                            autoCompleter.itemAutoComplete($(this), selectCallback, changeCallback);
                        })
                        function selectCallback(event, ui)
                        {
                            var item_count = ($(":input.item-searcher").length) - 1;
                            var $holder = $(event.target).closest('div.item_holder');
                            var qty_html;
                            var inst;
                            if(ui.item.palletized > 0)
                            {
                                var pallet_vals = ui.item.select_values.split(',');
                                var line_item_vals = ui.item.max_values.split(',');
                                qty_html = "<div class='col-sm-4'><input type='text' class='form-control number item_qty' name='items["+item_count+"][qty]' placeholder='Qty' /></div>";
                                qty_html += "<div class='col-sm-8'><select class='form-control selectpicker pallet_qty' name='items["+item_count+"][pallet_qty]'><option value='0'>Whole Pallet Qty</option>";
                                pallet_vals.forEach(function(pallet_val) {
                                    //console.log(pallet_val);
                                    qty_html += "<option>"+pallet_val+"</option>";
                                });
                                qty_html += "</select></div>";
                                inst = "<p class='inst'>There are currently <strong>"+ui.item.total_available+"</strong> of these available</p>";
                                inst += "<p class='inst'>There are<br/>";
                                var li = 0;
                                var count = 1;
                                line_item_vals.forEach(function(max){
                                    if(max == li)
                                    {
                                        ++count;
                                    }
                                    else if(li == 0)
                                    {
                                        //first pass
                                        //++count;
                                    }
                                    else
                                    {
                                        inst += "<strong>"+count+"</strong> pallets with "+li+" items,<br/>";
                                        count = 1;
                                    }
                                    li = max;
                                });
                                inst += "<strong>"+count+"</strong> pallets with "+li+" items</p>";
                                inst += "<p class='inst'>Select whole pallet amounts from the dropdown selector <br/>";
                                inst += "<strong>OR</strong><br/>If you require us to break a pallet, enter an amount in the 'Qty' text field</p>";
                            }
                            else
                            {
                                qty_html = "<input type='text' class='form-control number item_qty' name='items["+item_count+"][qty]' placeholder='Qty' />";
                                inst = "<p class='inst'>There are currently <strong>"+ui.item.total_available+"</strong> of these available";
                                inst += "<br/>Maximum allowed line item values are <strong>"+ui.item.max_values+"</strong></p>";
                            }
                            $holder.find('div.qty-holder').html(qty_html).find('input').focus();
                            $holder.find('input.item_id').val(ui.item.item_id);
                            $holder.find('div.qty-location').html(inst);
                            itemsUpdater.itemDelete();
                            itemsUpdater.updateValidation();
                            $holder.find('input.item_qty').focus();
                            $('.selectpicker').selectpicker();
                            //actions['item-searcher-test'].init();
                            return false;
                        }
                        function changeCallback(event, ui)
                        {
                            if (!ui.item)
                	        {
                                $(event.target).val("");
                                return false;
                            }
                            itemsUpdater.itemDelete();
                            //actions['item-searcher-test'].init();
                        }

                    }
                },
            }

            //run the script for the current page
            actions[config.curPage].init();
        </script>
        <?php Database::closeConnection(); ?>
    </body>
</html>