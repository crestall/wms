
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
                'add-customer':{
                    init: function(){
                        actions.common.init();
                        actions.common.addContact();
                        $('form#add_production_customer').submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Adding Customer...</h2></div>' });
                            }
                        });
                    }
                },
                'view-customers':{
                    init: function(){
                        actions.common.selectAll();
                        dataTable.init($('table#customer_list_table'), {
                            "order": []
                        } );
                        $('button#deactivate').click(function(e){
                            if(!$('input.select:checked').length)
                            {
                                swal({
                                    title: "No Customers Selected",
                                    text: "Please select at least one customer to delete",
                                    icon: "error"
                                });
                            }
                            else
                            {
                                swal({
                                    title: "Really Delete Customers(s)?",
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
                                                var customer_id = $(this).data('customerid');
                                                ids.push(customer_id);
                                            }
                                        });
                                        console.log('ids: '+ids);
                                        $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Deleting Customers...</h1></div>' });
                                        var data = {customerids: ids};
                                        $.post('/ajaxfunctions/delete-customers', data, function(d){
                                            location.reload();
                                        });
                                    }
                                });
                            }
                        });
                    }
                },
                'view-customer':{
                    init: function(){
                        $('button#print').click(function(e){
                        	$("div#print_this").printArea({
                                    //put some options in
                            });;
                        });
                    }
                },
                'edit-customer':{
                    init: function(){
                        actions.common.init();
                        actions.common.addContact();
                        $('form#edit_production_customer').submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Updating Customer...</h2></div>' });
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