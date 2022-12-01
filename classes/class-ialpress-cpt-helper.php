<?php

class Ialpress_Cpt_Helper
{
	protected $to_send = array(
		// 'andrea.varnier@gmail.com',
		'info@civiform.it',
		// 'segreteria@civiform.it',
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

	protected $testo_info_privacy = "INFORMAZIONI SUL TRATTAMENTO DEI DATI PERSONALI per iscrizione ad attività CIVIFORM ai sensi dell'articolo 13 del Regolamento (UE) 2016/679

Ai sensi del Regolamento (UE) 2016/679 (di seguito \"Regolamento\"), la presente informativa descrive le modalità di trattamento dei dati personali di coloro che aderiscono al servizio offerto da Civiform.

TITOLARE DEL TRATTAMENTO  Il Titolare del trattamento dei dati è:  Civiform Soc.Coop.Sociale  Sede Legale: Viale Gemona 5, 33043 Cividale del Friuli (UD) privacy@civiform.it

RESPONSABILE DELLA PROTEZIONE DEI DATI   Responsabile della Protezione dei Dati (RPD-DPO), designato dal Titolare del trattamento, è l'Avv. Antonella Fiaschi - avvocatofiaschi@gmail.com
FINALITÀ DI TRATTAMENTO   I dati inviati per scelta dell'utente e volontariamente verranno utilizzati per l'invio di dati necessari alla prestazione del servizio offerto da Civiform, come da richiesta dell'utente così che l'utente interessato possa ricevere aderire a specifiche attività. L'interessato ha comunque la possibilità di cambiare in qualsiasi momento le preferenze espresse qualora lo ritenga opportuno, inviando una mail a: privacy@civiform.it. I dati personali potranno essere trattati, previo consenso, attraverso strumenti automatizzati (quali email ed SMS) nonché mezzi tradizionali (come il contatto telefonico tramite operatore per consentire a Civiform: 
di inviare gli estremi del servizio offerto
utilizzare i dati dell'utente per la preiscrizione al servizio offerto 
attivare il servizio stesso
L'interessato ha diritto di opporsi in toto o in parte in qualsiasi momento al trattamento dei propri dati per motivi legittimi ancorchè pertinenti allo scopo della raccolta, per l'invio di materiale commerciale-promozionale ulteriore rispetto al servizio richiesto.

NATURA DEL CONFERIMENTO  Il conferimento dei dati è facoltativo, pur tuttavia l'eventuale rifiuto a rispondere comporta l'impossibilità per il titolare di dar corso alle richieste di adesione al servizio proposto.
BASE GIURIDICA DEL TRATTAMENTO
La base giuridica del trattamento è il consenso al trattamento dei suddetti dati, consenso che è necessario. Il consenso si considera prestato flaggando le apposite caselle poste nel form di iscrizione on-line.

MODALITÀ DI TRATTAMENTO   Il trattamento dei dati è eseguito attraverso procedure informatiche o comunque mezzi telematici e supporti cartacei ad opera di soggetti, interni od esterni, a ciò appositamente incaricati ed autorizzati e impegnati alla riservatezza. I dati sono trattati e conservati con strumenti idonei a garantirne la sicurezza, l'integrità e la riservatezza mediante l'adozione di misure di sicurezza adeguate come previsto dalla normativa.

TIPOLOGIA DI DATI TRATTATI 
Ai fini della facoltativa ricezione della Newsletter saranno trattati i dati personali identificativi forniti dall'utente (es. nome, cognome, email, numero di telefono, ecc.) per la fruizione del servizio.

DESTINATARI DEI DATI   I dati potranno essere trattati dai dipendenti o collaboratori delle funzioni aziendali deputate al perseguimento delle finalità sopra indicate, che sono stati espressamente autorizzati al trattamento e che hanno ricevuto adeguate istruzioni operative.  I dati possono altresì essere trattati, per conto di Civiform, da soggetti esterni nominati come responsabili del trattamento, a cui sono impartite adeguate istruzioni operative. Tali soggetti sono essenzialmente ricompresi in realtà operanti nel settore informatico e di assistenza informatica e società di hosting, società di web-marketing e telemarketing) ove necessarie per le finalità di cui alla presente informativa. L'elenco dei Responsabili esterni del Trattamento dei Dati Personali è disponibile presso la sede del Titolare. I dati potranno essere comunicati ad Enti esterni quali Amministrazioni Regionali, Enti locali, Inail per copi specifici precisi e definiti, per tutti gli adempimenti di norme cogenti.

PERIODO DI CONSERVAZIONE DEI DATI   I dati sono conservati fino alla richiesta dell'interessato di eventuale opposizione all'invio e alla volontà di questi di rinunciare alla ricezione della newsletter cliccando sul link di cancellazione dell'iscrizione posta in calce a ciascuna comunicazione e comunque fino a 60 mesi successivi, ai fini del completamento delle attività amministrative, oltre che per il tempo necessario ad adempiere gli obblighi di legge.

DIRITTI DEGLI INTERESSATI   Gli interessati hanno il diritto di ottenere dal Titolare, l'accesso ai dati personali e la rettifica o la cancellazione degli stessi o la limitazione del trattamento che li riguarda o di opporsi al trattamento (artt. 15 e ss. del Regolamento). L'apposita istanza può essere presentata in ogni momento dall'interessato contattando il Titolare o il Responsabile della protezione dei dati, come sopra individuati.

DIRITTO DI RECLAMO   Gli interessati che ritengono che il trattamento dei dati personali a loro riferiti avvenga in violazione di quanto previsto dal Regolamento hanno il diritto di proporre reclamo al Garante, come previsto dall'art. 77 del Regolamento stesso, o di adire le opportune sedi giudiziarie (art. 79 del Regolamento).

MODIFICHE E AGGIORNAMENTI   La presente informativa può essere soggetta a modifiche ed integrazioni, anche quale conseguenza dell'aggiornamento della normativa applicabile relativa alla protezione delle persone fisiche con riguardo al trattamento dei dati personali nonché alla libera circolazione di tali dati. 


DATA ULTIMO AGGIORNAMENTO: 19 ottobre 2022";

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
	    wp_register_script( 'misc-general-js', $plugin_url.'assets/js/mivar-iscrizioni.js', array( 'jquery' ), '3.13b' );
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