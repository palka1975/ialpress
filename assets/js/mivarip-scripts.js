jQuery(document).ready(function($){
	$('#update_domanda').click(function(e){
		e.preventDefault();
		let o = {
			action: 'mivarip_update_domanda'
		}
		$.post(ajaxurl, o, function(data){
			$('#update_console').html('<p>' + data.table + '<br/>Inseriti: ' + data.inseriti + '<br/>Aggiornati: ' + data.aggiornati + '</p>');
		}, 'json')
	});
});