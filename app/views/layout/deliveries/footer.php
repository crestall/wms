
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                'common':{
                    init: function(){
                        $('button.delivery_deletion').click(function(e){
                            var deliveryid = $(this).data('deliveryid');
                            swal({
                                title: "Really cancel this delivery?",
                                text: "This cannot be undone",
                                icon: "warning",
                                buttons: true,
                                dangerMode: true,
                            }).then( function(willCancel) {
                                if (willCancel) {
                                    $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Cancelling Delivery...</h2></div>' });
                                    var data = {deliveryid: deliveryid}
                                    $.post('/ajaxfunctions/cancel-delivery', data, function(d){
                                        location.reload();
                                    });
                                }
                            });
                        });

                        $('button.pickup_deletion').click(function(e){
                            var pickupid = $(this).data('pickupid');
                            swal({
                                title: "Really cancel this pickup?",
                                text: "This cannot be undone",
                                icon: "warning",
                                buttons: true,
                                dangerMode: true,
                            }).then( function(willCancel) {
                                if (willCancel) {
                                    $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Cancelling Pickup...</h2></div>' });
                                    var data = {pickupid: pickupid}
                                    $.post('/ajaxfunctions/cancel-pickup', data, function(d){
                                        location.reload();
                                    });
                                }
                            });
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
                    'pickup-docket': function(){
                        $('select.vehicle_type').each(function(i,e){
                            var pickup_id = $(this).data('pickupid');
                            //console.log('doing the select for '+pickup_id)
                            if($(this).val() == 0)
                                $('a#print_docket_'+pickup_id).addClass('disabled')
                            $(this).change(function(ev){
                                if($(this).val() == 0)
                                    $('a#print_docket_'+pickup_id).addClass('disabled').prop('href', '');
                                else
                                    $('a#print_docket_'+pickup_id).removeClass('disabled').prop('href','/pdf/printPickupDocket/pickup='+pickup_id+'/vehicle='+$(this).val());
                            });
                        });
                        $('a.print_docket').click(function(e){
                            window.open($(this).prop('href'),'_blank');
                            //window.location.reload();
                            setTimeout(() => window.location.reload(), 1000);
                        });
                    }
                },
                'item-searcher':function(adjust){
                    if(adjust === undefined) {
                        adjust = false;
                    }
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
                            html += "<div class='row'>";
                            html += "<div class='col-11'><h5 class='text-center'>"+ui.item.value+"</h5></div>";
                            html += "<div class='col-1'><div id='remove_"+ui.item.item_id+"' class='close_box'><i title='remove this item'  class='text-danger fa-duotone fa-rectangle-xmark'></i></div></div>";
                            html += "<p class='col-12 text-center'>Currently "+ta.toLocaleString('en')+" available in total<br>";
                            html += "<label for='select_all_"+ui.item.item_id+"'><em><small>Select All</small></em></label><input style='margin-left:5px' class='select_all' id='select_all_"+ui.item.item_id+"' data-itemid='"+ui.item.item_id+"' type='checkbox'></p>";
                            html += "</div>";
                            html += "<div class='row'>";
                            locations.forEach(function (location, ind)
                            {
                                loc_array = location.split("|");
                                html += "<div class='col-5'><label for='location_"+loc_array[1]+"'>Pallet With "+loc_array[0]+"</label></div>";
                                html += "<div class='col-1'><input id='location_"+loc_array[1]+"' class='item_selector select_"+ui.item.item_id+"' name='items["+ui.item.item_id+"][]' value='"+loc_array[1]+"_"+loc_array[0]+"' type='checkbox'></div>";
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
                            if(adjust)
                            {
                                $('input.item_selector').each(function(i,e){
                                    //var removed = $( this ).rules( "remove", "required minlength" );
                                    //console.log(removed);
                                	$(this).rules("add",{
                                    	required: true,
                                        minlength:1,
                                        messages:{
                                        	required: "Please choose at least one location"
                                        }
                                    });
                                });
                            }
                            else
                            {
                                $('input.item_selector').change(function(ev){
                                    if($('input.item_selector:checked').length)
                                        $("button#submitter").prop('disabled', false);
                                    else
                                        $("button#submitter").prop('disabled', true);
                                });
                            }
                            $('.close_box').each(function(index, element){
                                //console.log( $(this).attr('id') );
                                var close_box = $(this);
                                console.log(close_box.attr('id'));
                                close_box.click(function(ev){
                                    console.log('clicked close box');
                                });
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
                'adjust-delivery':{
                    init: function(){
                        actions['item-searcher'](true);
                        $('input.remove_location').change(function(ev){
                            //var line_id = $(this).data('lineid');
                            var selector = $(this).parents('div.checkbox_div').prevAll('div.select_div').find('select.location_selector');
                            //console.log(selector);
                            if(this.checked)
                            {
                                selector.rules("remove","notNone");
                                selector.prop('disabled', true);
                            }
                            else
                            {
                                selector.rules("add",{
                                    notNone: true,
                                    messages:{
                                        notNone: "A location is required"
                                    }
                                });
                                selector.prop('disabled', false);
                            }
                            $('form#adjust-delivery-items').valid()
                            $('.selectpicker').selectpicker('refresh');
                        });
                        $('select.location_selector').each(function(i,e){
                            $(this).change(function(ev){
                                $(this).valid();
                            })
                        });
                        $('form#adjust-delivery-items').submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h2>Adjusting Delivery Items...</h2></div>' });
                            }
                        });
                    }
                },
                'book-delivery':{
                    init: function(){
                        actions['item-searcher']();
                        $("select#urgency").change(function(){
                            $(this).valid();
                        });
                    }
                },
                'add-pickup':{
                    init: function(){
                        $('#client_selector').change(function(e){
                            if($('#client_selector').val() != 0)
                            {
                                 $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Generating Form..</h2></div>' });
                                var href = '/deliveries/add-pickup/client='+$('#client_selector').val();
                                window.location.href = href;
                            }
                        });
                        actions['book-pickup'].init();
                    }
                },
                'add-delivery':{
                    init: function(){
                        $('#client_selector').change(function(e){
                            if($('#client_selector').val() != 0)
                            {
                                 $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Generating Form..</h2></div>' });
                                var href = '/deliveries/add-delivery/client='+$('#client_selector').val();
                                window.location.href = href;
                            }
                        });
                        actions['book-delivery'].init();
                    }
                },
                'book-pickup':{
                    init: function(){
                        $("input#private_courier").change(function(e){
                            if(this.checked)
                                $('div#urgency_holder').slideUp();
                            else
                                $('div#urgency_holder').slideDown(); 
                        });

                        $("select#urgency").change(function(){
                            //console.log("urgency changed");
                            $(this).valid();
                        });
                        $("input#item_searcher").autocomplete({
                            source: function(req, response){
                                var client_id = $('#client_id').val();
                                var url = "/ajaxfunctions/getPickupItems/?item="+req.term+"&clientid="+client_id;
                                $.getJSON(url, function(data){
                                    response(data);
                                });
                            },
                            select: function(event,ui){
                                $("div#feedback_holder").hide();
                                if(!ui.item.item_id || ui.item.item_id < 0)
                                {
                                    var add_item_form_count = $("div#form_holder form.add_item_form").length;
                                    var client_id = $("#client_id").val();
                                    var data = {
                                        i: add_item_form_count,
                                        client_id: client_id
                                    }
                                    $.post('/ajaxfunctions/addNewDeliveryItem', data, function(d){
                                        if(d.error)
                                        {
                                            alert(d.feedback);
                                        }
                                        else
                                        {
                                            $('div#form_holder').append(d.html);
                                            $('form#form_'+add_item_form_count).validate({
                                                rules:{
                                                    client_product_id:{
                                                        remote:{
                                                            url: '/ajaxfunctions/checkClientProductIds',
                                                            data: { 'client_id': function(){ return $("#client_id").val(); } }
                                                        }
                                                    }
                                                },
                                                messages:{
                                                    client_product_id:{
                                                        remote: 'You already have a product with this Product ID<br>Please check the item is not already recorded in our system'
                                                    }
                                                }
                                            });
                                            $('form#form_'+add_item_form_count).submit(function(ev){
                                                ev.preventDefault();
                                                if($(this).valid())
                                                {
                                                    var data = $(this).serialize();
                                                    $.ajax({
                                                        url: "/ajaxfunctions/add-pickup-item",
                                                        data: data,
                                                        method: "post",
                                                        dataType: "json",
                                                        beforeSend: function(){
                                                            $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Recording Item Details...</h2></div>' });
                                                        },
                                                        success: function(d){
                                                            $.unblockUI();
                                                            if(d.error)
                                                            {
                                                                $("div#feedback_holder")
                                                                    .hide()
                                                                    .removeClass()
                                                                    .addClass("errorbox")
                                                                    .slideDown()
                                                                    .html("<h2><i class='far fa-times-circle'></i>There has been an error</h2>");
                                                            }
                                                            else
                                                            {
                                                                $("div#feedback_holder")
                                                                .hide()
                                                                .removeClass()
                                                                .addClass("feedbackbox")
                                                                .html("<h2><i class='far fa-check-circle'></i>Item Added</h2><p>You will need to complete the details below</p>")
                                                                .slideDown({
                                                                    complete: function(){
                                                                        $('div#form_'+add_item_form_count+'_holder').remove();
                                                                        actions['book-pickup'].pickupItems(d);
                                                                        actions['book-pickup'].reindexAddPickupItemForms();
                                                                    }
                                                                });
                                                            }
                                                        }
                                                    });
                                                }
                                            });
                                        }
                                    });
                                }
                                else
                                {
                                    actions['book-pickup'].pickupItems(ui.item);
                                }
                                $(event.target).val("");
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
                    pickupItems: function(item)
                    {
                        var add_item_count = $("div#items_holder div.pickup_item").length;
                        item.i = add_item_count;
                        var data = {
                            i: add_item_count,
                            item_id: item.item_id,
                            label: item.value
                        }
                        $.post('/ajaxfunctions/addItemToDelivery', data, function(d){
                            $('div#items_holder').append(d.html);
                            $("button.remove-pickup-item").each(function(index, element){
                                $(this).off('click').click(function(ev){
                                    ev.preventDefault();
                                    var row_to_go = $(this).data("rowid");
                                    //console.log("will remove row with id "+row_to_go);
                                    $('div#pickup_item_'+row_to_go).remove();
                                    actions['book-pickup'].reindexPickupItems();
                                    actions['book-pickup'].enableForm();
                                })
                            });
                            $("input.pickupcount").off('keyup').keyup(function(e){
                                actions['book-pickup'].enableForm();
                            });
                        });
                    },
                    reindexPickupItems: function()
                    {
                        $("div#items_holder div.pickup_item").each(function(ind,el){
                            $(this).attr("id", "pickup_item_"+ind);
                            $(this).find("button.remove-pickup-item").data("rowid", ind);
                        });
                    },
                    reindexAddPickupItemForms: function()
                    {
                        $("div#form_holder div.little_form_holder").each(function(ind,el){
                            $(this).attr("id", "form_"+ind+"_holder");
                            var $form = $(this).find('form.add_item_form');
                            $form.attr("id", "form_"+ind);
                            $form.find("input[name='form_id']").val("form_"+ind);
                        });
                        actions['book-pickup'].enableForm();
                    },
                    enableForm: function()
                    {
                        var disabled = true;
                        var allfilled = true;
                        if($('div#items_holder div.pickup_item').length)
                        {
                            $('div#items_holder div.pickup_item').each(function(i,e){
                                if( !$(this).find("input[type=text]").val() )
                                    allfilled = false;
                            });
                            disabled = !allfilled;
                        }
                        $("button#submitter").attr('disabled', disabled);
                    }
                },
                'view-deliveries':{
                    init: function(){
                        dataTable.init($('table#view_deliveries_table'), {
                            "columnDefs": [
                                { "orderable": false, "targets": [4,5] },
                                { "searchable": false, "targets": [3,5] },
                                { "width": "16%", "targets":[0,2,3] }
                            ],
                            "paging": false,
                            "order": [],
                            "mark": true
                        } );
                    }
                },
                'view-pickups':{
                    init: function(){
                        dataTable.init($('table#view_pickups_table'), {
                            "columnDefs": [
                                { "orderable": false, "targets": [4,5] },
                                { "searchable": false, "targets": [3,5] },
                                { "width": "16%", "targets":[0,2,3] }
                            ],
                            "paging": false,
                            "order": [],
                            "mark": true
                        } );
                    }
                },
                'manage-pickups': {
                    init: function(){
                        actions.common['select-all']();
                        actions.common['init']();
                        $('#client_selector').change(function(e){
                            $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Collecting data...</h2></div>' });
                            var href = '/deliveries/manage-pickups';
                            if($('#client_selector').val() != 0)
                                href += "/client="+$('#client_selector').val();
                            window.location.href = href;
                        });
                        var dtOptions = {
                            "columnDefs": [
                                { "orderable": false, "targets": [4,6,7] },
                                { "searchable": false, "targets": [5,6,7] },
                                { "width": "13%", "targets":[1,2] }
                            ],
                            "paging": false,
                            "order": [],
                            "dom" : '<<"row"<"col-lg-4"><"col-lg-6">><"row">t>',
                            "mark": true
                        }
                        var table = dataTable.init($('table#manage_pickups_table'), dtOptions );
                        $('#table_searcher').on( 'keyup search', function () {
                            table.search( this.value ).draw();
                        } );
                        actions.common['pickup-docket']();
                    }
                },
                'manage-pickup':{
                    init: function(){
                        actions.common['pickup-docket']();
                        actions.common.init();
                        $('select.pallet_location, select.pallet_size').each(function(i,e){
                            $(this).change(function(ev){
                                $(this).valid();
                            })
                        });
                        $("div.pallet_holder:last-of-type").removeClass("border-bottom border-secondary border-bottom-dashed");
                        $('form#pickup_putaways').submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h2>Putting Stock In Locations...</h2></div>' });
                            }
                        });
                    }
                },
                'manage-deliveries':{
                    init: function(){
                        actions.common['select-all']();
                        actions.common.init();
                        $('#client_selector').change(function(e){
                            $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Collecting data...</h2></div>' });
                            var href = '/deliveries/manage-deliveries';
                            if($('#client_selector').val() != 0)
                                href += "/client="+$('#client_selector').val();
                            window.location.href = href;
                        });
                        var dtOptions = {
                            "columnDefs": [
                                { "orderable": false, "targets": [4,6,7] },
                                { "searchable": false, "targets": [5,6,7] },
                                { "width": "13%", "targets":[1,2] }
                            ],
                            "paging": false,
                            "order": [],
                            "dom" : '<<"row"<"col-lg-4"><"col-lg-6">><"row">t>',
                            "mark": true
                        }
                        var table = dataTable.init($('table#manage_deliveries_table'), dtOptions );
                        $('#table_searcher').on( 'keyup search', function () {
                            table.search( this.value ).draw();
                        } );
                        $('select.vehicle_type').each(function(i,e){
                            var delivery_id = $(this).data('deliveryid');
                            //console.log('doing the select for '+pickup_id)
                            if($(this).val() == 0)
                                $('a#print_docket_'+delivery_id).addClass('disabled');
                            $(this).change(function(ev){
                                if($(this).val() == 0)
                                    $('a#print_docket_'+delivery_id).addClass('disabled').prop('href', '');
                                else
                                    $('a#print_docket_'+delivery_id).removeClass('disabled').prop('href','/pdf/printDeliveryDocket/delivery='+delivery_id+'/vehicle='+$(this).val());
                            });
                        });
                        $('a.print_docket, a.print_slip').click(function(e){
                            window.open($(this).prop('href'),'_blank');
                            //window.location.reload();
                            setTimeout(() => window.location.reload(), 1000);
                        });
                        $('button.delivery_completed').click(function(e){
                            var delivery_id = $(this).data("deliveryid");
                            var client_id = $(this).data("clientid");
                            //var client_id = $('input#client_id').val();
                            swal({
                                title: "Really mark as complete?",
                                text: "This will close the delivery,adjust stock, and calculate charges\n\nIt cannot be undone",
                                icon: "warning",
                                buttons: true,
                                dangerMode: true,
                            }).then( function(willComplete) {
                                if (willComplete) {
                                    $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Completing Delivery...</h2></div>' });
                                    var form = document.createElement('form');
                                    form.setAttribute("method", "post");
                                    form.setAttribute("action", "/form/procCompleteDelivery");
                                    var hiddenField = document.createElement("input");
                                    hiddenField.setAttribute("type", "hidden");
                                    hiddenField.setAttribute("name", "delivery_id");
                                    hiddenField.setAttribute("value", delivery_id);
                                    var hiddenField2 = document.createElement("input");
                                    hiddenField2.setAttribute("type", "hidden");
                                    hiddenField2.setAttribute("name", "csrf_token");
                                    hiddenField2.setAttribute("value", config.csrfToken);
                                    var hiddenField3 = document.createElement("input");
                                    hiddenField3.setAttribute("type", "hidden");
                                    hiddenField3.setAttribute("name", "client_id");
                                    hiddenField3.setAttribute("value", client_id);
                                    form.appendChild(hiddenField);
                                    form.appendChild(hiddenField2);
                                    form.appendChild(hiddenField3);
                                    document.body.appendChild(form);
                                    form.submit();
                                }
                            });
                        });
                        $('button.adjust_allocation').click(function(e){
                            console.log('click');
                            e.preventDefault();
                            var delivery_id = $(this).data('deliveryid')
                            //make the form window
                            $('<div id="allocation_pop" title="Adjust Allocation">').appendTo($('body'));
                            $("#allocation_pop")
                                .html("<p class='text-center'><img class='loading' src='/images/preloader.gif' alt='loading...' /><br />Getting Details...</p>")
                                .load('/ajaxfunctions/adjustDeliveryAllocationForm',{delivery_id: delivery_id},
                                    function(responseText, textStatus, XMLHttpRequest){
                                    if(textStatus == 'error') {
                                        $(this).html('<div class=\'errorbox\'><h2>There has been an error</h2></div>');
                                    }
                                    else
                                    {
                                        $('.selectpicker').selectpicker();
                                        $('form#adjust-delivery-allocation').submit(function(e){
                                            e.preventDefault();
                                            var data = $(this).serialize();
                                            $.ajax({
                                                url: "/ajaxfunctions/update-delivery-allocation",
                                                data: data,
                                                method: "post",
                                                dataType: "json",
                                                beforeSend: function(){
                                                    $("#div#feedback_holder")
                                                        .slideDown()
                                                        .html("<p class='text-center'><img class='loading' src='/images/preloader.gif' alt='loading...' /><br />Adjusting allocation...</p>");
                                                },
                                                success: function(d){
                                                    if(d.error)
                                                    {
                                                        $("div#feedback_holder")
                                                            .hide()
                                                            .removeClass()
                                                            .addClass("errorbox")
                                                            .slideDown()
                                                            .html("<h2><i class='far fa-times-circle'></i>There has been an error</h2>");
                                                    }
                                                    else
                                                    {
                                                        var delivery_id = $("#delivery_id").val();
                                                        $("div#feedback_holder")
                                                            .hide()
                                                            .removeClass()
                                                            .addClass("feedbackbox")
                                                            .html("<h2><i class='far fa-check-circle'></i>Allocations Updated</h2><p><a class='btn btn-block btn-outline-secondary slip-reprint' role='button' target='_blank' href='/pdf/printDeliveryPickslip/delivery="+delivery_id+"'>Reprint Print Pickslip</a>")
                                                            .slideDown({
                                                                complete:function(){
                                                                    $('a.slip-reprint').click(function(e){
                                                                        e.preventDefault();
                                                                        window.open($(this).prop('href'),'_blank');
                                                                        //window.location.reload();
                                                                        setTimeout(() => window.location.reload(), 1000);
                                                                    })
                                                                }
                                                            });
                                                    }
                                                }
                                            }) ;
                                        });
                                    }

                            });
                            $("#allocation_pop").dialog({
                                    draggable: false,
                                    modal: true,
                                    show: true,
                                    hide: true,
                                    autoOpen: false,
                                    height: 520,
                                    width: 620,
                                    close: function(){
                                        $("#allocation_pop").remove();
                                    },
                                    open: function(){
                                        $('.ui-widget-overlay').bind('click',function(){
                                            $('#allocation_pop').dialog('close');
                                        });

                                    }
                            });
                            $("#allocation_pop").dialog('open');
                        });
                    }
                },
                'manage-delivery':{
                    init: function(){

                    }
                },
                'delivery-search':{
                    init: function(){
                        datePicker.betweenDates();
                        $('button#form_submitter').prop("disabled", !actions['enable-search-form']());
                        $('select#client_id, select#status_id,#date_from_value,#date_to_value,#term,select#urgency_id').on("change keyup", function(ev){
                            $('button#form_submitter').prop("disabled", !actions['enable-search-form']());
                        });
                        $('form#pickup_search').submit(function(e){
                            $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h2>Searching For Deliveries...</h2></div>' });
                        });
                        var dtOptions = {
                            "columnDefs": [
                                { "orderable": false, "targets": [5,8] },
                                { "searchable": false, "targets": [3,4,6,7,8] },
                                { "width": "13%", "targets":[1,2] }
                            ],
                            "paging": false,
                            "order": [],
                            "dom" : '<<"row"<"col-lg-4"><"col-lg-6">><"row">t>',
                            "mark": true
                        }
                        var table = dataTable.init($('table#view_deliveries_table'), dtOptions );
                        $('#table_searcher').on( 'keyup search', function () {
                            table.search( this.value ).draw();
                        } );
                    }
                },
                'pickup-detail':{
                    init: function(){
                        $('button#print').click(function(e){
                        	$("div#print_this").printArea({
                                    //put some options in
                            });;
                        });
                    }
                },
                'delivery-detail':{
                    init: function(){
                        $('button#print').click(function(e){
                        	$("div#print_this").printArea({
                                    //put some options in
                            });;
                        });
                        $("div.item_holder:last-of-type").removeClass("border-bottom border-secondary border-bottom-dashed"); 
                    }
                },
                'pickup-search':{
                    init: function(){
                        //console.log("")
                        datePicker.betweenDates();
                        $('button#form_submitter').prop("disabled", !actions['enable-search-form']());
                        $('select#client_id, select#status_id,#date_from_value,#date_to_value,#term,select#urgency_id').on("change keyup", function(ev){
                            $('button#form_submitter').prop("disabled", !actions['enable-search-form']());
                        });
                        $('form#pickup_search').submit(function(e){
                            $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h2>Searching For Pickups...</h2></div>' });
                        });
                        var dtOptions = {
                            "columnDefs": [
                                { "orderable": false, "targets": [5,8] },
                                { "searchable": false, "targets": [3,4,6,7,8] },
                                { "width": "13%", "targets":[1,2] }
                            ],
                            "paging": false,
                            "order": [],
                            "dom" : '<<"row"<"col-lg-4"><"col-lg-6">><"row">t>',
                            "mark": true
                        }
                        var table = dataTable.init($('table#view_pickups_table'), dtOptions );
                        $('#table_searcher').on( 'keyup search', function () {
                            table.search( this.value ).draw();
                        } );
                    }
                },
                'enable-search-form': function(){
                    if($('select#client_id').val() > 0)
                        return true;
                    if($('select#status_id').val() > 0)
                        return true;
                    if($('#date_from_value').val() > 0)
                        return true;
                    if($('#date_to_value').val() > 0)
                        return true;
                    if($('#term').val())
                        return true;
                    if($('select#urgency_id').val() > 0)
                        return true;

                    return false;
                }
            }
            //console.log('current page: '+config.curPage);
            //run the script for the current page
            actions[config.curPage].init();
        </script>
        <?php Database::closeConnection(); ?>
    </body>
</html>