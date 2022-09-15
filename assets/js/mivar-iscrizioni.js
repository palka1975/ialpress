jQuery(document).ready(function($){
	jQuery.validator.addMethod("codfis", function(value, element) {
		return this.optional(element) || CodiceFiscale.check(value);
	}, "Il codice fiscale inserito non è valido. Per favore ricontrollare");

	$('#isc_datanascita').datepicker({
		dateFormat: 'dd/mm/yy'
	});
	$('#isc_datanascita_ws').datepicker({
		dateFormat: 'dd/mm/yy',
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
		},
		submitHandler: function(form) {
			dataLayer.push({
				'event': 'submit_form_iscrizione',
				'id_corso': $('#hid_isc_corso').val(),
				'nome_corso': $('#hid_isc_corso_nome').val()
			});
			form.submit();
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
		},
		submitHandler: function(form) {
			dataLayer.push({
				'event': 'submit_form_richiesta_informazioni',
				'id_corso': $('#hid_rin_corso').val(),
				'nome_corso': $('#hid_rin_corso_nome').val()
			});
			form.submit();
		}
	});

	var inputCF = $('#isc_codfis');
	// first submit CF
	inputCF.change(function(){
		let $this = $(this),
			o = {
				action: 'ipws_check_cf',
				cf: $this.val()
			};
		if ( o.cf!='' ) {
			$.post(ajaxurl, o, function(data){
				if ( data.Result==1 && data.ResponseData.CodiceFiscaleValido==false ) {
					$('<small id="isc_codfis-error" class="has-error">Inserire un codice fiscale valido</small>').insertAfter( $this );
					$this.parent().addClass('has-error');
				} else if ( data.Result==1 && data.ResponseData.CodiceFiscaleValido==true ) {
					$('#isc_codfis-error').remove();
					$this.parent().removeClass('has-error');
				}
			}, 'json');
		}
	});
	var inputCITTA = $('#isc_citta');
	if ( inputCITTA.length ) {
		inputCITTA.autocomplete({
			minLength: 3,
			source: function(request, response){
				$( "#isc_citta_id" ).val('');
				let o = {
					action: 'ipws_check_city',
					term: request.term
				};
				$.post(ajaxurl,o, function(data){
					response( data );
				}, 'json');
			},
			select: function( event, ui ) {
				inputCITTA.val( ui.item.Descrizione );
				$( "#isc_citta_id" ).val( ui.item.IDComune );
				$( "#isc_provincia" ).val( ui.item.Provincia );
				return false;
			}
		})
		.autocomplete( "instance" )._renderItem = function( ul, item ) {
			return $( "<li>" )
				.append( "<div>" + item.Descrizione + " (" + item.Provincia + ")</div>" )
				.appendTo( ul );
			};
		};
	var inputCITTANASC = $('#isc_luogonascita');
	if ( inputCITTANASC.length ) {
		inputCITTANASC.autocomplete({
			minLength: 3,
			source: function(request, response){
				$( "#isc_luogonascita_id" ).val('');
				let o = {
					action: 'ipws_check_city',
					term: request.term
				};
				$.post(ajaxurl,o, function(data){
					response( data );
				}, 'json');
			},
			select: function( event, ui ) {
				inputCITTANASC.val( ui.item.Descrizione );
				$( "#isc_luogonascita_id" ).val( ui.item.IDComune );
				return false;
			}
		})
		.autocomplete( "instance" )._renderItem = function( ul, item ) {
			return $( "<li>" )
				.append( "<div>" + item.Descrizione + " (" + item.Provincia + ")</div>" )
				.appendTo( ul );
			};
		};

	var inputSTATO = $('#isc_stato');
	if ( inputSTATO.length ) {
		inputSTATO.autocomplete({
			minLength: 3,
			source: function(request, response){
				$( "#isc_stato_id" ).val('');
				let o = {
					action: 'ipws_check_nation',
					term: request.term
				};
				$.post(ajaxurl,o, function(data){
					response( data );
				}, 'json');
			},
			select: function( event, ui ) {
				inputSTATO.val( ui.item.Descrizione );
				$( "#isc_stato_id" ).val( ui.item.IDNazione );
				return false;
			}
		})
		.autocomplete( "instance" )._renderItem = function( ul, item ) {
			return $( "<li>" )
				.append( "<div>" + item.Descrizione + "</div>" )
				.appendTo( ul );
			};
		};
	var inputSTATONASC = $('#isc_statonascita');
	if ( inputSTATONASC.length ) {
		inputSTATONASC.autocomplete({
			minLength: 3,
			source: function(request, response){
				$( "#isc_statonascita_id" ).val('');
				let o = {
					action: 'ipws_check_nation',
					term: request.term
				};
				$.post(ajaxurl,o, function(data){
					response( data );
				}, 'json');
			},
			select: function( event, ui ) {
				inputSTATONASC.val( ui.item.Descrizione );
				$( "#isc_statonascita_id" ).val( ui.item.IDNazione );
				return false;
			}
		})
		.autocomplete( "instance" )._renderItem = function( ul, item ) {
			return $( "<li>" )
				.append( "<div>" + item.Descrizione + "</div>" )
				.appendTo( ul );
			};
		};

	var inputCITTADINANZA = $('#isc_cittadinanza');
	if ( inputCITTADINANZA.length ) {
		inputCITTADINANZA.autocomplete({
			minLength: 3,
			source: function(request, response){
				$( "#isc_cittadinanza_id" ).val('');
				let o = {
					action: 'ipws_check_citizenship',
					term: request.term
				};
				$.post(ajaxurl,o, function(data){
					response( data );
				}, 'json');
			},
			select: function( event, ui ) {
				inputCITTADINANZA.val( ui.item.Descrizione );
				$( "#isc_cittadinanza_id" ).val( ui.item.IDCittadinanza );
				return false;
			}
		})
		.autocomplete( "instance" )._renderItem = function( ul, item ) {
			return $( "<li>" )
				.append( "<div>" + item.Descrizione + "</div>" )
				.appendTo( ul );
			};
		};

	$('#form_preiscrizione_ws').validate({
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
		},
		submitHandler: function(form) {
			let formOk = true;
			let $isc_citta = $('#isc_citta');
			let $isc_citta_id = $('#isc_citta_id');
			if ( $isc_citta_id.val()=='' ) {
				formOk = false;
				$('<small id="isc_citta_id-error" class="has-error">Usare l\'autocomplete per scegliere una città dall\'elenco</small>').insertAfter( $isc_citta );
				$isc_citta.parent().addClass('has-error');
			} else {
				$('#isc_citta_id-error').remove();
				$isc_citta.parent().removeClass('has-error');
			}
			let $isc_stato = $('#isc_stato');
			let $isc_stato_id = $('#isc_stato_id');
			if ( $isc_stato_id.val()=='' ) {
				formOk = false;
				$('<small id="isc_stato_id-error" class="has-error">Usare l\'autocomplete per scegliere una nazione dall\'elenco</small>').insertAfter( $isc_stato );
				$isc_stato.parent().addClass('has-error');
			} else {
				$('#isc_stato_id-error').remove();
				$isc_stato.parent().removeClass('has-error');
			}
			let $isc_statonascita = $('#isc_statonascita');
			let $isc_statonascita_id = $('#isc_statonascita_id');
			if ( $isc_statonascita_id.val()=='' ) {
				formOk = false;
				$('<small id="isc_statonascita_id-error" class="has-error">Usare l\'autocomplete per scegliere una nazione dall\'elenco</small>').insertAfter( $isc_statonascita );
				$isc_statonascita.parent().addClass('has-error');
			} else {
				$('#isc_statonascita_id-error').remove();
				$isc_statonascita.parent().removeClass('has-error');
			}
			let $isc_luogonascita = $('#isc_luogonascita');
			let $isc_luogonascita_id = $('#isc_luogonascita_id');
			if ( ($isc_luogonascita_id.val()=='' && $isc_statonascita_id.val()=='104') || ($isc_luogonascita_id.val()=='' && $isc_statonascita_id.val()=='') ) {
				formOk = false;
				$('<small id="isc_luogonascita_id-error" class="has-error">Usare l\'autocomplete per scegliere una città dall\'elenco</small>').insertAfter( $isc_luogonascita );
				$isc_luogonascita.parent().addClass('has-error');
			} else {
				$('#isc_luogonascita_id-error').remove();
				$isc_luogonascita.parent().removeClass('has-error');
			}
			let $isc_cittadinanza = $('#isc_cittadinanza');
			let $isc_cittadinanza_id = $('#isc_cittadinanza_id');
			if ( $isc_cittadinanza_id.val()=='' ) {
				formOk = false;
				$('<small id="isc_cittadinanza_id-error" class="has-error">Usare l\'autocomplete per scegliere una cittadinanza dall\'elenco</small>').insertAfter( $isc_cittadinanza );
				$isc_cittadinanza.parent().addClass('has-error');
			} else {
				$('#isc_cittadinanza_id-error').remove();
				$isc_cittadinanza.parent().removeClass('has-error');
			}
			if ( formOk ) {
				let comuneNascita = $('#isc_luogonascita_id').val(),
					comuneEsteroNascita = '',
					isComuneEsteroNascita = false;
				if ( $('#isc_statonascita_id').val()!=104 ) {
					comuneEsteroNascita = comuneNascita;
					comuneNascita = '';
					isComuneEsteroNascita = true;
				}
				let comuneResidenza = $('#isc_citta_id').val(),
					comuneEsteroResidenza = '',
					isComuneEsteroResidenza = false;
				if ( $('#isc_stato_id').val()!=104 ) {
					comuneEsteroResidenza = comuneResidenza;
					comuneResidenza = '';
					isComuneEsteroResidenza = true;
				}
				let _tNascita = $('#isc_datanascita_ws').val().split('/'),
					dataNascita = _tNascita[2] + '-' + _tNascita[1] + '-' + _tNascita[0];
				let o = {
					"action": "ipws_submit_iscrizione",
					"IDCorso": $('#isc_corso').val(),
					"CodiceFiscale": $('#isc_codfis').val(),
					"Cognome": $('#isc_cognome').val(),
					"Nome": $('#isc_nome').val(),
					"Sesso": $('#isc_sesso').val(),
					"DataNascita": dataNascita,
					"IDComuneNascita": comuneNascita,
					"IsComuneEsteroNascita": isComuneEsteroNascita,
					"ComuneEsteroNascita": comuneEsteroNascita,
					"IDNazioneNascita": $('#isc_statonascita_id').val(),
					"IDPrimaCittadinanza": $('#isc_cittadinanza_id').val(),
					"IDSecondaCittadinanza": -1,
					"IDComuneResidenza": comuneResidenza,
					"IsComuneEsteroResidenza": isComuneEsteroResidenza,
					"ComuneEsteroResidenza": comuneEsteroResidenza,
					"IDNazioneResidenza": $('#isc_stato_id').val(),
					"CAPResidenza": $('#isc_cap').val(),
					"IndirizzoResidenza": $('#isc_indirizzo').val(),
					"IDComuneDomicilio": -1,
					"IsComuneEsteroDomicilio": false,
					"ComuneEsteroDomicilio": '',
					"IDNazioneDomicilio": -1,
					"CAPDomicilio": '',
					"IndirizzoDomicilio": '',
					'TelefonoAbitazione': '',
					"EMailPersonale": $('#isc_email').val(),
					"CellularePersonale": $('#isc_cellulare').val()
				};
				let buttonMain = $('#ipws_submit_main').attr('disabled', true),
					_spinner = buttonMain.next('.spinner').addClass('is-active');
				$.post(ajaxurl, o, function(data){
					console.log(data)
					if ( data.Esito=='Iscritto' ) {
						// iscrizione OK
						dataLayer.push({
							'event': 'submit_form_iscrizione_ialman',
							'nome_corso': $('#hid_isc_corso_nome').val()
						});
						// $('#form_preiscrizione_ws').get(0).submit();
						o.action = "ipws_finalize_iscrizione";
						o.citta = $('#isc_citta').val();
						o.provincia = $('#isc_provincia').val();
						o.stato = $('#isc_stato').val();
						o.statonascita = $('#isc_statonascita').val();
						o.luogonascita = $('#isc_luogonascita').val();
						o.cittadinanza = $('#isc_cittadinanza').val();
						o.corso_civi = $('#isc_corso_civi').val();
						o.corso_nome = $('#hid_isc_corso_nome').val();
						o.newsletter = $('#isc_newsletter').is(':checked') ? 1 : 0;
						$.post(ajaxurl, o, function(data){
							$('#form_preiscrizione_ws').replaceWith(data);
						});
					} else if ( data.Esito=='GiaPresenteNelCorso' ) {
						// già iscritto
						alert( 'Attenzione: risulti già iscritto a questo corso. Contattataci se hai bisogno di ulteriori informazioni.' );
						_spinner.removeClass('is-active');
						buttonMain.removeAttr('disabled');
					} else if ( data.Esito=='KO' ) {
						// alert( data.Messaggio );
						alert( 'Impossibile completare l\'iscrizione. Per favore ricontrolla i dati. Contattataci se hai bisogno di aiuto.' );
						_spinner.removeClass('is-active');
						buttonMain.removeAttr('disabled');
					}
				}, 'json');
			} else return false;
		}
	});

	// schede iscrizione
	$('.link_scheda_iscrizione').click(function(){
		let $this = $(this);
		dataLayer.push({
			'event': $this.attr('id'),
			'nome_corso': $('#track_corso_name').val(),
			'id_corso': $('#track_corso_id').val()
		});
	});

	// link generico al form di iscrizione
	$('.to_form_ialman').click(function(){
		let $this = $(this);
		dataLayer.push({
			'event': 'btn_iscriviti',
			'nome_corso': $this.attr('id')
		});
	});
});