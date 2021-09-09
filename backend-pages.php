<?php

add_action( 'admin_enqueue_scripts', 'mivarip_backend_styles' );
function mivarip_backend_styles() {
    wp_enqueue_script( 'jquery-ui-draggable' );
    wp_enqueue_script( 'jquery-ui-droppable' );
    wp_enqueue_script( 'jquery-ui-tabs' );
    wp_enqueue_script( 'mivarip-js', plugin_dir_url( __FILE__ ).'assets/js/mivarip-scripts.js?v='.rand(), array('jquery') );
    wp_localize_script( 'mivarip-js', 'ajax_object', array( 'ajax_url'=>admin_url('admin-ajax.php') ) );

    wp_enqueue_style( 'jquery-ui-structure', plugin_dir_url( __FILE__ ).'assets/css/jquery-ui.structure.min.css' );
    wp_enqueue_style( 'jquery-ui-theme', plugin_dir_url( __FILE__ ).'assets/css/jquery-ui.theme.min.css' );
    wp_enqueue_style( 'mivarip-css', plugin_dir_url( __FILE__ ).'assets/css/mivarip-styles.css?v='.rand() );
}

// create custom plugin settings menu
add_action('admin_menu', 'mivarip_create_menu');
function mivarip_create_menu() {

	//create menu entries
    add_menu_page( 'IalPress Welcome Page', 'IalPress', 'manage_options', 'ialpress-main', 'mivarip_main_page_output', 'dashicons-chart-pie', 20 );
    add_submenu_page( 'ialpress-main', 'Elenco Domande di Preiscrizione', 'Preiscrizioni', 'manage_options', 'ialpress-domande', 'mivarip_domande_page_output' );
    add_submenu_page( 'ialpress-main', 'IalPress Tools', 'Utilities', 'manage_options', 'mivarip-tools', 'mivarip_tools_page' );
    add_submenu_page( 'ialpress-main', 'IalPress Impostazioni', 'Settings', 'manage_options', 'mivarip-settings', 'mivarip_settings_page' );

	//call register settings function
	add_action( 'admin_init', 'register_mivarip_settings' );
}

