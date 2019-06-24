
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                common: {
                    init: function(){

                    },
                    userActivation: function(){
                        $("a.deactivate").off('click').click(function(e){
                            //console.log('click');
                            var $but = $(this);
                            var thisuserid = $but.data('userid');
                            var data = {userid: thisuserid};
                            swal({
                                title: "Deactivate User?",
                                icon: "warning",
                                buttons: true,
                                dangerMode: true,
                            }).then( function(willDeactivate) {
                                if (willDeactivate) {
                                    $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Deactivating User...</h1></div>' });
                                    //console.log(data);
                                    $.post('/ajaxfunctions/deactivateUser', data, function(d){
                                        $but.closest('p').html("<a class='btn btn-success reactivate' data-userid='"+thisuserid+"'>Reactivate User</a>");
                                        $.unblockUI();
                                        actions.common.userActivation();
                                    });
                                }
                            });
                        });

                        $("a.reactivate").off('click').click(function(e){
                            var $but = $(this);
                            var thisuserid = $but.data('userid');
                            var data = {userid: thisuserid};
                            swal({
                                title: "Reactivate User?",
                                icon: "warning",
                                buttons: true,
                                dangerMode: true,
                            }).then( function(willReactivate) {
                                if (willReactivate) {
                                    $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Reactivating User...</h1></div>' });
                                    $.post('/ajaxfunctions/reactivateUser', data, function(d){
                                        $but.closest('p').html("<a class='btn btn-danger deactivate' data-userid='"+thisuserid+"'>Deactivate User</a>");
                                        $.unblockUI();
                                        actions.common.userActivation();
                                    });
                                }
                            });
                        });
                    }
                },
                'packing-types': {
                    init: function()
                    {
                        $('form#add-packtype, form.edit-packtype').submit(function(){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Processing form...</h2></div>' });
                            }
                            else
                            {
                                return false;
                            }
                        });
                    }
                },
                'store-chains':{
                    init: function(){
                        $('form#add-storechain, form.edit-storechain').submit(function(){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Processing form...</h2></div>' });
                            }
                            else
                            {
                                return false;
                            }
                        });
                    }
                },
                'solar-order-types':{
                    init: function(){
                        $('form#add-solartype, form.edit-solartype').submit(function(){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Processing form...</h2></div>' });
                            }
                            else
                            {
                                return false;
                            }
                        });
                    }
                },
                'couriers':{
                    init: function(){
                        $('form#add-courier, form.edit-courier').submit(function(){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Processing form...</h2></div>' });
                            }
                            else
                            {
                                return false;
                            }
                        });
                    }
                },
                'user-roles':{
                    init: function(){
                        /*
                        $.validator.addClassRules("userrolename", {
                            uniqueUserRole : {
                                url: '/ajaxfunctions/checkRoleNames',
                                //data: { 'term': function(){ return $(this).val(); } }
                            }
                        });
                        */
                        $( "#sortable" ).sortable();
                        $('form#add-userrole, form.edit-userrole').submit(function(){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Processing form...</h2></div>' });
                            }
                            else
                            {
                                return false;
                            }
                        });
                    }
                },
                'manage-users':{
                    init: function(){
                        /*
                        $('a.toggle_roles').click(function(e){
                            $(this).toggleClass('hiding');
                        });
                        */
                        $('a.toggle_roles').each(function(i,e){
                            $(this).click(function(e){
                                $(this).toggleClass('hiding');
                            })
                        });
                        actions.common.userActivation();
                    }
                },
                'locations' : {
                    init: function(){
                        $("form#add_location").submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Adding Location...</h2></div>' });
                            }
                        });
                        dataTable.init($('table#view_locations_table') , {
                            "drawCallback": function( settings ) {
                                $('a.update').click(function(e){
                                    e.preventDefault();
                                    actions.locations.update.click(this);
                                });
                            }
                         } );

                        $('a.update').click(function(e){
                            e.preventDefault();
                            actions.locations.update.click(this);
                        });
                    },
                    'update':{
                        click: function(el){
                            var id = $(el).data('locationid');
                            var data = {
                                'id': id,
                                'location': $('#location_'+id).val(),
                                'current_location': $('#current_location_'+id).val(),
                                'multisku': $('#multisku_'+id).prop('checked'),
                                'tray': $('#trays_'+id).prop('checked')
                            };
                            //console.log(data);
                            $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Updating Location...</h2></div>' });
                            $.post('/ajaxfunctions/updateLocation', data, function(d){
                                $.unblockUI();
                                if(d.error)
                                {
                                    swal({
                                        title: 'Could not update',
                                        text: d.feedback,
                                        icon: "error"
                                    });
                                }
                                else
                                {
                                    $('span#updated_'+id).html('Updated');
                                    $('tr#row_'+id).addClass('updated').delay(3500).queue(function(next){
                                        $(this).removeClass('updated');
                                        $('span#updated_'+id).html('');
                        			});
                                    $.unblockUI();
                                }
                            });
                        }
                    }
                },
                'stock-movement-reasons': {
                    init: function(){
                        dataTable.init($('table#view_reasons_table') , {
                            "drawCallback": function( settings ) {
                                $('a.update').click(function(e){
                                    e.preventDefault();
                                    //actions.update.click(this);
                                });
                            }
                        } );
                        $("form#add-movementreason").submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Adding Reason...</h2></div>' });
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