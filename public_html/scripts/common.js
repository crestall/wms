/************
Refresh Page if no activity and show a countdown
*************/
var time = new Date().getTime();
var refresh_rate = 300000; //milliseconds
refresh();
$(document).bind("mousemove keypress", function(e) {
    time = new Date().getTime();
    refresh();
});

function refresh() {
    var now = new Date().getTime();
    if (now - time >= refresh_rate)
    {
        window.location.reload(true);
    }
    else
    {
        var left = Math.ceil( (refresh_rate - (now -time))/1000 );
        var minutes = Math.floor(left/60);
        var seconds = left - (minutes * 60);
        --seconds;
        minutes = (seconds < 0) ? --minutes : minutes;
        minutes = (minutes < 10) ? '0' + minutes : minutes;
        seconds = (seconds < 0) ? 59 : seconds;
        seconds = (seconds < 10) ? '0' + seconds : seconds;
        $('div#countdown span').html(minutes+":"+seconds);
        if(left <= 60)
        {
            $('div#countdown span').addClass("text-danger");
        }
        else
        {
            $('div#countdown span').removeClass();
        }
        setTimeout(refresh, 1000);
    }
}
/************
* Navigation Scripting
************/
 $(function () {
    scroller.checkDisplay();
    $(window).scroll(function () {
        //console.log('scrolling');
        scroller.checkDisplay();
	});
    //add the active class to the menu item
    var foundpage = false
    for(var cat in config.allPages)
    {
        //console.log("1 doing: "+cat);
        for(var page in config.allPages[cat])
        {
            //console.log("2 doing: "+page);
            if(config.curPage == page)
            {
                //console.log("found: "+page);
                $("li#"+cat+" > a").addClass("active");
                foundpage = true;
                break;
            }
        }
        if(foundpage)
            break;
    }
});

$('button#navbar_toggler').click(function(e){
    var h = $('div#navbarNav ul.navbar-nav').height();
    if($('div#navbarNav').hasClass('show'))
    {
        $('ul.user-info').addClass('navbar').css({
            marginLeft: 0,
            marginTop: 0
        });
        $('div#navbarNav ul.navbar-nav').css({
            width :'auto'
        });
    }
    else
    {;
        $('ul.user-info').removeClass('navbar').css({
            marginLeft: '170px',
            marginTop: '-815px'
        });
        $('div#navbarNav ul.navbar-nav').css({
            width :'200px'
        });
    }
});

var scroller = {
    checkDisplay: function(){
        //console.log('check display');
        var $nav = $("nav.fixed-top");
        $nav.toggleClass('scrolled', $(window).scrollTop() > $nav.height());
        $('ul.user-info li').toggleClass('white', $(window).scrollTop() > $nav.height());
        $('ul.user-info div#countdown').toggleClass('text-white', $(window).scrollTop() < $nav.height());
	    $nav.toggleClass('navbar-light', $(window).scrollTop() > $nav.height());
        $nav.toggleClass('navbar-dark', $(window).scrollTop() < $nav.height());
        if( $(window).scrollTop() > $nav.height() )
        {
            $('img.custom-logo-transparent').hide();
            $('img.custom-logo').show();
        }
        else
        {
            $('img.custom-logo-transparent').show();
            $('img.custom-logo').hide();
        }
    },
    cardsInView: function(){
        //console.log('cards in view');
        var viewportTop = $(window).scrollTop();
        var viewportBottom = viewportTop + $(window).height();
        if($("div.homepagedeck").length)
        {
            var $cardContainer = $("div.homepagedeck");
            var top = Math.round( $cardContainer.offset().top );
            var bottom = top + $cardContainer.height();
            $cardContainer.find('div.homepagecard').each(function(){
                $(this).toggleClass('in-view', (top < viewportBottom) && (bottom > viewportTop));
            });
        }
    }
}

/************
Homepage card fadeins
*************/
 $(function () {
    scroller.cardsInView();
    $(window).scroll(function () {
        //console.log('scrolling');
        scroller.cardsInView();
	});
});

