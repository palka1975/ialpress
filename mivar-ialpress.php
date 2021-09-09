<?php

/**
* Plugin Name: IalPress
* Plugin URI: https://mivar.in/
* Description: Plugin di MiVar per la gestione dell'interfaccia IALMAN su WordPress. Include gestione iscrizioni, iscrizioni speciali e richieste informazioni per compatibilità col vecchio sistema.
* Version: 2.0
* Author: Mivar, Inc.
* Author URI: https://mivar.in/
*/

function get_plugin_version() {
	$installed_version = get_option('mivarip_version');
	if ( empty($installed_version) ) return 1.0;
	return $installed_version;
}
function check_plugin_version() {
	$current_version = 2.0;
	$installed_version = get_plugin_version();
	if ( $current_version>$installed_version ) {
		update_option('mivarip_version', $current_version);
		update_db();
	}
}

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
include "classes/class-ialman-ops.php";
include "classes/class-ialpress-domande-list-table.php";
include "classes/class-ialpress-mailer.php";
include "ajax.php";
include "backend-pages.php";
include "tables.php";

// installation routines
register_activation_hook( __FILE__, 'mivarip_install' );
check_plugin_version();

$iscrizioni_attive = get_option('iscrizioni_attive');
$iscrizioni_speciali_attive = get_option('iscrizioni_speciali_attive');
$richieste_informazioni_attive = get_option('richieste_informazioni_attive');

if ( $richieste_informazioni_attive OR $iscrizioni_attive OR $iscrizioni_speciali_attive ) include "classes/class-ialpress-cpt-helper.php";
if ( $iscrizioni_attive ) include "classes/class-ialpress-iscrizioni.php";
if ( $iscrizioni_speciali_attive ) include "classes/class-ialpress-iscrizioni-speciali.php";
if ( $richieste_informazioni_attive ) include "classes/class-ialpress-richinfo.php";

function update_db(){
	global $wpdb;

	$charset_collate = $wpdb->get_charset_collate();

	// --
	// -- Struttura della tabella mii_tipologie_fvg_tipologie_schede
	// --

	$sql = "CREATE TABLE mii_tipologie_fvg_tipologie_schede (
	  id_tipologia_fvg int(11) NOT NULL,
	  id_tipologia_scheda int(11) NOT NULL
	) $charset_collate;";
	
	dbDelta( $sql );

	$sql = "CREATE TABLE mii_domanda (
	  ID int(11) NOT NULL AUTO_INCREMENT,
	  id_ca_commessa int(11) NOT NULL,
	  anagrafica int(11) NOT NULL,
	  is_preiscritto int(11) NOT NULL,
	  data_preiscrizione datetime NULL,
	  is_ammesso tinyint(1) NOT NULL,
	  data_ammissione datetime NULL,
	  is_dimesso tinyint(1) NOT NULL,
	  data_dimissione datetime DEFAULT NULL,
	  update_timestamp datetime NULL,
	  archived tinyint(1) NOT NULL DEFAULT 0,
	  PRIMARY KEY  (ID),
	  UNIQUE  (id_ca_commessa,anagrafica)
	) $charset_collate;";
	
	dbDelta( $sql );

	$sql = "CREATE TABLE mii_crm_mail_log (
		ID int(11) NOT NULL AUTO_INCREMENT,
		destinatario int(11) NOT NULL,
		indirizzo_email varchar(255) NOT NULL,
		oggetto_mail varchar(255) NOT NULL,
		testo_mail longtext NOT NULL,
		data_invio datetime NOT NULL,
		token varchar(255) NOT NULL,
		letto datetime NULL,
		PRIMARY KEY  (ID)
	) $charset_collate;";
	
	dbDelta( $sql );

	$sql = "CREATE TABLE mii_settori_formativi_aree_corsi (
	  id_settore_formativo int(11) NOT NULL,
	  id_area_corso int(11) NOT NULL
	) $charset_collate;";
	
	dbDelta( $sql );
}