// domande ialman csv export
add_action( 'admin_footer', 'ialpress_export_csv' );
add_action( 'admin_init', 'do_export_csv' );
function ialpress_export_csv() {
    $screen = get_current_screen();
    if ( $screen->id == "ialpress_page_ialpress-domande" ) {?>
    <script type="text/javascript">
        jQuery(document).ready( function($)
        {
            $('.tablenav.top .clear').before('<form action="#" method="POST"><input type="hidden" id="ialpress_domande_csv_export" name="ialpress_domande_csv_export" value="1" /><input class="button button-primary user_export_button" type="submit" value="<?php esc_attr_e('Esporta CSV', 'mytheme');?>" /></form>');
        });
    </script>
    <?php } else return;
}
function do_export_csv() {
    if (!empty($_POST['ialpress_domande_csv_export'])) {
 
        if (current_user_can('upload_files')) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream; charset=UTF-8');
            header('Content-Disposition: attachment; filename="IALPRESS_Iscrizioni-EXPORT-'.date('Ymd_His').'.csv"');
            header('Content-Transfer-Encoding: binary');
            header('Connection: Keep-Alive');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            
            $args = array(
                'archived' => 0,
            );
            if ( ! empty( $_REQUEST['dom_status'] ) AND $_REQUEST['dom_status']=='archived' ) $args['archived'] = 1;
            $_ialman = new Ialman_Ops();
            $results = $_ialman->getDomande( $args );
            echo "Nome;Cognome;Sesso;Indirizzo;Citta';Cap;Provincia;Data di nascita;Luogo di nascita;Codoce Fiscale;Telefono;Cellulare;Email;Titolo di studio;Corso;Sede;Data Registrazione\r\n";
            foreach ( $results as $isc ) {
                $isc_nome = esc_html( $isc->nome );
                $isc_cognome = esc_html( $isc->cognome );
                $isc_sesso = esc_html( $isc->sesso );
                $isc_indirizzo = esc_html( $isc->indirizzo );
                $isc_citta = esc_html( $isc->recapito );
                $isc_cap = esc_html( $isc->cap );
                $isc_provincia = esc_html( $isc->prov );
                $isc_datanascita = date('d/m/Y', strtotime( $isc->data_nascita ) );
                $isc_luogonascita = esc_html( $isc->luogo_nascita );
                $isc_codfis = esc_html( $isc->cf );
                $isc_telefono = esc_html( $isc->telefono );
                $isc_cellulare = esc_html( $isc->cellulare );
                $isc_email = esc_html( $isc->mail );
                $isc_titolo_di_studio = esc_html( $isc->titolo_studio );
                $isc_corso = intval( $isc->descrizione );
                $corso = $_ialman->getImportedCommessa( $isc->id_corso );
                $terms_sedi = get_the_terms( $corso->ID, 'sede_corso' );
                $sedecorso = $terms_sedi[0];
                $ts_richiesta = date('d/m/Y', strtotime( $isc->data_preiscrizione ) );
 
                echo '"' . $isc_nome . '";"' . $isc_cognome . '";"' . $isc_sesso . '";"' . $isc_indirizzo . '";"' . $isc_citta . '";"' . $isc_cap . '";"' . $isc_provincia . '";"' . $isc_datanascita . '";"' . $isc_luogonascita . '";"' . $isc_codfis . '";"' . $isc_telefono . '";"' . $isc_cellulare . '";"' . $isc_email . '";"' . $isc_titolo_di_studio . '";"' . $corso . '";"' . $sedecorso->name . '";"' . $ts_richiesta . '"' . "\r\n";

                // wp_update_post(array(
                //  'ID' => $isc->ID,
                //  'post_status' => 'trash',
                // ));
            }
 
            exit();
        } else die("You don't have permissions to see this page.");
    }
}

// MAIN PAGE
function mivarip_main_page_output() {
    ?>
    <div class="wrap">
        <?php echo mivar_header() ?>
        <h2>Benvenuti in IalPress</h2>
        <div id="latest-ext">
            <?php
            $_ialman = new Ialman_Ops();

            $c = $_ialman->getLocalCorsi();

            if ( empty( $c ) ):
            ?>
                <p>Nessun corso importato nell'ultima settimana</p>
            <?php else: ?>
                <div>
                    <h4>Ultimi corsi importati</h4>
                    <table>
                        <thead>
                            <tr>
                                <th>
                                    CORSO
                                </th>
                                <th>
                                    Data
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($c as $post) : ?>
                            <tr>
                                <td>
                                    <a target="_blank" href="https://www.civiform.it/wp-admin/post.php?post=<?php echo $post->ID ?>&action=edit&classic-editor"><?php echo get_the_title( $post->ID ) ?></a>
                                </td>
                                <td>
                                    <?php echo get_the_date( '', $post->ID ) ?>
                                </td>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            <?php endif ?>
        </div>
    </div>
    <?php
}

