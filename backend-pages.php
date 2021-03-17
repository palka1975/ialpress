<?php

add_action( 'admin_enqueue_scripts', 'mivarip_backend_styles' );
/**
 * Enqueues styles.
 */

function mivarip_backend_styles() {
    wp_enqueue_script( 'mivarip-js', plugin_dir_url( __FILE__ ).'/assets/js/mivarip-scripts.js', array('jquery') );

    wp_enqueue_style( 'mivarip-css', plugin_dir_url( __FILE__ ).'/assets/css/mivarip-styles.css' );
}

// create custom plugin settings menu
add_action('admin_menu', 'mivarip_create_menu');
function mivarip_create_menu() {

	//create menu entries
    add_menu_page( 'Elenco Domande', 'IalPress', 'manage_options', 'ialpress_main', 'mivarip_main_page_output', 'dashicons-chart-pie' );
    // add_submenu_page( 'ialpress_main', 'IalPress Settings', 'Configurazione', 'manage_options', 'mivarip-settings', 'mivarip_settings_page' );
    add_submenu_page( 'ialpress_main', 'IalPress Tools', 'Utilities', 'manage_options', 'mivarip-tools', 'mivarip_tools_page' );

	//call register settings function
	// add_action( 'admin_init', 'register_mivarip_settings' );
}

// ajax
add_action( 'wp_ajax_mivarip_update_domanda', 'mivaripUpdateDomanda' );
add_action( 'wp_ajax_nopriv_mivarip_update_domanda', 'mivaripUpdateDomanda' );

function mivaripUpdateDomanda() {
    $hour = 12;
    $today              = strtotime($hour . ':00:00');
    $todayD             = date('Y-m-d', $today);
    $yesterday          = strtotime('-1 day', $today);
    $yesterdayD         = date('Y-m-d', $yesterday);
    $_ialman = new Ialman_Ops();
    $esito = $_ialman->updateDomanda( $yesterdayD );
    echo json_encode( $esito );
    die();
}

