
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
                                    console.log(data);
                                    $.post('/ajaxfunctions/deactivateUser', data, function(d){
                                        $but.closest('p').html("<a class='btn btn-sm btn-outline-success reactivate' data-userid='"+thisuserid+"'>Reactivate User</a>");
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
                                        $but.closest('p').html("<a class='btn btn-sm btn-outline-danger deactivate' data-userid='"+thisuserid+"'>Deactivate User</a>");
                                        $.unblockUI();
                                        actions.common.userActivation();
                                    });
                                }
                            });
                        });
                    },
                    locationActivation: function(){
                        $("a.deactivate").off('click').click(function(e){
                            //console.log('click');
                            var $but = $(this);
                            var thislocationid = $but.data('locationid');
                            var data = {locationid: thislocationid};
                            swal({
                                title: "Deactivate Location?",
                                icon: "warning",
                                buttons: true,
                                dangerMode: true,
                            }).then( function(willDeactivate) {
                                if (willDeactivate) {
                                    $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Deactivating Location...</h1></div>' });
                                    //console.log(data);
                                    $.post('/ajaxfunctions/deactivateLocation', data, function(d){
                                        $but.closest('p').html("<a class='btn btn-success reactivate' data-locationid='"+thislocationid+"'>Reactivate Location</a>");
                                        $.unblockUI();
                                        actions.common.locationActivation();
                                    });
                                }
                            });
                        });
                        $("a.reactivate").off('click').click(function(e){
                            var $but = $(this);
                            var thislocationid = $but.data('locationid');
                            var data = {locationid: thislocationid};
                            swal({
                                title: "Reactivate Location?",
                                icon: "warning",
                                buttons: true,
                                dangerMode: true,
                            }).then( function(willReactivate) {
                                if (willReactivate) {
                                    $.blockUI({ message: '<div style="height:160px; padding-top:40px;"><h1>Reactivating Location...</h1></div>' });
                                    $.post('/ajaxfunctions/reactivateLocation', data, function(d){
                                        $but.closest('p').html("<a class='btn btn-danger deactivate' data-locationid='"+thislocationid+"'>Deactivate Location</a>");
                                        $.unblockUI();
                                        actions.common.locationActivation();
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
                'drivers':{
                    init: function(){
                        $('form#add_driver, form.edit_driver').submit(function(){
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
                        $('form#add-userrole').submit(function(){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Processing form...</h2></div>' });
                            }
                            else
                            {
                                return false;
                            }
                        });
                        $("form.edit-userrole").each(function(i,e){
                            $(this).submit(function(e){
                                if($(this).valid())
                                {
                                    $.blockUI({ message: '<div style="height:160px; padding-top:20px;"><h2>Editing User Role...</h2></div>' });
                                }
                                else
                                {
                                    return false;
                                }
                            });
                        })
                    }
                },
                'manage-users':{
                    init: function(){
                        $('input.user_search').on('keyup',function(e){
                            var search_type = this.id.replace("_search","");
                            //console.log('search_type: '+search_type);
                            var search_term = $(this).val().toLowerCase();
                            $("div."+search_type).each(function() {
                                if ($(this).html().toLowerCase().indexOf(search_term) != -1) {
                                    $(this).show();
                                }
                                else {
                                    $(this).hide();
                                }
                            });
                        });
                        actions.common.userActivation();
                    }
                },
                'warehouse-locations' : {
                    init: function(){
                        
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
                                actions.common.locationActivation();
                            }
                         } );

                        $('a.update').click(function(e){
                            e.preventDefault();
                            actions.locations.update.click(this);
                        });
                        actions.common.locationActivation();
                    },
                    'update':{
                        click: function(el){
                            var id = $(el).data('locationid');
                            var data = {
                                'id': id,
                                'location': $('#location_'+id).val(),
                                'current_location': $('#current_location_'+id).val(),
                                'multisku': $('#multisku_'+id).prop('checked'),
                                'tray': $('#trays_'+id).prop('checked'),
                                'oversize': $('#oversize_'+id).prop('checked')
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
                                    actions['stock-movement-reasons'].update.click(this);
                                });
                            }
                        } );
                        $("form#add-movementreason").submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Adding Reason...</h2></div>' });
                            }
                            else
                            {
                                return false;
                            }
                        });
                        $('a.update').click(function(e){
                            e.preventDefault();
                            actions['stock-movement-reasons'].update.click(this);
                        });
                    },
                    'update':{
                        click: function(el){
                            var id = $(el).data('reasonid');
                            //console.log('click - reason id: '+id);
                            //return;
                            var data = {
                                'id': id,
                                'reason': $('#name_'+id).val(),
                                'current_reason': $('#current_name_'+id).val(),
                                'active': $('#active_'+id).prop('checked'),
                                'locked': $('#locked_'+id).prop('checked')
                            };
                            //console.log(data);
                            $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Updating Reason...</h2></div>' });
                            $.post('/ajaxfunctions/updateStockMovementReason', data, function(d){
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
                'delivery-urgencies': {
                    init: function(){
                        dataTable.init($('table#view_urgencies_table') , {
                            "drawCallback": function( settings ) {
                                $('a.update_urgency').click(function(e){
                                    e.preventDefault();
                                    actions['delivery-urgencies'].update.click(this);
                                });
                            },
                            "searching": false,
                            "ordering": false,
                            "paging": false,
                            "order": [],
                        } );
                        $("form#add-urgency").submit(function(e){
                            if($(this).valid())
                            {
                                $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Adding Urgency...</h2></div>' });
                            }
                            else
                            {
                                return false;
                            }
                        });
                        $('a.update').click(function(e){
                            e.preventDefault();
                            actions['delivery-urgencies'].update.click(this);
                        });
                    },
                    'update':{
                        click: function(el){
                            var id = $(el).data('urgencyid');
                            //console.log('click - reason id: '+id);
                            //return;
                            var data = {
                                'id': id,
                                'name': $('#name_'+id).val(),
                                'cut_off': $('#cutoff_'+id).val(),
                                'current_name': $('#current_name_'+id).val(),
                                'charge_level': $('#charge_level_'+id).val(),
                                'active': $('#active_'+id).prop('checked'),
                                'locked': $('#locked_'+id).prop('checked')
                            };
                            //console.log(data);
                            $.blockUI({ message: '<div style="height:140px; padding-top:20px;"><h2>Updating Urgency...</h2></div>' });
                            $.post('/ajaxfunctions/updateDeliveryUrgency', data, function(d){
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
                                    //$.unblockUI();
                                }
                            });
                        }
                    }
                }
            }

            //run the script for the current page
            actions[config.curPage].init();
        </script>
        <?php Database::closeConnection(); ?>
    </body>
</html>