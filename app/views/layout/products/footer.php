
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                common: {
                    init: function(){
                        $('input#external_image').change(function(e){
                            $('input.product_image').toggle();
                            if($(this).is(":checked"))
                                $('input#eximage').valid().focus();
                            else
                                $('em#eximage-error').remove();
                        });
                        $('input#boxed_item').click(function(e){
                            $('input#weight').valid();
                            $('input#width').valid();
                            $('input#depth').valid();
                            $('input#height').valid();
                        });
                        $('input#barcode').change(function(ev){
                            var val = $(this).val().replace(/\D/g, "");
                            $(this).val(val);
                        });
                        $('#client_selector').change(function(e){
                            console.log('client id: '+$(this).val());
                            if($('this').val() == 87)
                                $("div#is_arccos_holder").show();
                            else
                                $("div#is_arccos_holder").hide();
                        });
                    }
                },
                'client-product-edit': {
                    init: function(){
                        $('form#client_edit_product').submit(function(){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Processing form...</h2></div>' });
                            }
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
                            "processing": true,
                            "mark": true,
                            "language": {
                                processing: 'Fetching results and updating the display.....'
                            },
                            "serverSide": true,
                            "ajax": {
                                "url": "/ajaxfunctions/dataTablesViewProducts",
                                "data": function( d ){
                                    d.clientID = $("#client_id").val();
                                    d.active = $("#active").val();
                                }
                            },
                            "drawCallback": function( settings ) {
                                $('button.update_product').click(function(e) {
                                    actions.update.click(this);
                                })
                            }
                        } );

                        $('button.update_product').click(function(e) {
                            actions.update.click(this);
                        });
                    }
                },
                'collections-edit': {
                    init: function(){
                        $('a#remove-all-items').click(function(e){
                            e.preventDefault();
                            swal({
                                title: "Really remove all items?",
                                text: "This cannot be undone",
                                icon: "warning",
                                buttons: true,
                                dangerMode: true,
                            }).then( function(willRemove) {
                                if (willRemove) {
                                   $('div#the_items div.item_holder').remove();
                                   $('input#item_searcher').focus();
                                    itemsUpdater.itemDelete();
                                }
                            });
                        });
                        $('#product_selector').change(function(e){
                            if($(this).val() > 0)
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Collecting Collection Details...</h2></div>' });
                                var client_id = $('#client_selector').val();
                                window.location.href = "/products/collections-edit/client=" + client_id + "/product=" + $(this).val();
                            }
                        });
                        $('#client_selector').change(function(e){
                            if($(this).val() > 0)
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Collecting Client Collections...</h2></div>' });
                                window.location.href = "/products/collections-edit/client=" + $(this).val();
                            }
                        });
                        autoCompleter.itemAutoComplete($('#item_searcher'), selectCallback, changeCallback, false);
                        function selectCallback(event, ui)
                        {
                            var appendage = "<div class='row item_holder mb-3'>";
                            appendage += "<label class='col-md-7'>"+ui.item.value+"</label>";
                            appendage += "<div class='col-md-1'>";
                            appendage += "<input type='text' class='form-control required number' name='items["+ui.item.item_id+"][qty]' data-itemid='"+ui.item.item_id+"' />";
                            appendage += "</div>";
                            appendage += "<div class='col-md-2 delete-image-holder'>";
                            appendage += "<a class='delete' data-itemid="+ui.item.item_id+" title='remove this item'><i class='fad fa-times-square text-danger'></i> <span class='inst'>Remove</span></a>";
                            appendage += "</div> ";
                            appendage += "</div>";

                            $('div#the_items').prepend(appendage);
                            $('div#the_items .item_holder').first().find($('input')).focus();
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
                        $("form#collection_edit").submit(function(e){
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