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
    echo callWS( 'GET', 'CercaCF/'.$cf );
    die();
}

add_action( 'wp_ajax_ipws_check_ip', 'ipws_check_ip' );
add_action( 'wp_ajax_nopriv_ipws_check_ip', 'ipws_check_ip' );
function ipws_check_ip() {
    echo callWS( 'GET', 'IP' );
    die();
}

add_action( 'wp_ajax_ipws_check_city', 'ipws_check_city' );
add_action( 'wp_ajax_nopriv_ipws_check_city', 'ipws_check_city' );
function ipws_check_city() {
    $term = urlencode( $_POST['term'] );
    $res = json_decode( callWS( 'GET', 'Comuni/'.$term ) );
    if ( $res->Result==1 ) {
        echo json_encode( $res->ResponseData );
    } else {
        echo json_encode([
            [
                "Descrizione" => "Nessun comune trovato",
                "IDComune" => '',
                "Provincia" => 'ripetere la ricerca',
            ],
        ]);
    }
    die();
}

add_action( 'wp_ajax_ipws_check_nation', 'ipws_check_nation' );
add_action( 'wp_ajax_nopriv_ipws_check_nation', 'ipws_check_nation' );
function ipws_check_nation() {
    $term = urlencode( $_POST['term'] );
    $res = json_decode( callWS( 'GET', 'Nazioni/'.$term ) );
    if ( $res->Result==1 ) {
        echo json_encode( $res->ResponseData );
    } else {
        echo json_encode([
            [
                "Descrizione" => "Nessuno stato trovato",
                "IDNazione" => '',
            ],
        ]);
    }
    die();
}

add_action( 'wp_ajax_ipws_check_citizenship', 'ipws_check_citizenship' );
add_action( 'wp_ajax_nopriv_ipws_check_citizenship', 'ipws_check_citizenship' );
function ipws_check_citizenship() {
    $term = urlencode( $_POST['term'] );
    $res = json_decode( callWS( 'GET', 'Cittadinanze/'.$term ) );
    if ( $res->Result==1 ) {
        echo json_encode( $res->ResponseData );
    } else {
        echo json_encode([
            [
                "Descrizione" => "Nessuna corrispondenza trovata",
                "IDCittadinanza" => '',
            ],
        ]);
    }
    die();
}

add_action( 'wp_ajax_ipws_submit_iscrizione', 'ipws_submit_iscrizione' );
add_action( 'wp_ajax_nopriv_ipws_submit_iscrizione', 'ipws_submit_iscrizione' );
function ipws_submit_iscrizione() {
    $data = [
        "IDCorso" => $_POST['IDCorso'],
        "CodiceFiscale" => $_POST['CodiceFiscale'],
        "Cognome" => $_POST['Cognome'],
        "Nome" => $_POST['Nome'],
        "Sesso" => $_POST['Sesso'],
        "DataNascita" => $_POST['DataNascita'],
        "IDComuneNascita" => $_POST['IDComuneNascita'],
        "IsComuneEsteroNascita" => $_POST['IsComuneEsteroNascita'],
        "ComuneEsteroNascita" => $_POST['ComuneEsteroNascita'],
        "IDNazioneNascita" => $_POST['IDNazioneNascita'],
        "IDPrimaCittadinanza" => $_POST['IDPrimaCittadinanza'],
        "IDComuneResidenza" => $_POST['IDComuneResidenza'],
        "IsComuneEsteroResidenza" => $_POST['IsComuneEsteroResidenza'],
        "ComuneEsteroResidenza" => $_POST['ComuneEsteroResidenza'],
        "IDNazioneResidenza" => $_POST['IDNazioneResidenza'],
        "CAPResidenza" => $_POST['CAPResidenza'],
        "IndirizzoResidenza" => $_POST['IndirizzoResidenza'],
        "EMailPersonale" => $_POST['EMailPersonale'],
        "CellularePersonale" => $_POST['CellularePersonale'],
    ];
    $res = json_decode( callWS( 'SET', $data ) );
    if ( $res->Result==1 ) {
        echo json_encode( $res->ResponseData );
    } else {
        echo json_encode([
            "Esito" => "KO",
            "Messaggio" => $res->ResultMessage,
        ]);
    }
    // TEST
    // echo json_encode([
    //     "Esito" => "Iscritto",
    //     "Messaggio" => "CIAONE",
    // ]);
    die();
}

add_action( 'wp_ajax_ipws_finalize_iscrizione', 'ipws_finalize_iscrizione' );
add_action( 'wp_ajax_nopriv_ipws_finalize_iscrizione', 'ipws_finalize_iscrizione' );
function ipws_finalize_iscrizione() {
    $isc_nome = $_POST['Nome'];
    $isc_cognome = $_POST['Cognome'];
    $isc_codfis = $_POST['CodiceFiscale'];
    $isc_datanascita_ws = $_POST['DataNascita'];
    $isc_sesso = $_POST['Sesso'];
    $isc_indirizzo = $_POST['IndirizzoResidenza'];
    $isc_citta = $_POST['citta'];
    $isc_provincia = $_POST['provincia'];
    $isc_cap = $_POST['CAPResidenza'];
    $isc_stato = $_POST['stato'];
    $isc_statonascita = $_POST['statonascita'];
    $isc_luogonascita = $_POST['luogonascita'];
    $isc_cittadinanza = $_POST['cittadinanza'];
    $isc_email = $_POST['EMailPersonale'];
    $isc_cellulare = $_POST['CellularePersonale'];
    $isc_citta_id = $_POST['IDComuneResidenza'];
    $isc_stato_id = $_POST['IDNazioneResidenza'];
    $isc_statonascita_id = $_POST['IDNazioneNascita'];
    $isc_luogonascita_id = $_POST['IDComuneNascita'];
    $isc_cittadinanza_id = $_POST['IDPrimaCittadinanza'];
    $isc_corso = $_POST['IDCorso'];
    $isc_corso_civi = $_POST['corso_civi'];
    $isc_corso_nome = $_POST['corso_nome'];

    $ipws = new Ialpress_Iscrizioni_WS();
    $html = $ipws->mivar_iscrizioniws_ajax_finalize($isc_nome,$isc_cognome,$isc_codfis,$isc_datanascita_ws,$isc_sesso,$isc_indirizzo,$isc_citta,$isc_provincia,$isc_cap,$isc_stato,$isc_statonascita,$isc_luogonascita,$isc_cittadinanza,$isc_email,$isc_cellulare,$isc_citta_id,$isc_stato_id,$isc_statonascita_id,$isc_luogonascita_id,$isc_cittadinanza_id,$isc_corso,$isc_corso_civi,$isc_corso_nome);
    echo $html;
    die();
}

function callWS($method, $data){
    $url = IALMAN_WS_URL;
    $curl = curl_init();
    switch ($method){
        case "SET":
            $url .= 'Set/Iscrizione';
            curl_setopt($curl, CURLOPT_POST, 1);
            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
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