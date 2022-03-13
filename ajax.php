<?php

define( "IALMAN_WS_URL", "https://www.ialman.it/iscrizioniws.civiform/" );

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

add_action( 'wp_ajax_mivarip_salva_associazioni', 'mivaripSaveMappingTipologieCorsi' );
add_action( 'wp_ajax_nopriv_mivarip_salva_associazioni', 'mivaripSaveMappingTipologieCorsi' );
function mivaripSaveMappingTipologieCorsi() {
    $mappatura = $_POST['mappatura'];
    $_ialman = new Ialman_Ops();
    $esito = $_ialman->saveTipologieFormativeMapping( $mappatura );
    if ( $esito ) echo json_encode( array('esito'=>'ok') );
    else echo json_encode( array('esito'=>'ko') );
    die();
}

add_action( 'wp_ajax_mivarip_salva_associazioni_ac', 'mivaripSaveMappingAreeCorsi' );
add_action( 'wp_ajax_nopriv_mivarip_salva_associazioni_ac', 'mivaripSaveMappingAreeCorsi' );
function mivaripSaveMappingAreeCorsi() {
    $mappatura = $_POST['mappatura'];
    $_ialman = new Ialman_Ops();
    $esito = $_ialman->saveSettoriFormativiMapping( $mappatura );
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

add_action( 'wp_ajax_ipws_check_cf', 'ipws_check_cf' );
add_action( 'wp_ajax_nopriv_ipws_check_cf', 'ipws_check_cf' );
function ipws_check_cf() {
    $cf = $_POST['cf'];
    echo callWS( 'GET', 'CercaCF/'.$cf.'/IP/164.132.183.240' );
    die();
}

add_action( 'wp_ajax_ipws_check_ip', 'ipws_check_ip' );
add_action( 'wp_ajax_nopriv_ipws_check_ip', 'ipws_check_ip' );
function ipws_check_ip() {
    $cf = $_POST['cf'];
    echo callWS( 'GET', 'IP' );
    die();
}

function callWS($method, $data){
    $url = IALMAN_WS_URL;
    $curl = curl_init();
    switch ($method){
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);
            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);                              
            break;
        case "GET":
            $url .= 'Get/' . $data;
            break;
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
    }
    // OPTIONS:
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    // EXECUTE:
    $result = curl_exec($curl);
    if(!$result){die("Connection Failure");}
    curl_close($curl);
    return $result;
}