// DOMANDE
function mivarip_domande_page_output() {
    $action = !empty($_REQUEST['action']) ? $_REQUEST['action'] : false;

    if ( $action=='view' ) {
        $domanda = !empty($_REQUEST['ialman_domanda']) ? $_REQUEST['ialman_domanda'] : 0;

        $_ialman = new Ialman_Ops();
        $_ialmail = new Ialpress_Mailer();
        $item = false;
        if ( !empty( $domanda ) ) $item = $_ialman->getDomande( array('id'=>$domanda) );
        if ( !empty( $item ) ) {
        ?>
        <div class="wrap">
            <?php echo mivar_header() ?>
            <h2>Visualizza Domanda</h2>
            <div class="metabox-holder mivarip-detail">
                <div class="postbox-container clearfix">
                    <div class="postbox">
                        <div class="postbox-header">
                            <h2 class="hndle">Dati anagrafici</h2>
                        </div>
                        <div class="inside clearfix">
                            <div class="main">
                                <div class="cell clearfix">
                                    <span class="left">Nome</span>
                                    <span class="right"><?php echo $item->nome; ?></span>
                                </div>
                                <div class="cell clearfix">
                                    <span class="left">Cognome</span>
                                    <span class="right"><?php echo $item->cognome; ?></span>
                                </div>
                                <div class="cell clearfix">
                                    <span class="left">Partita IVA</span>
                                    <span class="right"><?php echo $item->piva; ?></span>
                                </div>
                                <div class="cell clearfix">
                                    <span class="left">Codice Fiscale</span>
                                    <span class="right"><?php echo $item->cf; ?></span>
                                </div>
                                <div class="cell clearfix">
                                    <span class="left">Sesso</span>
                                    <span class="right"><?php echo $item->sesso; ?></span>
                                </div>
                                <div class="cell clearfix">
                                    <span class="left">Data di nascita</span>
                                    <span class="right"><?php echo date('d/m/Y', strtotime( $item->data_nascita ) ); ?></span>
                                </div>
                                <div class="cell clearfix">
                                    <span class="left">Luogo di nascita</span>
                                    <span class="right"><?php echo $item->luogo_nascita; ?></span>
                                </div>
                                <div class="cell clearfix">
                                    <span class="left">Indirizzo</span>
                                    <span class="right"><?php echo $item->indirizzo; ?></span>
                                </div>
                                <div class="cell clearfix">
                                    <span class="left">Città</span>
                                    <span class="right"><?php echo $item->recapito; ?></span>
                                </div>
                                <div class="cell clearfix">
                                    <span class="left">CAP</span>
                                    <span class="right"><?php echo $item->cap; ?></span>
                                </div>
                                <div class="cell clearfix">
                                    <span class="left">Provincia</span>
                                    <span class="right"><?php echo  $item->prov ?></span>
                                </div>
                                <div class="cell clearfix">
                                    <span class="left">Stato</span>
                                    <span class="right"><?php echo  $item->stato ?></span>
                                </div>
                                <div class="cell clearfix">
                                    <span class="left">Telefono</span>
                                    <span class="right"><?php echo $item->telefono; ?></span>
                                </div>
                                <div class="cell clearfix">
                                    <span class="left">Cellulare</span>
                                    <span class="right"><?php echo $item->cellulare; ?></span>
                                </div>
                                <div class="cell clearfix">
                                    <span class="left">Email</span>
                                    <span class="right"><?php echo $item->mail; ?></span>
                                </div>
                                <div class="cell clearfix">
                                    <span class="left">Corso</span>
                                    <span class="right"><?php echo $item->descrizione; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="postbox">
                        <div class="postbox-header">
                            <h2 class="hndle">Stato Domanda</h2>
                        </div>
                        <?php
                        $data_ammissione = !empty($item->data_ammissione) ? date('d/m/Y', strtotime($item->data_ammissione)) : '-';
                        $data_dimissione = !empty($item->data_dimissione) ? date('d/m/Y', strtotime($item->data_dimissione)) : '-';
                        ?>
                        <div class="inside clearfix">
                            <div class="main">
                                <div class="cell clearfix">
                                    <span class="left">Ammesso</span>
                                    <span class="right"><?php echo $item->is_ammesso==1 ? 'Sì' : 'No'; ?></span>
                                </div>
                                <div class="cell clearfix">
                                    <span class="left">Data Ammissione</span>
                                    <span class="right"><?php echo $data_ammissione; ?></span>
                                </div>
                                <div class="cell clearfix">
                                    <span class="left">Dimesso</span>
                                    <span class="right"><?php echo $item->is_dimesso==1 ? 'Sì' : 'No'; ?></span>
                                </div>
                                <div class="cell clearfix">
                                    <span class="left">Data Dimissione</span>
                                    <span class="right"><?php echo $data_dimissione; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="postbox full">
                    <div class="postbox-header">
                        <h2 class="hndle">Operazioni CRM</h2>
                    </div>
                    <div id="crm-richinfo">
                        <ul>
                            <li><a href="#mivar-crm-1">Contatta il richiedente</a></li>
                            <li><a href="#mivar-crm-2">Storico mail inviate</a></li>
                        </ul>
                        <div id="mivar-crm-1">
                            <h3>Contatta il richiedente</h3>
                            <table>
                                <tbody>
                                    <tr>
                                        <td style="width: 201px" valign="top">Oggetto</td>
                                        <td style="width: 524px"><input type="text" id="contatta_subject" style="width: 100%; height: 34px;"></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 201px" valign="top">Scrivi il tuo messaggio</td>
                                        <td style="width: 524px"><textarea id="contatta_messaggio" style="width: 100%; height: 140px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 201px">&nbsp;</td>
                                        <td style="width: 524px">
                                            <button class="button button-primary"
                                                    id="invia_mail_contatto"
                                                    data-email-to="<?php echo $item->mail ?>"
                                                    data-id-anagrafica="<?php echo $domanda ?>">Invia</button> <span class="spinner" style="float: none;"></span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div id="mivar-crm-2">
                            <h3>Mail già inviate</h3>
                            <div id="sent_mail_table_ext">
                                <?php echo $_ialmail->getSentHtmlTable( $domanda ) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><?php
        } else { ?>
            <h2>Nessuna domanda selezionata</h2>
            <p><a href="<?php menu_page_url('ialpress-domande') ?>"><-- Torna all'elenco</a></p>
        <?php }

    } else {

        $domandeListTable = new Ialpress_Domande_List_Table();
        $domandeListTable->prepare_items();
        
        $s = !empty($_REQUEST['s']) ? $_REQUEST['s'] : '';
        ?>
        <div class="wrap">
            <?php echo mivar_header() ?>
            <h2>Elenco Domande di PREISCRIZIONE</h2>
            <?php $domandeListTable->views() ?>
            
            <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
            <form id="domande-filter" method="post">
                <p class="search-box">
                    <input type="search" id="post-search-input" name="s" value="<?php echo $s ?>">
                    <input type="submit" id="search-submit" class="button" value="Cerca nominativi">
                </p>
                <!-- For plugins, we also need to ensure that the form posts back to our current page -->
                <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
                <!-- Now we can render the completed list table -->
                <?php $domandeListTable->display() ?>
            </form>
            
        </div>
<?php
    }
}

