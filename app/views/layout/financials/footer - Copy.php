
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                common: {
                    init: function(){

                    }
                },
                'hunters-check': {
                    init: function(){

                        $('form#hunters_invoice_check').submit(function(e){
                            if($(this).valid())
                            {
                                //var form = $(this);
                                //var form = $('form')[0];
                                var formData = new FormData();
                                formData.append('header_row', $("#header_row").prop('checked'));
                                formData.append('csrf_token', config.csrfToken);
                                formData.append('csv_file', $('input[type=file]')[0].files[0]);

                                sessionStorage.setItem('data', formData);

                                sessionStorage.setItem('header_row', $("#header_row").prop('checked'));
                                sessionStorage.setItem('csrf_token', config.csrfToken);
                                sessionStorage.setItem('csv_file', $('input[type=file]')[0].files[0]);



                                $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Checking Invoice...</h2></div>' });
                            }
                        });
                        $('button#charge_update').click(function(e){
                            if($('input.update:checked').length)
                            {
                                swal({
                                    title: "Update these charges?",
                                    text: "This cannot be undone",
                                    icon: "warning",
                                    buttons: true,
                                    dangerMode: true,
                                }).then( function(willUpdate) {
                                    if (willUpdate) {
                                        var ids = [];
                                        $('input.update').each(function(i,e){
                                            if( $(this).prop('checked'))
                                            {
                                                ids.push($(this).data('orderid'));
                                            }
                                        });
                                        $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h1>Updating Charges...</h1></div>' });

                                        var data = {
                                            header_row: sessionStorage.getItem('header_row'),
                                            csrf_token: sessionStorage.getItem('csrf_token'),
                                            csv_file: sessionStorage.getItem('csv_file')
                                        }
                                        console.log(data);
                                        $.ajax({
                                            url: '/financials/proc-hunters-update',
                                            type: "post",
                                            data: data,
                                            contentType: false, // NEEDED, DON'T OMIT THIS (requires jQuery 1.6+)
                                            processData: false, // NEEDED, DON'T OMIT THIS
                                            success: function() {
                                                location.reload();
                                            }
                                        });
                                    }
                                });
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