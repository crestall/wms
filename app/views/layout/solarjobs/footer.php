
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
                        $("#type_selector").change(function(e){
                            if($(this).val() > 0)
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Finding Correct Form...</h2></div>' });
                                var urls = {
                                    1 : "/solar-jobs/add-origin-install",
                                    2 : "/solar-jobs/add-tlj-install",
                                    3 : "/solar-jobs/add-solargain-install"
                                }
                                window.location.href = urls[$(this).val()];
                            }
                        });
                    },
                    'add-item': function(){
                        $("a.add").off("click").click(function(e){
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
                    'select-all': function(){
                        $('#select_all').click(function(e){
                            var checked = this.checked;
                             $('.select').each(function(e){
                                this.checked =  checked;
                             })
                        });
                    },
                    'cancel-orders': function(solar, service){
                        if(solar === undefined) {
                            solar = false;
                        }
                        if(service === undefined) {
                            service = false;
                        }
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
                                        if(solar)
                                        {
                                            $.post('/ajaxfunctions/cancel-solarorders', data, function(d){
                                                location.reload();
                                            });
                                        }
                                        else if(service)
                                        {
                                            $.post('/ajaxfunctions/cancel-serviceorders', data, function(d){
                                                location.reload();
                                            });
                                        }
                                        else
                                        {
                                            $.post('/ajaxfunctions/cancel-orders', data, function(d){
                                                location.reload();
                                            });
                                        }

                                    }
                                }
                            });
                        });
                    }
                },
                'panel-calcs':{
                    init: function() {
                        $("input[name='roof_type']").click(function(e){
                            actions['panel-calcs'].openCalcButton();
                        })
                        $("a.addbank").click(function(e){
                            e.preventDefault;
                            var bank_count = $(":input.banks").length;
                            var html = "<div class='row bank_holder'>"
                            html += "<div class='col-sm-4'>";
                            html += "<p><input type='text' class='form-control required number banks' name=banks["+bank_count+"][qty]' placeholder='Panel Count' /></p>";
                            html += "</div>"; //col-sm-4
                            html += "<div class='col-sm-1 delete-image-holder'>";
                            html += "<a class='deletebank' title='remove this bank'><i class='fas fa-times-circle fa-2x text-danger'></i></a>";
                            html += "</div>"; //col-sm-1 item_id' />"
                            html += "</div>"; //row
                            $('div#banks_holder').append(html);
                            $('div#banks_holder:last-child').find('input').focus();
                            //itemsUpdater.itemDelete();
                            actions['panel-calcs'].deleteBank();
                            actions['panel-calcs'].checkBanks();
                            actions['panel-calcs'].openCalcButton();
                        });
                        actions['panel-calcs'].checkBanks();
                        actions['panel-calcs'].openCalcButton();
                    },
                    checkBanks: function(){
                        $('input.banks')
                            .off('change')
                            .change(function(e){
                                actions['panel-calcs'].openCalcButton();
                        });
                    },
                    openCalcButton: function(){
                        var lock = false;
                        //var validator = $( "#add_origin_order" ).validate();
                        //validator.element( "#myselect" );
                        $('input.banks').each(function(i,e){
                            if( isNaN($(this).val()) || $(this).val() == '' )
                            {
                                lock = true;
                            }
                            if(!$("input[name='roof_type']:checked").val())
                            {
                                lock = true;
                            }
                        });
                        $("button#calc_items").prop("disabled", lock);
                    },
                    deleteBank: function(){
                        $('a.deletebank')
                            .css('cursor', 'pointer')
                            .off('click')
                            .click(function(e){
                                $(this).closest('div.bank_holder').remove();
                                actions['panel-calcs'].openCalcButton();
                        });
                    },
                    calcItems: function(){
                        $("button#calc_items").click(function(e){
                            console.log('click');
                            e.preventDefault();
                            $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h1>Calculating Required Parts...</h1></div>' });
                            //console.log( 'roof type: '+ $("input[name='roof_type']:checked").val() );
                            //console.log('click');
                            var url = "/ajaxfunctions/calc-origin-pick";
                            var data = $("#add_solar_install").serialize();
                            $("div#install_items_holder").load(
                                url,
                                data,
                                function(h){
                                    $.unblockUI();
                                    actions['item-searcher'].init();
                                    actions.common['add-item']();
                                    itemsUpdater.itemDelete();
                                    $(this).show();
                                    //$("button#add_origin_order_submitter").prop("disabled", false);
                            });
                        });
                    }
                },
                'add-solar-install': {
                    init: function(){
                        //actions.common.init();
                        $('select#type_id').change(function(e){
                            if($(this).val() > 0)
                            {
                                $('div#rest_of_form').slideDown();
                            }
                        });
                        actions['panel-calcs'].init();
                        actions['panel-calcs'].calcItems();
                        actions.common['add-item']();
                        datePicker.fromDate();
                        if(!$('button#calc_items').prop('disabled'))
                        {
                            $('button#calc_items').click();
                        }
                        autoCompleter.addressAutoComplete($('#address'));
                        autoCompleter.suburbAutoComplete($('#suburb'));
                        $("form#add_solar_install").submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Adding Install...</h2></div>' });
                            }
                        });
                        $('select#team_id, select#type_id, #address, #suburb, #postcode, #country').change(function(e){
                            $(this).valid();
                        });
                        $("input.solar-item-searcher").each(function(i,e){
                            if($(this).data('ui-autocomplete') != undefined)
                            {
                                $(this).autocomplete( "destroy" );
                            }
                            autoCompleter.solarItemAutoComplete($(this), selectCallback, changeCallback);
                        })
                        $("input.item-searcher").each(function(i,e){
                            if($(this).data('ui-autocomplete') != undefined)
                            {
                                $(this).autocomplete( "destroy" );
                            }
                            autoCompleter.solarItemAutoComplete($(this), selectCallback, changeCallback);
                        })
                        function selectCallback(event, ui)
                        {
                            var $this = event.target;

                            if($this.id == "panel")
                            {
                                $("#panel_id").val(ui.item.item_id);
                                $("#panel_qty").removeAttr("disabled").focus().val('').addClass('required');
                                $("span#panel_count").html("<p class='inst'>There are currently <strong>"+ui.item.total_available+"</strong> of these available</p>");
                            }
                            else if($this.id == "inverter")
                            {
                                $("#inverter_id").val(ui.item.item_id);
                                $("#inverter_qty").removeAttr("disabled").focus().val(1).addClass('required');
                                $("span#inverter_count").html("<p class='inst'>There are currently <strong>"+ui.item.total_available+"</strong> of these available</p>");
                            }
                            else
                            {
                                var item_count = ($(":input.item-searcher").length) - 1;
                                var $holder = $($this).closest('div.item_holder');
                                var qty_html;
                                var inst;
                                qty_html = "<input type='text' class='form-control number item_qty' name='items["+item_count+"][qty]' placeholder='Qty' />";
                                inst = "<p class='inst'>There are currently <strong>"+ui.item.total_available+"</strong> of these available";
                                inst += "<br/>Maximum allowed line item values are <strong>"+ui.item.max_values+"</strong></p>";
                                $holder.find('div.qty-holder').html(qty_html).find('input').focus();
                                $holder.find('input.item_id').val(ui.item.item_id);
                                $holder.find('div.qty-location').html(inst);
                                itemsUpdater.itemDelete();
                                itemsUpdater.updateValidation();
                                $holder.find('input.item_qty').focus();
                            }
                            //console.log('vals '+ui.item.max_values);
                            //console.log('total available '+ui.item.total_available);
                            return false;
                        }
                        function changeCallback(event, ui)
                        {
                            if (!ui.item)
                	        {
                                $(event.target).val("");
                                return false;
                            }
                            //actions['add-origin-job'].deleteBank();
                        }
                    }
                },
                'add-service-job': {
                    init: function(){
                        actions.common.init();
                        actions['item-searcher'].init();
                        actions.common['add-item']();
                        itemsUpdater.itemDelete();
                        datePicker.fromDate();
                        autoCompleter.addressAutoComplete($('#address'));
                        autoCompleter.suburbAutoComplete($('#suburb'));
                        $("form#add-service-job").submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Adding Job...</h2></div>' });
                            }
                        });
                        $('select#team_id, select#job_type, #address, #suburb, #postcode, #country').change(function(e){
                            $(this).valid();
                        });
                    }
                },
                'update-solar-job': {
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
                'edit-servicejob': {
                    init: function(){

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
                                    console.log(pallet_val);
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
                            console.log(inst);
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
                'install-items-update' : {
                    init: function(){
                        actions.common['add-item']();
                        console.log('instal items update');
                        itemsUpdater.itemDelete();
                        //actions['item-searcher'].init();
                        $("form#items-update").submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Processing form...</h2></div>' });
                            }
                        });
                        $.validator.addClassRules("item-group", {
                            wholePallets : true
                        });
                        $("input.item-searcher").each(function(i,e){
                            if($(this).data('ui-autocomplete') != undefined)
                            {
                                $(this).autocomplete( "destroy" );
                            }
                            autoCompleter.solarItemAutoComplete($(this), selectCallback, changeCallback);
                        })
                        function selectCallback(event, ui)
                        {
                            var $this = event.target;

                            var item_count = ($(":input.item-searcher").length) - 1;
                            var $holder = $($this).closest('div.item_holder');
                            var qty_html;
                            var inst;
                            qty_html = "<input type='text' class='form-control number item_qty' name='items["+item_count+"][qty]' placeholder='Qty' />";
                            inst = "<p class='inst'>There are currently <strong>"+ui.item.total_available+"</strong> of these available";
                            inst += "<br/>Maximum allowed line item values are <strong>"+ui.item.max_values+"</strong></p>";
                            $holder.find('div.qty-holder').html(qty_html).find('input').focus();
                            $holder.find('input.item_id').val(ui.item.item_id);
                            $holder.find('div.qty-location').html(inst);
                            itemsUpdater.itemDelete();
                            itemsUpdater.updateValidation();
                            $holder.find('input.item_qty').focus();
                            //console.log('vals '+ui.item.max_values);
                            //console.log('total available '+ui.item.total_available);
                            return false;
                        }
                        function changeCallback(event, ui)
                        {
                            if (!ui.item)
                	        {
                                $(event.target).val("");
                                return false;
                            }
                            //actions['add-origin-job'].deleteBank();
                        }
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
                'update-service-details' : {
                    init: function(){
                        actions.common.init();
                        datePicker.fromDate();
                        autoCompleter.addressAutoComplete($('#address'));
                        autoCompleter.suburbAutoComplete($('#suburb'));
                        $("form#edit-service-job").submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Updating Job Details...</h2></div>' });
                            }
                        });
                        $('select#team_id, #address, #suburb, #postcode, #country').change(function(e){
                            $(this).valid();
                        });
                    }
                },
                'add-solargain-service-job' : {
                    init: function(){
                        actions.common.init();
                        actions['item-searcher'].init();
                        actions.common['add-item']();
                        itemsUpdater.itemDelete();
                        datePicker.fromDate();
                        autoCompleter.addressAutoComplete($('#address'));
                        autoCompleter.suburbAutoComplete($('#suburb'));
                        $("form#solargain-service-job").submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Adding Job...</h2></div>' });
                            }
                            else
                            {
                                return false;
                            }
                        });
                        $('select#team_id, #address, #suburb, #postcode, #country').change(function(e){
                            $(this).valid();
                        });
                    }
                },
                'add-tlj-service-job' : {
                    init: function(){
                        actions.common.init();

                    }
                },
                'view-installs': {
                    init: function(){
                        actions.common.init();
                        actions.common['select-all']();
                        actions.common['cancel-orders'](true);

                        $('#type_selector').change(function(e){
                            if($(this).val() > 0)
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Collecting Job Data...</h2></div>' });
                                window.location.href = "/solar-jobs/view-installs/type=" + $(this).val();
                            }
                        });

                        $('table#solar_orders_table').filterTable({
                            inputSelector: '#table_searcher'
                        });

                        $('a.order-label-print').click(function(e){
                            e.preventDefault();
                            var ids = [];
                            $('input.select').each(function(i,e){
                                var order_id = $(this).data('orderid');
                                if( $(this).prop('checked') )
                                {
                                    ids.push(order_id);
                                }
                            });
                            if(ids.length)
                            {
                                var form = document.createElement('form');
                                form.setAttribute("method", "post");
                                form.setAttribute("action", "/pdf/printSolarLabels");
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
                                form.setAttribute("action", "/pdf/printSolarPickslips");
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

                        $('a.order-fulfill').click(function(e){
                            e.preventDefault();
                            swal({
                                title: "Fulfill These Orders?",
                                text: "This will close each order and adjust stock\n\nIt cannot be undone",
                                icon: "warning",
                                buttons: true,
                                dangerMode: true
                            }).then( function(willFulfill) {
                                if (willFulfill) {
                                    var ids = [];
                                    $('input.select').each(function(i,e){
                                        var order_id = $(this).data('orderid');
                                        console.log('order_id: '+ order_id);
                                        if( $(this).prop('checked') )
                                        {
                                            ids.push(order_id);
                                        }
                                    });
                                    $.ajax({
                                        url: '/ajaxfunctions/fulfill-solarorder',
                                        method: 'post',
                                        data: {
                                            order_ids: ids
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
                                }
                            });
                        });
                    }
                },
                'view-service-jobs': {
                    init: function(){
                        actions.common.init();
                        actions.common['select-all']();
                        actions.common['cancel-orders'](false, true);

                        $('#type_selector').change(function(e){
                            if($(this).val() > 0)
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Collecting Job Data...</h2></div>' });
                                window.location.href = "/solar-jobs/view-service-jobs/type=" + $(this).val();
                            }
                        });

                        $('table#service_jobs_table').filterTable({
                            inputSelector: '#table_searcher'
                        });

                        $('a.order-label-print').click(function(e){
                            e.preventDefault();
                            var ids = [];
                            $('input.select').each(function(i,e){
                                var order_id = $(this).data('orderid');
                                if( $(this).prop('checked') )
                                {
                                    ids.push(order_id);
                                }
                            });
                            if(ids.length)
                            {
                                var form = document.createElement('form');
                                form.setAttribute("method", "post");
                                form.setAttribute("action", "/pdf/printServiceLabels");
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
                                form.setAttribute("action", "/pdf/printServicePickslips");
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

                        $('a.order-fulfill').click(function(e){
                            e.preventDefault();
                            swal({
                                title: "Fulfill These Orders?",
                                text: "This will close each order and adjust stock\n\nIt cannot be undone",
                                icon: "warning",
                                buttons: true,
                                dangerMode: true
                            }).then( function(willFulfill) {
                                if (willFulfill) {
                                    var ids = [];
                                    $('input.select').each(function(i,e){
                                        var order_id = $(this).data('orderid');
                                        console.log('order_id: '+ order_id);
                                        if( $(this).prop('checked') )
                                        {
                                            ids.push(order_id);
                                        }
                                    });
                                    $.ajax({
                                        url: '/ajaxfunctions/fulfill-solarservice',
                                        method: 'post',
                                        data: {
                                            order_ids: ids
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
                                }
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
                'update-install-details':{
                    init: function(){
                        datePicker.fromDate();
                        autoCompleter.addressAutoComplete($('#address'));
                        autoCompleter.suburbAutoComplete($('#suburb'));
                        $('select#team_id, select#type_id, #address, #suburb, #postcode, #country').change(function(e){
                            $(this).valid();
                        });
                        $("form#edit-solar-install").submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Updating Install Details...</h2></div>' });
                            }
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