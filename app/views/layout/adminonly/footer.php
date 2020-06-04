
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                common: {
                    init: function(){

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
                                $.ajax({
                                    url: "/ajaxfunctions/encryptSomeShit",
                                    data: { string: $('input#string').val()},
                                    method: "post",
                                    //dataType: "json",
                                    beforeSend: function(){
                                        console.log('ajax');
                                        $("div#feedback_holder")
                                            .slideDown()
                                            .html("<p></p><p class='text-center'><img class='loading' src='/images/preloader.gif' alt='loading...' /><br />Generating Encryption String</p>");
                                    },
                                    success: function(d){
                                        if(d.error)
                                        {
                                            $("div#feedback_holder")
                                                .hide()
                                                .removeClass()
                                                .addClass("errorbox")
                                                .slideDown()
                                                .html("<h2><i class='far fa-times-circle'></i>There has been an error</h2><p>"+d.error_string+"</p>");
                                        }
                                        else
                                        {
                                            $("div#feedback_holder")
                                                .hide()
                                                .removeClass()
                                                .addClass("feedbackbox")
                                                .html("<h2><i class='far fa-check-circle'></i>Encryption Results</h2><p>"+$('input#string').val()+" : "+d.encryptedvalue+"</p>")
                                                .slideDown();
                                        }
                                    }
                                });
                            }
                            return false;
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