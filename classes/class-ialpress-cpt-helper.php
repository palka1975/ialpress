<?php

class Ialpress_Cpt_Helper
{
	protected $to_send = array(
		// 'andrea.varnier@gmail.com',
		'info@civiform.it',
		'segreteria@civiform.it',
	);

	protected $sessi = array('M', 'F');
	protected $province = array(
		"EE"=>"STATO ESTERO",
		"AG"=>"AGRIGENTO",
		"AL"=>"ALESSANDRIA",
		"AN"=>"ANCONA",
		"AO"=>"AOSTA",
		"AR"=>"AREZZO",
		"AP"=>"ASCOLI PICENO",
		"AT"=>"ASTI",
		"AV"=>"AVELLINO",
		"BA"=>"BARI",
		"BL"=>"BELLUNO",
		"BN"=>"BENEVENTO",
		"BG"=>"BERGAMO",
		"BI"=>"BIELLA",
		"BO"=>"BOLOGNA",
		"BZ"=>"BOLZANO-BOZEN",
		"BS"=>"BRESCIA",
		"BR"=>"BRINDISI",
		"CA"=>"CAGLIARI",
		"CL"=>"CALTANISSETTA",
		"CB"=>"CAMPOBASSO",
		"CE"=>"CASERTA",
		"CT"=>"CATANIA",
		"CZ"=>"CATANZARO",
		"CH"=>"CHIETI",
		"CO"=>"COMO",
		"CS"=>"COSENZA",
		"CR"=>"CREMONA",
		"KR"=>"CROTONE",
		"CN"=>"CUNEO",
		"EN"=>"ENNA",
		"FE"=>"FERRARA",
		"FI"=>"FIRENZE",
		"FG"=>"FOGGIA",
		"FO"=>"FORLI'-CESENA",
		"FR"=>"FROSINONE",
		"GE"=>"GENOVA",
		"GO"=>"GORIZIA",
		"GR"=>"GROSSETO",
		"IM"=>"IMPERIA",
		"IS"=>"ISERNIA",
		"AQ"=>"L'AQUILA",
		"SP"=>"LA SPEZIA",
		"LT"=>"LATINA",
		"LE"=>"LECCE",
		"LC"=>"LECCO",
		"LI"=>"LIVORNO",
		"LO"=>"LODI",
		"LU"=>"LUCCA",
		"MC"=>"MACERATA",
		"MN"=>"MANTOVA",
		"MS"=>"MASSA-CARRARA",
		"MT"=>"MATERA",
		"ME"=>"MESSINA",
		"MI"=>"MILANO",
		"MO"=>"MODENA",
		"NA"=>"NAPOLI",
		"NO"=>"NOVARA",
		"NU"=>"NUORO",
		"OR"=>"ORISTANO",
		"PD"=>"PADOVA",
		"PA"=>"PALERMO",
		"PR"=>"PARMA",
		"PV"=>"PAVIA",
		"PG"=>"PERUGIA",
		"PU"=>"PESARO E URBINO",
		"PE"=>"PESCARA",
		"PC"=>"PIACENZA",
		"PI"=>"PISA",
		"PT"=>"PISTOIA",
		"PN"=>"PORDENONE",
		"PZ"=>"POTENZA",
		"PO"=>"PRATO",
		"RG"=>"RAGUSA",
		"RA"=>"RAVENNA",
		"RC"=>"REGGIO CALABRIA",
		"RE"=>"REGGIO EMILIA",
		"RI"=>"RIETI",
		"RN"=>"RIMINI",
		"RM"=>"ROMA",
		"RO"=>"ROVIGO",
		"SA"=>"SALERNO",
		"SS"=>"SASSARI",
		"SV"=>"SAVONA",
		"SI"=>"SIENA",
		"SR"=>"SIRACUSA",
		"SO"=>"SONDRIO",
		"TA"=>"TARANTO",
		"TE"=>"TERAMO",
		"TR"=>"TERNI",
		"TO"=>"TORINO",
		"TP"=>"TRAPANI",
		"TN"=>"TRENTO",
		"TV"=>"TREVISO",
		"TS"=>"TRIESTE",
		"UD"=>"UDINE",
		"VA"=>"VARESE",
		"VE"=>"VENEZIA",
		"VB"=>"VERBANO-CUSIO-OSSOLA",
		"VC"=>"VERCELLI",
		"VR"=>"VERONA",
		"VV"=>"VIBO VALENTIA",
		"VI"=>"VICENZA",
		"VT"=>"VITERBO",
	);
	protected $titoli_studio = array(
		"Nessun titolo",
		"Licenza elementare",
		"Diploma scuola media inferiore o equipollente",
		"Qualifica professionale",
		"Diploma scuola media superiore o equipollente",
		"Laurea triennale o equipollente",
		"Laurea specialistica o vecchio ordinamento o equipollente",
	);
	protected $titoli_studio_isp = array(
		"NESSUN TITOLO",
		"LICENZA ELEMENTARE",
		"DIPLOMA SCUOLA MEDIA INFERIORE O EQUIPOLLENTE",
		"QUALIFICA PROFESSIONALE",
		"DIPLOMA SCUOLA MEDIA SUPERIORE O EQUIPOLLENTE",
		"LAUREA TRIENNALE O EQUIPOLLENTE",
		"LAUREA SPECIALISTICA O VECCHIO ORDINAMENTO O EQUIPOLLENTE",
	);
	protected $stato_occupazionale = array(
		"Studente",
		"Inattivo",
		"Occupato",
		"Occupato a rischio disoccupazione",
		"Disoccupato / non occupato",
		"In CIG / mobilità",
	);
	protected $stato_occupazionale_isp = array(
		"EDUCATORE",
		"ASSISTENTE SOCIALE",
		"OPERATORE IN COMUNITÀ",
		"DISOCCUPATO",
		"ALTRO",
	);
	protected $ente_appartenenza_isp = array(
		"PUBBLICA AMMINISTRAZIONE",
		"ENTE PRIVATO/AZIENDA",
	);
	protected $come_conosciuto = array(
		"Articoli / pubblicità su giornali",
		"Manifesti pubblicitari",
		"Radio",
		"Informagiovani, Centri per l&#039;impiego, Centri Regionali di Orientamento",
		"Parenti/amici/conoscenti",
		"Sito o Segreteria di Civiform",
		"Contattato da Civiform (telefonata, SMS, e-mail, lettera)",
		"Facebook/Twitter",
		"Altro",
	);

