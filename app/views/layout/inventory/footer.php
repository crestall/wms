
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
                            shippingQuote.getQuotes($(this).data('orderid'));
                        });
                    },
                    'add-to-receiving': function()
                    {
                        $('#to_receiving').click(function(){
                            $('#pallet_count_holder').slideToggle();
                            if(this.checked)
                            {
                                $('select#add_to_location').prop('disabled', true).selectpicker('hide').addClass('disabled');
                            }
                            else
                            {
                                $('select#add_to_location').prop('disabled', false).selectpicker('show').removeClass('disabled');
                            }
                        });
                    }
                },
                "book-covers":{
                    init: function(){
                        $("form#add_bookcover").submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Adding Cover To System...</h2></div>' });
                            }
                        });
                        
                        var table = dataTable.init($('table#view_bookcovers_table') , {
                            "drawCallback": function( settings ) {
                                $('a.update').click(function(e){
                                    e.preventDefault();
                                    actions.locations.update.click(this);
                                });
                            }
                        });
                        $.fn.dataTable.ext.search.push(
                            function( settings, searchData, index, rowData, counter ) {
                                var search = $('div.dataTables_filter input').val();
                                //console.log(rowData)
                                var td = table.cell( index, columnIndex ).node();
                                var val = $('input', td);
                                console.log("val: "+val);
                                console.log(rowData["DT_RowId"]);
                                return false;
                            }
                        );
                    },
                    'update':{
                        'click': function(el){

                        }
                    }
                },
                'move-all-client-stock':{
                    init: function(){
                        $('#client_selector').change(function(e){
                            if($(this).val() > 0)
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Collecting Client Details...</h2></div>' });
                                window.location.href = "/inventory/move-all-client-stock/client=" + $(this).val();
                            }
                        });
                        $("form#move_all_client_stock").submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Processing Movement...</h2></div>' });
                            }
                            else
                            {
                                return false;
                            }
                        });
                    }
                },
                'replenish-pickface':{
                    init: function(){

                    }
                },
                'record-new-product': {
                    init: function(){
                        $('form#register_new_stock').submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Recording Data...</h2></div>' });
                            }
                        });
                        $('#client_product_id, #barcode').change(function(ev){
                            var val = $(this).val();
                            if(val != "")
                            {
                                if( this.id != 'barcode' || ( this.id == 'barcode' && $('#sku').val() == '' ) )
                                {
                                    $.ajax({
                                        url: "/ajaxfunctions/create-sku",
                                        method: 'post',
                                        data: {
                                            value: val
                                        },
                                        dataType: 'json',
                                        beforeSend: function(){
                                            $('#sku')
                                                .addClass('loading')
                                                .val('Calculating the new SKU');
                                        },
                                        success: function(d){
                                            $('#sku')
                                                .removeClass('loading')
                                                .val(d.sku);
                                        }
                                    });
                                }
                            }
                        });
                    }
                },
                'add-subtract-stock' : {
                    init: function(){
                        actions.common['add-to-receiving']();
                        $('button#add_stock_submitter').click(function(e){
                            e.preventDefault();
                            if($('form#add_to_stock').valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Adding Stock...</h2></div>' });
                                $('form#add_to_stock').submit();
                            }
                        });
                        $('button#subtract_stock_submitter').click(function(e){
                            e.preventDefault();
                            if($('form#subtract_from_stock').valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Removing Stock...</h2></div>' });
                                $('form#subtract_from_stock').submit();
                            }
                        });
                        $('select#subtract_from_location').change(function(ev){
                            var qty = "";
                            if($(this).val() > 0)
                                var qty = $(this).find(":selected").data('qty');
                            $('input#qty_subtract.delivery-client').val(qty);
                        });
                    }
                },
                'quality-control' : {
                    init: function(){
                        $('#qty_add').change(function(e){
                            if($(this).val() != '' && $(this).val() > 0)
                            {
                                $('#add_to_location').rules('add', 'notNone');
                            }
                            else
                            {
                                $('#add_to_location').rules('remove');
                            }
                        });
                        $('#qty_subtract').change(function(e){
                            if($(this).val() != '' && $(this).val() > 0)
                            {
                                $('#subtract_from_location').rules('add', 'notNone');
                            }
                            else
                            {
                                $('#subtract_from_location').rules('remove');
                            }
                        });
                        $('button#qc_submitter').click(function(e){
                            e.preventDefault();
                            if($('form#quality_control').valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Updating QC Status...</h2></div>' });
                                $('form#quality_control').submit();
                            }
                        });
                        $('select#add_to_location, select#subtract_from_location').change(function(e){
                            $(this).valid();
                        });
                    }
                },
                'move-stock': {
                    init: function(){
                        $('button#move_stock_submitter').click(function(e){
                            e.preventDefault();
                            if($('form#move_stock').valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Moving Stock...</h2></div>' });
                                $('form#move_stock').submit();
                            }
                        });
                        $('select#move_from_location').change(function(ev){
                            var qty = "";
                            if($(this).val() > 0)
                                var qty = $(this).find(":selected").data('qty');
                            $('input#qty_move').val(qty);
                            $(this).valid();
                            $('input#qty_move').valid();
                        })
                        $('select#move_to_location').change(function(e){
                            $(this).valid();
                        });
                    }
                },
                'view-inventory': {
                    init: function()
                    {
                        $('#client_selector').change(function(e){
                            if($(this).val() > 0)
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Collecting Products...</h2></div>' });
                                window.location.href = "/inventory/view-inventory/client=" + $(this).val();
                            }
                        });
                        dataTable.init($('table#view_items_table'), {
                            "columnDefs": [
                                { "searchable": false, "targets": [4,5,6,7,9] },
                                { "orderable": false, "targets": [9] }
                            ],
                            "processing": true,
                            "mark": true,
                            "language": {
                                processing: 'Fetching results and updating the display.....'
                            },
                            "serverSide": true,
                            "ajax": {
                                "url": "/ajaxfunctions/dataTablesViewInventory",
                                "data": function( d ){
                                    d.clientID = $("#client_id").val();
                                }
                            }
                        } );
                    }
                },
                'move-bulk-items': {
                    init: function()
                    {
                        $('#client_selector').change(function(e){
                            if($(this).val() > 0)
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Collecting Products...</h2></div>' });
                                window.location.href = "/inventory/move-bulk-items/client=" + $(this).val();
                            }
                        });
                        $('#move_to_location').change(function(e){
                            if($(this).val() > 0)
                            {
                                $("div#go_button_holder").slideDown();
                                $("button#move_stock_button").off('click').click(function(e){
                                    if($('input.select_move:checked').length)
                                    {
                                        var ids = [];
                                        $('input.select_move').each(function(i,e){
                                            if($(this).prop('checked'))
                                            {
                                                var lid = $(this).data('locationid');
                                                var ntm = $("input#number_from_"+lid).val();
                                                var ota = {};
                                                ota.fromid = lid;
                                                ota.toid = $('#move_to_location').val();
                                                ota.ntm = ntm;
                                                ota.iid = $(this).data('itemid');
                                                ids.push(ota);
                                            }
                                        });
                                        //console.log(ids);
                                        $.ajax({
                                            url: "/ajaxfunctions/bulk-move-stock",
                                            data: { ids: ids},
                                            method: "post",
                                            dataType: "json",
                                            beforeSend: function(){
                                                $("div#feedback_holder")
                                                    .slideDown()
                                                    .html("<p></p><p class='text-center'><img class='loading' src='/images/preloader.gif' alt='loading...' /><br />Moving Items...</p>");
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
                                                    $("div#feedback_holder")
                                                        .hide()
                                                        .removeClass()
                                                        .addClass("feedbackbox")
                                                        .html("<h2><i class='far fa-check-circle'></i>Items have been moved</h2><p>Please wait for page to reload</p>")
                                                        .slideDown({
                                                            complete: function(){
                                                                setTimeout(location.reload.bind(location), 2000);
                                                            }
                                                    });
                                                }
                                            }
                                        }) ;
                                        /*
                                        $.ajax({
                                            url: "/ajaxfunctions/update-allocation",
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
                                                    $("div#feedback_holder")
                                                        .hide()
                                                        .removeClass()
                                                        .addClass("feedbackbox")
                                                        .html("<h2><i class='far fa-check-circle'></i>Allocations Updated</h2><p><a class='btn btn-warning slip-reprint'><i class='fas fa-file-alt'></i> Reprint Pickingslip</a></p>")
                                                        .slideDown({
                                                            complete: function(){
                                                                $('a.slip-reprint').click(function(e){
                                                                    e.preventDefault();
                                                                    var ids = [order_id];
                                                                    var form = document.createElement('form');
                                                                    form.setAttribute("method", "post");
                                                                    form.setAttribute("action", "/pdf/printPickslips");
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
                                                                });
                                                            }
                                                    });
                                                }
                                            }
                                        }) ;*/
                                    }
                                    else
                                    {
                                        swal({
                                            title: "No Locations Chosen",
                                            text: "Please select something to move",
                                            icon: "error"
                                        });
                                    }
                                })
                            }
                        });
                        dataTable.init($('table#view_items_table'), {
                            "columnDefs": [
                                { "orderable": false, "targets": [1,2] }
                            ]
                        } );
                    }
                },
                "pack-items-manage" : {
                    init: function(){
                        actions.common['add-to-receiving']();
                        $('#product_selector').change(function(e){
                            if($(this).val() > 0)
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Collecting Products...</h2></div>' });
                                window.location.href = "/inventory/pack-items-manage/product=" + $(this).val();
                            }
                        });

                        $.validator.addClassRules("item_location", {
                            notNone: true
                        });

                        $('form#break_pack_items').submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Breaking Packs...</h2></div>' });
                            }
                        });
                    }
                },
                "receive-pod-stock": {
                    init: function(){
                        barcodeScanner.init({
                            onComplete: function(barcode, qty){
                        	    //console.log('barcode: '+barcode);
                                if(!barcodeScanner.checkEan(barcode))
                                {
                                    swal({
                                        title: "Scanning Error!",
                                        text: "The barcode will need to be rescanned",
                                        icon: "error",
                                    });
                                    $.playSound("/sounds/bff-strike.wav");
                                    return;
                                }
                                $("#item_barcode").val(barcode);
                                $('button#get_item').click();
                            }
                        });
                        
                        $('button#get_item').click(function(e){
                            e.preventDefault();
                            var barcode = $("#item_barcode").val();
                            var data = {
                                barcode: barcode,
                                pod_invoice: $('#pod_invoice_selector').val(),
                                order_id: $('#order_id').val()
                            };
                            var url = "/ajaxfunctions/get-pod-item-by-barcode";
                            if( barcode != "" && $('#pod_invoice_selector').val() != 0)
                            {
                                $("div#pod_details")
                                .html("<div class='row'><div class='col-md-12'><p class='text-center'><img class='loading' src='/images/preloader.gif' alt='loading...' /><br />Finding Item...</p></div></div>")
                                .load(url, data, function(d){
                                    $('button.receive_pod_item').off("click").click(function(e){
                                        var item_id = $(this).data("itemid");
                                        var order_id = $(this).data("orderid");
                                        var order_item_id = $(this).data("orderitemid");
                                        var num_received = $("input#received_"+order_item_id).val();
                                        var num_required = $("input#required_"+order_item_id).val();
                                        if(num_received && !isNaN(num_received) && (function(x) { return (x | 0) === x; })(parseFloat(num_received)))
                                        {
                                            if(num_received != num_required)
                                            {
                                                $("span#errortext_"+order_item_id).text("Can only be processed if received is equal to required");
                                            }
                                            else
                                            {
                                                $("span#errortext_"+order_item_id).text("");
                                                //OK Update the system
                                                var url = "/ajaxfunctions/receive-pod-items";
                                                var data = {
                                                    item_id         : item_id,
                                                    order_id        : order_id,
                                                    order_item_id   : order_item_id,
                                                    num_received    : num_received,
                                                    num_required    : num_required
                                                };
                                                $.ajax({
                                                    url: url,
                                                    type:"post",
                                                    data: data,
                                                    beforeSend: function(){
                                                        $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Updating the system...</h2></div>' });
                                                    },
                                                    success: function(d){
                                                        if(d.error)
                                                        {
                                                            $("div#feedback_holder")
                                                                .hide()
                                                                .removeClass()
                                                                .addClass("errorbox")
                                                                .slideDown()
                                                                .html("<h2><i class='far fa-times-circle'></i>There has been an error</h2>" + d.html);
                                                        }
                                                        else
                                                        {
                                                            $("input#item_barcode").val("");
                                                            $("div#pod_details").empty();
                                                            $("div#feedback_holder")
                                                                .hide()
                                                                .removeClass()
                                                                .addClass("feedbackbox")
                                                                .slideDown()
                                                                .html("<h2><i class='far fa-check-circle'></i>The system has been updated</h2>" + d.html);
                                                        }
                                                        $.unblockUI();
                                                    }
                                                });
                                            }
                                        }
                                        else
                                        {
                                            $("span#errortext_"+order_item_id).text("Only enter positive whole numbers");
                                        }
                                    });
                                });
                            }
                        });
                    }
                },
                "scan-to-inventory": {
                    init: function(){

                        $('#client_selector').change(function(e){
                            if($(this).val() > 0)
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Collecting Client Data...</h2></div>' });
                                window.location.href = "/inventory/scan-to-inventory/client=" + $(this).val();
                            }
                        });

                        barcodeScanner.init({
                            onComplete: function(barcode, qty){
                        	    //console.log('barcode: '+barcode);
                                if(!barcodeScanner.checkEan(barcode))
                                {
                                    swal({
                                        title: "Scanning Error!",
                                        text: "The barcode will need to be rescanned",
                                        icon: "error",
                                    });
                                    $.playSound("/sounds/bff-strike.wav");
                                    return;
                                }
                                $("#item_barcode").val(barcode);
                                $('button#get_item').click();
                            }
                        });

                        $('button#get_item').click(function(e){
                            e.preventDefault();
                            var barcode = $("#item_barcode").val();
                            var data = {barcode: barcode, client_id: $('#client_selector').val()};
                            var url = "/ajaxfunctions/get-item-by-barcode";
                            if( barcode != "")
                            {
                                $("div#item_details")
                                .html("<div class='row'><div class='col-md-12'><p class='text-center'><img class='loading' src='/images/preloader.gif' alt='loading...' /><br />Finding Item...</p></div></div>")
                                .load(url, data, function(d){
                                    //$('#available').focus();
                                    actions.common['add-to-receiving']();
                                    $('.selectpicker').selectpicker('refresh');
                                    $('select#preferred_pick_location_id').change(function(e){
                                        var val = this.value
                                        $('select#add_to_location').selectpicker('val', this.value);
                                    });
                                    $('#add_to_stock').validate({
                                        rules:{
                                            qty:{
                                                min: 1,
                                                digits: true
                                            },
                                            add_to_location:{
                                                notNone: true
                                            },
                                        },
                                        messages:{
                                            add_to_location:{
                                                notNone: "A location is required"
                                            },
                                            qty:{
                                                min: "Please enter a number greater than zero"
                                            }
                                        }
                                    });
                                    $('#add_new_item').validate({
                                        rules:{
                                            add_to_location:{
                                                notNone: true
                                            },
                                            sku: {
                                                remote: {
                                                    url: '/ajaxfunctions/checkSkus'
                                                }
                                            },
                                            barcode: {
                                                remote: {
                                                    url: '/ajaxfunctions/checkBarcodes'
                                                }
                                            },
                                        },
                                        messages:{
                                            add_to_location:{
                                                notNone: "A location is required"
                                            },
                                            sku: {
                                				remote: 'This SKU is already in use. SKUs must be unique'
                                			},
                                            barcode: {
                                				remote: 'This barcode is already in use. Barcodes must be unique'
                                			}
                                        }
                                    });
                                });
                            }
                        });
                    }
                },
                'clients-locations' : {
                    init: function(){
                        dataTable.init($('table#client_locations_table'), {
                            "columnDefs": [
                                { "orderable": false, "targets": [2,3] }
                            ],
                            "drawCallback": function( settings ) {
                                $('button.deletebutton').click(function(e){
                                    e.preventDefault();
                                    var $but = $(e.target);
                                    swal({
                                        title: "Really delete this allocation?",
                                        text: "This cannot be undone",
                                        icon: "warning",
                                        buttons: true,
                                        dangerMode: true,
                                    }).then( function(willDelete) {
                                        if (willDelete) {
                                            console.log('target: '+e.target);
                                            $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Deleting allocation...</h1></div>' });
                                            $.post('/ajaxfunctions/deleteClientLocation', {id: $but.data('allocationid')}, function(d){
                                                window.location.reload();
                                            })
                                        }
                                    });
                                });
                            }
                        } );

                        $('select#client_id, select#location').change(function(e){
                            $(this).valid();
                        });

                        $('button.deletebutton').click(function(e){
                            e.preventDefault();
                            var $but = $(e.target);
                            swal({
                                title: "Really delete this allocation?",
                                text: "This cannot be undone",
                                icon: "warning",
                                buttons: true,
                                dangerMode: true,
                            }).then( function(willDelete) {
                                if (willDelete) {
                                    $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Deleting allocation...</h1></div>' });
                                    $.post('/ajaxfunctions/deleteClientLocation', {id: $but.data('allocationid')}, function(d){
                                        window.location.reload();
                                    })
                                }
                            });
                        });
                    }
                },
                'client-inventory' : {
                    init: function(){
                        dataTable.init($('table#client_inventory_table'), {
                            "columnDefs": [
                                { "searchable": false, "targets": [4,5,6,7,8,9,10] },
                                { "orderable": false, "targets": [4,9,10] }
                            ],
                            "processing": true,
                            "mark": true,
                            "language": {
                                processing: 'Fetching results and updating the display.....'
                            },
                            "serverSide": true,
                            "ajax": {
                                "url": "/ajaxfunctions/dataTablesClientsViewInventory",
                                "data": function( d ){
                                    d.clientID = $("#client_id").val();
                                }
                            },
                            "drawCallback": function( settings ) {
                                $('button.update_product').click(function(e) {
                                    actions.update.click(this);
                                })
                            }
                        } );
                        $('button#csv_download').click(function(e) {
                            var data = {
                                client_id: $("#client_id").val(),
                                csrf_token: config.csrfToken
                            }
                            var url = "/downloads/clientInventoryCSV";
                            fileDownload.download(url, data);
                        });
                    }
                },
                'expected-shipments' : {
                    init: function(){
                        dataTable.init($('table#expected_shipments_table'), {
                            "ordering": false
                        } );
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
                            if( (!isNaN(value) && (function(x) { return (x | 0) === x; })(parseFloat(value))) && value >= 0 )
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
                },
                'goods-in': {
                    init: function(){
                        $('#client_selector').change(function(e){
                            if($(this).val() > 0)
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Collecting Client Data...</h2></div>' });
                                window.location.href = "/inventory/goods-in/client=" + $(this).val();
                            }
                        });
                        barcodeScanner.init({
                            onComplete: function(barcode, qty){
                        	    var conid = false;
                                var process = true;
                                $('input.number').each(function(i,e){
                                    $(this).val($(this).val().replace(barcode,''))
                                });
                                if( barcode.startsWith("VV") )
                                {
                                    conid = barcode.substring(0, 8)
                                }
                                else
                                {
                                    //var ep = new RegExp("RJM\d{7}|HKM\d{7}|S2U\d{7}|F24\d{7}|F25\d{7}|XTS\d{7}", "i");
                                    //var ep = /RJM\d{7}|HKM\d{7}|S2U\d{7}|F24\d{7}|F25\d{7}|XTS\d{7}|LH\d{9}AU|CH\d{9}AU/i;
                                    var ep = /ZQD\d{7}|HKM\d{7}|S2U\d{7}|F24\d{7}|F25\d{7}|XTS\d{7}|LH\d{9}AU|CH\d{9}AU/i;
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
                                    process = false;
                                }
                                if(process)
                                {
                                    $('#consignment_id').val(conid);
                                }
                            }
                        });
                        $('button#find_order').click(function(e){
                            e.preventDefault();
                            var client_id = $('select#client_selector').val();
                            var con_id = $('#consignment_id').val();
                            if(con_id != "")
                            {
                                var url = "/ajaxfunctions/getOrderByConID";
                                var data = {
                                    con_id : con_id,
                                    client_id : client_id
                                };
                                $.ajax({
                                    url: url,
                                    method: "post",
                                    data: data,
                                    dataType: 'json',
                                    beforeSend: function(){
                                        $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Finding Order...</h1></div>' });
                                    },
                                    success: function(d){
                                        $.unblockUI();
                                        if(d.error)
                                        {
                                            $("#submit_button").prop('disabled', true).addClass('disabled');
                                        }
                                        $("div#order_details").html(d.html).show();
                                        $('button#clear_order').show();
                                        $.validator.addClassRules("return_items", {
                                            require_from_group: [1, ".return_items"]
                                        });
                                    }
                                });
                            }
                        });
                        $('button#clear_order').click(function(e){
                            e.preventDefault();
                            $("div#order_details").html("").hide();
                            $("#consignment_id").val("");
                            $("#submit_button").prop('disabled', false).removeClass('disabled');
                            $(this).hide();
                        });
                        $('form#goodsin').submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Processing form...</h2></div>' });
                            }
                        });
                    }
                },
                'goods-out' :{
                    init: function(){
                        $('#client_selector').change(function(e){
                            if($(this).val() > 0)
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Collecting Client Data...</h2></div>' });
                                window.location.href = "/inventory/goods-out/client=" + $(this).val();
                            }
                        });
                        $('form#goodsout').submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Processing form...</h2></div>' });
                            }
                        });
                    }

                },
                'transfer-location': {
                    init: function(){
                        $('form#transfer_location').submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Transferring Items...</h2></div>' });
                            }
                        });
                        $('select#move_to_location, select#move_from_location').change(function(e){
                            $(this).valid();
                        });
                    }
                },
                'view-collections': {
                    init: function(){
                        dataTable.init($('table#client_collection_table'), {
                            "columnDefs": [
                                { "orderable": false, "targets": [3] }
                            ],
                            "processing": true,
                            "mark": true,
                            "language": {
                                processing: 'Fetching results and updating the display.....'
                            },
                            "serverSide": true,
                            "ajax": {
                                "url": "/ajaxfunctions/dataTablesClientsViewCollections",
                                "data": function( d ){
                                    d.clientID = $("#client_id").val();
                                    d.active = 1;
                                }
                            }
                        } );
                    }
                }
            }
            console.log('current page: '+config.curPage);
            //run the script for the current page
            actions[config.curPage].init();
        </script>
        <?php Database::closeConnection(); ?>
    </body>
</html>