
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
                    },
                    addContact: function(){
                        $("a.add-contact").click(function(e){
                            e.preventDefault();
                            var contact_count = $("div#contacts_holder div.acontact").length;
                            //console.log('packages: '+contact_count);
                            var data = {
                                i: contact_count
                            }
                            $.post('/ajaxfunctions/addFinisherContact', data, function(d){
                                $('div#contacts_holder').append(d.html);
                            });
                        });
                        $("a#remove-all-contacts").click(function(e){
                            e.preventDefault();
                            $('div#contacts_holder div.acontact').not(':first').remove();
                        });
                    }
                },
                'add-finisher':{
                    init: function(){
                        actions.common.init();
                        actions.common.addContact();
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
                        actions.common.addContact();
                        $('form#edit_production_finisher').submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Updating Details...</h2></div>' });
                            }
                        });
                    }
                },
                'view-finisher':{
                    init: function(){
                        $('button#print').click(function(e){
                        	$("div#print_this").printArea({
                                    //put some options in
                            });;
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
                                        $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Deleting Finishers...</h1></div>' });
                                        var data = {finisherids: ids};
                                        $.post('/ajaxfunctions/delete-finishers', data, function(d){
                                            location.reload();
                                        });
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