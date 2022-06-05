
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                common: {
                    addFinisher: function(){
                        $("a.add-finisher").click(function(e){
                            e.preventDefault();
                            var finisher_count = $("div#finishers_holder div.afinisher").length;
                            //console.log('packages: '+contact_count);
                            var data = {
                                i: finisher_count
                            }
                            $.post('/ajaxfunctions/addJobFinisher', data, function(d){
                                $('div#finishers_holder').append(d.html);
                                actions.common.removeFinisher();
                                actions.common.finisherAutocomplete();
                                $([document.documentElement, document.body]).animate({
                                    scrollTop: $("#finisher_"+finisher_count).offset().top
                                }, 1000);
                            });
                        });
                    },
                    customerContactChange: function(){
                        $('select#customer_contact_id').change(function(e){
                            if($(this).val() != 0)
                            {
                                $('input#customer_contact_name').val($(this).find(":selected").text()).valid();
                                $('input#customer_contact_email').val($(this).find(":selected").data("contactemail"));
                                $('input#customer_contact_role').val($(this).find(":selected").data("contactrole"));
                                $('input#customer_contact_phone').val($(this).find(":selected").data("contactphone"));
                            }
                            else
                            {
                                $('input#customer_contact_name').val('').valid();
                                $('input#customer_contact_email').val('');
                                $('input#customer_contact_role').val('');
                                $('input#customer_contact_phone').val('');
                            }
                            if($('#send_to_customer').prop('checked'))
                            {
                                $('input#attention').val($('input#customer_contact_name').val());
                            }
                        });
                    },
                    deliverToAutoCompleteCustomer: function(){
                        autoCompleter.productionJobCustomerAutoComplete($('input#ship_to'), selectDeliveryCallback, changeDeliveryCallback);
                        function selectDeliveryCallback(event, ui)
                        {
                            $('input#ship_to').val(ui.item.value).valid();
                            $('input#address').val(ui.item.address).valid();
                            $('input#address2').val(ui.item.address_2);
                            $('input#suburb').val(ui.item.suburb).valid();
                            $('input#state').val(ui.item.state).valid();
                            $('input#postcode').val(ui.item.postcode).valid();
                            if(ui.item.contacts)
                            {
                                var contacts =  (ui.item.contacts).split('|');
                                var contact = contacts[0].split(',');
                                $('input#attention').val(contact[1]);
                            }
                        }
                        function changeDeliveryCallback(event, ui)
                        {
                            return false;
                        }
                    },
                    customerAutoComplete: function(){
                        autoCompleter.addressAutoComplete($('#customer_address'), 'customer_');
                        autoCompleter.suburbAutoComplete($('#customer_suburb'), 'customer_');
                        autoCompleter.productionJobCustomerAutoComplete($('input#customer_name'), selectCustomerCallback, changeCustomerCallback);
                        function selectCustomerCallback(event, ui)
                        {
                            $('input#customer_email').val(ui.item.email);
                            $('input#customer_phone').val(ui.item.phone);
                            $('input#customer_id').val(ui.item.customer_id);
                            $('input#customer_address').val(ui.item.address);
                            $('input#customer_address2').val(ui.item.address_2);
                            $('input#customer_suburb').val(ui.item.suburb);
                            $('input#customer_state').val(ui.item.state);
                            $('input#customer_country').val(ui.item.country);
                            $('input#customer_postcode').val(ui.item.postcode);
                            $('input#customer_website').val(ui.item.website);
                            if($('#send_to_customer').prop('checked'))
                            {
                                $('input#ship_to').val(ui.item.value).valid();
                                $('input#address').val(ui.item.address).valid();
                                $('input#address2').val(ui.item.address_2);
                                $('input#suburb').val(ui.item.suburb).valid();
                                $('input#state').val(ui.item.state).valid();
                                $('input#country').val(ui.item.country).valid();
                                $('input#postcode').val(ui.item.postcode).valid();
                            }
                            //contacts
                            if(ui.item.contacts)
                            {
                                var contacts =  (ui.item.contacts).split('|');
                                if(contacts.length > 1)
                                {
                                    $('input.customer_contact').each(function(i,e){
                                        $(this).val('');
                                    });
                                    var html = "<label class='col-md-4 col-form-label'>Job Contact</label>";
                                    html += "<div class='col-md-8'>";
                                    html += "<select id='customer_contact_id' class='form-control selectpicker' name='customer_contact_id' data-style='btn-outline-secondary'>";
                                    html += "<option value='0'>Choose a Contact</option>";
                                    $.each(contacts, function(i,v){
                                        var contact = contacts[i].split(',');
                                        html += "<option value='"+contact[0]+"' data-contactemail='"+contact[2]+"' data-contactphone='"+contact[3]+"' data-contactrole='"+contact[4]+"'>"+contact[1]+"</option>";
                                    });
                                    html += "</select></div>";
                                    $('div#contact_chooser').html(html);
                                    $('.selectpicker').selectpicker();
                                    actions.common.customerContactChange();
                                }
                                else
                                {
                                    $('select#customer_contact_id').off('change');
                                    var contact = contacts[0].split(',');
                                    $('div#contact_chooser').html('<input type="hidden" id="customer_contact_id" name="customer_contact_id" value="'+contact[0]+'" > ');
                                    $('input#customer_contact_name').val(contact[1]).valid();
                                    $('input#customer_contact_email').val(contact[2]);
                                    $('input#customer_contact_role').val(contact[4]);
                                    $('input#customer_contact_phone').val(contact[3]);
                                    if($('#send_to_customer').prop('checked'))
                                    {
                                        $('input#attention').val(contact[1]);
                                    }
                                }
                            }
                            return false;
                        }
                        function changeCustomerCallback(event, ui)
                        {
                            if (!ui.item)
                	        {
                	            var $target = $(event.target);
                                $('input#customer_id').val(0);
                                $('input.customer').each(function(element, index){
                                    $(this).val("");
                                });
                                $target.val("");
                                return false;
                            }
                        }
                    },
                    finisherAutocomplete: function(){
                        $("div#finishers_holder div.afinisher").each(function(i,e){
                            var $this_input = $(this).find("input.finisher_name:not(.no-autocomplete)");
                            var $this_finisher_details = $(this).find("div.this_finisher_details");
                            if($this_input.data('ui-autocomplete') != undefined)
                            {
                                $this_input.autocomplete("destroy" );
                            }
                            autoCompleter.productionJobFinisherAutoComplete($this_input, selectFinisherCallback, changeFinisherCallback);
                        });
                        function selectFinisherCallback(event, ui)
                        {
                            var $target = $(event.target)
                            var $this_finisher = $target.closest("div.afinisher");
                            var this_finisher_ind  = $target.data("finisher");
                            $this_finisher.find("div.this_finisher_details").show();
                            var $this_finisher_details = $this_finisher.find("div.this_finisher_hidden_details");
                            $this_finisher_details.find("input").each(function(element, index){
                                var fclass = $(this).attr("class");
                                $(this).val(ui.item[fclass]);
                            });
                            actions.common.finisherExpectedDeliveryDates();
                            jobDeliveryDestinations.updateEvents();
                            var data = {
                                finisher_id : ui.item.finisher_id,
                                finisher_ind : this_finisher_ind
                            }
                            $.post('/ajaxfunctions/makeFinisherContactSelect', data, function(d){
                                $('div#contact_selector_'+this_finisher_ind).html(d.html);
                                $('.selectpicker').selectpicker();
                            });
                            return false;
                        }
                        function changeFinisherCallback(event, ui)
                        {
                            if (!ui.item)
                	        {
                                var $target = $(event.target)
                                var $this_finisher = $target.closest("div.afinisher");
                                $this_finisher.find("div.this_finisher_details").hide();
                                $target.val("");
                                var $this_finisher_details = $this_finisher.find("div.this_finisher_hidden_details");
                                $this_finisher_details.find("input").each(function(element, index){
                                    $(this).val("");
                                });
                                return false;
                            }
                        }
                    },
                    finisherExpectedDeliveryDates: function(){
                        $("div#finishers_holder div.afinisher").each(function(i,e){
                            var $this_input = $(this).find("input.finisher_ed_date");
                            var $this_value_input = $(this).find("input.finisher_ed_date_value");
                            var $this_calendar_icon = $(this).find("span.calendar_icon");
                            $this_calendar_icon.css('cursor', 'pointer').click(function(e){
                                $this_input.focus();
                                console.log('this input is ' + $this_input);
                            });
                            if(!$this_input.hasClass("hasDatepicker"))
                            {
                                $this_input.datepicker({
                                    changeMonth: true,
                                    changeYear: true,
                                    dateFormat: "dd/mm/yy",
                                    onClose: function(selectedDate){
                                        //console.log('selecteddate: '+ selectedDate);
                                        if(selectedDate == "")
                                        {
                                            $this_value_input.val('');
                                            $this_input.val('');
                                        }
                                        else
                                        {
                                            var d = new Date( selectedDate.replace( /(\d{2})[-/](\d{2})[-/](\d{4})/, "$2\/$1\/$3") );
                                            s = d.valueOf()/1000;
                                            $this_value_input.val(s);
                                        }
                                    }
                                });
                            }
                        });
                    },
                    removeFinisher: function(){
                        $("a.remove-finisher").off('click').click(function(e){
                            e.preventDefault();
                            var $this = $(this);
                            swal({
                                title: "Really Remove This Finisher?",
                                text: "Make sure to check the delivery details are not affected by this",
                                icon: "warning",
                                buttons: true,
                                dangerMode: true
                            }).then( function(removeFinisher) {
                                if(removeFinisher)
                                {
                                    var this_finisher = $this.data('finisher');
                                    $("div#finisher_"+this_finisher).remove();
                                    //redo indexing of finishers
                                    $("div#finishers_holder div.afinisher").each(function(i,e){
                                        $(this).attr("id", "finisher_"+i);
                                        var plusi = i + 1;
                                        var new_num = toWords(plusi);
                                        var uc_new_num = new_num.charAt(0).toUpperCase() + new_num.slice(1)
                                        $(this).find("h4.finisher_title").text("Finisher "+uc_new_num+"'s Details");
                                        $(this).find("a.remove-finisher").data("finisher", i);
                                        $(this).find("input.send_to_finisher").data("finisher", i);
                                        $(this).find("input.finisher_name").data("finisher", i);
                                        $(this).find("input.finisher_name").attr("name", "finishers["+i+"][name]");
                                        $(this).find("input.finisher_po").attr("name", "finishers["+i+"][purchase_order]");
                                        $(this).find("input.finisher_id").attr("name", "finishers["+i+"][finisher_id]");
                                        $(this).find("select.finisher_contact_id").attr("name", "finishers["+i+"][contact_id]");
                                        $(this).find("input.finisher_ed_date").attr("name", "finishers["+i+"][ed_date]");
                                        $(this).find("input.finisher_ed_date_value").attr("name", "finishers["+i+"][ed_date_value]");
                                        $(this).find("input.send_to_finisher").attr("name", "send_to_finisher_"+i);
                                        $(this).find("input.send_to_finisher").attr("id", "send_to_finisher_"+i);
                                        $(this).find("div.contact_selector").attr("id", "contact_selector_"+i);
                                        $(this).find("label.send_to_finisher").attr("for", "send_to_finisher_"+i);
                                        var $this_finisher_details = $(this).find("div.this_finisher_hidden_details");
                                        //$this_finisher_details.find('input.finisher_id').val(ui.item.finisher_id);
                                        $this_finisher_details.find("input").each(function(element, index){
                                            var fclass = $(this).attr("class");
                                            $(this).attr("name", "finishers["+i+"]["+fclass+"]");
                                        });
                                    });
                                }
                            });
                        });
                    },
                    jobsTable: function(paging){
                        if(paging === undefined) {
                            paging = $('input#completed').val() == 1;
                        }
                        jQuery.extend( jQuery.fn.dataTableExt.oSort, {
                            "non-empty-string-asc": function (str1, str2) {
                                if(str1 == "")
                                    return 1;
                                if(str2 == "")
                                    return -1;
                                return ((str1 < str2) ? -1 : ((str1 > str2) ? 1 : 0));
                            },
                            "non-empty-string-desc": function (str1, str2) {
                                if(str1 == "")
                                    return 1;
                                if(str2 == "")
                                    return -1;
                                return ((str1 < str2) ? 1 : ((str1 > str2) ? -1 : 0));
                            }
                        });
                        $.fn.dataTable.ext.order['dom-select'] = function  ( settings, col )
                        {
                            return this.api().column( col, {order:'index'} ).nodes().map( function ( td, i ) {
                                return $('select', td).data("ranking");
                            } );
                        }
                        var options = {
                            "paging":   paging,
                            //No initial sort,
                            "order": [],
                            //search highlighting
                            mark: true,
                            //but blanks on the bottom when sorting
                            columnDefs: [
                                {
                                    type: 'non-empty-string',
                                    targets: 0 //priority is the second column
                                },
                                {
                                    orderDataType: "dom-select",
                                    targets: 0
                                },
                                {
                                    orderable: false,
                                    targets: "no-sort"
                                }
                            ],
                            "dom" : '<<"row"<"col-lg-4"i><"col-lg-6"l>><"row">tp>',
                        };
                        var table = dataTable.init($('table#production_jobs_table'), options );
                        table.on( 'draw', function () {
                            //console.log( 'Redraw occurred at: '+new Date().getTime() );
                            $('.selectpicker').selectpicker();
                        });
                        $('#live-filter-text').on( 'keyup', function () {
                            table.search( this.value ).draw();
                        } );
                    },
                    selectAll: function(){
                        $('#select_all').click(function(e){
                            var checked = this.checked;
                             $('.select').each(function(e){
                                this.checked =  checked;
                             })
                        });
                        $('#status_all').change(function(e){
                            var c = $(this).val();
                            $("select.status").each(function(i,e){
                                $(this).val(c).change();
                            });
                        });
                    },
                    doDates: function(){
                        $( "#date_entered" ).datepicker({
                            changeMonth: true,
                            changeYear: true,
                            dateFormat: "dd/mm/yy",
                            onSelect: function(selectedDate) {
                                var d = new Date( selectedDate.replace( /(\d{2})[-/](\d{2})[-/](\d{4})/, "$2/$1/$3") );
                                s = d.valueOf()/1000;
                                $('#date_entered_value').val(s);
                            }
                        });
                        $('#date_entered_calendar').css('cursor', 'pointer').click(function(e){
                            $('input#date_entered').focus();
                        });
                        $( ".runsheet_day" ).datepicker({
                            changeMonth: true,
                            changeYear: true,
                            dateFormat: "dd/mm/yy",
                            onSelect: function(selectedDate) {
                                var d = new Date( selectedDate.replace( /(\d{2})[-/](\d{2})[-/](\d{4})/, "$2/$1/$3") );
                                s = d.valueOf()/1000;
                                var $tr = $(this).closest('tr');
                                var ar = $tr.prop('id').split("_");
                                var job_id = ar[1];
                                //console.log('input: input#runsheet_daydate_value_'+job_id);
                                //console.log('s: '+s);
                                $('input#runsheet_daydate_value_'+job_id).val(s);
                            }
                        });
                        $('.runsheet_calendar').css('cursor', 'pointer').click(function(e){
                            var $tr = $(this).closest('tr');
                            var ar = $tr.prop('id').split("_");
                            var job_id = ar[1];
                            //console.log('Job ID: '+job_id);
                            $('input#runsheet_daydate_'+job_id).focus();
                        });
                        $( "#date_due" ).datepicker({
                            changeMonth: true,
                            changeYear: true,
                            dateFormat: "dd/mm/yy",
                            onClose: function(selectedDate){
                                //console.log('selecteddate: '+ selectedDate);
                                var patt = new RegExp(/\d{2}[-/]\d{2}[-/]\d{4}/);
                                if( patt.test(selectedDate) )
                                {
                                    //console.log("true");
                                    var d = new Date( selectedDate.replace( /(\d{2})[-/](\d{2})[-/](\d{4})/, "$2\/$1\/$3") );
                                    s = d.valueOf()/1000;
                                    $('#date_due_value').val(s);
                                }
                                else
                                {
                                    $(this).val('');
                                    $('#date_due_value').val('');
                                }
                            }
                        });
                        $('#strict_dd').click(function(e){
                            $('#rdd').toggle();
                            $('#date_due').valid();

                        });
                        $('#strict_dd').change(function(e){
                            if(this.checked)
                            {
                                $('#asap').attr("checked", false);
                            }
                        });
                        $('#asap').change(function(e){
                            if(this.checked)
                            {
                                $('div#due_date_holder').hide();
                                $('#strict_dd').attr("checked", false);
                                $('#date_due, #date_due_value').val('');
                            }
                            else
                            {
                                $('div#due_date_holder').show();
                                var plusSeven = Date.now() + 7 * 24 * 60 * 60 * 1000;
                                var d = new Date(plusSeven);
                                var date = d.getDate();
                                var month = d.getMonth() + 1;
                                var year = d.getFullYear();
                                var dateString = (date <= 9 ? '0' + date : date) + '/' + (month <= 9 ? '0' + month : month) + '/' + year;
                                $('#date_due_value').val(Math.floor(plusSeven/1000));
                                $('#date_due').val(dateString);
                            }
                        });
                        $('#date_due_calendar').css('cursor', 'pointer').click(function(e){
                            $('input#date_due').focus();
                        }); 
                    },
                    autoComplete: function(){
                        autoCompleter.addressAutoComplete($('#address'));
                        autoCompleter.suburbAutoComplete($('#suburb'));
                    }
                },
                'add-job':{
                    init: function(){
                        actions.common.autoComplete();
                        actions.common.deliverToAutoCompleteCustomer();
                        actions.common.customerAutoComplete();
                        actions.common.doDates();
                        actions.common.addFinisher();
                        jobDeliveryDestinations.updateEvents();
                        $("form#add_production_job").submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Adding the Job...</h2></div>' });
                            }
                        });
                        $('select#status_id, #state, #postcode, #suburb, #country, select#salesrep_id').change(function(e){
                            $(this).valid();
                        });
                    }
                },
                'view-jobs':{
                    init: function(){
                        actions.common.jobsTable();
                        actions.common.selectAll();
                        actions.common.doDates();

                        $('button.production_note').click(function(e){
                            var job_id = $(this).data('jobid');
                            var job_no = $(this).data('jobno');
                            //console.log("will add a note to job id: "+job_id) ;
                            $('<div id="note_pop" title="Add Note For Production">').appendTo($('body'));
                            $('#note_pop')
                                .html("<p class='text-center'><img class='loading' src='/images/preloader.gif' alt='loading...' /><br />Creating Form...</p>")
                                .load('/ajaxfunctions/addProductionJobNoteForm',{job_id: job_id},
                                    function(responseText, textStatus, XMLHttpRequest){
                                        if(textStatus == 'error') {
                                            $(this).html('<div class=\'errorbox\'><h2>There has been an error</h2></div>');
                                        }
                                        $('form#jobs-add-production-note').submit(function(e){
                                            if($(this).valid())
                                            {

                                            }
                                            else
                                            {
                                                e.preventDefault();
                                            }
                                        });
                                    }
                                );
                            dialog = $("#note_pop").dialog({
                                    draggable: true,
                                    modal: true,
                                    show: true,
                                    hide: true,
                                    autoOpen: false,
                                    height: "auto",
                                    width: "auto",
                                    buttons:{
                                        'Update Notes': function(){
                                            $('form#jobs-add-production-note').submit();
                                            $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Updating Notes...</h2></div>' });
                                        }
                                    },
                                    create: function( event, ui ) {
                                        // Set maxWidth
                                        $(this).css("maxWidth", "660px");
                                    },
                                    close: function(){
                                        $("#note_pop").remove();
                                    },
                                    open: function(){
                                        $('.ui-widget-overlay').bind('click',function(){
                                            $('#note_pop').dialog('close');
                                        });
                                    }
                            });
                            $("#note_pop").dialog('open');
                            form = dialog.find( "form" ).on( "submit", function( e ) {
                                $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Updating Notes...</h2></div>', baseZ: 2000 });
                            });
                        });

                        $('button.delivery_note').click(function(e){
                            var job_id = $(this).data('jobid');
                            var job_no = $(this).data('jobno');
                            //console.log("will add a note to job id: "+job_id) ;
                            $('<div id="delivery_note_pop" title="Add Note For Delivery">').appendTo($('body'));
                            $('#delivery_note_pop')
                                .html("<p class='text-center'><img class='loading' src='/images/preloader.gif' alt='loading...' /><br />Creating Form...</p>")
                                .load('/ajaxfunctions/addProductionJobDeliveryNoteForm',{job_id: job_id},
                                    function(responseText, textStatus, XMLHttpRequest){
                                        if(textStatus == 'error') {
                                            $(this).html('<div class=\'errorbox\'><h2>There has been an error</h2></div>');
                                        }
                                        $('form#jobs-add-production-delivery-note').submit(function(e){
                                            if($(this).valid())
                                            {

                                            }
                                            else
                                            {
                                                e.preventDefault();
                                            }
                                        });
                                    }
                                );
                            dialog = $("#delivery_note_pop").dialog({
                                    draggable: true,
                                    modal: true,
                                    show: true,
                                    hide: true,
                                    autoOpen: false,
                                    height: "auto",
                                    width: "auto",
                                    buttons:{
                                        'Update Notes': function(){
                                            $('form#jobs-add-production-delivery-note').submit();
                                            $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Updating Delivery Notes...</h2></div>' });
                                        }
                                    },
                                    create: function( event, ui ) {
                                        // Set maxWidth
                                        $(this).css("maxWidth", "660px");
                                    },
                                    close: function(){
                                        $("#note_pop").remove();
                                    },
                                    open: function(){
                                        $('.ui-widget-overlay').bind('click',function(){
                                            $('#delivery_note_pop').dialog('close');
                                        });
                                    }
                            });
                            $("#delivery_note_pop").dialog('open');
                            form = dialog.find( "form" ).on( "submit", function( e ) {
                                $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Updating Delivery Notes...</h2></div>', baseZ: 2000 });
                            });
                        });



                        $('button.print-sheet').each(function(i,e){
                            $(this).click(function(e){
                                var runsheet_id = $(this).data('runsheetid');
                                var driver_id = $(this).data('driverid');
                                console.log('runsheet_id: '+runsheet_id);
                                console.log('driver_id: '+driver_id);
                                var form = document.createElement('form');
                                form.setAttribute("method", "post");
                                form.setAttribute("action", "/pdf/printRunsheet");
                                form.setAttribute("target", "runsheetformresult");
                                var hiddenField = document.createElement("input");
                                hiddenField.setAttribute("type", "hidden");
                                hiddenField.setAttribute("name", "runsheet_id");
                                hiddenField.setAttribute("value", runsheet_id);
                                form.appendChild(hiddenField);
                                var hiddenField2 = document.createElement("input");
                                hiddenField2.setAttribute("type", "hidden");
                                hiddenField2.setAttribute("name", "driver_id");
                                hiddenField2.setAttribute("value", driver_id);
                                form.appendChild(hiddenField2);
                                document.body.appendChild(form);
                                window.open('','runsheetformresult');
                                form.submit();
                            });
                        });
                        //create pdf of table
                        $('button#create_pdf').click(function(e){
                            if(!$('input.select:checked').length)
                            {
                                swal({
                                    title: "No Jobs Selected",
                                    text: "Please select at least one job to create the PDF",
                                    icon: "error"
                                });
                            }
                            else
                            {
                                var ids = [];
                                $('input.select').each(function(i,e){
                                    if($(this).prop('checked') )
                                    {
                                        ids.push($(this).data('jobid'));
                                    }
                                });
                                var form = document.createElement('form');
                                form.setAttribute("method", "post");
                                form.setAttribute("action", "/pdf/printJobsTable");
                                form.setAttribute("target", "printjobsformresult");
                                $.each( ids, function( index, value ) {
                                    var hiddenField = document.createElement("input");
                                    hiddenField.setAttribute("type", "hidden");
                                    hiddenField.setAttribute("name", "orderids[]");
                                    hiddenField.setAttribute("value", value);
                                    form.appendChild(hiddenField);
                                });
                                document.body.appendChild(form);
                                window.open('','printjobsformresult');
                                form.submit();
                            }
                        })
                        //update job priority
                        $('button#priority_change').click(function(e){
                            if(!$('input.select:checked').length)
                            {
                                swal({
                                    title: "No Jobs Selected",
                                    text: "Please select at least one job to update its priority",
                                    icon: "error"
                                });
                            }
                            else
                            {
                                swal({
                                    title: "Update the Priority?",
                                    text: "This can only be undone by changing it back",
                                    icon: "warning",
                                    buttons: true,
                                    dangerMode: true
                                }).then( function(changePriority) {
                                    if(changePriority)
                                    {
                                        var ids = [];
                                        $('input.select').each(function(i,e){
                                            if($(this).prop('checked') )
                                            {
                                                var job_id = $(this).data('jobid');
                                                var priority = $('select#priority_'+job_id).val();
                                                var ent = {
                                                    jobid: job_id,
                                                    priority: priority
                                                }
                                                ids.push(ent);
                                            }
                                            $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Updating Priorities...</h1></div>' });
                                            var data = {jobids: ids};
                                            $.post('/ajaxfunctions/update-jobs-priority', data, function(d){
                                                location.reload();
                                            });
                                        });
                                    }
                                });
                            }
                        });
                        //update job status
                        $('button#status').click(function(e){
                            if(!$('input.select:checked').length)
                            {
                                swal({
                                    title: "No Jobs Selected",
                                    text: "Please select at least one job to update its status",
                                    icon: "error"
                                });
                            }
                            else
                            {
                                swal({
                                    title: "Update the status?",
                                    text: "This can only be undone by changing it back",
                                    icon: "warning",
                                    buttons: true,
                                    dangerMode: true
                                }).then( function(changeStatus) {
                                    if(changeStatus)
                                    {
                                        var ids = [];
                                        $('input.select').each(function(i,e){
                                            if($(this).prop('checked') )
                                            {
                                                var job_id = $(this).data('jobid');
                                                var status_id = $('select#status_'+job_id).val();
                                                var ent = {
                                                    jobid: job_id,
                                                    statusid: status_id
                                                }
                                                ids.push(ent);
                                            }
                                        });
                                        $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Updating Status...</h1></div>' });
                                        var data = {jobids: ids};
                                        $.post('/ajaxfunctions/update-job-status', data, function(d){
                                            location.reload();
                                        });
                                    }
                                });
                            }
                        });
                        //add to driver runsheet
                        $('button#runsheet').click(function(e){
                            if(!$('input.select:checked').length)
                            {
                                swal({
                                    title: "No Jobs Selected",
                                    text: "Please select at least one job to add to the runsheet",
                                    icon: "error"
                                });
                            }
                            else
                            {
                                var rs_count = $('input.select:checked').length
                                swal({
                                    title: "Add "+rs_count+" orders to the runsheet?",
                                    text: "This will add the selected orders to the day's runsheet\n\nor create a new runsheet if one doe not exist",
                                    icon: "warning",
                                    buttons: true,
                                    dangerMode: true
                                }).then( function(addToSheet) {
                                    if(addToSheet)
                                    {
                                        var runsheet_days = [];
                                        $('input.select').each(function(i,e){
                                            var job_id = $(this).data('jobid');
                                            var daydate_value = $('input#runsheet_daydate_value_'+job_id).val();
                                            if($(this).prop('checked') )
                                            {
                                                var ent = {
                                                    'timestamp' : daydate_value,
                                                    'job_id'    : job_id
                                                }
                                                runsheet_days.push(ent);
                                            }
                                        });
                                        //console.log(runsheet_days);
                                        /**/
                                        $.ajax({
                                            url: '/ajaxfunctions/add-job-runsheets',
                                            method: 'post',
                                            data: {
                                                runsheets: runsheet_days
                                            },
                                            dataType: 'json',
                                            beforeSend: function(){
                                                $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Creating/Editing Runsheet...</h1></div>' });
                                            },
                                            success: function(d){
                                                if(d.error)
                                                {
                                                    $.unblockUI();
                                                    alert('error');
                                                }
                                                else
                                                {
                                                    location.reload(true);
                                                    //window.location.href = "http://stackoverflow.com";
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

                        });
                        $('button.remove-from-runsheet').click(function(e){
                            var job_id = $(this).data('jobid');
                            var runsheet_id = $(this).data('runsheetid');
                            swal({
                                    title: "Really remove this job from the runsheet?",
                                    text: "This cannot be undone",
                                    icon: "warning",
                                    buttons: true,
                                    dangerMode: true
                                }).then( function(removeFromSheet) {
                                    if(removeFromSheet)
                                    {
                                        //console.log('job id: '+job_id);
                                        //console.log('runsheet id: '+runsheet_id);
                                        $.ajax({
                                            url: '/ajaxfunctions/remove-job-from-runsheet',
                                            method: 'post',
                                            data: {
                                                job_id: job_id,
                                                runsheet_id: runsheet_id
                                            },
                                            dataType: 'json',
                                            beforeSend: function(){
                                                $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Removing From Runsheet...</h1></div>' });
                                            },
                                            success: function(d){
                                                if(d.error)
                                                {
                                                    $.unblockUI();
                                                    alert('error');
                                                }
                                                else
                                                {
                                                    location.reload(true);
                                                    //window.location.href = "http://stackoverflow.com";
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
                        });
                        $('button#filter_jobs').click(function(e){
                            var customers = $('select#customer_id').val();
                            var finishers = $('select#finisher_id').val();
                            var salesreps = $('select#salesrep_id').val();
                            var status = $('select#status_id').val();
                            var url = "/jobs/view-jobs";
                            if($('input#cancelled').val() == 1)
                                url +="/cancelled=1";
                            else if($('input#completed').val() == 1)
                                url +="/completed=1";
                            else if(!(!status || 0 === status.length))
                            {
                                url += "/status_ids="+status;
                            }
                            if(!(!customers || 0 === customers.length))
                            {
                                url += "/customer_ids="+customers;
                            }
                            if(!(!finishers || 0 === finishers.length))
                            {
                                url += "/finisher_ids="+finishers;
                            }
                            if(!(!salesreps || 0 === salesreps.length))
                            {
                                url += "/contacts_ids="+salesreps;
                            }
                            //console.log("URL: "+url);
                            $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Applying Filters...</h2></div>' });
                            window.location.href = url;
                        });
                        $('button#unfilter_jobs').click(function(e){
                            $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Removing Filters...</h2></div>' });
                            var url =  "/jobs/view-jobs";
                            if($('input#cancelled').val() == 1)
                                url +="/cancelled=1";
                            else if($('input#completed').val() == 1)
                                url +="/completed=1";
                            window.location.href = url;
                        });
                        var $checks = $('input.status_override');
                        $checks.click(function() {
                            $checks.not(this).prop("checked", false);
                        });
                    }//init
                },
                'update-job':{
                    init: function(){
                        actions.common.doDates();
                        actions.common.autoComplete();
                        actions.common.addFinisher();
                        actions.common.removeFinisher();
                        actions.common.customerAutoComplete();
                        jobDeliveryDestinations.updateEvents();
                        actions.common.finisherExpectedDeliveryDates();
                        actions.common.customerContactChange();
                        $('button#job_details_update_submitter').click(function(e){
                            $('form#job_details_update').submit();
                        });
                        $('button#delivery_details_update_submitter').click(function(e){
                            $('form#delivery_details_update').submit();
                        });
                        $('button#customer_details_update_submitter').click(function(e){
                            $('form#customer_details_update').submit();
                        });
                        $('button#finisher_details_update_submitter').click(function(e){
                            $('form#finisher_details_update').submit();
                        });
                        $('form#job_details_update, form#customer_details_update, form#finisher_details_update, form#delivery_details_update').submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Updating Details...</h2></div>' });
                            }
                            else
                            {
                                return false;
                            }
                        });
                        $('select#status').change(function(e){
                            $(this).valid();
                        });
                    }
                },
                'job-search':{
                    init: function(){
                        datePicker.betweenDates();
                        $('form#job_order_search').submit(function(e){
                            $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Searching For Jobs...</h2></div>' });
                        });
                    }
                },
                'job-search-results':{
                    init: function(){
                        actions['job-search'].init();
                        actions.common.jobsTable(true);
                        actions.common.selectAll();
                    }
                },
                'create-delivery-docket':{
                    init: function(){
                        actions.common.autoComplete();
                        $("input#per_box").keyup(function(e){
                            actions['create-delivery-docket']['box-count-calcs']();
                        });
                        $("input#quantity").keyup(function(e){
                            actions['create-delivery-docket']['box-count-calcs']();
                        });
                    },
                    'box-count-calcs': function(){
                        var pb = parseInt($('#per_box').val()) || 0;
                        var q = parseInt($('#quantity').val()) || 0;
                        console.log("pb: "+pb);
                        console.log("q: "+q);
                        if(pb > 0 && q > 0)
                        {
                            var bc = Math.ceil(q/pb);
                            console.log("bc: "+bc);
                            $("#box_count").val(bc);
                        }
                        else
                        {
                            $("#box_count").val('');
                        }
                    }
                },
                'create-shipment': {
                    init: function(){
                        actions.common.autoComplete();
                        $('button#delivery_details_update_submitter').click(function(ev){
                            if($('form#job_delivery_details_update').valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h1>Saving address...</h1></div>' });
                                $('form#job_delivery_details_update').submit();
                            }
                        });
                        $('button#add_package').click(function(e){
                            //make the package form window
                            var shipment_id = $(this).data('shipmentid');
                            var job_id = $(this).data('jobid');
                            $('<div id="order-add-package" title="Add Packages or Pallets">').appendTo($('body'));
                            $("#order-add-package")
                                .html("<p class='text-center'><img class='loading' src='/images/preloader.gif' alt='loading...' /><br />Creating Form...</p>")
                                .load('/ajaxfunctions/addShipmentPackageForm',{shipment_id: shipment_id, job_id: job_id},
                                    function(responseText, textStatus, XMLHttpRequest){
                                    if(textStatus == 'error') {
                                        $(this).html('<div class=\'errorbox\'><h2>There has been an error</h2></div>');
                                    }
                                    $('form#order-add-shipment-package').submit(function(e){
                                        if($(this).valid())
                                        {

                                        }
                                        else
                                        {
                                            e.preventDefault();
                                        }
                                    });
                            });
                            $("#order-add-package").dialog({
                                    draggable: true,
                                    modal: true,
                                    show: true,
                                    hide: true,
                                    autoOpen: false,
                                    height: "auto",
                                    width: "auto",
                                    create: function( event, ui ) {
                                        // Set maxWidth
                                        $(this).css("maxWidth", "660px");
                                    },
                                    close: function(){
                                        $("#order-add-package").remove();
                                    },
                                    open: function(){
                                        $('.ui-widget-overlay').bind('click',function(){
                                            $('#order-add-package').dialog('close');
                                        });

                                    },
                                    position: { my: "center", at: "center", of: window }
                            });
                            $("#order-add-package").dialog('open');
                        });
                        $('a.delete-package')
                            .css('cursor', 'pointer')
                            .click(function(e){
                                if(confirm('Really delete this package?'))
                                {
                                    $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Deleting package...</h2></div>' });
                                    var data = {
                                        lineid: $(this).data('packageid')
                                    };
                                    $.post('/ajaxfunctions/deleteShipmentPackage', data, function(d){
                                        location.reload();
                                    });
                                }
                        });
                        $('button.mobile-link').click(function(ev){
                            var $target_card = $('div#'+this.id);
                            var $nav = $("nav.fixed-top");
                            var scrollSpot = $target_card.offset().top - $nav.height();
                            $('html, body').animate({
                                scrollTop: scrollSpot
                            }, 1000);
                        });
                        $('button.quote_button').click(function(ev){
                            var shipment_id = $(this).data('shipmentid');
                            var address_string = $(this).data('destination');
                            //make the quote window
                            $('<div id="quote_pop" title="Shipping Quotes">').appendTo($('body'));
                            $("#quote_pop")
                                .html("<p class='text-center'><img class='loading' src='/images/preloader.gif' alt='loading...' /><br />Fetching Quotes...</p>")
                                .load('/ajaxfunctions/getProductionShippingQuotes',{shipment_id: shipment_id, address_string: address_string},
                                    function(responseText, textStatus, XMLHttpRequest){
                                    if(textStatus == 'error') {
                                        $(this).html('<div class=\'errorbox\'><h2>There has been an error</h2><p>Please check the address details for issues</p><p></div>');
                                    }
                                    else
                                    {
                                        //truckCost.getQuote();
                                    }
                            });
                        });
                    }
                },
                'shipment-address-update':{
                    init: function(){
                        actions.common.autoComplete();
                        $('form#shipment-address-update').submit(function(e){
                            $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Saving Address Changes...</h2></div>' });
                        });

                    }
                }, 
                errors:{
                    init:function(){

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