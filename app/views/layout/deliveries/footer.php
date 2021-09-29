
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                'common':{
                    init: function(){

                    },
                    'select-all': function(){
                        $('#select_all').click(function(e){
                            var checked = this.checked;
                             $('.select').each(function(e){
                                this.checked =  checked;
                             })
                        });
                    }
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
                        $("select#urgency").change(function(){
                            $(this).valid();
                        })
                    }
                },
                'book-pickup':{
                    init: function(){
                        $("input#item_searcher").autocomplete({
                            source: function(req, response){
                                var client_id = $('#client_id').val();
                                var url = "/ajaxfunctions/getPickupItems/?item="+req.term+"&clientid="+client_id;
                                console.log(url);
                                $.getJSON(url, function(data){
                                    response(data);
                                });
                            },
                            select: function(event,ui){
                                //console.log(ui.item);
                                if(!ui.item.item_id || ui.item.item_id < 0)
                                {
                                    //$('div#form_holder').append('<h2>Gonna Load The Add Item Form</h2>');
                                    $.post('/ajaxfunctions/getOrderItemsForSerials', data, function(d){
                                        $.unblockUI();
                                        //$('div#order_details').html(d);
                                        if(d.error)
                                        {
                                            alert(d.feedback);
                                        }
                                        else
                                        {
                                            $('div#form_holder').append(d.html);
                                            /*
                                            $.validator.addClassRules("unique", {
                                                noDuplicates: true
                                            });
                                            $('form#add_serials').validate();
                                            $('form#add_serials').submit(function(e){
                                                if($(this).valid())
                                                {
                                                    $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Recording Serials...</h2></div>' });
                                                }
                                            });
                                            */
                                        }
                                    });
                                }
                                else
                                {
                                    $('div#items_holder').append('<h2>Gonna Load The Items Form</h2>');
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
                'manage-deliveries':{
                    init: function(){
                        actions.common['select-all']();
                        $('#client_selector').change(function(e){
                            $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h1>Collecting data...</h1></div>' });
                            var href = '/deliveries/manage-deliveries';
                            if($('#client_selector').val() != 0)
                                href += "/client="+$('#client_selector').val();
                            window.location.href = href;
                        });
                        var dtOptions = {
                            "columnDefs": [
                                { "orderable": false, "targets": [3,5,6] },
                                { "searchable": false, "targets": [2,4,5,6] },
                                { "width": "16%", "targets":[1,2] }
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
                        $('a.slip-print').click(function(e){
                            e.preventDefault();
                            console.log('click');
                            if($('input.select:checked').length)
                            {
                                var ids = [];
                                $('input.select').each(function(i,e){
                                    if($(this).prop('checked'))
                                    {
                                        ids.push($(this).data('deliveryid'));
                                    }
                                });
                                //console.log(ids);
                                //return;
                                var form = document.createElement('form');
                                form.setAttribute("method", "post");
                                form.setAttribute("action", "/pdf/printDeliveryPickslips");
                                //form.setAttribute("action", "/misc-functions/make-packslips-pdf.php");
                                form.setAttribute("target", "formresult");
                                $.each( ids, function( index, value ) {
                                    var hiddenField = document.createElement("input");
                                    hiddenField.setAttribute("type", "hidden");
                                    hiddenField.setAttribute("name", "delivery_ids[]");
                                    hiddenField.setAttribute("value", value);
                                    form.appendChild(hiddenField);
                                });
                                document.body.appendChild(form);
                                window.open('','formresult');
                                form.submit();
                            }
                            else
                            {
                                swal({
                                    title: "No Deliveries Selected",
                                    text: "Please select deliveries by checking their checkbox",
                                    icon: "error",
                                });
                            }
                        });
                        $('a.docket-print').click(function(e){
                            e.preventDefault();
                            //console.log('click');
                            if($('input.select:checked').length)
                            {
                                var ids = [];
                                $('input.select').each(function(i,e){
                                    if($(this).prop('checked'))
                                    {
                                        ids.push($(this).data('deliveryid'));
                                    }
                                });
                                //console.log(ids);
                                //return;
                                var form = document.createElement('form');
                                form.setAttribute("method", "post");
                                form.setAttribute("action", "/pdf/printDeliveryDockets");
                                //form.setAttribute("action", "/misc-functions/make-packslips-pdf.php");
                                form.setAttribute("target", "formresult");
                                $.each( ids, function( index, value ) {
                                    var hiddenField = document.createElement("input");
                                    hiddenField.setAttribute("type", "hidden");
                                    hiddenField.setAttribute("name", "delivery_ids[]");
                                    hiddenField.setAttribute("value", value);
                                    form.appendChild(hiddenField);
                                });
                                document.body.appendChild(form);
                                window.open('','formresult');
                                form.submit();
                            }
                            else
                            {
                                swal({
                                    title: "No Deliveries Selected",
                                    text: "Please select deliveries by checking their checkbox",
                                    icon: "error",
                                });
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