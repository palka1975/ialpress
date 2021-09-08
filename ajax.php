<?php

add_action( 'wp_ajax_mivarip_update_table', 'mivaripUpdateTable' );
add_action( 'wp_ajax_nopriv_mivarip_update_table', 'mivaripUpdateTable' );
function mivaripUpdateTable() {
    $table = $_POST['table'];
    $hour = 12;
    $today              = strtotime($hour . ':00:00');
    $todayD             = date('Y-m-d', $today);
    $yesterday          = strtotime('-1 day', $today);
    $yesterdayD         = date('Y-m-d', $yesterday);
    $_ialman = new Ialman_Ops();
    if ( $table=='domande' ) $esito = $_ialman->updateDomanda( $yesterdayD );
    else if ( $table=='corsi' ) $esito = $_ialman->updateCorsi( $yesterdayD );
    else if ( $table=='anagrafica' ) $esito = $_ialman->updateAnagrafica( $yesterdayD );

    echo json_encode( $esito );
    die();
}

add_action( 'wp_ajax_mivarip_salva_associazioni', 'mivaripSaveMapping' );
add_action( 'wp_ajax_nopriv_mivarip_salva_associazioni', 'mivaripSaveMapping' );
function mivaripSaveMapping() {
    $mappatura = $_POST['mappatura'];
    $_ialman = new Ialman_Ops();
    $esito = $_ialman->saveTipologieFormativeMapping( $mappatura );
    if ( $esito ) echo json_encode( array('esito'=>'ok') );
    else echo json_encode( array('esito'=>'ko') );
    die();
}

add_action( 'wp_ajax_crm_send_mail', 'crm_send_mail' );
add_action( 'wp_ajax_nopriv_crm_send_mail', 'crm_send_mail' );
function crm_send_mail() {
    $email_to = $_POST['email_to'];
	$id_domanda = $_POST['id_domanda'];
	$subj = $_POST['subj'];
	$msg = $_POST['msg'];

	$_ialmail = new Ialpress_Mailer();

	$esito = $_ialmail->send_mail( $email_to, $id_domanda, $subj, $msg );
	echo json_encode( $esito );
    die();
}

add_action( 'wp_ajax_get_sent_mail_table', 'get_sent_mail_table' );
add_action( 'wp_ajax_nopriv_get_sent_mail_table', 'get_sent_mail_table' );
function get_sent_mail_table() {
	$id_domanda = $_POST['id_domanda'];

	$_ialmail = new Ialpress_Mailer();

	echo $_ialmail->getSentHtmlTable( $id_domanda );
    die();
}

add_action( 'wp_ajax_associa_corso_ialman', 'associa_corso_ialman' );
add_action( 'wp_ajax_nopriv_associa_corso_ialman', 'associa_corso_ialman' );
function associa_corso_ialman() {
    $post_id = $_POST['post_id'];
    $id_commessa = $_POST['id_commessa'];

    $_ialman = new Ialman_Ops();
    echo json_encode( $_ialman->associaCorsoIalman( $post_id, $id_commessa ) );
    die();
}

add_action( 'wp_ajax_mivarip_resync_corsi', 'mivarip_resync_corsi' );
add_action( 'wp_ajax_nopriv_mivarip_resync_corsi', 'mivarip_resync_corsi' );
function mivarip_resync_corsi() {
    $_ialman = new Ialman_Ops();
    echo json_encode( $_ialman->resyncCorsi() );
    die();
}