function register_mivarip_settings() {
    register_setting( 'mivarip-settings-group', 'crea_bozze_corsi' );
	register_setting( 'mivarip-settings-group', 'iscrizioni_attive' );
    register_setting( 'mivarip-settings-group', 'iscrizioni_speciali_attive' );
    register_setting( 'mivarip-settings-group', 'richieste_informazioni_attive' );
}

function mivarip_settings_page() {
	// wp_enqueue_media();
	global $post;
?>
<div class="wrap">
    <?php echo mivar_header() ?>
    <h1>IalPress - Impostazioni</h1>
    <form method="post" action="options.php">
        <?php settings_fields( 'mivarip-settings-group' ); ?>
        <?php do_settings_sections( 'mivarip-settings-group' ); ?>
        <h3>Corsi</h3>
        <table class="form-table">
            <tr valign="top">
                <th scope="row" style="width: 300px;">Crea Bozze per i nuovi corsi</th>
                <td>
                    <label class="switch">
                      <input type="checkbox" name="crea_bozze_corsi" value="1" <?php if (get_option('crea_bozze_corsi')==1) echo 'checked="checked"' ?>>
                      <span class="slider round"></span>
                    </label>
                </td>
            </tr>
        </table>
        <hr>
        <h3>Legacy Post Types</h3>
        <table class="form-table">
            <tr valign="top">
            	<th scope="row" style="width: 300px;">Attiva modulo "Iscrizioni"</th>
            	<td>
                    <label class="switch">
                      <input type="checkbox" name="iscrizioni_attive" value="1" <?php if (get_option('iscrizioni_attive')==1) echo 'checked="checked"' ?>>
                      <span class="slider round"></span>
                    </label>
                </td>
        	</tr>
            <tr valign="top">
                <th scope="row" style="width: 300px;">Attiva modulo "Iscrizioni Speciali"</th>
                <td>
                    <label class="switch">
                      <input type="checkbox" name="iscrizioni_speciali_attive" value="1" <?php if (get_option('iscrizioni_speciali_attive')==1) echo 'checked="checked"' ?>>
                      <span class="slider round"></span>
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" style="width: 300px;">Attiva modulo "Richieste Informazioni"</th>
                <td>
                    <label class="switch">
                      <input type="checkbox" name="richieste_informazioni_attive" value="1" <?php if (get_option('richieste_informazioni_attive')==1) echo 'checked="checked"' ?>>
                      <span class="slider round"></span>
                    </label>
                </td>
            </tr>
        </table>
        <?php submit_button(); ?>
    </form>
</div>
<?php }

