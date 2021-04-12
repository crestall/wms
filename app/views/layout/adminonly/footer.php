
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                common: {
                    init: function(){

                    }
                },
                'data-tables-testing':{
                    init: function(){
                        $('#client_selector').change(function(e){
                            if($(this).val() > 0)
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Collecting Products...</h2></div>' });
                                window.location.href = "/admin-only/data-tables-testing/client=" + $(this).val();
                            }
                        });
                        var table = dataTable.init($('table#view_items_table'), {
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
                            },
                            "createdRow": function( row, data, dataIndex, cells ){
                                $( cells[0] ).attr("data-label", "Name");
                            }
                        } );

                        table.on( 'xhr', function () {
                            var json = table.ajax.json();
                            //alert( json.data.length +' row(s) were loaded' );
                            //console.log('json: ' + json.data);
                        } );
                    }
                },
                'production-database-tables-update':{
                    init: function(){

                    }
                },
                'api-tester' :{
                    init: function()
                    {

                    }
                }, 
                'ebay-api-testing' :{
                    init: function()
                    {

                    }
                },
                'runsheet-completion-tidy':{
                    init: function(){
                        $('form#runsheet_completion_updater').submit(function(){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Updating Runsheet Completion Status...</h2></div>' });
                            }
                        });
                    }
                },
                'reece-data-tidy' :{
                    init: function()
                    {
                        $('form#reece-department-upload, form#reece-user-upload').submit(function(){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Uploading and storing data...</h2></div>' });
                            }
                        });
                        $('form#reece-supplied-data-upload-department').submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Uploading and checking the data...</h2></div>' });
                                /*e.preventDefault(); // avoid to execute the actual submit of the form.
                                var form = $(this)[0];
                                var url = $(this).attr('action');
                                $.ajax({
                                       type: "POST",
                                       url: url,
                                       data: new FormData(form),
                                       processData: false,
                                       contentType: false,
                                       //enctype: 'multipart/form-data',
                                       success: function(d)
                                       {
                                           //alert(data); // show response from the php script.
                                           $.unblockUI();
                                       }
                                });*/
                            }
                        });
                    }
                },
                'add-sales-rep':{
                    init: function()
                    {
                        autoCompleter.addressAutoComplete($('#address'));
                        autoCompleter.suburbAutoComplete($('#suburb'));
                        $('form#add-sales-rep').submit(function(){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Processing form...</h2></div>' });
                            }
                        });
                    }
                },
                'edit-sales-rep':{
                    init: function()
                    {
                        autoCompleter.addressAutoComplete($('#address'));
                        autoCompleter.suburbAutoComplete($('#suburb'));
                        $('form#edit-sales-rep').submit(function(){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Processing form...</h2></div>' });
                            }
                        });
                    }
                },
                'view-reps':{
                    init: function(){
                        //console.log('correct');
                        $('table#view_reps_table').filterTable({
                            inputSelector: '#table_searcher'
                        });

                        $('table#view_reps_table').stickyTableHeaders();
                    }
                },
                'client-bay-fixer' :{
                    init: function(){
                        $("select#client_selector").change(function(e){
                            if($(this).val() > 0)
                            {
                                window.location.href = "/admin-only/client-bay-fixer/client="+$(this).val();
                            }
                        });
                    }
                },
                'encrypt-some-shit':{
                    init: function(){
                        $('form#string-encrypter').submit(function(){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Generating Encryption String...</h2></div>' });
                            }
                            else
                            {
                                return false;
                            }
                        });
                    }
                },
                'update-configuration':{
                    init: function(){
                        $('form#add-config-value').submit(function(){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Adding/Updating Value</h2></div>' });
                            }
                            else
                            {
                                return false;
                            }
                        });
                        $('button.delete').click(function(e){
                            e.preventDefault();
                            var $but = $(e.target);
                            swal({
                                title: "Really delete this value?",
                                text: "This cannot be undone",
                                icon: "warning",
                                buttons: true,
                                dangerMode: true,
                            }).then( function(willDelete) {
                                if (willDelete) {
                                    $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Deleting configuration value...</h1></div>' });
                                    $.post('/ajaxfunctions/deleteConfiguration', {id: $but.data('configurationid')}, function(d){
                                        window.location.reload();
                                    })
                                }
                            });
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