/************
* File Uploading
************/
var fileUpload = {
    makeFileList: function(id){
        if(id === undefined) {
            id = 'invoice';
        }
        //get the input and UL list
        var input = document.getElementById(id);
        var list = document.getElementById('fileList');

        //empty list for now...
        list.innerHTML = '';

        //for every file...
        for (var x = 0; x < input.files.length; x++) {
        	//add to list
        	var li = document.createElement('li');
        	li.innerHTML = 'File ' + (x + 1) + ':  ' + input.files[x].name;
        	list.append(li);
        }
    }
}
/************
* File Downloading
************/
var fileDownload = {
    download: function(url, data){
        $.fileDownload( url ,{
            preparingMessageHtml: "Preparing the file, please wait...",
            failMessageHtml: "There was a problem generating your file, please try again.",
            httpMethod: "POST",
            data: data
        });
    }
}
/************
* Barcode Scanning
************/
var barcodeScanner = {
    unfocus: function(){
        $('input:focus').blur();
    },
    init: function(options) {
        var opts = {
            timeBeforeScanTest: 200, // wait for the next character for upto 200ms
        	startChar: [], // Prefix character for the cabled scanner (OPL6845R)
        	endChar: [13], // be sure the scan is complete if key 13 (enter) is detected
        	avgTimeByChar: 40, // it's not a barcode if a character takes longer than 40ms
        }
        $.extend( opts, options );
        $(document).scannerDetection( opts );
    },
    checkEan : function(eanCode) {
    	// Check if only digits
    	var ValidChars = "0123456789";
    	for (i = 0; i < eanCode.length; i++) {
    		digit = eanCode.charAt(i);
    		if (ValidChars.indexOf(digit) == -1) {
    			return false;
    		}
    	}

    	// Add five 0 if the code has only 8 digits
    	if (eanCode.length == 8 ) {
    		eanCode = "00000" + eanCode;
    	}
    	// Check for 13 digits otherwise
    	else if (eanCode.length != 13) {
    		return false;
    	}

    	// Get the check number
    	originalCheck = eanCode.substring(eanCode.length - 1);
    	eanCode = eanCode.substring(0, eanCode.length - 1);

    	// Add even numbers together
    	even = Number(eanCode.charAt(1)) +
    	       Number(eanCode.charAt(3)) +
    	       Number(eanCode.charAt(5)) +
    	       Number(eanCode.charAt(7)) +
    	       Number(eanCode.charAt(9)) +
    	       Number(eanCode.charAt(11));
    	// Multiply this result by 3
    	even *= 3;

    	// Add odd numbers together
    	odd = Number(eanCode.charAt(0)) +
    	      Number(eanCode.charAt(2)) +
    	      Number(eanCode.charAt(4)) +
    	      Number(eanCode.charAt(6)) +
    	      Number(eanCode.charAt(8)) +
    	      Number(eanCode.charAt(10));

    	// Add two totals together
    	total = even + odd;

    	// Calculate the checksum
        // Divide total by 10 and store the remainder
        checksum = total % 10;
        // If result is not 0 then take away 10
        if (checksum != 0) {
            checksum = 10 - checksum;
        }

    	// Return the result
    	if (checksum != originalCheck) {
    		return false;
    	}

        return true;
    }
}
/************
* Job Delivery Destinations
************/
var jobDeliveryDestinations = {
    updateEvents: function(){
        var $checkboxes = $("input.send_to_address");
        $checkboxes.click(function(){
            //console.log('click');
            $checkboxes.not(this).prop('checked', false).change();
        });
        $('input#held_in_store').change(function(e){
            if($('input#held_in_store').prop('checked'))
            {
                console.log('will disable everything');
                $("div#delivery_address_holder input").each(function(i,e){
                    $( this ).prop( "disabled", true );
                });
            }
            else
            {
                console.log('will enable everything');
                $("div#delivery_address_holder input").each(function(i,e){
                    $( this ).prop( "disabled", false );
                });
            }
        });
    }
}
/************
* Data Tables
************/
var dataTable = {
    init: function(el, options){
        //console.log('init');
        var opts = {
            "initComplete": function( settings, json ) {
                //console.log('initComplete');
                $("div#waiting").remove();
                $("div#table_holder").show();
                $(".dataTables_length select").addClass("form-control selectpicker");
                $(".dataTables_filter input").addClass("form-control");
                $(".dataTables_filter").addClass("form-group")
            },
            "dom" : '<<"row"<"col-lg-3"f><"col-lg-3"l>><"row"i>tp>',
            "oLanguage": {
                "sLengthMenu": "Rows to Show _MENU_"
            },
            "lengthMenu": [[25, 50, 75, -1], [25, 50, 75, "All"]],
            "pageLength": 25
        };

        $.extend( opts, options );
        return el.DataTable( opts );
    }
}
/***********
* Items Updater
************/
var itemsUpdater = {
    itemSearch: function(){

    },
    itemDelete: function(){
        $('a.delete')
            .css('cursor', 'pointer')
            .off('click')
            .click(function(e){
                $(this).closest('div.item_holder').remove();
                itemsUpdater.updateInputNames();
            });
    },
    updateInputNames: function(){
        $(":input.item-searcher").each(function(i,e){
            $(this).attr('name', 'items['+i+'][name]');
            //console.log( 'has class: '+$(this).closest('div.item_holder').find('.qty-holder').hasClass('col-sm-4') );
            $(this).closest('div.item_holder').find('input.item_qty').attr('name', 'items['+i+'][qty]');
            $(this).closest('div.item_holder').find('select.pallet_qty').attr('name', 'items['+i+'][qty]');
            $(this).closest('div.item_holder').find('input.item_id').attr('name', 'items['+i+'][id]');
            //adjust the validation
            itemsUpdater.updateValidation();
        });
    },
    updateValidation: function(){
        $( "input.item_qty, select.pallet_qty" ).each(function(i,e){
            $(this).rules( "remove");
        });
        if($(".item_qty").length)
        {
            $.validator.addClassRules('item_qty',{
                required: function(el){
                    var $holder = $(el).closest('div.item_holder');
                    var val = $holder.find('select.pallet_qty').val();
                    //console.log('pallet_qty val: '+ val);
                    return (val === 0 || val === undefined );
                },
                digits: true
            });
        }
        if($(".pallet_qty").length)
        {
            $.validator.addClassRules('pallet_qty',{
                notNone: true
            });
        }
        /*
        $('select.pallet_qty').each(function(i,e){
            $(this).off('change');
            $(this).change(function(e){
                //$(this).valid();
                var $holder = $(this).closest('div.item_holder');
                $holder.find('input.item_qty').valid();
            });
        });

        $('input.item_qty').each(function(i,e){
            $(this).off('change');
            $(this).change(function(e){
                $(this).valid();
            });
        });
        */
    }
}
/************
* Date pickers
************/
var datePicker = {
    betweenDates: function(noFuture){
        if(noFuture === undefined) {
            noFuture = false;
        }
        var from_opts = {
            changeMonth: true,
            changeYear: true,
            dateFormat: "dd/mm/yy",
            onSelect: function(selectedDate) {
                var d = new Date( selectedDate.replace( /(\d{2})[-/](\d{2})[-/](\d{4})/, "$2/$1/$3") );
                s = d.valueOf()/1000;
                $('#date_from_value').val(s);
                //set min date on other picker
                $("#date_to").datepicker("option", "minDate", selectedDate);
            }
        }
        if(noFuture)
        {
            from_opts['maxDate'] = 0;
        }
        $( "#date_from" ).datepicker(from_opts);
        var to_opts = {
            changeMonth: true,
            changeYear: true,
            dateFormat: "dd/mm/yy",
            onSelect: function(selectedDate) {
                var d = new Date( selectedDate.replace( /(\d{2})[-/](\d{2})[-/](\d{4})/, "$2/$1/$3") );
                s = (d.valueOf()/1000) + 60*60*24;
                $('#date_to_value').val(s);
                //set max date on other picker
                $("#date_from").datepicker("option", "maxDate", selectedDate);
            }
        }
        if(noFuture)
        {
            to_opts['maxDate'] = 0;
        }
        $( "#date_to" ).datepicker(to_opts);
        $('span.input-group-text').css('cursor', 'pointer').click(function(e) {
            $(this).closest('div.input-group').find('input.form-control').focus();
        });
    },
    fromDate: function(){
        $( "#date" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: "dd/mm/yy",
            onSelect: function(selectedDate) {
                var d = new Date( selectedDate.replace( /(\d{2})[-/](\d{2})[-/](\d{4})/, "$2/$1/$3") );
                s = d.valueOf()/1000;
                $('#date_value').val(s);
            }
        });
        $('i.fa-calendar-alt').css('cursor', 'pointer').click(function(e){
            $("#date").focus();
        });
    }
};
/************
* Shipping quote window
************/
var shippingQuote = {
    getQuotes: function(order_id, address_string)
    {
        if(address_string === undefined) {
            address_string = false;
        }
        //make the quote window
        $('<div id="quote_pop" title="Shipping Quotes">').appendTo($('body'));
        $("#quote_pop")
            .html("<p class='text-center'><img class='loading' src='/images/preloader.gif' alt='loading...' /><br />Fetching Quotes...</p>")
            .load('/ajaxfunctions/getShippingQuotes',{order_id: order_id, address_string: address_string},
				function(responseText, textStatus, XMLHttpRequest){
				if(textStatus == 'error') {
					$(this).html('<div class=\'errorbox\'><h2>There has been an error</h2><p>Please check the address details for issues</p><p></div>');
				}
                else
                {
                    //truckCost.getQuote();
                }
		});
		$("#quote_pop").dialog({
				draggable: false,
				modal: true,
				show: true,
				hide: true,
				autoOpen: false,
				height: 520,
				width: 620,
                close: function(){
                    $("#quote_pop").remove();
                },
                open: function(){
                    $('.ui-widget-overlay').bind('click',function(){
                        $('#quote_pop').dialog('close');
                    });

                }
		});
		$("#quote_pop").dialog('open');
    }
}

