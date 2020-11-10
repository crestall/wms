
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                common: {
                    init: function(){
                        autoCompleter.addressAutoComplete($('#address'));
                        autoCompleter.suburbAutoComplete($('#suburb'));
                    },
                    selectAll: function(){
                        $('#select_all').click(function(e){
                            var checked = this.checked;
                             $('.select').each(function(e){
                                this.checked =  checked;
                             })
                        });
                    }
                },
                'add-finisher':{
                    init: function(){
                        actions.common.init();
                        $('form#add_production_finisher').submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Adding Finisher...</h2></div>' });
                            }
                        });
                    }
                },
                'edit-finisher':{
                    init: function(){
                        actions.common.init();
                        $('form#edit_production_finisher').submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Updating Details...</h2></div>' });
                            }
                        });
                    }
                },
                'view-finishers':{
                    init: function(){
                        actions.common.selectAll();
                        dataTable.init($('table#finisher_list_table'), {
                            "order": []
                        } );
                        $('button#deactivate').click(function(e){
                            if(!$('input.select:checked').length)
                            {
                                swal({
                                    title: "No Finishers Selected",
                                    text: "Please select at least one finisher to delete",
                                    icon: "error"
                                });
                            }
                            else
                            {
                                swal({
                                    title: "Really Delete Finisher(s)?",
                                    text: "This cannot be undone without manually altering database values",
                                    icon: "warning",
                                    buttons: true,
                                    dangerMode: true
                                }).then( function(deactivateFinisher) {
                                    if(deactivateFinisher)
                                    {
                                        var ids = [];
                                        $('input.select').each(function(i,e){
                                            if($(this).prop('checked') )
                                            {
                                                var finisher_id = $(this).data('finisherid');
                                                ids.push(finisher_id);
                                            }
                                        });
                                        console.log('ids: '+ids);
                                    }
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