function mivarip_tools_page(){
    $_ialman = new Ialman_Ops();

    $agg_domande = $_ialman->getMeta('aggiornamento_domande');
    $agg_corsi = $_ialman->getMeta('aggiornamento_corsi');
    $agg_anagrafica = $_ialman->getMeta('aggiornamento_anagrafica');
    ?>
    <div class="wrap">
        <?php echo mivar_header() ?>
        <h1>Strumenti di manutenzione</h1>
        <div id="maintenance-main">
            <ul>
                <li><a href="#mivarip-tab-1">Mappatura Tipologie Formative</a></li>
                <li><a href="#mivarip-tab-3">Mappatura Settori Formativi</a></li>
                <li><a href="#mivarip-tab-2">Update Manuale</a></li>
            </ul>
            <div id="mivarip-tab-1">
                <div class="tools_field">
                    <h4>Mappatura Tipologie Formative FVG (IALMan) su Tipologie Corsi</h4>
                    <?php
                    $tipologia_formativa_fvg = $_ialman->getReferenceTableValues( 'tipologia_formativa_fvg' );
                    $tipologia_scheda_corso = get_terms( array('taxonomy'=>'tipologia_corsi', 'hide_empty'=>false) );
                    $already_mapped = array();

                    $current_mapping = $_ialman->getTipologieFormativeMapping();
                    $tp_html = array();
                    foreach ($tipologia_scheda_corso as $term) {
                        if ( isset($current_mapping[$term->term_id]) ) {
                            foreach ($tipologia_formativa_fvg as $row) {
                                if ( in_array($row->ID, $current_mapping[$term->term_id]) ) {
                                    array_push($already_mapped, $row->ID);
                                    if ( !isset($tp_html[$term->term_id]) ) $tp_html[$term->term_id] = '';
                                    $tp_html[$term->term_id] .= '<span class="mivarip-draggable tipo_fvg" data-tipologia-id="' . $row->ID . '">' . $row->descrizione . '</span>';
                                }
                            }
                        }
                    }
                    ?>
                    <div id="tipologie_fvg_ext" class="mivarip-droppable">
                        <?php
                        foreach ($tipologia_formativa_fvg as $row) {
                            if ( ! in_array($row->ID, $already_mapped) )
                                echo '<span class="mivarip-draggable tipo_fvg" data-tipologia-id="' . $row->ID . '">' . $row->descrizione . '</span>';
                        }
                        ?>
                    </div>
                    <div id="tipologie_schede_corsi_ext" class="clearfix">
                        <?php
                        foreach ($tipologia_scheda_corso as $term) {
                            ?>
                            <div class="mivarip-tipologia_scheda_corso_box">
                                <h4><?php echo $term->name ?></h4>
                                <div class="mivarip-droppable" data-tipologia-assign="<?php echo $term->term_id ?>"><?php if ( isset($tp_html[$term->term_id]) ) echo $tp_html[$term->term_id]; ?></div>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                    <button id="salva_associazioni" class="button-primary">Salva associazioni</button>
                </div>
            </div>
            <div id="mivarip-tab-3">
                <div class="tools_field">
                    <h4>Mappatura Settori Formativi (IALMan) su Aree Corsi</h4>
                    <?php
                    $settore_formativo = $_ialman->getReferenceTableValues( 'settore_formativo' );
                    $aree_corsi = get_terms( array('taxonomy'=>'area_corsi', 'hide_empty'=>false) );
                    $already_mapped = array();

                    $current_mapping = $_ialman->getSettoriFormativiMapping();
                    $tp_html = array();
                    foreach ($aree_corsi as $term) {
                        if ( isset($current_mapping[$term->term_id]) ) {
                            foreach ($settore_formativo as $row) {
                                if ( in_array($row->ID, $current_mapping[$term->term_id]) ) {
                                    array_push($already_mapped, $row->ID);
                                    if ( !isset($tp_html[$term->term_id]) ) $tp_html[$term->term_id] = '';
                                    $tp_html[$term->term_id] .= '<span class="mivarip-draggable tipo_fvg" data-settore-id="' . $row->ID . '">' . $row->descrizione . '</span>';
                                }
                            }
                        }
                    }
                    ?>
                    <div id="settori_formativi_ext" class="mivarip-droppable">
                        <?php
                        foreach ($settore_formativo as $row) {
                            if ( ! in_array($row->ID, $already_mapped) )
                                echo '<span class="mivarip-draggable sett_fvg" data-settore-id="' . $row->ID . '">' . $row->descrizione . '</span>';
                        }
                        ?>
                    </div>
                    <div id="aree_corsi_ext" class="clearfix">
                        <?php
                        foreach ($aree_corsi as $term) {
                            ?>
                            <div class="mivarip-area_corso_box">
                                <h4><?php echo $term->name ?></h4>
                                <div class="mivarip-droppable" data-area-assign="<?php echo $term->term_id ?>"><?php if ( isset($tp_html[$term->term_id]) ) echo $tp_html[$term->term_id]; ?></div>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                    <button id="salva_associazioni_ac" class="button-primary">Salva associazioni</button>
                </div>
            </div>
            <div id="mivarip-tab-2">
                <div class="tools_field">
                    <h4>Update Manuale</h4>
                    <p>Queste funzioni permettono di fare una chiamata al server <strong>IALMAN</strong> e aggiornare i dati in tempo reale.<br />Da usare con parsimonia.</p>

                    <div class="update-box">
                        <p>Ultimo aggiornamento: <span class="latest"><?php echo !empty($agg_domande) ? date( 'd/m/Y H:i', $agg_domande+3600 ) : '-' ?></span></p>
                        <button class="button button-primary update-table" data-mii-table="domande" id="update_domanda">Aggiorna Domande</button>
                    </div>
                    <div class="update-box">
                        <p>Ultimo aggiornamento: <span class="latest"><?php echo !empty($agg_corsi) ? date( 'd/m/Y H:i', $agg_corsi+3600 ) : '-' ?></span></p>
                        <button class="button button-primary update-table" data-mii-table="corsi" id="update_corsi">Aggiorna Corsi</button>
                    </div>
                    <div class="update-box">
                        <p>Ultimo aggiornamento: <span class="latest"><?php echo !empty($agg_anagrafica) ? date( 'd/m/Y H:i', $agg_anagrafica+3600 ) : '-' ?></span></p>
                        <button class="button button-primary update-table" data-mii-table="anagrafica" id="update_corsi">Aggiorna Anagrafica</button>
                    </div>
                    <div id="update_console"></div>
                    <div class="update-box" style="margin-top:30px;">
                        <p>Usare questo bottone per eseguire un resync delle informazioni dei corsi, nel caso risultassero delle anomalie.</p>
                        <button class="button button-primary" id="resync_corsi">Resync Corsi</button> <span class="spinner" style="float: none;"></span>
                        <div id="esito_resync"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php }

function mivar_header(){
    return '<div id="mivar-backend-header"><img src="'.plugin_dir_url( __FILE__ ).'assets/img/IalPress-MiVar-header.png'. '" class="mivar-header" /></div>';
}