/************
* Some Autocompleters
************/
var autoCompleter = {
    suburbAutocomplete: function(element, selectCallback, changeCallback)
    {
        element.autocomplete({
            source: function(req, response){
            	var url = "/ajaxfunctions/getSuburbs?term="+req.term;
                //console.log(url);
            	$.getJSON(url, function(data){
            		response(data);
            	});
            },
            select: function(event, ui) {
                selectCallback(event, ui);
            },
            change: function (event, ui) {
                changeCallback(event, ui);
            },
            minLength: 2
        });
    },
    suburbAutoComplete: function(element, prefix)
    {
        if(prefix === undefined) {
            prefix = "";
        }
        element.autocomplete({
            source: function(req, response){
            	var url = "/ajaxfunctions/getSuburbs?term="+req.term;
                //console.log(url);
            	$.getJSON(url, function(data){
            		response(data);
            	});
            },
            select: function(event, ui) {
                $('#'+prefix+'state').val(ui.item.state).valid();
                $('#'+prefix+'suburb').val(ui.item.suburb).valid();
                $('#'+prefix+'postcode').val(ui.item.postcode).valid();
                $('#'+prefix+'country').val('AU').valid();
                return false;
            },
            change: function (event, ui) {
                return false;
            },
            minLength: 2
        });
    },
    suburbAutoCompleteSelect: function(element, prefix)
    {
        if(prefix === undefined) {
            prefix = "";
        }
        element.autocomplete({
            source: function(req, response){
            	var url = "/ajaxfunctions/getSuburbs?term="+req.term;
                //console.log(url);
            	$.getJSON(url, function(data){
            		response(data);
            	});
            },
            select: function(event, ui) {
                $('#'+prefix+'state').val(ui.item.state).change();
                console.log('suburb '+ui.item.suburb);
                $('#'+prefix+'suburb').val(ui.item.suburb);
                $('#'+prefix+'postcode').val(ui.item.postcode).change();
                $('#'+prefix+'country').val('AU');
                return false;
            },
            change: function (event, ui) {
                return false;
            },
            minLength: 2
        });
    },
    itemAutoComplete: function(element, selectCallback, changeCallback, check_available)
    {
        if(check_available === undefined) {
            check_available = true;
        }
        element.autocomplete({
            source: function(req, response){
                var url;
                if(check_available)
                {
                    url = "/ajaxfunctions/getItems/?item="+req.term+"&clientid="+$('#client_id').val()+"&checkavailable="+check_available;
                }
                else
                {
                   url = "/ajaxfunctions/getAllItems/?item="+req.term+"&clientid="+$('#client_id').val()+"&checkavailable="+check_available;
                }
                //console.log(url);
            	$.getJSON(url, function(data){
            		response(data);
            	});
            },
            select: function(event, ui) {
                selectCallback(event, ui);
            },
            change: function (event, ui) {
                changeCallback(event, ui);
            },
            minLength: 2
        });
    },
    productionJobCustomerAutoComplete: function(element, selectCallback, changeCallback)
    {
        element.autocomplete({
            source: function(req, response){
                var url;
                url = "/ajaxfunctions/getJobCustomer/?customer="+req.term;
                //console.log(url);
            	$.getJSON(url, function(data){
            		response(data);
            	});
            },
            select: function(event, ui) {
                selectCallback(event, ui);
            },
            change: function (event, ui) {
                changeCallback(event, ui);
            },
            minLength: 2
        });
    },
    productionJobFinisherAutoComplete: function(element, selectCallback, changeCallback)
    {
        element.autocomplete({
            source: function(req, response){
                var url;
                url = "/ajaxfunctions/getJobFinisher/?finisher="+req.term;
                //console.log(url);
            	$.getJSON(url, function(data){
            		response(data);
            	});
            },
            select: function(event, ui) {
                selectCallback(event, ui);
            },
            change: function (event, ui) {
                changeCallback(event, ui);
            },
            minLength: 2
        });
    },
    addressAutoComplete: function(element, prefix)
    {
        //console.log('element is '+element);
        //console.log('prefix is '+prefix);
        if(prefix === undefined) {
            prefix = "";
        }
        element.autocomplete({
            source: function(req, response){
                //console.log('term: '+req.term);
                var data = {
                    streetAddress: req.term,
                    formatCase: true,
                    apiKey: "b445fee0-4ffa-4ad4-84f3-050d0a170d10"
                }
                $.ajax({
                    url: "https://mappify.io/api/rpc/address/autocomplete",
                    type: 'POST',
                    dataType: 'json',
                    contentType: 'application/json',
                    processData: false,
                    data: JSON.stringify(data),
                    success: function (data) {
                      response($.map(data.result, function(item){
                        var returnData = {
                            label: item.streetAddress,
                            state: item.state,
                            postcode: item.postCode,
                            suburb: item.suburb
                        };
                        var address1 = '';
                        var address2 = '';
                        var address = '';
                        if(item.flatNumber)
                        {
                            address += "Unit "+item.flatNumber+", ";
                        }
                        if(item.levelNumber)
                        {
                            address += "Level "+item.levelNumber+", ";
                        }
                        address += item.numberFirst;
                        if(item.numberLast)
                        {
                            address += " - "+item.numberLast;
                        }
                        address += " "+item.streetName + " " + item.streetType;
                        if(item.buildingName)
                        {
                            address1 += item.buildingName;
                            address2 += address;
                        }
                        else
                        {
                            address1 += address;
                        }
                        returnData.value = address1;
                        returnData.address = address1;
                        returnData.address_2 = address2;
                        //console.log(returnData);
                        return returnData
                      }));
                    },
                    error: function(){
                      console.log("Cannot get data");
                    }
                });
            },
            select: function(event, ui) {
                $('#'+prefix+'address').val(ui.item.address).change();
                $('#'+prefix+'address2').val(ui.item.address_2).change();
                $('#'+prefix+'suburb').val(ui.item.suburb).change();
                $('#'+prefix+'state').val(ui.item.state).change();
                $('#'+prefix+'postcode').val(ui.item.postcode).change();
                $('#'+prefix+'country').val("AU").change();
                $(event.target).val(ui.item.address);
            },
            change: function (event, ui) {
                return false;
            },
            minLength: 5
        });

    }
};