function mivarip_main_page_output() {
    $action = !empty($_REQUEST['action']) ? $_REQUEST['action'] : false;

    if ( $action=='view' ) {
        $domanda = !empty($_REQUEST['ialman_domanda']) ? $_REQUEST['ialman_domanda'] : 0;

        if ( $domanda!=0 ) {
            $_ialman = new Ialman_Ops();
            $item = $_ialman->getDomande( array('id'=>$domanda) );
        ?><div class="wrap">
            <h2>Visualizza Domanda</h2>
            <div>
                <table class="table table-bordered" style="border: 1px solid #555; background: #fff; padding: 10px 20px;">
                    <tbody>
                        <tr>
                            <td style="width: 201px">Nome</td>
                            <td><?php echo $item->nome; ?></td>
                        </tr>
                        <tr>
                            <td style="width: 201px">Cognome</td>
                            <td><?php echo $item->cognome; ?></td>
                        </tr>
                        <tr>
                            <td style="width: 201px">Partita IVA</td>
                            <td><?php echo $item->piva; ?></td>
                        </tr>
                        <tr>
                            <td style="width: 201px">Codice Fiscale</td>
                            <td><?php echo $item->cf; ?></td>
                        </tr>
                        <tr>
                            <td style="width: 201px">Sesso</td>
                            <td><?php echo $item->sesso; ?></td>
                        </tr>
                        <tr>
                            <td style="width: 201px">Data di nascita</td>
                            <td><?php echo $item->data_nascita; ?></td>
                        </tr>
                        <tr>
                            <td style="width: 201px">Luogo di nascita</td>
                            <td><?php echo $item->luogo_nascita; ?></td>
                        </tr>
                        <tr>
                            <td style="width: 201px">Indirizzo</td>
                            <td><?php echo $item->indirizzo; ?></td>
                        </tr>
                        <tr>
                            <td style="width: 201px">Città</td>
                            <td><?php echo $item->recapito; ?></td>
                        </tr>
                        <tr>
                            <td style="width: 201px">CAP</td>
                            <td><?php echo $item->cap; ?></td>
                        </tr>
                        <tr>
                            <td style="width: 201px">Provincia</td>
                            <td><?php echo  $item->prov ?></td>
                        </tr>
                        <tr>
                            <td style="width: 201px">Stato</td>
                            <td><?php echo  $item->stato ?></td>
                        </tr>
                        <tr>
                            <td style="width: 201px">Telefono</td>
                            <td><?php echo $item->telefono; ?></td>
                        </tr>
                        <tr>
                            <td style="width: 201px">Cellulare</td>
                            <td><?php echo $item->cellulare; ?></td>
                        </tr>
                        <tr>
                            <td style="width: 201px">Email</td>
                            <td><?php echo $item->mail; ?></td>
                        </tr>
                        <tr>
                            <td style="width: 201px">Corso</td>
                            <td><?php echo $item->descrizione; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div><?php
        } else { ?>
            <h2>Nessuna domanda selezionata</h2>
            <p><a href="<?php menu_page_url('ialpress_main') ?>">Torna all'elenco</a></p>
        <?php }

    } else {

        $domandeListTable = new Ialpress_Domande_List_Table();
        $domandeListTable->prepare_items();
        
        $s = !empty($_REQUEST['s']) ? $_REQUEST['s'] : '';
        ?>
        <div class="wrap">
            
            <div id="icon-users" class="icon32"><br/></div>
            <h2>Elenco Domande</h2>
            
            <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
            <form id="domande-filter" method="get">
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
	// register_setting( 'nhpr-settings-group', 'mivarip_number_of_stars' );
	// register_setting( 'nhpr-settings-group', 'mivarip_multiple_votes' );
	// register_setting( 'nhpr-settings-group', 'mivarip_star_on' );
	// register_setting( 'nhpr-settings-group', 'mivarip_star_off' );
}

function mivarip_settings_page() {
	wp_enqueue_media();
    return true;
	global $post;
?>
<div class="wrap">
<h1>IalPress</h1>
<form method="post" action="options.php">
    <?php settings_fields( 'nhpr-settings-group' ); ?>
    <?php do_settings_sections( 'nhpr-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        	<th scope="row">Numero di stelle</th>
        	<td><input type="text" name="mivarip_number_of_stars" value="<?php echo esc_attr( get_option('mivarip_number_of_stars') ); ?>" /></td>
        </tr>
        <tr valign="top">
        	<th scope="row">Gli utenti possono votare più volte</th>
        	<td><input type="checkbox" name="mivarip_multiple_votes" value="1" <?php if (get_option('mivarip_multiple_votes')==1) echo 'checked="checked"' ?> /></td>
        </tr>
        <tr>
        	<th scope="row">Immagine stella ON</th>
         	<td>
         		<?php
				$star_on_img_id = get_option( 'mivarip_star_on' );
				$star_on_img_src = wp_get_attachment_image_src( $star_on_img_id );
				$has_image = is_array( $star_on_img_src );
				?>

				<!-- Your image container, which can be manipulated with js -->
				<div class="custom-img-container">
				    <img class="preview_img" src="<?php if ( $has_image ) echo $star_on_img_src[0] ?>" alt="" style="max-width:100%;" />
				</div>

				<!-- Your add & remove image links -->
				<p class="hide-if-no-js">
				    <a class="upload-custom-img <?php if ( $has_image  ) { echo 'hidden'; } ?>" href="#">Carica immagine</a>
				    <a class="delete-custom-img <?php if ( ! $has_image  ) { echo 'hidden'; } ?>" href="#">Rimuovi immagine</a>
				</p>

				<!-- A hidden input to set and post the chosen image id -->
				<input class="upload_image_input" name="mivarip_star_on" id="mivarip_star_on" type="hidden" value="<?php echo esc_attr( $star_on_img_id ); ?>" />
    		</td>
    	</tr>
    	<tr>
        	<th scope="row">Immagine stella OFF</th>
			<td>
				<?php
				$star_off_img_id = get_option( 'mivarip_star_off' );
				$star_off_img_src = wp_get_attachment_image_src( $star_off_img_id );
				$has_image = is_array( $star_off_img_src );
				?>

				<!-- Your image container, which can be manipulated with js -->
				<div class="custom-img-container">
				    <img class="preview_img" src="<?php if ( $has_image ) echo $star_off_img_src[0] ?>" alt="" style="max-width:100%;" />
				</div>

				<!-- Your add & remove image links -->
				<p class="hide-if-no-js">
				    <a class="upload-custom-img <?php if ( $has_image  ) { echo 'hidden'; } ?>" href="#">Carica immagine</a>
				    <a class="delete-custom-img <?php if ( ! $has_image  ) { echo 'hidden'; } ?>" href="#">Rimuovi immagine</a>
				</p>

				<!-- A hidden input to set and post the chosen image id -->
				<input class="upload_image_input" name="mivarip_star_off" id="mivarip_star_off" type="hidden" value="<?php echo esc_attr( $star_off_img_id ); ?>" />
    		</td>
    	</tr>
    </table>
    
    <?php submit_button(); ?>

</form>
<script type="text/javascript">
var frame, custom_uploader;
jQuery(function($){
	// Set all variables to be used in scope
	var metaBox = $('#meta-box-id.postbox'), // Your meta box id here
		addImgLink = metaBox.find('.upload-custom-img'),
		delImgLink = metaBox.find( '.delete-custom-img'),
		imgContainer = metaBox.find( '.custom-img-container'),
		imgIdInput = metaBox.find( '.custom-img-id' );

    $('.upload-custom-img').on('click', function(e){
        e.preventDefault();
        let $this = $(this),
        	$p = $this.parent().parent(),
        	$del = $p.find('.delete-custom-img'),
        	$target = $p.find('.upload_image_input'),
        	$prev = $p.find('.preview_img');
        //If the uploader object has already been created, reopen the dialog
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }
        //Extend the wp.media object
        custom_uploader = wp.media({
            title: 'Scegli immagine',
            button: {
                text: 'Scegli immagine'
            },
            multiple: false
        });
        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function() {
            attachment = custom_uploader.state().get('selection').first().toJSON();
            $target.val(attachment.id);
            $prev.attr('src', attachment.url);
            $this.addClass('hidden');
            $del.removeClass('hidden');
            custom_uploader = false;
        });
        //Open the uploader dialog
        custom_uploader.open();
    });

	// DELETE IMAGE LINK
	$('.delete-custom-img').on('click', function(e){
		e.preventDefault();
		let $this = $(this),
        	$p = $this.parent().parent(),
        	$add = $p.find('.upload-custom-img'),
        	$target = $p.find('.upload_image_input'),
        	$prev = $p.find('.preview_img');
		$prev.attr('src', '');
		$add.removeClass('hidden');
		$this.addClass('hidden');
		$target.val('');
	});

});
</script>
</div>
<?php }

function mivarip_tools_page(){
    ?>
    <div class="wrap">
        <h1>Strumenti di manutenzione</h1>
        <div class="tools_field">
            <h3>Update Manuale</h3>
            <p>Queste funzioni permettono di fare una chiamata al server <strong>IALMAN</strong> e aggiornare i dati in tempo reale. Si prega di usare con parsimonia.</p>

            <button class="button button-primary" id="update_domanda">Aggiorna Domande</button> <button class="button button-primary" id="update_corsi">Aggiorna Corsi</button>
            <div id="update_console"></div>
        </div>
    </div>
<?php }