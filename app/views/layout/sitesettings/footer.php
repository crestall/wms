
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                common: {
                    init: function(){

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
                                    actions.update.click(this);
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
                    }
                }
            }

            //run the script for the current page
            actions[config.curPage].init();
        </script>
        <?php Database::closeConnection(); ?>
    </body>
</html>