/************
* Ajax
************/
var ajax = {
    send: function(url, postData, callback, spinnerBlock, loadMessage){

        var spinnerEle = null;

        $.ajax({
                url: config.root + url,
                type: "POST",
                data: helpers.appendCsrfToken(postData),
                dataType: "json",
                beforeSend: function() {
                    $(spinnerBlock).html("<p class='text-center'><img class='loading' src='/images/preloader.gif' alt='loading...' /><br />"+loadMessage+"</p>")
                }
            })
            .done(function(data) {
                callback(data);
            })
            .fail(function(jqXHR) {
                switch (jqXHR.status){
                    case 0:
                        callback(null);
                    case 302:
                        helpers.redirectTo(config.root);
                        break;
                    default:
                        helpers.displayErrorPage(jqXHR);
                }
            });
    },

    /**
     * Ajax call - ONLY for files.
     *
     * @param  string   url             URL to send ajax call
     * @param  object   fileData        data(formData) that will be sent to the server(PHP)
     * @param  function callback        Callback Function that will be called upon success or failure
     *
     */
    upload: function(url, fileData, callback){

        $.ajax({
            url: config.root + url,
            type: "POST",
            data: helpers.appendCsrfToken(fileData),
            dataType: "json",
            beforeSend: function () {
                // reset the progress bar
                $(".progress .progress-bar").css("width", "0%").html("0%");
            },
            xhr: function() {
                var myXhr = $.ajaxSettings.xhr();
                // check if upload property exists
                if(myXhr.upload){
                    myXhr.upload.addEventListener('progress', ajax.progressbar, false);
                    $(".progress").removeClass("display-none");
                }
                return myXhr;
            },
            contentType: false,
            cache: false,
            processData:false
        })
            .done(function(data) {
                callback(data);
            })
            .fail(function(jqXHR) {
                switch (jqXHR.status){
                    case 0:
                        callback(null);
                    case 302:
                        helpers.redirectTo(config.root);
                        break;
                    default:
                        helpers.displayErrorPage(jqXHR);
                }
            })
            .always(function() {
                $(".progress").addClass("display-none");
            });
    },
    progressbar: function(e){
        if(e.lengthComputable){
            var meter = parseInt((e.loaded/e.total) * 100);
            $(".progress .progress-bar").css("width", meter+"%").html(meter + "%");
        }
    },
    runSpinner: function(spinnerBlock, spinnerEle){

        if(!helpers.empty(spinnerBlock)) {
            // var spinner = $(spinnerBlock).nextAll(".spinner:eq(0)");
            $(spinnerEle).show();
            $(spinnerBlock).css("opacity","0.6");
        }
    },
    stopSpinner: function(spinnerBlock, spinnerEle){
        if(!helpers.empty(spinnerBlock) ) {
            // var spinner = $(spinnerBlock).nextAll(".spinner:eq(0)");
            $(spinnerEle).remove();
            $(spinnerBlock).css("opacity","1");
        }
    }
};

