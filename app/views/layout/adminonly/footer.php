
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                common: {
                    init: function(){

                    }
                },
                'api-tester' :{
                    init: function()
                    {

                    }
                },
                'reece-data-tidy' :{
                    init: function()
                    {

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