// JavaScript Document
$(document).ready(function() {
	////////////////////////////////////////////////////////////
	//extra methods
	$.validator.addMethod("wordCount",
   			function(value, element, params) {
      			var typedWords = jQuery.trim(value).split(' ').length;
      			if(typedWords <= params[0]) {
         			return true;
      			}
   			},
   			$.validator.format("Only {0} words allowed.")
	);

	$.validator.addMethod('notNone', function(value, element) {
            return (value != '0');
    }, 'Please make a selection');
	
	$.validator.addMethod('pageNames', function(value, element){
			return (!/[^a-z0-9-]/.test(value));
	});

    $.validator.addMethod('ozPostcodes', function(value, element){
			return (/^(0[289][0-9]{2})|([1345689][0-9]{3})|(2[0-8][0-9]{2})|(290[0-9])|(291[0-4])|(7[0-4][0-9]{2})|(7[8-9][0-9]{2})$/.test(value));
	});

    $.validator.addMethod("currency", function (value, element) {
            return (this.optional(element) || /^(\d{1,3}(\,\d{3})*|(\d+))(\.\d{2})?$/.test(value) );
    });

    $.validator.addMethod('positiveNumber', function (value, element) {
        	return (this.optional(element) || Number(value) > 0 );
    }, 'Enter a positive number.');

    $.validator.addMethod('positiveNumber0', function (value, element) {
        	return (this.optional(element) || Number(value) >= 0 );
    }, 'Enter a positive whole number or zero.');

    $.validator.addMethod('wholePallets', function (value, element){
            var item_id = $(element).data('itemid');
            if( $("#pallet_"+item_id).is(':checked') )
            {
                var pallet_count = $("#pallet_size_"+item_id).val();
                return( (value % pallet_count) == 0 );
            }
            else
            {
                return true;
            }
    }, 'Cannot make whole pallets from this number');

    $.validator.addMethod("pickChecker", function(value, element) {
        if(value)
        {
            //return $(element).val() === $(element).parent().parent().find("input[name='thing1']").val();
            //console.log('pickcheck: '+$(element).data('pickcheck'));
            return parseInt($(element).val()) === parseInt($(element).data('pickcheck'));
        }
        else
        {
            return true;
        }
    }, 'Pick count is wrong');

    $.validator.addMethod("noDuplicates", function(value, element) {
        var matches  =  new Array();
        $('input.unique').each(function(index, item) {
            if (value == $(item).val()) {
                matches.push(item);
            }
        });
        return matches.length == 1;
    }, "Duplicate input detected.");

    //$.validator.addMethod("uniqueUserRole", $.validator.methods.remote, "User Role names need to be unique");

	////////////////////////////////////////////////////////////
	//Validator default
    //console.log('validator loaded');
    $.validator.setDefaults({
        //errorElement: "p",
        errorElement: "em",
        invalidHandler: function(form, validator) {
            var errors = validator.numberOfInvalids();
            if (errors) {
                validator.errorList[0].element.focus();
            }
        },
        highlight: function ( element, errorClass, validClass ) {
        	$( element ).parents( ".form-group" ).addClass( "has-error" ).removeClass( "has-success" );
        },
        unhighlight: function (element, errorClass, validClass) {
        	$( element ).parents( ".form-group" ).addClass( "has-success" ).removeClass( "has-error" );
        },
        errorPlacement: function ( error, element ) {
        	// Add the `text-danger` class to the error element
            //console.log(element.prop( "type" ))
        	error.addClass( "text-danger" );
            //error.addClass("font-italic");
        	if ( (element.prop( "type" ) === "checkbox")  ) {
        		error.insertAfter( element.parent().find( "label" ) );
        	}
            else if( (element.prop( "type" ) === "radio") ) {
                error.insertAfter( element.parent().parent().parent() );
            }
            else if( element.prop( "type" ) === "select-one" ) {
                error.insertAfter( element.closest( "div.bootstrap-select" ) );
            }
            else if ( element.parent().hasClass('input-group')){
                error.insertAfter( element.next( "div.input-group-append" ) );
            }
            else {
        		error.insertAfter( element );
        	}
        }
    });

	//Validators
    ///////////////////////////////////////////////////////////////////////////////
    $("form#print_runsheet").validate({
        ignore: {
            task: true
        },
        rules: {
            driver_id:{
                notNone: true
            },
            task: {
               required: function (element) {
                    var boxes = $('.task');
                    if (boxes.filter(':checked').length == 0) {
                        return true;
                    }
                    return false;
                }
            }
        },
        messages: {
            task: {
                required: "Please select at least one job or order to add to the runsheet."
            },
            driver_id:{
                notNone: "A driver is required"
            }
        }
	});
    ///////////////////////////////////////////////////////////////////////////////
    $('form#add_production_job, form#job_details_update').validate({
         rules:{
    		status_id:{
    			notNone: true
    		},
            job_id: {
				remote: {
                    url: '/ajaxfunctions/checkJobIds',
                    data: { 'current_jobid': function(){ return $("#current_jobid").val(); } }
                }
			}
    	},
		messages:{
			status_id:{
				notNone: "A status must be chosen"
			},
            job_id: {
				remote: 'This SKU is already in use. SKUs must be unique'
			}
		}
    });
    ///////////////////////////////////////////////////////////////////////////////
    $('form#get_quotes').validate({
         rules:{
    		state:{
    			notNone: true
    		}
    	},
		messages:{
			state:{
				notNone: "A state must be chosen"
			}
        }
    });
    ///////////////////////////////////////////////////////////////////////////////
    $('form#add_production_finisher').validate();
    ///////////////////////////////////////////////////////////////////////////////
    $('form#add_production_customer').validate();
    ///////////////////////////////////////////////////////////////////////////////
    $('form#edit_production_finisher, form#finisher_details_update').validate();
    ///////////////////////////////////////////////////////////////////////////////
    $('form#edit_production_customer, form#customer_details_update').validate();
    ///////////////////////////////////////////////////////////////////////////////
    $("form#add-job-status").validate({
    	rules:{
    		name: {
				remote: {
                    url: '/ajaxfunctions/checkJobStatusNames'
                }
			}
    	},
		messages:{
			name: {
				remote: 'This name is already in use.<br/>Status names must be unique'
			}
		}
    });
    ///////////////////////////////////////////////////////////////////////////////
	$("#register_new_stock").validate({
    	rules:{
    		sku: {
				remote: {
                    url: '/ajaxfunctions/checkSkus'
                }
			}
    	},
		messages:{
			sku: {
				remote: 'This SKU is already in use. SKUs must be unique'
			}
		}
	});
    ///////////////////////////////////////////////////////////////////////////////
    $("#make_pack_items").validate({
         rules:{
    		make_to_location:{
    			notNone: true
    		}
    	},
		messages:{
			make_to_location:{
				notNone: "A location must be chosen"
			},

		}
	});
    ///////////////////////////////////////////////////////////////////////////////
    $("#move_all_client_stock").validate({
         rules:{
    		move_to_location:{
    			notNone: true
    		}
    	},
		messages:{
			move_to_location:{
				notNone: "A location must be chosen"
			},

		}
	});
    ///////////////////////////////////////////////////////////////////////////////
    $("#container_unloading").validate({
        rules:{
            container_size:{
                notNone: true
            },
            client_id:{
                notNone: true
            },
            load_type:{
                notNone: true
            }
        },
        messages:{
            container_size:{
                notNone: "A container size is required"
            },
            client_id:{
                notNone: "A client is required"
            },
            load_type:{
                notNone: "A load type is required"
            }
        }
	});
    ///////////////////////////////////////////////////////////////////////////////
    $("#break_pack_items").validate({
        rules:{
            break_count:{
                max: function() {
                    return parseInt($('#available_packs').val());
                }
            },
            pi_location_id:{
                notNone: true
            }
        },
        messages:{
            break_count:{
                max: "You cannot break more than there are"
            }
        }

	});
	////////////////////////////////////////////////////////////
	$("#client_edit, #client_add").validate({
    	rules:{
    		client_logo:{
    			accept: "image/*"
    		},
    	},
		messages:{
			client_logo:{
				accept: "Only upload image files here"
			}
		}
	});
    ////////////////////////////////////////////////////////////
    $('form#form-login').validate({

    });
    ////////////////////////////////////////////////////////////
    $('form#df_collection').validate({
        rules:{
            carton_width:{
                required: function(e){
                    return $("#carton_count").val().length > 0;
                }
            },
            carton_length:{
                required: function(e){
                    return $("#carton_count").val().length > 0;
                }
            },carton_height:{
                required: function(e){
                    return $("#carton_count").val().length > 0;
                }
            },
            pallet_count:{
                require_from_group: [1, ".count"]
            },
            carton_count:{
                require_from_group: [1, ".count"]
            }
        }

    });
    ////////////////////////////////////////////////////////////
    $('form#address-update').validate({

    });
    ////////////////////////////////////////////////////////////
    $('form#client_daily_reports').validate({
        rules:{
            client_id:{
                notNone: true
            },
            "client_reports[]": {
                required: true
            }
        }
    });
    ////////////////////////////////////////////////////////////
    $('form#bulk-order-csv-upload').validate({
        rules:{
            client_id:{
                notNone: true
            },
            csv_file:{
    			extension: "csv",
                required: true
    		}
        },
		messages:{
			csv_file:{
				extension: "Only upload csv files here"
			}
		}
    });
    ////////////////////////////////////////////////////////////
    $('form#bulk_production_add, form#bulk_customer_add, form#bulk_finisher_add').validate({
        rules:{
            csv_file:{
    			extension: "csv",
                required: true
    		}
        },
		messages:{
			csv_file:{
				extension: "Only upload csv files here"
			}
		}
    });
    ///////////////////////////////////////////////////////////////////////////////
    $("#book-pickup").validate({
        rules:{
            pallets:{
                require_from_group: [1, ".counter"]
            },
            cartons:{
                require_from_group: [1, ".counter"]
            }
    	}
	});
    ///////////////////////////////////////////////////////////////////////////////
    $("#record-pickup").validate({
        rules:{
            pallets:{
                require_from_group: [1, ".counter"]
            },
            cartons:{
                require_from_group: [1, ".counter"]
            }
    	}
	});
    ////////////////////////////////////////////////////////////
    $('form#truck-usage').validate({
        rules:{
            client_id:{
                notNone: true
            }
        }
    });
    ////////////////////////////////////////////////////////////
    $('form#items-update').validate({
        rules:{
            'items[]':{
                wholePallets: true
            }
        }
    });
    ////////////////////////////////////////////////////////////
    $('form#goodsin, form#goodsout').validate({
        rules: {
            pallet_count: {
                require_from_group: [1, ".counter"]
            },
            carton_count: {
                require_from_group: [1, ".counter"]
            },
            satchel_count: {
                require_from_group: [1, ".counter"]
            }
        }
    });
    ////////////////////////////////////////////////////////////
    $('form#quality_control').validate({
        rules: {
            qty_add: {
                require_from_group: [1, ".number"]
            },
            qty_subtract: {
                require_from_group: [1, ".number"]
            }
        }
    });
    ////////////////////////////////////////////////////////////
    $('form#add_user').validate({
        rules:{
            role_id:{
                notNone: true
            }
        },
        messages:{
            role_id:{
                notNone: "Please select a role"
            }
        }
    });
    ////////////////////////////////////////////////////////////
    $('form#add_to_stock').validate({
        rules:{
            add_to_location:{
                notNone: true
            },
            reason_id:{
                notNone: true
            }
        },
        messages:{
            add_to_location:{
                notNone: "Please select a location"
            },
            reason_id:{
                notNone: "Please select a reason"
            }
        }
    });
    ////////////////////////////////////////////////////////////
    $('form#move_stock').validate({
        rules:{
            move_to_location:{
                notNone: true
            },
            move_from_location:{
                notNone: true
            }
        },
        messages:{
            move_to_location:{
                notNone: "Please select a location"
            },
            move_from_location:{
                notNone: "Please select a location"
            }
        }
    });
    ////////////////////////////////////////////////////////////
    $('form#subtract_from_stock').validate({
        rules:{
            subtract_from_location:{
                notNone: true
            },
            reason_id:{
                notNone: true
            }
        },
        messages:{
            subtract_from_location:{
                notNone: "Please select a location"
            },
            reason_id:{
                notNone: "Please select a reason"
            }
        }
    });
    ////////////////////////////////////////////////////////////
    $('form#profile_update').validate({
    	rules:{
    		image:{
    			accept: "image/*"
    		},
            conf_new_password:{
                equalTo: "#new_password"
            }
    	},
		messages:{
			image:{
				accept: "Only upload image files here"
			},
            conf_new_password:{
                equalTo: "This does not match. Please check"
            }
		}
    });
    ////////////////////////////////////////////////////////////
    $('form#add-sales-rep, form#edit-sales-rep').validate({
    	rules:{
    		client_id:{
    			notNone: true
    		}
    	},
		messages:{
			client_id:{
				notNone: "A client must be chosen"
			}
		}
    });
    ////////////////////////////////////////////////////////////
    $('form#add-store, form#edit-store').validate({
    	rules:{
    		chain_id:{
    			notNone: true
    		}
    	},
		messages:{
			chain_id:{
				notNone: "A chain must be chosen"
			}
		}
    });
    ////////////////////////////////////////////////////////////
    $('form#add_client_location').validate({
    	rules:{
    		location:{
    			notNone: true
    		},
            client_id:{
    			notNone: true
    		}
    	},
		messages:{
			location:{
    			notNone: 'A location must be selected'
    		},
            client_id:{
    			notNone: 'A client must be selected'
    		}
		}
    });
    /*///////////////////////////////////////////////////////////
    $('form#order_picking').validate({

    });
    *////////////////////////////////////////////////////////////
    $('form#add-packtype').validate({

    });
    ////////////////////////////////////////////////////////////
    $('form#bb_single_import').validate({

    });
    ////////////////////////////////////////////////////////////
    $('form#add-userrole').validate({
        rules:{
            name:{
                remote: {
                    url: '/ajaxfunctions/checkRoleNames'
                }
            }
        },
        messages:{
            name:{
                remote: 'User Role names must be unique'
            }
        }
    });
    ///////////////////////////////////////////////////////////////////////////////
    $("#add_location").validate({
        rules:{
            location:{
                remote:"/ajaxfunctions/checkLocations"
            }
        },
        messages:{
            location:{
                remote: "This location name is in use"
            }
        }
	});
    ////////////////////////////////////////////////////////////
    $('form.edit-userrole').validate({
        rules:{
            name:{
                remote: {
                    url: '/ajaxfunctions/checkRoleNames'
                }
            }
        },
        messages:{
            name:{
                remote: 'User Role names must be unique'
            }
        }
    });
    ////////////////////////////////////////////////////////////
    $('form#add_driver').validate({
        rules:{
            name:{
                remote: {
                    url: '/ajaxfunctions/checkDriverNames'
                },
                required: true
            }
        },
        messages:{
            name:{
                remote: '<p>This name is already in the system.<br>Driver names must be unique</p>'
            }
        }
    });
    ////////////////////////////////////////////////////////////
    $('form.edit_driver').each(function(i,e){
        $(this).validate();
    });
    $('input.driver_name').each(function(i,e){
        var line_id = $(e).prop('id').split('_').pop();
        $(this).rules('add',{
                required: true,
                remote: {
                    url: '/ajaxfunctions/checkDriverNames',
                    data: { current_name: function() { return $('input#current_name_'+line_id).val();}
                },
                messages:{
                    remote: '<p>This name is already in the system.<br>Driver names must be unique</p>',
                    required: 'A name is required'
                }
            }
        })
    })
    ////////////////////////////////////////////////////////////
    $('#add_origin_order').validate({
        rules:{
            roof_type:{
                required:true
            },
            team_id:{
                notNone:true
            }
        },
        messages:{
            roof_type:{
                required: "Please select a roof type"
            },
            team_id:{
                notNone: "Please select a team"
            }
        }
    });
    ////////////////////////////////////////////////////////////
    $('#add-solar-install').validate({
        rules:{
            team_id:{
                notNone:true
            },
            type_id:{
                notNone:true
            },
            inverter_qty:{
                min: 0,
                integer: true
            },
            panel_qty:{
                integer: true,
                min: 0
            }
        },
        messages:{
            team_id:{
                notNone: "Please select a team"
            },
            type_id:{
                notNone: "Please select an install type"
            },
            inverter_qty:{
                integer: "Only whole numbers greater than or equal to zero"
            },
            panel_qty:{
                integer: "Only whole numbers greater than or equal to zero"
            }
        }
    });
    ////////////////////////////////////////////////////////////
    $('#edit-solar-install').validate({
        rules:{
            team_id:{
                notNone:true
            },
            type_id:{
                notNone:true
            }
        },
        messages:{
            team_id:{
                notNone: "Please select a team"
            },
            type_id:{
                notNone: "Please select an install type"
            }
        }
    });
    ////////////////////////////////////////////////////////////
    $('#add-service-job').validate({
        rules:{
            team_id:{
                notNone:true
            },
            job_type:{
                notNone:true
            }
        },
        messages:{
            team_id:{
                notNone: "Please select a team"
            },
            job_type:{
                notNone: "Please select the job type"
            }
        }
    });
    ////////////////////////////////////////////////////////////
    $('form#transfer_location').validate({
        rules:{
            move_from_location:{
                notNone:true
            },
            move_to_location:{
                notNone:true
            }
        },
        messages:{
            move_from_location:{
                notNone: "Please select a location"
            },
            move_to_location:{
                notNone: "Please select a location"
            }
        }
    });
    ////////////////////////////////////////////////////////////
    $('form#form-forgot-password').validate({

    });
    ////////////////////////////////////////////////////////////
    $('form#pickup-update').validate({

    });
    ///////////////////////////////////////////////////////////////////////////////
	$("form#form-update-password").validate({
		rules: {
			confirm_password: {
				equalTo: "#password"
			}
		},
		messages: {
			confirm_password: {
				equalTo: "Passwords don't match."
			}
		}
	});
    ///////////////////////////////////////////////////////////////////////////////
	$("#add_product").validate({
    	rules:{
    		sku: {
				remote: {
                    url: '/ajaxfunctions/checkSkus'
                }
			},
            barcode: {
				remote: {
                    url: '/ajaxfunctions/checkBarcodes'
                }
			},
            box_barcode: {
				remote: {
                    url: '/ajaxfunctions/checkBoxBarcodes'
                }
			},
    		image:{
    			accept: "image/*"
    		},
			client_id:{
    			notNone: true
    		}
    	},
		messages:{
			sku: {
				remote: 'This SKU is already in use. SKUs must be unique'
			},
            barcode: {
				remote: 'This barcode is already in use. Barcodes must be unique'
			},
            box_barcode: {
				remote: 'This barcode is already in use. Barcodes must be unique'
			},
			image:{
				accept: "Only upload image files here"
			},
			client_id:{
				notNone: "A Client must be chosen"
			}
		}
	});
    ///////////////////////////////////////////////////////////////////////////////
	$("#edit_product").validate({
        rules:{
    		sku: {
				remote: {
                    url: '/ajaxfunctions/checkSkus',
                    data: { 'current_sku': function(){ return $("#current_sku").val(); } }
                }
			},
            barcode: {
				remote: {
                    url: '/ajaxfunctions/checkBarcodes',
                    data: { 'current_barcode': function(){ return $("#current_barcode").val(); } }
                }
			},
            box_barcode: {
				remote: {
                    url: '/ajaxfunctions/checkBoxBarcodes',
                    data: { 'current_barcode': function(){ return $("#current_box_barcode").val(); } }
                }
			},
    		image:{
    			accept: "image/*"
    		},
    	},
		messages:{
			sku: {
				remote: 'This SKU is already in use. SKUs must be unique'
			},
            barcode: {
				remote: 'This barcode is already in use. Barcodes must be unique'
			},
            box_barcode: {
				remote: 'This barcode is already in use. Barcodes must be unique'
			},
			image:{
				accept: "Only upload image files here"
			}
		}
	});
    ///////////////////////////////////////////////////////////////////////////////
    $("#bulk_order_import").validate({
    	rules:{
    		csv_file:{
    			extension: "csv",
                required: true
    		},
    	},
		messages:{
			csv_file:{
				extension: "Only upload csv files here"
			}
		}
	});
    ///////////////////////////////////////////////////////////////////////////////
    $("#hunters_invoice_check").validate({
    	rules:{
    		csv_file:{
    			extension: "csv",
                required: true
    		},
    	},
		messages:{
			csv_file:{
				extension: "Only upload csv files here"
			}
		}
	});
    ///////////////////////////////////////////////////////////////////////////////
    $("#add_order").validate({
    	rules:{
    		client_id:{
    			notNone: true
    		},
            'invoice[]':{
    			accept: "application/pdf"
    		}
    	},
		messages:{
			client_id:{
				notNone: "A client must be chosen"
			},
            'invoice[]':{
				accept: "Only upload pdf files here"
			}
		}
	});
    ///////////////////////////////////////////////////////////////////////////////
    $("#order-csv-upload").validate({
    	rules:{
    		csv_file:{
    			extension: "csv",
                required: true
    		},
    	},
		messages:{
			csv_file:{
				extension: "Only upload csv files here"
			}
		}
	});
    ///////////////////////////////////////////////////////////////////////////////
    $("#reece-department-upload").validate({
    	rules:{
    		csv_file:{
    			extension: "csv",
                required: true
    		},
    	},
		messages:{
			csv_file:{
				extension: "Only upload csv files here"
			}
		}
	});
    ///////////////////////////////////////////////////////////////////////////////
    $("#reece-user-upload").validate({
    	rules:{
    		csv_user_file:{
    			extension: "csv",
                required: true
    		},
    	},
		messages:{
			csv_user_file:{
				extension: "Only upload csv files here"
			}
		}
	});
    ///////////////////////////////////////////////////////////////////////////////
    $("#reece-supplied-data-upload-department").validate({
    	rules:{
    		reece_csv_file:{
    			extension: "csv",
                required: true
    		},
    	},
		messages:{
			reece_csv_file:{
				extension: "Only upload csv files here"
			}
		}
	});
    ///////////////////////////////////////////////////////////////////////////////
    $("#reece-supplied-data-upload-user").validate({
    	rules:{
    		reece_user_csv_file:{
    			extension: "csv",
                required: true
    		},
    	},
		messages:{
			reece_user_csv_file:{
				extension: "Only upload csv files here"
			}
		}
	});
    ///////////////////////////////////////////////////////////////////////////////
    $("#order-edit").validate({
    	rules:{
    		'invoice[]':{
    			accept: "application/pdf"
    		}
    	},
		messages:{
			'invoice[]':{
				accept: "Only upload pdf files here"
			}
		}
	});
});//end doc ready function