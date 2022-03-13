jQuery(document).ready(function($){
	jQuery.validator.addMethod("codfis", function(value, element) {
		return this.optional(element) || CodiceFiscale.check(value);
	}, "Il codice fiscale inserito non è valido. Per favore ricontrollare");

	$('#isc_datanascita').datepicker({
		dateFormat: 'dd/mm/yy'
	});

	$('#form-preiscrizione').validate({
		errorClass: 'has-error',
		errorElement: 'small',
		highlight: function(element, errorClass) {
			if ( !$(element).is(':checkbox') ) $(element).parent().addClass(errorClass);
			else $(element).parent().parent().addClass(errorClass);
		},
		unhighlight: function(element, errorClass) {
			if ( !$(element).is(':checkbox') ) $(element).parent().removeClass(errorClass);
			else $(element).parent().parent().removeClass(errorClass);
		},
		errorPlacement: function(error, element) {
		    if ( !element.is(':checkbox') ) error.appendTo( element.parent() );
		    else error.insertAfter( element );
		}
	});

	$('#form-preiscrizione-speciale').validate({
		errorClass: 'has-error',
		errorElement: 'small',
		rules: {
			isp_codfis: {
				codfis: true,
				required: true
			}
		},
		highlight: function(element, errorClass) {
			if ( !$(element).is(':checkbox') ) $(element).parent().addClass(errorClass);
			else $(element).parent().parent().addClass(errorClass);
		},
		unhighlight: function(element, errorClass) {
			if ( !$(element).is(':checkbox') ) $(element).parent().removeClass(errorClass);
			else $(element).parent().parent().removeClass(errorClass);
		},
		errorPlacement: function(error, element) {
		    if ( !element.is(':checkbox') ) error.appendTo( element.parent() );
		    else error.insertAfter( element );
		}
	});

	$('#form-informazioni').validate({
		errorClass: 'has-error',
		errorElement: 'small',
		highlight: function(element, errorClass) {
			if ( !$(element).is(':checkbox') ) $(element).parent().addClass(errorClass);
			else $(element).parent().parent().addClass(errorClass);
		},
		unhighlight: function(element, errorClass) {
			if ( !$(element).is(':checkbox') ) $(element).parent().removeClass(errorClass);
			else $(element).parent().parent().removeClass(errorClass);
		},
		errorPlacement: function(error, element) {
		    if ( !element.is(':checkbox') ) error.appendTo( element.parent() );
		    else error.insertAfter( element );
		}
	});

	var checkCF = $('#ws_pre_check_cf');
	if ( checkCF.length ) {
		// authorize IP
		$.post(ajaxurl, {action: 'ipws_check_ip'}, function(data){
			if ( data.Result==1 ) {
				
				// first submit CF
				checkCF.click(function(){
					var o = {
						action: 'ipws_check_cf',
						cf: $('#ws_cf_pre').val()
					};
					if ( o.cf!='' ) {
						$.post(ajaxurl, o, function(data){
							
						}, 'json');
					}
				});

			} else {
				alert( "Impossibile completare l'operazione al momento. Si prega di riprovare più tardi." );
			}
		}, 'json');
	}
});