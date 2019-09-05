
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                common: {
                    init: function(){
                        $('#palletized').click(function(e){
                            $("#per_pallet_holder").slideToggle('slow');
                        });

                        $('#collection').click(function(e){
                            if(this.checked)
                            {
                                $('#pack_item').prop('checked', false);
                            }

                        });

                        $('#pack_item').click(function(e){
                            if(this.checked)
                            {
                                $('#collection').prop('checked', false);
                            }

                        });

                        $('#package_type').change(function(e){
                            var html = "";
                            $("option:selected", this).each(function(){
                                if($(this).data('multiples') == 1)
                                {
                                    var count = ($( "#pt_count_"+$(this).val() ).val())? $( "#pt_count_"+$(this).val() ).val(): "" ;
                                    html += "<div class='form-group row'>";
                                    html += "<label class='col-md-3 col-form-label'><sup><small><i class='fas fa-asterisk text-danger'></i></small></sup> Number in "+$(this).text()+"</label>";
                                    html += "<div class='col-md-4'>";
                                    html += "<input type='text' class='form-control required number' name='number_in_"+$(this).val()+"' id='number_in_"+$(this).val()+"' value='"+count+"' />";
                                    html += "</div>";
                                    html += "</div>";
                                }
                            });
                            $("#type_holder").html(html);
                        });
                    }
                },
                'edit-product': {
                    init: function()
                    {
                        actions.common.init();
                        $('form#edit_product').submit(function(){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Processing form...</h2></div>' });
                            }
                        });
                        $('#package_type').change();
                    }
                },
                'add-product': {
                    init: function()
                    {
                        actions.common.init();
                        $('form#add_product').submit(function(){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Processing form...</h2></div>' });
                            }
                        });
                    }
                },
                'view-products': {
                    init: function()
                    {
                        $('#client_selector').change(function(e){
                            if($(this).val() > 0)
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Collecting Products...</h2></div>' });
                                window.location.href = "/products/view-products/client=" + $(this).val();
                            }
                        });

                        dataTable.init($('table#view_items_table'), {
                            "columnDefs": [
                                { "orderable": false, "targets": [1,2,3,4,8,9] }
                            ]
                        } );

                        dataTable.init($('table#view_solar_items_table'), {
                            "columnDefs": [
                                { "orderable": false, "targets": [4, 5] }
                            ],
                            "drawCallback": function( settings ) {
                                $('button.update_product').click(function(e){
                                    actions.update.click(this);
                                });
                            }
                        } );
                        $('button.update_product').click(function(e) {
                            actions.update.click(this);
                        });
                    }
                },
                'pack-items-edit': {
                    init: function(){
                        $('#product_selector').change(function(e){
                            if($(this).val() > 0)
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Collecting Details...</h2></div>' });
                                window.location.href = "/products/pack-items-edit/product=" + $(this).val();
                            }
                        });
                        autoCompleter.itemAutoComplete($('#item_searcher'), selectCallback, changeCallback, false);
                        function selectCallback(event, ui)
                        {
                            var appendage = "<div class='row form-group'><div class='item_holder'><label class='col-md-3 col-form-label'>"+ui.item.value+"</label><div class='col-md-4'><input type='text' class='required number form-control item-group count' placeholder='qty' name='items["+ui.item.item_id+"][qty]' id='item_"+ui.item.item_id+"' data-itemid='"+ui.item.item_id+"' /></div>";
                            appendage += "<div class='col-md-1 delete-image-holder'>";
                            appendage += "<a class='delete' data-itemid='"+ui.item.item_id+"' title='remove this item'><i class='fas fa-backspace fa-2x text-danger'></i></a></div>";
                            appendage += "</div></div>";
                            $('div#the_items').append(appendage);
                            if($('#selected_items').val() == '')
                            {
                                $('#selected_items').val(ui.item.item_id);
                            }
                            else
                            {
                                $('#selected_items').val($('#selected_items').val()+ ','+ui.item.item_id);
                            }
                            $(event.target).val('');
                            ui.item.value="";
                            itemsUpdater.itemDelete();
                            return false;
                        }
                        function changeCallback(event, ui)
                        {
                            if (!ui.item)
                	        {
                                $('#item_searcher').val("");
                                return false;
                            }
                        }
                        itemsUpdater.itemDelete();
                        $("form#pack_item_edit").submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Updating Details...</h2></div>' });
                            }
                            else
                            {
                                return false;
                            }
                        });
                    }
                },
                'collections-edit': {
                    init: function(){
                        $('#product_selector').change(function(e){
                            if($(this).val() > 0)
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Collecting Details...</h2></div>' });
                                window.location.href = "/products/collections-edit/product=" + $(this).val();
                            }
                        });
                        autoCompleter.itemAutoComplete($('#item_searcher'), selectCallback, changeCallback, false);
                        function selectCallback(event, ui)
                        {
                            var appendage = "<div class='row form-group'><div class='item_holder'><label class='col-md-3 col-form-label'>"+ui.item.value+"</label><div class='col-md-4'><input type='text' class='required number form-control item-group count' placeholder='qty' name='items["+ui.item.item_id+"][qty]' id='item_"+ui.item.item_id+"' data-itemid='"+ui.item.item_id+"' /></div>";
                            appendage += "<div class='col-md-1 delete-image-holder'>";
                            appendage += "<a class='delete' data-itemid='"+ui.item.item_id+"' title='remove this item'><i class='fas fa-backspace fa-2x text-danger'></i></a></div>";
                            appendage += "</div></div>";
                            $('div#the_items').append(appendage);
                            if($('#selected_items').val() == '')
                            {
                                $('#selected_items').val(ui.item.item_id);
                            }
                            else
                            {
                                $('#selected_items').val($('#selected_items').val()+ ','+ui.item.item_id);
                            }
                            $(event.target).val('');
                            ui.item.value="";
                            itemsUpdater.itemDelete();
                            return false;
                        }
                        function changeCallback(event, ui)
                        {
                            if (!ui.item)
                	        {
                                $('#item_searcher').val("");
                                return false;
                            }
                        }
                        itemsUpdater.itemDelete();
                        $("form#pack_item_edit").submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Updating Details...</h2></div>' });
                            }
                            else
                            {
                                return false;
                            }
                        });
                    }
                },
                'update':{
                    click: function(el){
                        var prod_id = $(el).data('productid');
                        var $feedbackbox = $('div#feedback_'+prod_id);
                        var $errorbox = $('div#error_'+prod_id);
                        var value = $('#lowstock_'+prod_id).val();
                        $feedbackbox.slideUp('slow');
                        $errorbox.slideUp('slow');
                        if(value != "")
                        {
                            if( (!isNaN(value) && (function(x) { return (x | 0) === x; })(parseFloat(value))) && value > 0 )
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Updating Records...</h2></div>' });
                                url = "/ajaxfunctions/updateWarningLevel";
                                $.ajax({
                                    url: url,
                                    type:"post",
                                    data: {product_id: prod_id, value: value},
                                    error: function(request, status, error){
                                        $errorbox.html(error).slideDown('slow');
                                        $.unblockUI();
                                    },
                                    success: function(d){
                                        $feedbackbox.slideDown('slow');
                                        $.unblockUI();
                                    }
                                });
                            }
                            else
                            {
                                $errorbox.slideDown('slow');
                            }
                        }
                    }
                }
            }

            //run the script for the current page
            actions[config.curPage].init();
        </script>
        <?php Database::closeConnection(); ?>
    </body>
</html>