	protected $testo_info_privacy = "INFORMATIVA E CONSENSO PER IL TRATTAMENTO DEI DATI PERSONALI
		(artt.13 e 23, d.lgs. n.196/2003)
		Il decreto legislativo n.196/2003 prevede che il trattamento dei dati personali si svolga nel rispetto dei diritti, delle libertà fondamentali, nonché della dignità delle persone, con particolare riferimento alla riservatezza ed all'identità personale. Il trattamento dei Suoi dati personali avverrà, conformemente a quanto previsto dalla legge, secondo criteri di correttezza, liceità, adeguatezza e trasparenza, tutelando la Sua riservatezza e i Suoi diritti.
		Ai sensi dell'art.13 del d.lgs. n.196/2003, La informiamo quindi che:
		Il trattamento ha le seguenti finalità: trattare e conservare i dati richiesti da Civiform soc. coop. sociale per l'invio della news letter ed altre comunicazioni relative alle iscrizioni on-line dei corsi di formazione;
		Il trattamento sarà effettuato con le seguenti modalità: i dati raccolti verranno trasferiti in archivio elettronico oltre che conservati così come vengono forniti.
		Il conferimento dei dati è facoltativo; in assenza di tali dati non sarà peraltro possibile dare seguito all'invio delle comunicazioni.
		I suoi dati (nome, cognome, indirizzo mail e cellulare) verranno comunicati a: Civiform soc. coop. sociale, Cividale (cap 33043) - UD  viale Gemona, 7, ai fini dell'invio delle newsletter e di altre eventuali comunicazioni.
		In relazione al trattamento Lei potrà esercitare i diritti di cui all'art.7 del d.lgs. n.196/2003;
		Il titolare del trattamento è Civiform soc. coop. sociale.
		Il responsabile del trattamento è  ANDREA ROSSATO.
		Avendo compilato questo modulo e premuto il pulsante \"Invia\" di questa pagina, ricevute le informazioni di cui all'art.13 del d.lgs. n.196/2003, sopra riportate, presto il mio consenso al trattamento dei dati personali da voi richiesti.

