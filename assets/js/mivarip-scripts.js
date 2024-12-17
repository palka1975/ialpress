jQuery(document).ready(function($){
	function showLoader(element) {
		$(element).html('<div class="mivarip-loader">Loading...</div>');
	}
	$('.update-table').click(function(e){
		e.preventDefault();
		$('.update-table, #resync_corsi').attr('disabled', true);
		let $this = $(this),
			o = {
				action: 'mivarip_update_table',
				table: $(this).data('mii-table')
			};
		showLoader('#update_console');
		$.post(ajax_object.ajax_url, o, function(data){
			console.log(data)
			if ( typeof data!='undefined' ) {
				$('#update_console').html('<p>' + data.table + '<br/>Inseriti: ' + data.inseriti + '<br/>Aggiornati: ' + data.aggiornati + '</p>');
				$this.parent().find('.latest').text( data.data );
				$('.update-table, #resync_corsi').removeAttr('disabled');
			}
		}, 'json');
	});
	$('#resync_corsi').click(function(e){
		e.preventDefault();
		let $this = $(this),
			_spinner = $this.next('.spinner'),
			o = {
				action: 'mivarip_resync_corsi'
			};
		_spinner.addClass('is-active');
		$('.update-table, #resync_corsi').attr('disabled', true);
		$('#esito_resync').html('');
		$.post(ajax_object.ajax_url, o, function(data){
			_spinner.removeClass('is-active');
			$('#esito_resync').html('<p>Corsi esaminati: ' + data.corsi_esaminati + ', corsi aggiornati: ' + data.corsi_aggiornati + '</p>');
			$('.update-table, #resync_corsi').removeAttr('disabled');
		}, 'json');
	});

	// maintenance tools
	$('#maintenance-main').tabs();

	$('.mivarip-draggable').draggable({
		revert: "invalid"
	});
	$('.mivarip-droppable').droppable({
		drop: function( event, ui ) {
			ui.draggable.css({left:0,top:0}).appendTo(this);
		}
	});

	$('#salva_associazioni').click(function(e){
		e.preventDefault();
		var $button = $(this),
			o = {
				action: 'mivarip_salva_associazioni',
				mappatura: {}
			};
		$('.mivarip-tipologia_scheda_corso_box').each(function(){
			let $this = $(this),
				$dp = $this.find('.mivarip-droppable'),
				_id_tipologia_scheda = $dp.data('tipologia-assign');
			o.mappatura[_id_tipologia_scheda] = [];
			$dp.children('.mivarip-draggable').each(function(){
				o.mappatura[_id_tipologia_scheda].push($(this).data('tipologia-id'));
			});
		});

		if ( $('.mivarip-droppable').not(':empty').length>0 ) {
			$('<div id="salva_associazioni_loader" class="loader-container"><div class="mivarip-loader">Loading...</div></div>').insertAfter($button);
			$.post(ajax_object.ajax_url, o, function(data){
				$('#salva_associazioni_loader').remove();
				if ( data.esito=='ok' ) {
					$('<p id="salva_associazioni_msg">Associazioni salvate correttamente</p>').insertAfter($button);
					window.setTimeout(function(){
						$('#salva_associazioni_msg').fadeOut(function(){$(this).remove()});
					}, 5000);
				}
			}, 'json');
		}
	});

	$('#salva_associazioni_ac').click(function(e){
		e.preventDefault();
		var $button = $(this),
			o = {
				action: 'mivarip_salva_associazioni_ac',
				mappatura: {}
			};
		$('.mivarip-area_corso_box').each(function(){
			let $this = $(this),
				$dp = $this.find('.mivarip-droppable'),
				_id_area_corso = $dp.data('area-assign');
			o.mappatura[_id_area_corso] = [];
			$dp.children('.mivarip-draggable').each(function(){
				o.mappatura[_id_area_corso].push($(this).data('settore-id'));
			});
		});

		if ( $('.mivarip-droppable').not(':empty').length>0 ) {
			$('<div id="salva_associazioni_loader" class="loader-container"><div class="mivarip-loader">Loading...</div></div>').insertAfter($button);
			$.post(ajax_object.ajax_url, o, function(data){
				$('#salva_associazioni_loader').remove();
				if ( data.esito=='ok' ) {
					$('<p id="salva_associazioni_msg">Associazioni salvate correttamente</p>').insertAfter($button);
					window.setTimeout(function(){
						$('#salva_associazioni_msg').fadeOut(function(){$(this).remove()});
					}, 5000);
				}
			}, 'json');
		}
	});

	// crm richiesta informazioni
	$('.messaggio-contatto').each(function(){
		let $this = $(this),
			_txth = $this.height();
		if (_txth>50) {
			$this.css({
				height: '50px',
				overflow: 'hidden',
				position: 'relative'
			});
			$this.append('<div class="trigger-open closed"><span class="trigger-int">Mostra tutto</span></div>');
		}
	});

	$(document).on('click', '.trigger-int', function(){
		let $this = $(this),
			$t = $this.parent(),
			$p = $t.parent();
		if ( $t.hasClass('closed') ) {
			$t.removeClass('closed');
			$p.css({
				height: 'auto',
				overflow: 'visible'
			});
			$this.text('Mostra meno');
		} else {
			$t.addClass('closed');
			$p.css({
				height: '50px',
				overflow: 'hidden'
			});
			$this.text('Mostra tutto');
		}
	});

	$('#crm-richinfo').tabs();

	$('#invia_mail_contatto').click(function(e){
		e.preventDefault();
		var $this = $(this),
			_spinner = $this.next('.spinner'),
			_email_to = $this.data('email-to'),
			_id_domanda = $this.data('id-anagrafica'),
			o = {};
		o.action = 'crm_send_mail';
		o.email_to = _email_to;
		o.id_domanda = _id_domanda;
		o.subj = $('#contatta_subject').val();
		o.msg = $('#contatta_messaggio').val();
		if ( o.msg!='' && o.subj!='' ) {
			_spinner.addClass('is-active');
			$.post(ajax_object.ajax_url, o, function(data){
				_spinner.removeClass('is-active');
				if ( data.esito=='ok' ) {
					alert( "Mail inviata correttamente" );
					$('#contatta_subject').val('');
					$('#contatta_messaggio').val('');
					let ob = {};
					ob.action = 'get_sent_mail_table';
					ob.id_domanda = _id_domanda;
					$.post(ajax_object.ajax_url, ob, function(data){
						$('#sent_mail_table_ext').html( data );
					}, 'html');
				} else {
					alert( data.msg );
				}
			}, 'json');
		} else {
			alert('Compilare Oggetto e Messaggio.');
		}
	});
});