
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                common: {
                    init: function(){
                        $('button.ship_quote').click(function(e)
                        {
                            e.preventDefault();
                            shippingQuote.getQuotes($(this).data('orderid'), $(this).data('destination'));
                        });
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
                        });
                    }
                },
                'order-edit': {
                    init: function(){
                        $('form#order-edit').submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Updating Order...</h2></div>' });
                            }
                            else
                            {
                                return false;
                            }
                        });
                    }
                },
                'order-update' : {
                    init: function(){
                        actions.common.init();
                        $('a.delete-package')
                            .css('cursor', 'pointer')
                            .click(function(e){
                                if(confirm('Really delete this package?'))
                                {
                                    $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Deleting package...</h2></div>' });
                                    var data = {
                                        lineid: $(this).data('packageid')
                                    };
                                    $.post('/ajaxfunctions/deletePackage', data, function(d){
                                        location.reload();
                                    });
                                }
                        });

                        $('form#order-courier-update').submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Updating Courier...</h2></div>' });
                            }
                            else
                            {
                                return false;
                            }
                        });

                        $('select#courier_id').change(function(e){
                            if($(this).val() == $("#local_id").val())
                            {
                                $('#local-details').slideDown();
                                $('#local_display').removeAttr('disabled');
                            }
                            else
                            {
                                $('#local-details').slideUp();
                                $('#local_display').attr('disabled', true);
                            }
                        });

                        $('a.eparcel-label').click(function(e){
                            e.preventDefault();
                            var ids = [];
                            ids.push($(this).data('orderid'));
                            $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h1>Generating Labels...</h1></div>' });
                            var form = document.createElement('form');
                            form.setAttribute("method", "post");
                            form.setAttribute("action", "/labels/eparcel-labels");
                            $.each( ids, function( index, value ) {
                                var hiddenField = document.createElement("input");
                                hiddenField.setAttribute("type", "hidden");
                                hiddenField.setAttribute("name", "orders[]");
                                hiddenField.setAttribute("value", value);
                                form.appendChild(hiddenField);
                            });
                            //window.open('','formresult');
                            document.body.appendChild(form);
                            form.submit();
                        });

                        $('a.hunters-label').click(function(e){
                            e.preventDefault();
                            var ids = [];
                            ids.push($(this).data('orderid'));
                            var form = document.createElement('form');
                            form.setAttribute("method", "post");
                            form.setAttribute("action", "/pdf/printHuntersLabels");
                            form.setAttribute("target", "formresult");
                            $.each( ids, function( index, value ) {
                                var hiddenField = document.createElement("input");
                                hiddenField.setAttribute("type", "hidden");
                                hiddenField.setAttribute("name", "orders[]");
                                hiddenField.setAttribute("value", value);
                                form.appendChild(hiddenField);
                            });
                            document.body.appendChild(form);
                            window.open('','formresult');
                            form.submit();
                        });

                        $('button#truck_charge_calc').click(function(e) {
                            e.preventDefault();
                    		var dest = $(this).data("destination");
                            truckCost.getCharge();
                        });

                        $('button#order_fulfill').click(function(e){
                            var courier_id = $(this).data('courierid');
                            var order_ids = $(this).data('orderid')
                            var valid = true;
                            var progress = (
                                courier_id == config.huntersId ||
                                courier_id == config.huntersPluId ||
                                courier_id == config.eParcelId ||
                                courier_id == config.eParcelExpressId ||
                                courier_id == config.huntersPalId
                            );
                            var text = "";
                            var consignment_id = "";
                            var pallet_count = 0;
                            var truck_charge = 0;
                            var local_charge = 0;
                            if(courier_id == $('#three3pltruck_id').val())
                            {
                                //$("#our_truck").validate().element('#consignment_id');
                                if($('#our_truck').valid())
                                {
                                    consignment_id = $('#consignment_id').val();
                                    pallet_count = $('#truck_pallets').val();
                                    truck_charge = $('#truck_charge').val();
                                    if(consignment_id == "")
                                    {
                                        valid = false;
                                        text += "Consignment ID for the truck is required\n";
                                    }
                                    if(truck_charge == "")
                                    {
                                        valid = false;
                                        text += "A charge for the truck is required\n";
                                    }
                                    if(pallet_count <= 0)
                                    {
                                        valid = false;
                                        text += "The pallet count must be greater than 0\n";
                                    }
                                    progress = true;
                                }
                            }
                            if(courier_id == $('#local_id').val())
                            {
                                //$("#our_truck").validate().element('#consignment_id');
                                if($('#local_courier').valid())
                                {
                                    consignment_id = $('#consignment_id').val();
                                    local_charge = $('#local_charge').val();
                                    if(consignment_id == "")
                                    {
                                        valid = false;
                                        text += "Consignment ID for local couriers is required\n";
                                    }
                                    if(local_charge == "")
                                    {
                                        valid = false;
                                        text += "A charge for local couriers is required\n";
                                    }
                                    progress = true;
                                }
                            }
                            if(progress)
                            {
                                if(!valid)
                                {
                                    swal({
                                       title: "Cannot Fulfill Order",
                                       text: text,
                                       icon: "error"
                                    });
                                }
                                else
                                {
                                    swal({
                                        title: "Fulfill This Order?",
                                        text: "This will close the order and adjust stock\n\nIt cannot be undone",
                                        icon: "warning",
                                        buttons: true,
                                        dangerMode: true
                                    }).then( function(willFulfill) {
                                        if (willFulfill) {
                                            //console.log('will fulfill order '+$(this).data('orderid'));
                                            $.ajax({
                                                url: '/ajaxfunctions/fulfill-order',
                                                method: 'post',
                                                data: {
                                                    order_ids: order_ids,
                                                    courier_id: courier_id,
                                                    consignment_id : consignment_id,
                                                    pallet_count: pallet_count,
                                                    truck_charge: truck_charge,
                                                    local_charge: local_charge
                                                },
                                                dataType: 'json',
                                                beforeSend: function(){
                                                    $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Fulfilling Order...</h1></div>' });
                                                },
                                                success: function(d){
                                                    if(d.error)
                                                    {
                                                        $.unblockUI();
                                                        alert('error');
                                                    }
                                                    else
                                                    {
                                                        location.reload();
                                                    }
                                                },
                                                error: function(jqXHR, textStatus, errorThrown){
                                                    $.unblockUI();
                                                    document.open();
                                                    document.write(jqXHR.responseText);
                                                    document.close();
                                                }
                                            });
                                        }
                                    });
                                }
                            }

                        });
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
                                qty_html = "<div class='col-sm-4'><input type='text' class='form-control number item_qty' name='items["+item_count+"][qty]' placeholder='Qty' /></div>";
                                qty_html += "<div class='col-sm-8'><select class='form-control selectpicker pallet_qty' name='items["+item_count+"][pallet_qty]'><option value='0'>Whole Pallet Qty</option>";
                                pallet_vals.forEach(function(pallet_val) {
                                    //console.log(pallet_val);
                                    qty_html += "<option>"+pallet_val+"</option>";
                                });
                                qty_html += "</select></div>";
                                inst = "<p class='inst'>There are currently <strong>"+ui.item.total_available+"</strong> of these available";
                                inst += "<p class='inst'>Select whole pallet amounts from the dropdown selector <strong>OR</strong>";
                                inst += "<br/>If you require us to break a pallet, enter an amount in the 'Qty' text field</p>";
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
                            $holder.find('input.item_qty').focus();
                            $('.selectpicker').selectpicker();
                            //actions['item-searcher-test'].init();
                            //add some validation for the form
                            $( "input.item_qty, select.pallet_qty" ).each(function(i,e){
                                $(this).rules( "remove");
                            });
                            $.validator.addClassRules('item_qty',{
                                required: function(el){
                                    console.log('next element name: '+$(el).parent().next().find('.pallet_qty').attr('name'));
                                    return ($(el).next('.pallet_qty').val() === 0 || $(el).next('.pallet_qty').val() === undefined );
                                },
                                digits: true
                            });
                            $.validator.addClassRules('pallet_qty',{
                                notNone: function(el){
                                    console.log('prev element name: '+$(el).prev('.item_qty').attr('name'));
                                    return ( $(el).prev('.item_qty').val() === 0 || $(el).prev('.item_qty').val() === "" );
                                }
                            });
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
                'items-update' : {
                    init: function(){
                        actions.common['add-item']();
                        itemsUpdater.itemDelete();
                        actions['item-searcher'].init();
                        $("form#items-update").submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Processing form...</h2></div>' });
                            }
                        });
                        $.validator.addClassRules("item-group", {
                            wholePallets : true
                        });
                    }
                },
                'address-update':{
                    init: function(){
                        autoCompleter.addressAutoComplete($('#address'));
                        autoCompleter.suburbAutoComplete($('#suburb'));
                        $("form#address-update").submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Processing form...</h2></div>' });
                            }
                        });
                    }
                },
                'add-order': {
                    init: function()
                    {
                        //$('select#client_id').val(0);
                        actions.common.init();
                        $('select#client_id').change(function(e){
                            if($(this).val() != "0")
                                $('div#item_selector').show();
                            else
                                $('div#item_selector').hide();
                        });
                        actions.common['add-item']();
                        actions['item-searcher'].init();
                        autoCompleter.addressAutoComplete($('#address'));
                        autoCompleter.suburbAutoComplete($('#suburb'));
                        itemsUpdater.itemDelete();
                        $("form#add_order").submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Processing form...</h2></div>' });
                            }
                        });
                    }
                },
                'view-storeorders' : {
                    init: function(){
                        actions.common.init();
                        $("button#show_fulfilled").click(function(e){
                            var href = '/orders/view-storeorders';
                            if($('#client_selector').val() != 0)
                                href += "/client="+$('#client_selector').val();
                            href += "/fulfilled=1";
                            window.location.href = href;
                        });
                        $("button#show_unfulfilled").click(function(e){
                            var href = '/orders/view-storeorders';
                            if($('#client_selector').val() != 0)
                                href += "/client="+$('#client_selector').val();
                            window.location.href = href;
                        });
                        $('#client_selector').change(function(e){
                            $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h1>Collecting data...</h1></div>' });
                            var href = '/orders/view-storeorders';
                            if($('#client_selector').val() != 0)
                                href += "/client="+$('#client_selector').val();
                            if($('#fulfilled').val() != 0)
                                href += "/fulfilled="+$('#fulfilled').val();
                            window.location.href = href;
                        });

                        $('a.packslip-print').click(function(e){
                            e.preventDefault();
                            //console.log('click');
                            if($('input.select:checked').length)
                            {
                                var ids = [];
                                $('input.select').each(function(i,e){
                                    if($(this).prop('checked'))
                                    {
                                        ids.push($(this).data('orderid'));
                                    }
                                });


                                var form = document.createElement('form');
                                form.setAttribute("method", "post");
                                form.setAttribute("action", "/pdf/printPackslips");
                                //form.setAttribute("action", "/misc-functions/make-packslips-pdf.php");
                                form.setAttribute("target", "packslipformresult");
                                $.each( ids, function( index, value ) {
                                    var hiddenField = document.createElement("input");
                                    hiddenField.setAttribute("type", "hidden");
                                    hiddenField.setAttribute("name", "items[]");
                                    hiddenField.setAttribute("value", value);
                                    form.appendChild(hiddenField);
                                });
                                document.body.appendChild(form);
                                window.open('','packslipformresult');
                                form.submit();
                            }
                        });

                        $('a.pickslip-print').click(function(e){
                            e.preventDefault();
                            //console.log('click');
                            if($('input.select:checked').length)
                            {
                                var ids = [];
                                $('input.select').each(function(i,e){
                                    if($(this).prop('checked'))
                                    {
                                        ids.push($(this).data('orderid'));
                                    }
                                });


                                var form = document.createElement('form');
                                form.setAttribute("method", "post");
                                form.setAttribute("action", "/pdf/printPickslips");
                                //form.setAttribute("action", "/misc-functions/make-packslips-pdf.php");
                                form.setAttribute("target", "pickslipformresult");
                                $.each( ids, function( index, value ) {
                                    var hiddenField = document.createElement("input");
                                    hiddenField.setAttribute("type", "hidden");
                                    hiddenField.setAttribute("name", "items[]");
                                    hiddenField.setAttribute("value", value);
                                    form.appendChild(hiddenField);
                                });
                                document.body.appendChild(form);
                                window.open('','pickslipformresult');
                                form.submit();
                            }
                        });

                        $('a.cancel-order').click(function(e){
                            e.preventDefault();
                            swal({
                                title: "Really cancel these orders?",
                                text: "This cannot be undone",
                                icon: "warning",
                                buttons: true,
                                dangerMode: true,
                            }).then( function(willCancel) {
                                if (willCancel) {
                                    if($('input.select:checked').length)
                                    {
                                    	var ids = [];
                                        $('input.select').each(function(i,e){
                                            if( $(this).prop('checked'))
                                            {
                                                ids.push($(this).data('orderid'));
                                            }
                                        });

                                        $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h1>Cancelling Orders...</h1></div>' });

                                        var data = {orderids: ids}
                                        $.post('/ajaxfunctions/cancel-orders', data, function(d){
                                            location.reload();
                                        });
                                    }
                                }
                            });
                        });

                        $('#select_all').click(function(e){
                            var checked = this.checked;
                             $('.select').each(function(e){
                                this.checked =  checked;
                             })
                        });

                        $('.selectpicker').selectpicker({});

                        /**/
                        $('table#client_orders_table').filterTable({
                            inputSelector: '#table_searcher'
                        });
                    }
                },
                'view-pickups': {
                    init: function(){
                        $('#client_selector').change(function(e){
                            $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h1>Collecting data...</h1></div>' });
                            var href = '/orders/view-pickups';
                            if($('#client_selector').val() != 0)
                                href += "/client="+$('#client_selector').val();
                            window.location.href = href;
                        });

                        $('table#pickups_table').filterTable({
                            inputSelector: '#table_searcher'
                        });

                        $('button.cancel-pickup').click(function(e){
                            var pickupid = $(this).data('pickupid');
                            swal({
                                title: "Really cancel this pickup?",
                                text: "This cannot be undone",
                                icon: "warning",
                                buttons: true,
                                dangerMode: true,
                            }).then( function(willCancel) {
                                if (willCancel) {
                                    console.log(pickupid);
                                }
                            });
                        });
                    }
                },
                'pickup-update': {
                    init: function(){
                        $('#partof_order').click(function(){
                            if($(this).prop('checked'))
                            {
                                $('#order_number_holder').slideDown();
                                $("#order_number").addClass('required');
                                $("#truck_charge").removeClass("required").val('').prop('disabled', true);
                            }
                            else
                            {
                                $('#order_number_holder').slideUp();
                                $("#order_number").removeClass('required');
                                $("#truck_charge").addClass("required").prop('disabled', false);
                            }
                        });

                        $('button#truck_charge_calc').click(function(e){
                            e.preventDefault();
                            truckCost.getCharge();
                        });

                        $('form#pickup-update').submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Saving Details...</h1></div>' });
                            }
                        });
                    }
                },
                'view-orders': {
                    init: function(){
                        actions.common.init();
                        $('#client_selector, #courier_selector').change(function(e){
                            $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h1>Collecting data...</h1></div>' });
                            var href = '/orders/view-orders';
                            if($('#client_selector').val() != 0)
                                href += "/client="+$('#client_selector').val();
                            if($('#courier_selector').val() != -1)
                                href += "/courier="+$('#courier_selector').val();
                            if($('#fulfilled').val() != 0)
                                href += "/fulfilled="+$('#fulfilled').val();
                            window.location.href = href;
                        });

                        $('#select_all').click(function(e){
                            var checked = this.checked;
                             $('.select').each(function(e){
                                this.checked =  checked;
                             })
                        });

                        $('.selectpicker').selectpicker({});

                        $('select#courier_all').change(function(e){
                           	var c = $(this).val();
                            console.log('change');
                            $("select.courier").each(function(i,e){
                                if(!$(this).prop('disabled'))
                            	    $(this).val(c).change();
                            });
                        });

                        /**/
                        $('table#client_orders_table').filterTable({
                            inputSelector: '#table_searcher'
                        });

                        //$('table#client_orders_table').stickyTableHeaders();

                        $('a.select-courier').click(function(e){
                            e.preventDefault();
                            if($('input.select:checked').length)
                            {
                                var ids = {};
                                $('input.select').each(function(i,e){
                                    var thisid = $(this).data('orderid');
                                    //console.log('orderid: '+thisid);
                                    var courier_id = $('select#courier_'+ thisid).val();
                    				if( $(this).prop('checked') && courier_id >= 0)
                    				{
                    					//ids.push($(this).data('orderid'));
                    					ids[thisid] = courier_id;
                    				}
                                });
                                if(Object.keys(ids).length)
                                {
                                    $.ajax({
                                        url: '/ajaxfunctions/select-courier',
                                        method: 'post',
                                        data: { order_ids: ids },
                                        dataType: 'json',
                                        beforeSend: function(){
                                            $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Contacting Couriers...</h2></div>' });
                                        },
                                        success: function(d){
                                            if(d.error)
                                            {
                                                $.unblockUI();
                                                alert('error');
                                            }
                                            else
                                            {
                                                location.reload();
                                            }
                                        }
                                    });
                                }
                            }
                        });

                        $('a.eparcel-label-print').click(function(e){
                            e.preventDefault();

                            if($('input.select:checked').length)
                            {
                                var ids = [];
                                $('input.select').each(function(i,e){
                                    var order_id = $(this).data('orderid');
                                    console.log('order_id: '+ order_id);
                                    if($(this).prop('checked') && ( $('select#courier_'+order_id).val() == config.eParcelId || $('select#courier_'+order_id).val() == config.eParcelExpressId ))
                                    {
                                        ids.push(order_id);
                                    }
                                });
                                if(ids.length)
                                {
                                    $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h1>Generating Labels...</h1></div>' });
                                    var form = document.createElement('form');
                                    form.setAttribute("method", "post");
                                    form.setAttribute("action", "/labels/eparcel-labels");
                                    //form.setAttribute("action", "/misc-functions/make-packslips-pdf.php");
                                    //form.setAttribute("target", "formresult");
                                    $.each( ids, function( index, value ) {
                                        var hiddenField = document.createElement("input");
                                        hiddenField.setAttribute("type", "hidden");
                                        hiddenField.setAttribute("name", "orders[]");
                                        hiddenField.setAttribute("value", value);
                                        form.appendChild(hiddenField);
                                    });
                                    document.body.appendChild(form);
                                    //window.open('','formresult');
                                    form.submit();
                                }
                            }
                        });

                        $('a.hunters-label-print').click(function(e){
                            e.preventDefault();
                            var ids = [];
                            $('input.select').each(function(i,e){
                                var order_id = $(this).data('orderid');
                                if($(this).prop('checked') && ( $('select#courier_'+order_id).val() == config.huntersId || $('select#courier_'+order_id).val() == config.huntersPluId || $('select#courier_'+order_id).val() == config.huntersPalId ))
                                {
                                    ids.push(order_id);
                                }
                            });
                            var form = document.createElement('form');
                            form.setAttribute("method", "post");
                            form.setAttribute("action", "/pdf/printHuntersLabels");
                            form.setAttribute("target", "formresult");
                            $.each( ids, function( index, value ) {
                                var hiddenField = document.createElement("input");
                                hiddenField.setAttribute("type", "hidden");
                                hiddenField.setAttribute("name", "orders[]");
                                hiddenField.setAttribute("value", value);
                                form.appendChild(hiddenField);
                            });
                            document.body.appendChild(form);
                            window.open('','formresult');
                            form.submit();
                        });


                        $('a.slip-print').click(function(e){
                            e.preventDefault();
                            //console.log('click');
                            if($('input.select:checked').length)
                            {
                                var ids = [];
                                $('input.select').each(function(i,e){
                                    if($(this).prop('checked'))
                                    {
                                        ids.push($(this).data('orderid'));
                                    }
                                });


                                var form = document.createElement('form');
                                form.setAttribute("method", "post");
                                form.setAttribute("action", "/pdf/printPickslips");
                                //form.setAttribute("action", "/misc-functions/make-packslips-pdf.php");
                                form.setAttribute("target", "formresult");
                                $.each( ids, function( index, value ) {
                                    var hiddenField = document.createElement("input");
                                    hiddenField.setAttribute("type", "hidden");
                                    hiddenField.setAttribute("name", "items[]");
                                    hiddenField.setAttribute("value", value);
                                    form.appendChild(hiddenField);
                                });
                                document.body.appendChild(form);
                                window.open('','formresult');
                                form.submit();
                            }
                        });

                        $('a.print-invoices').click(function(e){
                           	e.preventDefault();
                            if($('input.select:checked').length)
                            {
                                var ids = [];
                                $('input.select').each(function(i,e){
                                    if($(this).prop('checked'))
                                    {
                                        ids.push($(this).data('orderid'));
                                    }
                                });

                                var form = document.createElement('form');
                                form.setAttribute("method", "post");
                                //form.setAttribute("action", "/misc-functions/print-invoices.php");
                                form.setAttribute("action", "/pdf/printInvoices");
                                form.setAttribute("target", "formresult");
                                $.each( ids, function( index, value ) {
                                    var hiddenField = document.createElement("input");
                                    hiddenField.setAttribute("type", "hidden");
                                    hiddenField.setAttribute("name", "orders[]");
                                    hiddenField.setAttribute("value", value);
                                    form.appendChild(hiddenField);
                                });
                                document.body.appendChild(form);
                                window.open('','formresult');
                                form.submit();
                            }
                        });

                        $('a.cancel-order').click(function(e){
                            e.preventDefault();
                            swal({
                                title: "Really cancel these orders?",
                                text: "This cannot be undone",
                                icon: "warning",
                                buttons: true,
                                dangerMode: true,
                            }).then( function(willCancel) {
                                if (willCancel) {
                                    if($('input.select:checked').length)
                                    {
                                    	var ids = [];
                                        $('input.select').each(function(i,e){
                                            if( $(this).prop('checked'))
                                            {
                                                ids.push($(this).data('orderid'));
                                            }
                                        });

                                        $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h1>Cancelling Orders...</h1></div>' });

                                        var data = {orderids: ids}
                                        $.post('/ajaxfunctions/cancel-orders', data, function(d){
                                            location.reload();
                                        });
                                    }
                                }
                            });
                        });

                        $('a.eparcel-fulfill').click(function(e){
                            e.preventDefault();
                            swal({
                                title: "Fulfill These Orders?",
                                text: "This will close each order and adjust stock\n\nIt cannot be undone",
                                icon: "warning",
                                buttons: true,
                                dangerMode: true
                            }).then( function(willFulfill) {
                                var ids = [];
                                $('input.select').each(function(i,e){
                                    var order_id = $(this).data('orderid');
                                    console.log('order_id: '+ order_id);
                                    if($(this).prop('checked') && ( $('select#courier_'+order_id).val() == config.eParcelId || $('select#courier_'+order_id).val() == config.eParcelExpressId ))
                                    {
                                        ids.push(order_id);
                                    }
                                });
                                $.ajax({
                                    url: '/ajaxfunctions/fulfill-order',
                                    method: 'post',
                                    data: {
                                        order_ids: ids,
                                        courier_id: config.eParcelId
                                    },
                                    dataType: 'json',
                                    beforeSend: function(){
                                        $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Fulfilling Orders...</h1></div>' });
                                    },
                                    success: function(d){
                                        if(d.error)
                                        {
                                            $.unblockUI();
                                            alert('error');
                                        }
                                        else
                                        {
                                            location.reload();
                                        }
                                    },
                                    error: function(jqXHR, textStatus, errorThrown){
                                        $.unblockUI();
                                        document.open();
                                        document.write(jqXHR.responseText);
                                        document.close();
                                    }
                                });
                            });
                        });

                        $('a.hunters-fulfill').click(function(e){
                            e.preventDefault();
                            swal({
                                title: "Fulfill These Orders?",
                                text: "This will close each order and adjust stock\n\nIt cannot be undone",
                                icon: "warning",
                                buttons: true,
                                dangerMode: true
                            }).then( function(willFulfill) {
                                var ids = [];
                                $('input.select').each(function(i,e){
                                    var order_id = $(this).data('orderid');
                                    console.log('order_id: '+ order_id);
                                    if($(this).prop('checked') && ( $('select#courier_'+order_id).val() == config.huntersId || $('select#courier_'+order_id).val() == config.huntersPluId || $('select#courier_'+order_id).val() == config.huntersPalId ))
                                    {
                                        ids.push(order_id);
                                    }
                                });
                                $.ajax({
                                    url: '/ajaxfunctions/fulfill-order',
                                    method: 'post',
                                    data: {
                                        order_ids: ids,
                                        courier_id: config.huntersId
                                    },
                                    dataType: 'json',
                                    beforeSend: function(){
                                        $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Fulfilling Orders...</h1></div>' });
                                    },
                                    success: function(d){
                                        if(d.error)
                                        {
                                            $.unblockUI();
                                            alert('error');
                                        }
                                        else
                                        {
                                            location.reload();
                                        }
                                    }
                                });
                            });
                        });
                    }
                },
                'order-search':{
                    init: function(){
                        $("form#order_search").submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Searching Orders...</h2></div>' });
                            }
                            else
                            {
                                return false;
                            }
                        });

                        datePicker.betweenDates();
                    }
                },
                'order-search-results':{
                    init: function(){
                        actions['order-search'].init();
                    }
                },
                'order-dispatching' : {
                    init: function(){
                        barcodeScanner.init({
                            onComplete: function(barcode, qty){
                        	    var conid = false;
                                var process = true;
                                if( barcode.toUpperCase().startsWith("VV") )
                                {
                                    conid = barcode.substring(0, 8)
                                }
                                else
                                {
                                    //var ep = new RegExp("RJM\d{7}|HKM\d{7}|S2U\d{7}|F24\d{7}|F25\d{7}|XTS\d{7}", "i");
                                    var ep = /RJM\d{7}|HKM\d{7}|S2U\d{7}|F24\d{7}|F25\d{7}|XTS\d{7}|LH\d{9}AU/i;
                                    //ep.lastIndex = 0;
                                    var conida = ep.exec(barcode);
                                    if(!conida)
                                    {
                                        swal({
                                            title: "Error!",
                                            text: 'Consignment ID not found from '+barcode,
                                            icon: "error",
                                        })
                                        .then(function(value){
                                            process = true;
                                        });
                                        $.playSound("/sounds/bff-strike.wav");
                                        $("#label_barcode").removeClass('loading').attr("placeholder", "Scan Label Barcode");
                                        process = false;
                                    }
                                    else
                                    {
                                        conid = conida[0];
                                        console.log("conid: "+conid);
                                        console.log("barcode: "+barcode);
                                    }
                                }
                                if(!conid)
                                {
                                    swal({
                                        title: "Error!",
                                        text: 'Consignment ID not found from '+barcode,
                                        icon: "error",
                                    })
                                    .then(function(value){
                                        process = true;
                                    });
                                    $.playSound("/sounds/bff-strike.wav");
                                    $("#label_barcode").removeClass('loading').attr("placeholder", "Scan Label Barcode");
                                    process = false;
                                }
                                if(process)
                                {
                                    process = false;
                                    //find order
                                    $.ajax({
                                        url: '/ajaxfunctions/record-dispatch',
                                        method: 'post',
                                        data: { consignment_id: conid },
                                        dataType: 'json',
                                        beforeSend: function(){
                                            $("#label_barcode").addClass('loading').attr("placeholder", "Locating Order");
                                        },
                                        success: function(d){
                                            if(d.error)
                                            {
                                                swal({
                                                    title: "Error!",
                                                    text: d.error_string,
                                                    icon: "error",
                                                });
                                                $.playSound("/sounds/bff-strike.wav");
                                                $("#label_barcode").removeClass('loading').attr("placeholder", "Scan Label Barcode");
                                            }
                                            else
                                            {
                                                swal({
                                                    title: "Dispatch Recorded",
                                                    icon: "success",
                                                });
                                                $.playSound("/sounds/bff-good.wav");
                                                $("#label_barcode").removeClass('loading').attr("placeholder", "Scan Label Barcode");
                                            }
                                            process = true;
                                        }
                                    });
                                }
                            }
                        });
                    }
                },
                'order-picking' : {
                    init: function() {
                        $('button#reset').click(function(e){
                            e.preventDefault();
                            $("#reset_holder").hide();
                            $("#order_number").val("").removeClass('loading').attr("placeholder", "Scan Order Summary Barcode");;
                            $("#orders_details").html("");
                        });
                        barcodeScanner.init({
                            /**/ preventDefault: true,
                            onError: function(string, qty) {
                                //$('#userInput').val ($('#userInput').val()  + string);
                                $( document.activeElement ).val( $( document.activeElement ).val() + string);
                            },
                            onComplete: function(barcode, qty){
                        	    /*console.log('barcode: '+barcode);
                                */
                                if( $("#order_number").val() == "" )
                                {
                                    $.ajax({
                                        url: '/ajaxfunctions/getASummary',
                                        method: 'post',
                                        data: { barcode: barcode },
                                        dataType: 'json',
                                        beforeSend: function(){
                                            $("#order_number").addClass('loading').attr("placeholder", "Preparing Pick Summary");
                                        },
                                        success: function(d){
                                            //console.log(d);
                                            if(d.error)
                                            {
                                                //alert(d.error_string);
                                                swal({
                                                    title: d.error_string,
                                                    icon: "error",
                                                });
                                                $.playSound("/sounds/bff-strike.wav");
                                                $("#order_number").removeClass('loading').attr("placeholder", "Scan Order Summary Barcode");
                                                $("#reset_holder").hide();
                                            }
                                            else
                                            {
                                                $("#order_number").removeClass('loading').val(d.order_number).prop('disabled', true);
                                                $("#order_details").html(d.items);
                                                //$("#reset_holder").show();
                                                $('button#reset').off('click').click(function(e){
                                                    e.preventDefault();
                                                    $("#reset_holder").hide();
                                                    $("#order_number").val("").removeClass('loading').attr("placeholder", "Scan Order Summary Barcode");
                                                    $("#order_details").html("");
                                                });
                                            }
                                        },
                                        error: function(xhr, e){
                                            console.log('xhr: ' + JSON.stringify(xhr, null, 4));
                                            console.log('error: ' + e);
                                        }
                                    });
                                }
                                else
                                {
                                    //if(barcode == "12345670")
                                    var location = barcode.substr(0, barcode.length - 1);
                                    var lid = location.replace(/\./g,"").toUpperCase();
                                    //console.log(lid);
                                    var $location_div = $('div#'+lid);
                                    $location_div.addClass('bs-callout bs-callout-danger scanned').removeClass('unscanned');
                                    $location_div.find('input.pick_count').attr("placeholder", "Enter Pick Count").prop("disabled", false).val("").focus();
                                    if( $("form div.unscanned").length == 0)
                                    {
                                        $('button#submit_button').prop('disabled', false);


                                        $('form#order_picking').validate({});

                                        $('input.pick_count').each(function(i,e){
                                            $(this).rules('add',{
                                                required: true,
                                                digits: true,
                                                pickChecker: true
                                            });
                                        });
                                        /*
                                        $.validator.addClassRules("pick_count", {
                                            required: true,
                                            digits: true
                                        });
                                        */
                                        $('form#order_picking').submit(function(e){
                                            if($(this).valid())
                                            {
                                                $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Recording Picks...</h1></div>' });
                                            }
                                            else
                                            {
                                                return false;
                                            }
                                        })
                                    }
                                }
                            }
                        });
                    }
                },
                'order-packing' : {
                    init: function() {
                        barcodeScanner.init({
                            //ignoreIfFocusOn: 'input',
                            preventDefault: true,
                            onError: function(string, qty) {
                                //$('#userInput').val ($('#userInput').val()  + string);
                                $( document.activeElement ).val( $( document.activeElement ).val() + string);
                            },
                            onComplete: function(barcode, qty){
                                /* console.log('barcode: '+barcode);
                                console.log('qty: '+qty);      */
                                if( $("#order_number").val() == "" )
                                {
                                    $.ajax({
                                        url: '/ajaxfunctions/getAnOrderByNumber',
                                        method: 'post',
                                        data: { barcode: barcode },
                                        dataType: 'json',
                                        beforeSend: function(){
                                            $("#order_number").addClass('loading').attr("placeholder", "Locating Order");
                                        },
                                        success: function(d){
                                            //console.log(d);
                                            if(d.error)
                                            {
                                                //alert(d.error_string);
                                                swal({
                                                    title: d.error_string,
                                                    icon: "error",
                                                });
                                                $.playSound("/sounds/bff-strike.wav");
                                                $("#order_number").removeClass('loading').attr("placeholder", "Scan Order Barcode").prop('disabled', false);
                                                //$("#reset_holder").hide();
                                            }
                                            else
                                            {
                                                $("#order_number").removeClass('loading').val(d.order_number).prop('disabled', true);
                                                $("#order_details").html(d.items);
                                                //$("#reset_holder").show();
                                                $('button#reset').off('click').click(function(e){
                                                    e.preventDefault();
                                                    $("#reset_holder").hide();
                                                    $("#order_number").val("").removeClass('loading').attr("placeholder", "Scan Order Barcode");;
                                                    $("#order_details").html("");
                                                });
                                            }
                                        },
                                        error: function(xhr, e){
                                            console.log('xhr: ' + JSON.stringify(xhr, null, 4));
                                            console.log('error: ' + e);
                                        }
                                    });
                                }
                                else
                                {
                                    $.ajax({
                                        url: '/ajaxfunctions/getScannedItem',
                                        method: 'post',
                                        data: {
                                            barcode: barcode,
                                            orderid: $("#order_id").val(),
                                            clientid: $("#client_id").val()
                                        },
                                        dataType: 'json',
                                        beforeSend: function(){
                                            $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Finding Item...</h1></div>' });
                                        },
                                        success: function(d){
                                            $.unblockUI();
                                            if(d.error)
                                            {
                                                swal({
                                                    title: "Error!",
                                                    text: d.error_string,
                                                    icon: "error",
                                                });
                                                $.playSound("/sounds/bff-strike.wav");
                                            }
                                            else
                                            {
                                                $.playSound("/sounds/bff-good.wav");
                                                var $item = $('input#packed_'+d.item_id);
                                                console.log($item.closest('div.row').hasClass('form-group'));
                                                $item.closest('div.row').addClass('bs-callout bs-callout-danger');
                                                $item.prop('disabled', false).attr('placeholder', "Enter Amount Packed").focus().blur(function(e){
                                                    //console.log('ordercount: '+$(this).data('ordercount'));
                                                    //console.log('value: '+$(this).data());
                                                    if( $(this).val() == parseInt($(this).data('ordercount')) )
                                                    {
                                                        $('i#good_'+d.item_id).show();
                                                        $('i#bad_'+d.item_id).hide();
                                                    }
                                                    else
                                                    {
                                                        $('i#bad_'+d.item_id).show();
                                                        $('i#good_'+d.item_id).hide();
                                                    }
                                                });
                                                if($('form input[type="text"]:disabled').length == 0)
                                                {
                                                    $('button#submit_button').prop('disabled', false);
                                                    $('form#order_packing').submit(function(e){
                                                        if($(this).valid())
                                                        {
                                                            $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Packing Order...</h1></div>' });
                                                        }
                                                        else
                                                        {
                                                            return false;
                                                        }
                                                    })
                                                }
                                            }
                                        },
                                        error: function(xhr, e){
                                            console.log('xhr: ' + JSON.stringify(xhr, null, 4));
                                            console.log('error: ' + e);
                                        }
                                    });
                                }
                            }
                        });
                    }
                },
                'order-summaries': {
                    init: function(){
                        dataTable.init($('table#order_summaries_table'), {
                            "columnDefs": [
                                { "orderable": false, "targets": [4,5] }
                            ],
                            "order": []
                        } );

                        $('a.summary').click(function(e){
                            window.location.reload();
                        });
                    }
                },
                'clients-orders' : {
                    init: function(){
                        dataTable.init($('table#client_orders_table'), {
                            "columnDefs": [
                                { "orderable": false, "targets": [0,1,5,6,8] }
                            ],
                            "order": [],
                            fixedHeader: true
                        } );
                        datePicker.betweenDates();
                        $('button#change_dates').click(function(e){
                            e.preventDefault();
                            $.blockUI({ message: '<div style="height:120px; padding-top:40px;"><h1>Collecting Orders...</h1></div>' });
                            var from = $('#date_from_value').val();
                            var to = $('#date_to_value').val();
                            window.location.href = "/orders/client-orders/from="+from+"/to="+to;
                        });
                    }
                },
                'order-tracking' : {
                    init: function(){

                    }
                },
                'order-detail' : {
                    init: function(){
                        $('button#print').click(function(e){
                        	$("div#print_this").printArea({
                                    //put some options in
                            });;
                        });
                    }
                },
                'import-orders' : {
                    init: function(){
                        $('form#bulk_order_import').submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Importing Orders...</h1></div>' });
                            }
                        });
                    }
                },
                'truck-usage' :{
                    init:function(){
                        $('select#client_id, #address, #suburb, #postcode, #country').change(function(e){
                            $(this).valid();
                        });
                        autoCompleter.addressAutoComplete($('#address'));
                        autoCompleter.suburbAutoComplete($('#suburb'));
                        datePicker.fromDate();
                        $('button#truck_charge_calc').click(function(e) {
                            e.preventDefault();
                            if($('#suburb').val() == "" || $('#postcode').val() == "")
                            {
                                swal({
                                    title: "Both a suburb and a postcode is required",
                                    icon: "error",
                                });
                                $.playSound("/sounds/bff-strike.wav");
                                return;
                            }
                    		//make the cost window
                            var dest = $('#address').val();
                            dest += " "+$('#address_2').val();
                            dest += " "+$('#suburb').val();
                            dest += " "+$('#state').val();
                            dest += " "+$('#postcode').val();
                            dest += " "+$('#country').val();

                    		$(this).data("destination", dest);
                            truckCost.getCharge();
                        });

                        $('form#truck_usage').submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Saving Details...</h1></div>' });
                            }
                        });
                    }
                },
                'book-pickup' :{
                    init: function(){
                        autoCompleter.addressAutoComplete($('#address'));
                        autoCompleter.addressAutoComplete($('#puaddress'), "pu");

                        $('form#book-pickup').submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Saving Booking...</h1></div>' });
                            }
                        });
                    }
                },
                'record-pickup' :{
                    init: function(){
                        $('#client_selector').change(function(e){
                            if($(this).val() > 0)
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Collecting Client data...</h2></div>' });
                                window.location.href = "/orders/record-pickup/client=" + $(this).val();
                            }
                        });
                        autoCompleter.addressAutoComplete($('#address'));
                        $('#address, #suburb, #postcode, #country').change(function(e){
                            $(this).valid();
                        });
                        $('form#record-pickup').submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Recording Pickup...</h1></div>' });
                            }
                        });
                    }
                },
                'order-importing': {
                    init:function(){
                        $("button#bb_full_import, button#noa_full_import, button#nuchev_full_import").click(function(e){
                            var action = $(this).data('function');
                            swal({
                                title: "Really run a full import?",
                                icon: "warning",
                                buttons: true,
                                dangerMode: true,
                            }).then( function(willImport) {
                                if (willImport) {
                                    $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Contacting Server...</h1></div>' });
                                    var url = "/orders/"+action;
                                    window.location.href = url;
                                }
                            });
                        });

                        $('form#bb_single_import, form#nuchev_single_import, form#noa_single_import').submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Importing Order...</h1></div>' });
                            }
                            else
                            {
                                return false;
                            }
                        });

                        $('button#figure8_import').click(function(e){
                            swal({
                                title: "Really run a this import?",
                                icon: "warning",
                                buttons: true,
                                dangerMode: true,
                            }).then( function(willImport) {
                                if (willImport) {
                                    $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Getting Emails...</h1></div>' });
                                    var url = "/orders/importFEightOrders";
                                    window.location.href = url;
                                }
                            });
                        });

                        $('button#nuchev_samples').click(function(e){
                            swal({
                                title: "Really run a this import?",
                                icon: "warning",
                                buttons: true,
                                dangerMode: true,
                            }).then( function(willImport) {
                                if (willImport) {
                                    $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Getting Emails...</h1></div>' });
                                    var url = "/orders/importNuchevSamples";
                                    window.location.href = url;
                                }
                            });
                        });
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