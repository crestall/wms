
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                common: {
                    init: function(){

                    }
                },
                '3pl-stock-movement-summary': {
                    init: function()
                    {

                    }
                },
                'solar-consumables-reorder':{
                    init: function()
                    {
                        dataTable.init($('table#consumables_reorder_table'), {
                            "columnDefs": [
                                { "orderable": false, "targets": [0] }
                            ],
                            "order": [],
                            fixedHeader: true
                        } );
                        $('button#csv_download').click(function(e) {
                            var data = {
                                csrf_token: config.csrfToken
                            }
                            var url = "/downloads/solarConsumablesReorderCSV";
                            fileDownload.download(url, data);
                        });
                    }
                },
                'client-stock-movement-summary': {
                    init: function()
                    {
                        dataTable.init($('table#client_stock_movement_summary_table'), {
                            fixedHeader: true
                        } );
                        datePicker.betweenDates();
                        $('button#change_dates').click(function(e){
                            e.preventDefault();
                            $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Collecting Orders...</h1></div>' });
                            var from = $('#date_from_value').val();
                            var to = $('#date_to_value').val();
                            window.location.href = "/reports/stock-movement-summary/from="+from+"/to="+to;
                        });
                        $('button#csv_download').click(function(e) {
                            var data = {
                                from: $('#date_from_value').val(),
                                to:   $('#date_to_value').val(),
                                client_id: $('#client_id').val(),
                                csrf_token: config.csrfToken
                            }
                            var url = "/downloads/clientStockSummaryCSV";
                            fileDownload.download(url, data);
                        });
                    }
                },
                '3pl-stock-movement-report': {
                    init: function()
                    {
                        $('select#client_selector').change(function(e){
                            if($(this).val() > 0)
                            {
                                var from = $('#date_from_value').val();
                                var to = $('#date_to_value').val();
                                var client_id = $(this).val();
                                var url = '/reports/stock-movement-report/client='+client_id;
                                if($('#date_from_value').val())
                                    url += "/from="+$('#date_from_value').val();
                                if($('#date_to_value').val())
                                    url += "/to="+$('#date_to_value').val();
                                window.location.href = url;
                            }
                        });
                        dataTable.init($('table#stock_movement_table'), {
                            "columnDefs": [
                                { "orderable": false, "targets": [0,3,8] }
                            ],
                            "order": [],
                            fixedHeader: true
                        } );
                        datePicker.betweenDates();
                        $('button#change_dates').click(function(e){
                            e.preventDefault();
                            $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Collecting Orders...</h1></div>' });
                            var from = $('#date_from_value').val();
                            var to = $('#date_to_value').val();
                            var client_id = $('select#client_selector').val();
                            window.location.href = "/reports/stock-movement-report/client="+client_id+"/from="+from+"/to="+to;
                        });
                        $('button#csv_download').click(function(e) {
                            var data = {
                                from: $('#date_from_value').val(),
                                to:   $('#date_to_value').val(),
                                client_id: $('#client_id').val(),
                                csrf_token: config.csrfToken
                            }
                            var url = "/downloads/stockMovementCSV";
                            fileDownload.download(url, data);
                        });
                    }
                },
                'client-stock-movement-report': {
                    init: function()
                    {
                        dataTable.init($('table#client_stock_movement_table'), {
                            "columnDefs": [
                                { "orderable": false, "targets": [0,3,8] }
                            ],
                            "order": [],
                            fixedHeader: true
                        } );
                        datePicker.betweenDates();
                        $('button#change_dates').click(function(e){
                            e.preventDefault();
                            $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Collecting Orders...</h1></div>' });
                            var from = $('#date_from_value').val();
                            var to = $('#date_to_value').val();
                            window.location.href = "/reports/stock-movement-report/from="+from+"/to="+to;
                        });
                        $('button#csv_download').click(function(e) {
                            var data = {
                                from: $('#date_from_value').val(),
                                to:   $('#date_to_value').val(),
                                client_id: $('#client_id').val(),
                                csrf_token: config.csrfToken
                            }
                            var url = "/downloads/clientStockMovementCSV";
                            fileDownload.download(url, data);
                        });
                    }
                },
                'swatches-report':{
                    init: function()
                    {
                        $('select#client_selector').change(function(e){
                            if($(this).val() > 0)
                            {
                                var client_id = $(this).val();
                                var url = '/reports/inventory-report/client='+client_id;
                                window.location.href = url;
                            }
                        });
                        dataTable.init($('table#swatch_report_table'), {
                            "columnDefs": [
                                { "orderable": false, "targets": [6] }
                            ],
                            "order": [],
                            fixedHeader: true
                        } );
                    }
                },
                'inventory-report':{
                    init: function()
                    {
                        $('select#client_selector').change(function(e){
                            if($(this).val() > 0)
                            {
                                var client_id = $(this).val();
                                var url = '/reports/inventory-report/client='+client_id;
                                window.location.href = url;
                            }
                        });
                        dataTable.init($('table#inventory_report_table'), {
                            "columnDefs": [
                                { "orderable": false, "targets": [6] }
                            ],
                            "order": [],
                            fixedHeader: true
                        } );
                        $('button#csv_download').click(function(e) {
                            var data = {
                                client_id: $('#client_id').val(),
                                csrf_token: config.csrfToken
                            }
                            var url = "/downloads/inventoryReportCSV";
                            fileDownload.download(url, data);
                        });
                    }
                },
                '3pl-dispatch-report': {
                    init: function()
                    {
                        $('select#client_selector').change(function(e){
                            if($(this).val() > 0)
                            {
                                var from = $('#date_from_value').val();
                                var to = $('#date_to_value').val();
                                var client_id = $(this).val();
                                var url = '/reports/dispatch-report/client='+client_id;
                                if($('#date_from_value').val())
                                    url += "/from="+$('#date_from_value').val();
                                if($('#date_to_value').val())
                                    url += "/from="+$('#date_to_value').val();
                                window.location.href = url;
                            }
                        });

                        dataTable.init($('table#client_dispatch_table'), {
                            "columnDefs": [
                                { "orderable": false, "targets": [0,2,5,10,11,12] }
                            ],
                            "order": [],
                            fixedHeader: true
                        } );
                        datePicker.betweenDates();
                        $('button#change_dates').click(function(e){
                            e.preventDefault();
                            $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Collecting Orders...</h1></div>' });
                            var from = $('#date_from_value').val();
                            var to = $('#date_to_value').val();
                            var client_id = $('#client_id').val();
                            var url = '/reports/dispatch-report/client='+client_id+"/from="+from+"/to="+to;
                            window.location.href = url;
                        });
                        $('button#csv_download').click(function(e) {
                            var data = {
                                from: $('#date_from_value').val(),
                                to:   $('#date_to_value').val(),
                                client_id: $('#client_id').val(),
                                csrf_token: config.csrfToken
                            }
                            var url = "/downloads/dispatchReportCSV";
                            fileDownload.download(url, data);
                        });
                        $('button#comment_update').click(function(e) {
                            $('#block_message')
                                .css({height: 160, paddingTop:40})
                                .html("<h1>Updating Comments...</h1>");
                            $.blockUI({ message: $('#block_message') });
                            var updates = {};
                            $('textarea.3pl_comments').each(function(index, element)
                            {
                                if($(this).val() != "")
                                {
                                    var order_id = $(this).data('orderid');
                                    var comment = $(this).val();
                                    console.log('order_id: '+order_id);
                                    updates[order_id] = comment;
                                }
                            });
                             $.ajax({
                                url: '/ajaxfunctions/updateOrderComments',
                                method: 'post',
                                data: {updates: updates},
                                success: function(d){
                                    $('#block_message').html("<h1>All comments updated</h1>");
                                    setTimeout(function () {
                                       $.unblockUI();
                                    }, 2000);
                                }
                            });
                            return false;
                        });
                    }
                },
                '3pl-order-serials-report': {
                    init: function()
                    {
                        $('select#client_selector').change(function(e){
                            if($(this).val() > 0)
                            {
                                var from = $('#date_from_value').val();
                                var to = $('#date_to_value').val();
                                var client_id = $(this).val();
                                var url = '/reports/order-serial-numbers-report/client='+client_id;
                                if($('#date_from_value').val())
                                    url += "/from="+$('#date_from_value').val();
                                if($('#date_to_value').val())
                                    url += "/from="+$('#date_to_value').val();
                                window.location.href = url;
                            }
                        });

                        dataTable.init($('table#client_dispatch_table'), {
                            "columnDefs": [
                                { "orderable": false, "targets": [0,2,5,6,7] }
                            ],
                            "order": [],
                            fixedHeader: true
                        } );
                        datePicker.betweenDates();
                        $('button#change_dates').click(function(e){
                            e.preventDefault();
                            $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Collecting Orders...</h1></div>' });
                            var from = $('#date_from_value').val();
                            var to = $('#date_to_value').val();
                            var client_id = $('#client_id').val();
                            var url = '/reports/order-serial-numbers-report/client='+client_id+"/from="+from+"/to="+to;
                            window.location.href = url;
                        });
                        $('button#csv_download').click(function(e) {
                            var data = {
                                from: $('#date_from_value').val(),
                                to:   $('#date_to_value').val(),
                                client_id: $('#client_id').val(),
                                csrf_token: config.csrfToken
                            }
                            var url = "/downloads/dispatchReportCSV";
                            fileDownload.download(url, data);
                        });
                    }
                },
                'pickups-report': {
                    init: function()
                    {
                        $('select#client_selector').change(function(e){
                            if($(this).val() > 0)
                            {
                                var from = $('#date_from_value').val();
                                var to = $('#date_to_value').val();
                                var client_id = $(this).val();
                                var url = '/reports/pickups-report/client='+client_id;
                                if($('#date_from_value').val())
                                    url += "/from="+$('#date_from_value').val();
                                if($('#date_to_value').val())
                                    url += "/from="+$('#date_to_value').val();
                                window.location.href = url;
                            }
                        });
                        datePicker.betweenDates();
                        $('button#change_dates').click(function(e){
                            e.preventDefault();
                            $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Collecting Orders...</h1></div>' });
                            var from = $('#date_from_value').val();
                            var to = $('#date_to_value').val();
                            var client_id = $('#client_id').val();
                            var url = '/reports/pickups-report/client='+client_id+"/from="+from+"/to="+to;
                            window.location.href = url;
                        });
                        /*
                        dataTable.init($('table#client_dispatch_table'), {
                            "columnDefs": [
                                { "orderable": false, "targets": [0,2,5,10,11,12] }
                            ],
                            "order": [],
                            fixedHeader: true
                        } );
                        $('button#csv_download').click(function(e) {
                            var data = {
                                from: $('#date_from_value').val(),
                                to:   $('#date_to_value').val(),
                                client_id: $('#client_id').val(),
                                csrf_token: config.csrfToken
                            }
                            var url = "/downloads/dispatchReportCSV";
                            fileDownload.download(url, data);
                        });
                        $('button#comment_update').click(function(e) {
                            $('#block_message')
                                .css({height: 160, paddingTop:40})
                                .html("<h1>Updating Comments...</h1>");
                            $.blockUI({ message: $('#block_message') });
                            var updates = {};
                            $('textarea.3pl_comments').each(function(index, element)
                            {
                                if($(this).val() != "")
                                {
                                    var order_id = $(this).data('orderid');
                                    var comment = $(this).val();
                                    console.log('order_id: '+order_id);
                                    updates[order_id] = comment;
                                }
                            });
                             $.ajax({
                                url: '/ajaxfunctions/updateOrderComments',
                                method: 'post',
                                data: {updates: updates},
                                success: function(d){
                                    $('#block_message').html("<h1>All comments updated</h1>");
                                    setTimeout(function () {
                                       $.unblockUI();
                                    }, 2000);
                                }
                            });
                            return false;
                        });
                        */
                    }
                },
                'client-dispatch-report': {
                    init: function()
                    {
                        dataTable.init($('table#client_dispatch_table'), {
                            "columnDefs": [
                                { "orderable": false, "targets": [0,2,5,6] }
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
                            window.location.href = "/reports/dispatch-report/from="+from+"/to="+to;
                        });
                        $('button#csv_download').click(function(e) {
                            var data = {
                                from: $('#date_from_value').val(),
                                to:   $('#date_to_value').val(),
                                client_id: $('#client_id').val(),
                                csrf_token: config.csrfToken
                            }
                            var url = "/downloads/clientDispatchReportCSV";
                            fileDownload.download(url, data);
                        });
                    }
                },
                'returns-report': {
                    init: function()
                    {
                        dataTable.init($('table#returns_table'), {
                            "columnDefs": [
                                { "orderable": false, "targets": [0,4,5] }
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
                            window.location.href = "/reports/returns-report/from="+from+"/to="+to;
                        });
                        $('button#csv_download').click(function(e) {
                            var data = {
                                from: $('#date_from_value').val(),
                                to:   $('#date_to_value').val(),
                                client_id: $('#client_id').val(),
                                csrf_token: config.csrfToken
                            }
                            var url = "/downloads/returnsReportCSV";
                            fileDownload.download(url, data);
                        });
                    }
                },
                'goods-in-report': {
                    init: function(){
                        dataTable.init($('table#goodsin_table'), {
                            "columnDefs": [
                                { "orderable": false, "targets": [0] }
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
                            window.location.href = "/reports/goods-in-report/from="+from+"/to="+to;
                        });
                        $('button#csv_download').click(function(e) {
                            var data = {
                                from: $('#date_from_value').val(),
                                to:   $('#date_to_value').val(),
                                csrf_token: config.csrfToken
                            }
                            var url = "/downloads/goodsInReportCSV";
                            fileDownload.download(url, data);
                        });
                    }
                },
                'goods-out-report': {
                    init: function(){
                        dataTable.init($('table#goodsout_table'), {
                            "columnDefs": [
                                { "orderable": false, "targets": [0] }
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
                            window.location.href = "/reports/goods-out-report/from="+from+"/to="+to;
                        });
                        $('button#csv_download').click(function(e) {
                            var data = {
                                from: $('#date_from_value').val(),
                                to:   $('#date_to_value').val(),
                                csrf_token: config.csrfToken
                            }
                            var url = "/downloads/goodsOutReportCSV";
                            fileDownload.download(url, data);
                        });
                    }
                },
                'truck-run-sheet': {
                    init: function(){
                        dataTable.init($('table#runsheet_table'), {
                            "columnDefs": [
                                { "orderable": false, "targets": [0] }
                            ],
                            "order": [],
                            fixedHeader: true
                        } );
                        datePicker.betweenDates();
                        $('button#change_dates').click(function(e){
                            e.preventDefault();
                            $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Collecting Orders...</h1></div>' });
                            var from = $('#date_from_value').val();
                            var to = $('#date_to_value').val();
                            window.location.href = "/reports/truck-run-sheet/from="+from+"/to="+to;
                        });
                        $('button#csv_download').click(function(e) {
                            var data = {
                                from: $('#date_from_value').val(),
                                to:   $('#date_to_value').val(),
                                csrf_token: config.csrfToken
                            }
                            var url = "/downloads/truckRunSheetCSV";
                            fileDownload.download(url, data);
                        });
                    }
                },
                'location-report' : {
                    init: function(){
                        dataTable.init($('table#location_report_table'), {
                            "order": [],
                            fixedHeader: true
                        } );
                        $('button#csv_download').click(function(e) {
                            var data = {
                                csrf_token: config.csrfToken
                            }
                            var url = "/downloads/locationReportCSV";
                            fileDownload.download(url, data);
                        });
                    }
                },
                'goods-in-summary' : {
                    init: function(){
                        datePicker.betweenDates(true);
                        dataTable.init($('table#goods_in_summary'), {
                            "order": [],
                            fixedHeader: true
                        } );
                        $('button#csv_download').click(function(e) {
                            var data = {
                                from: $('#date_from_value').val(),
                                to:   $('#date_to_value').val(),
                                csrf_token: config.csrfToken
                            }
                            var url = "/downloads/goodsinSummaryCSV";
                            fileDownload.download(url, data);
                        });
                        $('button#change_dates').click(function(e){
                            e.preventDefault();
                            $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Generating Summary...</h1></div>' });
                            var from = $('#date_from_value').val();
                            var to = $('#date_to_value').val();
                            window.location.href = "/reports/goods-in-summary/from="+from+"/to="+to;
                        });
                    }
                },
                'goods-out-summary' : {
                    init: function(){
                        datePicker.betweenDates(true);
                        dataTable.init($('table#goods_out_summary'), {
                            "order": [],
                            fixedHeader: true
                        } );
                        $('button#csv_download').click(function(e) {
                            var data = {
                                from: $('#date_from_value').val(),
                                to:   $('#date_to_value').val(),
                                csrf_token: config.csrfToken
                            }
                            var url = "/downloads/goodsoutSummaryCSV";
                            fileDownload.download(url, data);
                        });
                        $('button#change_dates').click(function(e){
                            e.preventDefault();
                            $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Generating Summary...</h1></div>' });
                            var from = $('#date_from_value').val();
                            var to = $('#date_to_value').val();
                            window.location.href = "/reports/goods-out-summary/from="+from+"/to="+to;
                        });
                    }
                },
                'unloaded-containers-report' : {
                    init: function(){
                        datePicker.betweenDates(true);
                        dataTable.init($('table#unloaded_containers'), {
                            "order": [],
                            fixedHeader: true
                        } );
                        $('button#csv_download').click(function(e) {
                            var data = {
                                from: $('#date_from_value').val(),
                                to:   $('#date_to_value').val(),
                                csrf_token: config.csrfToken
                            }
                            var url = "/downloads/unloadedContainersCSV";
                            fileDownload.download(url, data);
                        });
                        $('button#change_dates').click(function(e){
                            e.preventDefault();
                            $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Generating Summary...</h1></div>' });
                            var from = $('#date_from_value').val();
                            var to = $('#date_to_value').val();
                            window.location.href = "/reports/unloaded-containers-report/from="+from+"/to="+to;
                        });
                    }
                },
                'client-bay-usage' :{
                    init: function(){
                        datePicker.betweenDates(true);
                        $('button#change_dates').click(function(e){
                            e.preventDefault();
                            $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Collecting Data...</h1></div>' });
                            var from = $('#date_from_value').val();
                            var to = $('#date_to_value').val();
                            window.location.href = "/reports/client-space-usage-report/from="+from+"/to="+to;
                        });

                        $('button#csv_download').click(function(e) {
                            var data = {
                                from: $('#date_from_value').val(),
                                to:   $('#date_to_value').val(),
                                csrf_token: config.csrfToken
                            }
                            var url = "/downloads/clientBayUsageCSV";
                            fileDownload.download(url, data);
                        });
                    }
                },
                'empty-bay-report': {
                    init: function(){
                        dataTable.init($('table#emptybay_table'), {
                            "order": [],
                            fixedHeader: true
                        } );
                        $('button#csv_download').click(function(e) {
                            var data = {
                                csrf_token: config.csrfToken
                            }
                            var url = "/downloads/emptyBaysCSV";
                            fileDownload.download(url, data);
                        });
                    }
                },
                'stock-at-date' : {
                    init: function(){
                        datePicker.fromDate();
                        $('button#change_date').click(function(e){
                            e.preventDefault();
                            $.blockUI({ message: '<div style="height:120px; padding-top:40px;"><h1>Collecting Orders...</h1></div>' });
                            var date = $('#date_value').val();
                            var client_id = $('#client_id').val();
                            var url = '/reports/stock-at-date/date='+date;
                            if($('#show_client').length)
                            {
                                url += "/client="+client_id;
                            }
                            window.location.href = url;
                        });
                        $('select#client_selector').change(function(e){
                            if($(this).val() > 0)
                            {
                                var date = $('#date_value').val();
                                var client_id = $(this).val();
                                var url = '/reports/stock-at-date/client='+client_id;
                                if($('#date_value').length)
                                {
                                    url += "/date="+date;
                                }
                                window.location.href = url;
                            }
                        });
                        dataTable.init($('table#sad_table'), {
                            "order": [],
                            fixedHeader: true
                        } );
                        $('button#csv_download').click(function(e) {
                            var data = {
                                date:   $('#date_value').val(),
                                client_id: $('#client_id').val(),
                                csrf_token: config.csrfToken
                            }
                            var url = "/downloads/stockAtDateCSV";
                            fileDownload.download(url, data);
                        });
                    }
                },
                'client-daily-reports': {
                    init: function(){
                        $('select#client_id').change(function(e){
                            $(this).valid();
                        });
                        $('form#client_daily_reports').submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Sending Reports...</h2></div>' });
                            }
                        });
                    }
                }
            }

            //run the script for the current page
            actions[config.curPage].init();
        </script>
        <?php Database::closeConnection(); ?>
    </body>
</html>