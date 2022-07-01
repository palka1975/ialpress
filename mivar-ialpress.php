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
include "classes/class-ialpress-iscrizioniws.php";

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

// ricerca ordini per codice_disegno_vedo
add_action( 'admin_footer', 'search_custom_corso_itemmeta' );
function search_custom_corso_itemmeta() {
    $screen = get_current_screen();
    if ( $screen->id == "edit-corsi" ) {?>
    <script type="text/javascript">
        jQuery(document).ready( function($)
        {
            $('.tablenav.top .clear').before('<form action="#" id="search_custom_ialman" method="POST"><label for="custom_ialman_code"><?php esc_attr_e('Cerca per codice IAL', 'thecolorsoup');?>: </label><input type="text" id="custom_ialman_code" name="custom_ialman_code" value="" /></form>');
            $('#search_custom_ialman').submit(function(e){
            	e.preventDefault();
            	return false;
            });
            $('#custom_ialman_code')
	            .autocomplete({
					source: function( request, response ) {
						let o = {
							'action': 'custom_ialman_search',
							'term': request.term
						};
						$.post(ajaxurl, o, function(data){
							response(data);
						}, 'json');
					},
					minLength: 3,
					focus: function( event, ui ) {
						return false;
					},
					select: function( event, ui ) {
						// log( "Selected: " + ui.item.value + " aka " + ui.item.id );
						return false;
					}
				})
				.autocomplete( "instance" )._renderItem = function( ul, item ) {
					return $( "<li>" )
						.append( "<div>" + item.label + "</div>" )
						.appendTo( ul );
				};
        });
    </script>
	<?php } else return;
}

add_action('wp_ajax_custom_ialman_search', 'custom_ialman_search');
add_action('wp_ajax_nopriv_custom_ialman_search', 'custom_ialman_search');
function custom_ialman_search() {
	$term = $_POST['term'];
	global $wpdb;
	$sql = "SELECT * FROM `wp_36fb4p_postmeta` WHERE `meta_key` = 'corso_ialman' AND meta_value = '$term';";
	$row = $wpdb->get_row( $sql );
	if ( ! empty( $row ) ) {
		$ret = [
			[
				'value' => 0,
				'label' => '<a href="/wp-admin/post.php?post=' . $row->post_id . '&action=edit">Ordine ' . $row->post_id . ' - ' . get_the_title( $row->post_id ) . '</a>',
			],
		];
	} else {
		$ret = [
			[
				'value' => 0,
				'label' => 'Nessun ordine trovato',
			],
		];
	}

	echo json_encode( $ret );
	exit();
}