		Letta la presente nota informativa, esprimo dunque il mio consenso al trattamento ed alla comunicazione dei miei dati personali e ai correlati trattamenti ai soggetti che svolgono le attività indicate nella stessa informativa. Sono consapevole che in mancanza del mio consenso la registrazione non potrà essere eseguita.";

	public static function register()
	{
		$plugin = new self();
		add_action( 'wp_enqueue_scripts', array( $plugin, 'mivar_iscrizioni_load_scripts' ) );

		// ASSOCIA CORSI CIVIFORM CON COMMESSE IALMAN
		add_action( 'add_meta_boxes', 'corso_ialman_meta_box', 30 );
		function corso_ialman_meta_box() {
			add_meta_box( 'ialpress_box_corso', 'Corso IALMAN Associato', 'id_commessa_meta_box', 'corsi', 'side' );
		}
		function id_commessa_meta_box() {
			global $post;
			$corso_ialman = get_post_meta( $post->ID, 'corso_ialman', true );
			if ( empty( $corso_ialman ) ) {
				echo '<input id="associa_ialman" value="" /> <button class="button button-primary" id="action_associa" data-post-id="' . $post->ID . '">Associa</button> <span class="spinner" style="float:none;"></span>';
			} else {
				echo '<input type="text" readonly value="' . $corso_ialman . '">';
			}
		}
		add_action( 'admin_footer', 'my_action_javascript' );
		function my_action_javascript() {
			global $post;
			?>
			<script type="text/javascript" >
			jQuery(document).ready(function($) {
				$('#action_associa').click(function(e){
					e.preventDefault();
					var $this = $(this),
						_spinner = $this.next('.spinner'),
						o = {},
						ajax_url = '<?php echo admin_url( 'admin-ajax.php' ) ?>';
					o.action = 'associa_corso_ialman';
					o.post_id = $this.data('post-id');
					o.id_commessa = $('#associa_ialman').val();
					if ( o.id_commessa!='' ) {
						_spinner.addClass('is-active');
						$.post( ajax_url, o, function(data){
							$('#associa_ialman').attr('readonly', true);
							$this.remove();
							_spinner.removeClass('is-active');
						}, 'json' );
					} else alert( "Inserire codice commessa IALMAN" );
				});
			});
			</script> <?php
		}
	}

	function mivar_iscrizioni_load_scripts() {
		$plugin_url = plugin_dir_url( __DIR__.'../' );
		// scripts
	    wp_enqueue_script( 'jquery-ui-datepicker' );
	    wp_enqueue_script( 'jquery-ui-autocomplete' );
	    if ( $_SERVER['HTTP_HOST']==='local.civiform.it' ) {
	    	wp_register_script( 'jquery-ui-local', $plugin_url.'assets/js/jquery-ui.min.js', array( 'jquery' ) );
	    	wp_enqueue_script( 'jquery-ui-local' );
	    }
	    wp_register_script( 'jquery-validation', $plugin_url.'assets/js/jquery.validate.min.js', array( 'jquery' ) );
	    wp_enqueue_script( 'jquery-validation' );
	    wp_register_script( 'js-codice-fiscale', $plugin_url.'assets/js/codice.fiscale.var.js', array() );
	    wp_enqueue_script( 'js-codice-fiscale' );
	    wp_register_script( 'jquery-validation-add', $plugin_url.'assets/js/additional-methods.min.js', array( 'jquery' ) );
	    wp_enqueue_script( 'jquery-validation-add' );
	    wp_register_script( 'jquery-validation-loc', $plugin_url.'assets/js/localization/messages_it.min.js', array( 'jquery' ) );
	    wp_enqueue_script( 'jquery-validation-loc' );
	    wp_register_script( 'misc-general-js', $plugin_url.'assets/js/mivar-iscrizioni.js', array( 'jquery' ) );
	    wp_enqueue_script( 'misc-general-js' );
	    // styles
	    wp_register_style( 'jquery-ui-structure', $plugin_url.'assets/css/jquery-ui.structure.min.css' );
	    wp_enqueue_style( 'jquery-ui-structure' );
	    wp_register_style( 'jquery-ui-theme', $plugin_url.'assets/css/jquery-ui.theme.min.css' );
	    wp_enqueue_style( 'jquery-ui-theme' );
	    wp_register_style( 'misc-general-styles', $plugin_url.'assets/css/mivar-iscrizioni.css' );
	    wp_enqueue_style( 'misc-general-styles' );
	}

}
Ialpress_Cpt_Helper::register();