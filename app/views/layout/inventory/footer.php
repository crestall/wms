
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
                'replenish-pickface':{
                    init: function(){

                    }
                },
                'solar-returns':{
                    init: function(){
                        $('#order_type_id').change(function(e){
                            if($(this).val() > 0)
                            {
                                $("div#item_holder").slideDown();
                                $("#form_submitter").removeAttr('disabled');
                            }
                        });
                        $("form#solar_return").submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Recording Return...</h2></div>' });
                            }
                            else
                            {
                                return false;
                            }
                        });
                        $("input.item-searcher").each(function(i,e){
                            if($(this).data('ui-autocomplete') != undefined)
                            {
                                $(this).autocomplete( "destroy" );
                            }
                            autoCompleter.solarAllItemsAutoComplete($(this), selectCallback, changeCallback);
                        });
                        function selectCallback(event, ui)
                        {
                            $("#item_id").val(ui.item.item_id);
                        }
                        function changeCallback(event, ui)
                        {
                            if (!ui.item)
                	        {
                                $(event.target).val("");
                                return false;
                            }
                        }
                    }
                },
                'register-newstock': {
                    init: function(){
                        $('form#register_new_stock').submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Recording Data...</h2></div>' });
                            }
                        });
                    }
                },
                'add-subtract-stock' : {
                    init: function(){
                        actions.common['add-to-receiving']();
                        $('form#add_to_stock').submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Processing form...</h2></div>' });
                            }
                        });

                        $('form#subtract_from_stock').submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Processing form...</h2></div>' });
                            }
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

                        $('form#quality_control').submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Processing form...</h2></div>' });
                            }
                        });
                    }
                },
                'move-stock': {
                    init: function(){
                        $('form#move_stock').submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Moving Stock...</h2></div>' });
                            }
                        });
                        $('select#move_to_location, select#move_from_location').change(function(e){
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
                                { "orderable": false, "targets": [1,2,7,8] }
                            ]
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
                'view-solar-inventory': {
                    init: function()
                    {
                        dataTable.init($('table#solar-inventory-table'), {
                            "columnDefs": [
                                { "orderable": false, "targets": [2,3] }
                            ]
                         } );
                         $('button#csv_download').click(function(e) {
                            var data = {
                                csrf_token: config.csrfToken
                            }
                            var url = "/downloads/solarInventoryCSV";
                            fileDownload.download(url, data);
                        });
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
                                    $('#palletized').click(function(e){
                                        $("#per_pallet_holder").slideToggle('slow');
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
                                { "orderable": false, "targets": [6,7] }
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
                                    var ep = /RJM\d{7}|HKM\d{7}|S2U\d{7}|F24\d{7}|F25\d{7}|XTS\d{7}|LH\d{9}AU|CH\d{9}AU/i;
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
                        $('select#move_from_location').change(function(e){
                            var loc_id = $(this).val();
                            var url = "/ajax-functions/get-items-in-location";
                            var data = {loc_id:loc_id};
                            $('div#move_to_holder')
                                .slideDown()
                                .html("<div class='row'><div class='col-md-12'><p class='text-center'><img class='loading' src='/images/preloader.gif' alt='loading...' /><br />Finding Items...</p></div></div>")
                                .load(url, data, function(d){

                                });
                        });
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