/*
 * Helpers
 *
 */

var helpers = {

    /**
     * append csrf token to data that will be sent in ajax
     *
     * @param  mixed  data
     *
     */
    appendCsrfToken: function (data){

        if(typeof (data) === "string"){
            if(data.length > 0){
                data = data + "&csrf_token=" + config.csrfToken;
            }else{
                data = data + "csrf_token=" + config.csrfToken;
            }
        }

        else if(data.constructor.name === "FormData"){
            data.append("csrf_token", config.csrfToken);
        }

        else if(typeof(data) === "object"){
            data.csrf_token = config.csrfToken;
        }

        return data;
    },

    /**
     * replaces the current page with error page returned from ajax
     *
     * @param  XMLHttpRequest  jqXHR
     * @see http://stackoverflow.com/questions/4387688/replace-current-page-with-ajax-content
     */
    displayErrorPage: function (jqXHR) {
        document.open();
        document.write(jqXHR.responseText);
        document.close();
    },

    /**
     * Extract keys from JavaScript object to be used as variables
     * @param  object  data
     */
    extract: function (data) {
        for (var key in data) {
            window[key] = data[key];
        }
    },

    /**
     * Checks if an element is empty(set to null or undefined)
     *
     * @param  mixed foo
     * @return boolean
     *
     */
    empty: function (foo){
        return (foo === null || typeof(foo) === "undefined")? true: false;
    },

    /**
     * extends $().html() in jQuery
     *
     * @param   string  target
     * @param   string  str
     */
    html: function (target, str){
        $(target).html(str);
    },

    /**
     * extends $().after() in jQuery
     *
     * @param   string  target
     * @param   string  str
     */
    after: function (target, str){
        $(target).after(str);
    },

    /**
     * clears all error and success messages
     *
     * @param   string  target
     */
    clearMessages: function (target){

        if(helpers.empty(target)){
            $(".error").remove();
            $(".success").remove();
        } else{
            // $(target).next(".error").remove();
            // $(target).next(".success").remove();
            $(target).nextAll(".error:eq(0)").remove();
            $(target).nextAll(".success:eq(0)").remove();
        }
    },

    /**
     * Extend the serialize() function in jQuery.
     * This function is designed to add extra data(name => value) to the form.
     *
     * @param   object  ele     Form element
     * @param   string  str     String to be appended to the form data.
     * @return  string          The serialized form data in form of: "name=value&name=value"
     *
     */
    serialize: function (ele, str){
        if(helpers.empty(str)){
            return $(ele).serialize();
        } else {
            return $(ele).serialize()  + "&" + str;
        }
    },

    /**
     * This function is used to redirect.
     *
     * @param string location
     */
    redirectTo: function (location){
        window.location.href = location;
    },

    /**
     * encode potential text
     * All encoding are done and must be done on the server side,
     * but you can use this function in case it's needed on client.
     *
     * @param string  str
     */
    encodeHTML: function (str){
        return $('<div />').text(str).html();
    },

    /**
     * validate form file size
     * It's important to validate file size on client-side to avoid overflow in $_POST & $_FILES
     *
     * @param   string form  form element
     * @param   string id    id of the file input element
     * @see     app/core/Request/dataSizeOverflow()
     */
    validateFileSize: function (fileId){

        var size = document.getElementById(fileId).files[0].size;
        return size < config.fileSizeOverflow;
    },

    /**
     * display error message
     *
     * @param  string  targetBlock  The target block where the error or success alerts will be inserted
     * @param  string  message      error message
     *
     */
    displayError: function (targetBlock, message){

        // 1. clear
        helpers.clearMessages(targetBlock);

        // 2. display
        var alert    = $("<div>").addClass("alert alert-danger");
        var notation = $("<i>").addClass("fa fa-exclamation-circle");
        alert.append(notation);

        message =  helpers.empty(message)? "Sorry there was a problem": message;
        alert.append(" " + message);

        var error = $("<div>").addClass("errorbox").html(alert);
        $(targetBlock).after(error);
    }

};
