<?php

function mivarip_install()
{
	global $wpdb;

	$charset_collate = $wpdb->get_charset_collate();
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	// --
	// -- Struttura della tabella mii_anagrafica
	// --

	$sql = "CREATE TABLE mii_anagrafica (
	  ID int(11) NOT NULL AUTO_INCREMENT,
	  cognome varchar(255) DEFAULT NULL,
	  nome varchar(255) DEFAULT NULL,
	  cf varchar(255) DEFAULT NULL,
	  piva varchar(255) DEFAULT NULL,
	  sesso varchar(255) DEFAULT NULL,
	  data_nascita datetime DEFAULT NULL,
	  luogo_nascita varchar(255) DEFAULT NULL,
	  indirizzo varchar(255) DEFAULT NULL,
	  cap varchar(255) DEFAULT NULL,
	  recapito varchar(255) DEFAULT NULL,
	  prov varchar(255) DEFAULT NULL,
	  stato varchar(255) DEFAULT NULL,
	  telefono varchar(255) DEFAULT NULL,
	  cellulare varchar(255) DEFAULT NULL,
	  mail varchar(255) DEFAULT NULL,
	  is_ditta_individuale tinyint(1) DEFAULT NULL,
	  e_perc_inps int(11) DEFAULT NULL,
	  e_perc_cassa int(11) DEFAULT NULL,
	  e_perc_iva int(11) DEFAULT NULL,
	  e_categoria_inps int(11) DEFAULT NULL,
	  titolo_studio int(11) DEFAULT NULL,
	  e_tipo_rapporto_lavoro int(11) DEFAULT NULL,
	  e_azienda_appartenenza int(11) DEFAULT NULL,
	  is_docente tinyint(1) DEFAULT NULL,
	  is_dipendente_pubblico int(11) DEFAULT NULL,
	  is_disattivo tinyint(1) DEFAULT NULL,
	  update_timestamp datetime DEFAULT NULL,
	  PRIMARY KEY  (ID)
	) $charset_collate;";

	dbDelta( $sql );

	// --
	// -- Struttura della tabella mii_ana_com_per_val
	// --

	$sql = "CREATE TABLE mii_ana_com_per_val (
	  anagrafica int(11) NOT NULL,
	  id_ca_commessa int(11) NOT NULL,
	  periodo varchar(255) NOT NULL,
	  e_nome_valore int(11) NOT NULL,
	  valore decimal(8,4) NOT NULL,
	  update_timestamp datetime NOT NULL,
	  UNIQUE  (anagrafica,id_ca_commessa,periodo,e_nome_valore)
	) $charset_collate;";

	dbDelta( $sql );

	// --
	// -- Struttura della tabella mii_anno_formativo
	// --

	$sql = "CREATE TABLE mii_anno_formativo (
	  ID int(11) NOT NULL AUTO_INCREMENT,
	  descrizione varchar(255) NOT NULL,
	  PRIMARY KEY  (ID)
	) $charset_collate;";

	dbDelta( $sql );

	// --
	// -- Struttura della tabella mii_attivita_corso
	// --

	$sql = "CREATE TABLE mii_attivita_corso (
	  ID int(11) NOT NULL AUTO_INCREMENT,
	  descrizione varchar(255) NOT NULL,
	  PRIMARY KEY  (ID)
	) $charset_collate;";

	dbDelta( $sql );

	// --
	// -- Struttura della tabella mii_ca_commessa
	// --

	$sql = "CREATE TABLE mii_ca_commessa (
		ID int(11) NOT NULL AUTO_INCREMENT,
		codice_interno varchar(255) DEFAULT NULL,
		codice_esterno varchar(255) DEFAULT NULL,
		data_inizio_prevista datetime DEFAULT NULL,
		data_termine_prevista datetime DEFAULT NULL,
		descrizione varchar(255) DEFAULT NULL,
		stato_corso int(11) DEFAULT NULL,
		tipologia_corso int(11) DEFAULT NULL,
		attivita_corso int(11) DEFAULT NULL,
		sotto_tipologia_attivita int(11) DEFAULT NULL,
		corso_webforma int(11) DEFAULT NULL,
		codice_padre varchar(255) DEFAULT NULL,
		macro_tipologia_corso int(11) DEFAULT NULL,
		id_anagrafica_titolare int(11) DEFAULT NULL,
		id_anagrafica_capofila int(11) DEFAULT NULL,
		id_anagrafica_gestore int(11) DEFAULT NULL,
		anno_formativo int(11) DEFAULT NULL,
		id_sede_ial int(11) DEFAULT NULL,
		fonte_finanziamento int(11) DEFAULT NULL,
		settore_formativo int(11) DEFAULT NULL,
		tipologia_formativa_fvg int(11) DEFAULT NULL,
		numero_ore_teoria_previste decimal(5,1) DEFAULT NULL,
		ore_esame decimal(5,1) DEFAULT NULL,
		numero_ore_pratica_previste decimal(5,1) DEFAULT NULL,
		numero_ore_stage_previste decimal(5,1) DEFAULT NULL,
		ore_larsa decimal(5,1) DEFAULT NULL,
		numero_ore_previste decimal(5,1) DEFAULT NULL,
		numero_allievi_previsti int(11) DEFAULT NULL,
		tipologia_svantaggio_corso int(11) DEFAULT NULL,
		data_inizio_effettiva datetime DEFAULT NULL,
		data_termine_effettiva datetime DEFAULT NULL,
		prevede_selezione int(11) DEFAULT NULL,
		id_ca_commessa_padre int(11) DEFAULT NULL,
		nickname varchar(255) DEFAULT NULL,
		ati int(11) DEFAULT NULL,
		numero_ore_e_learning decimal(5,1) DEFAULT NULL,
		tipologia_utenti int(11) DEFAULT NULL,
		max_num_allievi int(11) DEFAULT NULL,
		tipologia_utenza_corso int(11) DEFAULT NULL,
		altra_tipologia_svantaggio int(11) DEFAULT NULL,
		prevede_visita_didattica int(11) DEFAULT NULL,
		data_prevista_svolgimento_prove_ammissione datetime DEFAULT NULL,
		data_svolgimento_prove_ammissione datetime DEFAULT NULL,
		imp_erogazione_del_servizio decimal(8,4) DEFAULT NULL,
		riconosciuto_regione decimal(8,4) DEFAULT NULL,
		data_rendiconto datetime DEFAULT NULL,
		update_timestamp datetime DEFAULT NULL,
	  PRIMARY KEY  (ID)
	) $charset_collate;";

	dbDelta( $sql );

	// --
	// -- Struttura della tabella mii_com_ana_per_ruo
	// --

	$sql = "CREATE TABLE mii_com_ana_per_ruo (
	  id_ca_commessa int(11) NOT NULL,
	  anagrafica int(11) NOT NULL,
	  ruo_cod int(11) NOT NULL,
	  periodo varchar(255) NOT NULL,
	  ore decimal(5,1) NOT NULL,
	  update_timestamp datetime NOT NULL,
	  UNIQUE  (id_ca_commessa,anagrafica,ruo_cod,periodo)
	) $charset_collate;";

	dbDelta( $sql );

	// --
	// -- Struttura della tabella mii_com_ana_ruo
	// --

	$sql = "CREATE TABLE mii_com_ana_ruo (
	  id_ca_commessa int(11) NOT NULL,
	  anagrafica int(11) NOT NULL,
	  ruo_cod int(11) NOT NULL,
	  ore decimal(5,1) NOT NULL,
	  update_timestamp datetime NOT NULL,
	  UNIQUE  (id_ca_commessa,anagrafica,ruo_cod)
	) $charset_collate;";

	dbDelta( $sql );

	// --
	// -- Struttura della tabella mii_com_per_val
	// --

	$sql = "CREATE TABLE mii_com_per_val (
	  id_ca_commessa int(11) NOT NULL,
	  periodo varchar(255) NOT NULL,
	  e_nome_valore int(11) NOT NULL,
	  valore decimal(8,4) NOT NULL,
	  update_timestamp datetime NOT NULL,
	  UNIQUE  (id_ca_commessa,periodo,e_nome_valore)
	) $charset_collate;";

	// --
	// -- Struttura della tabella mii_com_val
	// --

	$sql = "CREATE TABLE mii_com_val (
	  id_ca_commessa int(11) NOT NULL,
	  e_nome_valore int(11) NOT NULL,
	  valore decimal(8,4) NOT NULL,
	  update_timestamp datetime NOT NULL,
	  UNIQUE  (id_ca_commessa,e_nome_valore)
	) $charset_collate;";

	dbDelta( $sql );

	// --
	// -- Struttura della tabella mii_domanda
	// --

	$sql = "CREATE TABLE mii_domanda (
	  ID int(11) NOT NULL AUTO_INCREMENT,
	  id_ca_commessa int(11) NOT NULL,
	  anagrafica int(11) NOT NULL,
	  is_ammesso tinyint(1) NOT NULL,
	  data_ammissione datetime NOT NULL,
	  is_dimesso tinyint(1) NOT NULL,
	  data_dimissione datetime DEFAULT NULL,
	  update_timestamp datetime NOT NULL,
	  PRIMARY KEY  (ID),
	  UNIQUE  (id_ca_commessa,anagrafica)
	) $charset_collate;";

	dbDelta( $sql );

	// --
	// -- Struttura della tabella mii_e_nome_valore
	// --

	$sql = "CREATE TABLE mii_e_nome_valore (
	  ID int(11) NOT NULL AUTO_INCREMENT,
	  descrizione varchar(255) NOT NULL,
	  is_disattivo tinyint(1) NOT NULL,
	  PRIMARY KEY  (ID)
	) $charset_collate;";

	dbDelta( $sql );

	// --
	// -- Struttura della tabella mii_fonte_finanziamento
	// --

	$sql = "CREATE TABLE mii_fonte_finanziamento (
	  ID int(11) NOT NULL AUTO_INCREMENT,
	  descrizione varchar(255) NOT NULL,
	  PRIMARY KEY  (ID)
	) $charset_collate;";

	dbDelta( $sql );

	// --
	// -- Struttura della tabella mii_ruo_cod
	// --

	$sql = "CREATE TABLE mii_ruo_cod (
	  ID int(11) NOT NULL AUTO_INCREMENT,
	  descrizione varchar(255) NOT NULL,
	  is_docenza tinyint(1) NOT NULL,
	  PRIMARY KEY  (ID)
	) $charset_collate;";

	dbDelta( $sql );

	// --
	// -- Struttura della tabella mii_settore_formativo
	// --

	$sql = "CREATE TABLE mii_settore_formativo (
	  ID int(11) NOT NULL AUTO_INCREMENT,
	  descrizione varchar(255) NOT NULL,
	  PRIMARY KEY  (ID)
	) $charset_collate;";

	dbDelta( $sql );

	// --
	// -- Struttura della tabella mii_sotto_tipologia_attivita
	// --

	$sql = "CREATE TABLE mii_sotto_tipologia_attivita (
	  ID int(11) NOT NULL AUTO_INCREMENT,
	  descrizione varchar(255) NOT NULL,
	  PRIMARY KEY  (ID)
	) $charset_collate;";

	dbDelta( $sql );

	// --
	// -- Struttura della tabella mii_stato_corso
	// --

	$sql = "CREATE TABLE mii_stato_corso (
	  ID int(11) NOT NULL AUTO_INCREMENT,
	  descrizione varchar(255) NOT NULL,
	  PRIMARY KEY  (ID)
	) $charset_collate;";

	dbDelta( $sql );

	// --
	// -- Struttura della tabella mii_tipologia_corso_rispetto_ial
	// --

	$sql = "CREATE TABLE mii_tipologia_corso_rispetto_ial (
	  ID int(11) NOT NULL AUTO_INCREMENT,
	  descrizione varchar(255) NOT NULL,
	  PRIMARY KEY  (ID)
	) $charset_collate;";

	dbDelta( $sql );

	// --
	// -- Struttura della tabella mii_tipologia_formativa_fvg
	// --

	$sql = "CREATE TABLE mii_tipologia_formativa_fvg (
	  ID int(11) NOT NULL AUTO_INCREMENT,
	  descrizione varchar(255) NOT NULL,
	  PRIMARY KEY  (ID)
	) $charset_collate;";

	dbDelta( $sql );

	// --
	// -- Struttura della tabella mii_tipologia_svantaggio_corso
	// --

	$sql = "CREATE TABLE mii_tipologia_svantaggio_corso (
	  ID int(11) NOT NULL AUTO_INCREMENT,
	  descrizione varchar(255) NOT NULL,
	  PRIMARY KEY  (ID)
	) $charset_collate;";

	dbDelta( $sql );

	// --
	// -- Struttura della tabella mii_tipologia_utenti
	// --

	$sql = "CREATE TABLE mii_tipologia_utenti (
	  ID int(11) NOT NULL AUTO_INCREMENT,
	  descrizione varchar(255) NOT NULL,
	  PRIMARY KEY  (ID)
	) $charset_collate;";

	dbDelta( $sql );

	// --
	// -- Struttura della tabella mii_tipologia_utenza_corso
	// --

	$sql = "CREATE TABLE mii_tipologia_utenza_corso (
	  ID int(11) NOT NULL AUTO_INCREMENT,
	  descrizione varchar(255) NOT NULL,
	  PRIMARY KEY  (ID)
	) $charset_collate;";

	dbDelta( $sql );
}