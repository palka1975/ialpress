<?php

class Ialpress_Iscrizioni_WS extends Ialpress_Cpt_Helper
{
	public static function register()
	{
		$plugin = new self();
		add_action( 'init', array( $plugin, 'mivar_iscrizioniws_create_post_types' ) );
		add_action( 'admin_init', array( $plugin, 'mivar_iscrizioniws_admin_init' ) );
		add_shortcode( 'mivar-form-iscrizione-new', array( $plugin, 'mivar_iscrizioniws_form' ) );
		add_action( 'template_redirect', array( $plugin, 'mivar_iscrizioniws_detect_form_submit' ) );
	}

	function mivar_iscrizioniws_create_post_types() {
		register_post_type( 'iscrizioniws',
			array(
				'labels' => array(
					'name' => 'Iscrizioni IALMan',
					'singular_name' => 'Iscrizione IALMan',
					'add_new' => 'Aggiungi',
					'add_new_item' => 'Aggiungi Iscrizione IALMan',
					'edit' => 'Modifica',
					'edit_item' => 'Modifica Iscrizione IALMan',
					'new_item' => 'Nuova Iscrizione IALMan',
					'view' => 'Vedi',
					'view_item' => 'Vedi Iscrizione IALMan',
					'search_items' => 'Cerca Iscrizioni IALMan',
					'not_found' => 'Nessuna iscrizione IALMan trovata',
					'not_found_in_trash' => 'Nessuna iscrizione IALMan nel cestino',
					'parent' => 'Iscrizione IALMan genitore'
				),
				'public' => true,
				'menu_position' => 20,
				'show_in_menu' => false,
				'supports' => array( 'title' ),
				'taxonomies' => array( '' ),
				'menu_icon' => "dashicons-id-alt",
				'has_archive' => false,
				'exclude_from_search' => true,
			)
		);
	}

// isc_nome
// isc_cognome
// isc_codfis
// isc_datanascita_ws
// isc_sesso
// isc_indirizzo
// isc_citta
// isc_provincia
// isc_cap
// isc_stato
// isc_statonascita
// isc_luogonascita
// isc_cittadinanza
// isc_email
// isc_cellulare
// isc_citta_id
// isc_stato_id
// isc_statonascita_id
// isc_luogonascita_id
// isc_cittadinanza_id
// isc_newsletter

	function mivar_iscrizioniws_admin_init() {
		$plugin = new self();
		add_meta_box( 'mivar_iscrizioniws_details_meta_box', 'Dettagli iscrizione', array( $plugin, 'mivar_iscrizioniws_display_details_meta_box' ), 'iscrizioniws', 'normal', 'high' );
	}
	function mivar_iscrizioniws_display_details_meta_box( $isc ) {
		$isc_nome = esc_html( get_post_meta( $isc->ID, 'isc_nome', true ) );
		$isc_cognome = esc_html( get_post_meta( $isc->ID, 'isc_cognome', true ) );
		$isc_codfis = esc_html( get_post_meta( $isc->ID, 'isc_codfis', true ) );
		$isc_datanascita = esc_html( get_post_meta( $isc->ID, 'isc_datanascita_ws', true ) );
		$isc_sesso = esc_html( get_post_meta( $isc->ID, 'isc_sesso', true ) );
		$isc_indirizzo = esc_html( get_post_meta( $isc->ID, 'isc_indirizzo', true ) );
		$isc_citta = esc_html( get_post_meta( $isc->ID, 'isc_citta', true ) );
		$isc_provincia = esc_html( get_post_meta( $isc->ID, 'isc_provincia', true ) );
		$isc_cap = esc_html( get_post_meta( $isc->ID, 'isc_cap', true ) );
		$isc_stato = esc_html( get_post_meta( $isc->ID, 'isc_stato', true ) );
		$isc_statonascita = esc_html( get_post_meta( $isc->ID, 'isc_statonascita', true ) );
		$isc_luogonascita = esc_html( get_post_meta( $isc->ID, 'isc_luogonascita', true ) );
		$isc_cittadinanza = esc_html( get_post_meta( $isc->ID, 'isc_cittadinanza', true ) );
		$isc_email = esc_html( get_post_meta( $isc->ID, 'isc_email', true ) );
		$isc_cellulare = esc_html( get_post_meta( $isc->ID, 'isc_cellulare', true ) );
		$isc_timestamp = get_post_meta( $isc->ID, 'isc_timestamp', true );
		$isc_corso = intval( get_post_meta( $isc->ID, 'isc_corso', true ) );
		$isc_corso_nome = get_post_meta( $isc->ID, 'isc_corso_nome', true );
		$isc_newsletter = intval( get_post_meta( $isc->ID, 'isc_newsletter', true ) );
		?>
		<table class="table table-bordered">
			<tbody>
				<tr>
					<td style="width: 201px">Nome</td>
					<td><input type='text' size='80' name='isc_nome' value='<?php echo $isc_nome; ?>' /></td>
				</tr>
				<tr>
					<td style="width: 201px">Cognome</td>
					<td><input type='text' size='80' name='isc_cognome' value='<?php echo $isc_cognome; ?>' /></td>
				</tr>
				<tr>
					<td style="width: 201px">Codice Fiscale</td>
					<td><input type='text' size='80' maxlength="16" name='isc_codfis' value='<?php echo $isc_codfis; ?>' /></td>
				</tr>
				<tr>
					<td style="width: 201px">Data di Nascita</td>
					<td><input type='text' size='80' name='isc_datanascita' value='<?php echo $isc_datanascita; ?>' /></td>
				</tr>
				<tr>
					<td style="width: 201px">Sesso</td>
					<td>
						<select style="width: 100px" name="isc_sesso">
							<option value="">...</option>
							<?php foreach ($this->sessi as $s) { ?>
							<option value="<?php echo $s; ?>" <?php selected( $s, $isc_sesso ); ?>><?php echo $s; ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<td style="width: 201px">Indirizzo</td>
					<td><input type='text' size='80' maxlength="255" name='isc_indirizzo' value='<?php echo $isc_indirizzo; ?>' /></td>
				</tr>
				<tr>
					<td style="width: 201px">Città</td>
					<td><input type='text' size='80' name='isc_citta' value='<?php echo $isc_citta; ?>' /></td>
				</tr>
				<tr>
					<td style="width: 201px">Provincia</td>
					<td>
						<select style="width: 200px" name="isc_provincia">
							<option value="">...</option>
							<?php foreach ($this->province as $sigla=>$prov) { ?>
							<option value="<?php echo $sigla; ?>" <?php selected( $sigla, $isc_provincia ); ?>><?php echo $prov; ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<td style="width: 201px">CAP</td>
					<td><input type='text' size='5' maxlength="5" name='isc_cap' value='<?php echo $isc_cap; ?>' /></td>
				</tr>
				<tr>
					<td style="width: 201px">Stato</td>
					<td><input type='text' size='80' name='isc_stato' value='<?php echo $isc_stato; ?>' /></td>
				</tr>
				<tr>
					<td style="width: 201px">Stato di Nascita</td>
					<td><input type='text' size='80' name='isc_statonascita' value='<?php echo $isc_statonascita; ?>' /></td>
				</tr>
				<tr>
					<td style="width: 201px">Luogo di Nascita</td>
					<td><input type='text' size='80' name='isc_luogonascita' value='<?php echo $isc_luogonascita; ?>' /></td>
				</tr>
				<tr>
					<td style="width: 201px">Cittadinanza</td>
					<td><input type='text' size='80' name='isc_cittadinanza' value='<?php echo $isc_cittadinanza; ?>' /></td>
				</tr>
				<tr>
					<td style="width: 201px">Cellulare</td>
					<td><input type='text' size='80' name='isc_cellulare' value='<?php echo $isc_cellulare; ?>' /></td>
				</tr>
				<tr>
					<td style="width: 201px">Email</td>
					<td><input type='text' size='80' name='isc_email' value='<?php echo $isc_email; ?>' /></td>
				</tr>
				<tr>
					<td style="width: 201px">Corso</td>
					<td><input type='hidden' name='isc_corso' value='<?php echo $isc_corso; ?>'/>
						<?php
						if ( !empty($isc_corso_nome) ) echo '<p>' . $isc_corso_nome . '</p>';
						else echo '<p>n.a.</p>';
						?>
					</td>
				</tr>
				<tr>
					<td style="width: 201px">Iscrizione Newsletter</td>
					<td>
						<select style="width: 80px" name="isc_newsletter">
							<option value="1" <?php selected( 1, $isc_newsletter ); ?>>Sì</option>
							<option value="0" <?php selected( 0, $isc_newsletter ); ?>>No</option>
						</select>
					</td>
				</tr>
				<tr>
					<td style="width: 201px">Timestamp</td>
					<td><input type='hidden' name='isc_timestamp' value='<?php echo $isc_timestamp; ?>'/>
						<?php
						$t = explode('.', $isc_timestamp);
						$dt = new DateTime('@'.$t[0]);
						$dt->setTimeZone(new DateTimeZone('Europe/Rome'));
						// echo $dt->format('F j, Y, g:i a');
						echo '<p>' . $dt->format('d/m/Y H:i:s') . '.' . $t[1] . '</p>';
						?>
					</td>
				</tr>
			</tbody>
		</table>

	<?php }

	function mivar_iscrizioniws_form() {
		if ( !empty( $_GET['nisc'] ) && wp_verify_nonce( $_REQUEST['tokn'], 'new-iscrizione' ) ): ?>
		
		<div class="row">
			<div class="col-md-12">
				<p><?php _e('Grazie per esserti iscritto. Ecco i dati che abbiamo registrato.'); ?></p>
				<p><?php _e('Abbiamo spedito questo riepilogo alla mail che hai indicato nella registrazione. Se non hai ricevuto la mail, controlla la tua casella SPAM (posta indesiderata) o controlla che il tuo indirizzo mail sia stato inserito correttamente. In caso contrario ti consigliamo di compilare nuovamente il form di iscrizione partendo dalla scheda corso.') ?></p>
				<?php
					$nisc = esc_sql( $_GET['nisc'] );
					$isc = get_post( $nisc );
					$isc_corso = intval( get_post_meta( $isc->ID, 'isc_corso', true ) );
					if ( !empty($isc_corso) ) {
						// $pcorso = get_post( $isc_corso );
						// $corso = get_the_title( $isc_corso );
						// echo '<p>' . __('Si prega di salvare la ricevuta in formato PDF.') . '</p>';
					}
					if ( is_object($isc) ) {
						echo $this->mivar_iscrizioniws_display_details($isc);

						// if ( !empty($isc_corso) ) {
						if ( false ) {
					?>
					<script type="text/javascript">
				        jQuery(document).ready( function($){
				            window.open('/pdf_receipt_ws.php?nisc=<?php echo $nisc ?>', 'preview', 'menubar=no, scrollbars=auto, toolbar=no, resizable=yes, width=1050, height=600').focus();
				        });
				    </script>
				<?php 	}
					} ?>
			</div>
		</div>
		
		<?php
		else:

			$get_corso = isset($_REQUEST['getc']) ? $_REQUEST['getc'] : '';

			$corso_ialman = get_post_meta( $get_corso, 'corso_ialman', true );
			?>

			<form method="post" id="form_preiscrizione_ws" action="">
				<!-- Nonce fields to verify visitor provenance -->
				<?php wp_nonce_field( 'add_iscrizione', 'mivar_iscws_form' ); ?>

			    <!-- Post variable to indicate user-submitted items -->
			    <input type="hidden" name="misc_form_submitws" value="1" />
				<input type="hidden" name="isc_corso" id="isc_corso" value="<?php echo $corso_ialman ?>" />
				<input type="hidden" name="isc_corso_civi" id="isc_corso_civi" value="<?php echo $get_corso ?>" />
				<input type="hidden" name="isc_corso_nome" id="hid_isc_corso_nome" value="<?php echo sanitize_title( get_the_title( $get_corso ) ) ?>" />
				<?php
					$terms = get_the_terms( $get_corso, 'tipologia_corsi' );
					$tipologie = [];
					foreach( $terms as $term ) {
						$tipologie[] = $term->slug;
					}
					if ( in_array( 'ifts-post-diploma', $tipologie ) ) $tipo_corso = 'IFTS';
					else if ( in_array( 'piazzagol', $tipologie ) ) $tipo_corso = 'PG';
					// else if ( in_array( 'pipol-soft-skills', $tipologie ) ) $tipo_corso = 'PSR';
					else $tipo_corso = '';

					$terms = get_the_terms( $get_corso, 'sede_corso' );
					$sedi = [];
					foreach( $terms as $term ) {
						$sedi[] = $term->slug;
					}
					if ( in_array( 'trieste', $sedi ) ) $sede_corso = 'TS';
					else if ( in_array( 'cividale', $sedi ) ) $sede_corso = 'CIVI';
					else $sede_corso = '';
				?>
				<input type="hidden" name="isc_corso_tipo" id="hid_isc_corso_tipo" value="<?php echo $tipo_corso ?>" />
				<input type="hidden" name="isc_corso_sede" id="hid_isc_corso_sede" value="<?php echo $sede_corso ?>" />

				<h4>Dati anagrafici</h4>
				<div class="form-row">
					<div class="form-group col-md-6">
						<label class="control-label" for="isc_nome"><?php _e( 'Nome', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<input type="text" class="form-control" id="isc_nome" name="isc_nome" placeholder="<?php _e( 'Nome', 'mivar_iscrizioni_plugin' ) ?>" required>
					</div>
					<div class="form-group col-md-6">
						<label class="control-label" for="isc_cognome"><?php _e( 'Cognome', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<input type="text" class="form-control" id="isc_cognome" name="isc_cognome" placeholder="<?php _e( 'Cognome', 'mivar_iscrizioni_plugin' ) ?>" required>
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-md-6">
						<label class="control-label" for="isc_codfis"><?php _e( 'Codice Fiscale', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<input type="text" class="form-control" id="isc_codfis" name="isc_codfis" placeholder="<?php _e( 'Codice Fiscale', 'mivar_iscrizioni_plugin' ) ?>" required>
					</div>
					<div class="form-group col-md-4">
						<label class="control-label" for="isc_datanascita_ws"><?php _e( 'Data di Nascita', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<input type="text" class="form-control" id="isc_datanascita_ws" name="isc_datanascita_ws" placeholder="<?php _e( 'Data di Nascita', 'mivar_iscrizioni_plugin' ) ?>" required>
					</div>
					<div class="form-group col-md-2">
						<label class="control-label" for="isc_cap"><?php _e( 'Sesso', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<select name="isc_sesso" id="isc_sesso" class="form-control" required>
							<option value="">...</option>
							<?php foreach ($this->sessi as $s) { ?>
							<option value="<?php echo $s; ?>"><?php echo $s; ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<h4>Residenza</h4>
				<div class="form-row">
					<div class="form-group col-md-12">
						<label class="control-label" for="isc_indirizzo"><?php _e( 'Indirizzo', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<input type="text" class="form-control" id="isc_indirizzo" name="isc_indirizzo" placeholder="<?php _e( 'Indirizzo', 'mivar_iscrizioni_plugin' ) ?>" required>
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-md-6">
						<label class="control-label" for="isc_citta"><?php _e( 'Città', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<input type="text" class="form-control" id="isc_citta" name="isc_citta" placeholder="<?php _e( 'Città', 'mivar_iscrizioni_plugin' ) ?>" required>
					</div>
					<div class="form-group col-md-4">
						<label class="control-label" for="isc_provincia"><?php _e( 'Provincia', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<select id="isc_provincia" name="isc_provincia" class="form-control" required>
							<option value="">...</option>
							<?php foreach ($this->province as $sigla=>$prov) { ?>
							<option value="<?php echo $sigla; ?>"><?php echo $prov; ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="form-group col-md-2">
						<label class="control-label" for="isc_cap"><?php _e( 'CAP', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<input type="text" class="form-control" id="isc_cap" name="isc_cap" placeholder="<?php _e( 'CAP', 'mivar_iscrizioni_plugin' ) ?>" required>
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-md-6">
						<label class="control-label" for="isc_stato"><?php _e( 'Stato', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<input type="text" class="form-control" id="isc_stato" name="isc_stato" placeholder="<?php _e( 'Stato', 'mivar_iscrizioni_plugin' ) ?>" required>
					</div>
					<div class="form-group col-md-6">&nbsp;</div>
				</div>
				<h4 style="clear: both;">Nascita e cittadinanza</h4>
				<div class="form-row">
					<div class="form-group col-md-6 form-check form-switch">
						<label class="control-label" for="isc_statonascita"><?php _e( 'Stato di Nascita', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<input type="text" class="form-control" id="isc_statonascita" name="isc_statonascita" placeholder="<?php _e( 'Stato di Nascita', 'mivar_iscrizioni_plugin' ) ?>" required>
					</div>
					<div class="form-group col-md-6">
						<label class="control-label" for="isc_luogonascita"><?php _e( 'Località di Nascita', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<input type="text" class="form-control" id="isc_luogonascita" name="isc_luogonascita" placeholder="<?php _e( 'Località di Nascita', 'mivar_iscrizioni_plugin' ) ?>" required>
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-md-6">
						<label class="control-label" for="isc_cittadinanza"><?php _e( 'Cittadinanza', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<input type="text" class="form-control" id="isc_cittadinanza" name="isc_cittadinanza" placeholder="<?php _e( 'Cittadinanza', 'mivar_iscrizioni_plugin' ) ?>" required>
					</div>
					<div class="form-group col-md-6">&nbsp;</div>
				</div>
				<h4 style="clear: both;">Contatti</h4>
				<div class="form-row">
					<div class="form-group col-md-6">
						<label class="control-label" for="isc_email"><?php _e( 'Email', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<input type="email" class="form-control" id="isc_email" name="isc_email" placeholder="<?php _e( 'Email', 'mivar_iscrizioni_plugin' ) ?>" required>
					</div>
					<div class="form-group col-md-6">
						<label for="isc_cellulare"><?php _e( 'Cellulare', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<input type="tel" class="form-control" id="isc_cellulare" name="isc_cellulare" placeholder="<?php _e( 'Cellulare', 'mivar_iscrizioni_plugin' ) ?>" required>
					</div>
				</div>

				<div class="form-row">
					<div class="form-group col-md-12">
						<textarea class="form-control" id="isc_info" rows="5" readonly="readonly"><?php echo $this->testo_info_privacy ?></textarea>
					</div>
				</div>

				<div class="form-row">
					<div class="form-group col-sm-12">
						<div class="checkbox">
							<label>
								<input type="checkbox" is="isc_consenso" name="isc_consenso" value="1" required> <?php _e('Presto il mio consenso al trattamento dei dati personali da voi richiesti', 'mivar_iscrizioni_plugin') ?>
							</label>
						</div>
					</div>
				</div>

				<div class="form-row">
					<div class="form-group col-sm-12">
						<div class="checkbox">
							<label>
								<input type="checkbox" is="isc_newsletter" name="isc_newsletter" value="1"> <?php _e('Iscrivimi alla newsletter per tenermi informato sulle novità di Civiform.<br>La newsletter ti verrà inviata nella tua casella di posta elettronica rispettando scrupolosamente la nostra politica sulla privacy.', 'mivar_iscrizioni_plugin') ?>
							</label>
						</div>
					</div>
				</div>

				<div class="form-row">
					<div class="form-group col-md-12">
						<input type="hidden" id="isc_citta_id" name="isc_citta_id" value="">
						<input type="hidden" id="isc_stato_id" name="isc_stato_id" value="">
						<input type="hidden" id="isc_statonascita_id" name="isc_statonascita_id" value="">
						<input type="hidden" id="isc_luogonascita_id" name="isc_luogonascita_id" value="">
						<input type="hidden" id="isc_cittadinanza_id" name="isc_cittadinanza_id" value="">
						<input type="submit" class="btn btn-default" name="submit" id="ipws_submit_main" value="<?php _e('Invia', 'mivar_iscrizioni_plugin') ?>" /> <span class="spinner"></span>
					</div>
				</div>
			</form>
			<?php
		endif;
	}

	function mivar_iscrizioniws_detect_form_submit( $template ) {	
		
		if ( !empty( $_POST['misc_form_submitws'] ) ) {
			$this->mivar_iscrizioniws_process_form_submit();
		} else {
			return $template;
		}		
	}
	function mivar_iscrizioniws_process_form_submit() {

		// $_POST['isc_nome']
		// $_POST['isc_cognome']
		// $_POST['isc_codfis']
		// $_POST['isc_datanascita_ws']
		// $_POST['isc_sesso']
		// $_POST['isc_indirizzo']
		// $_POST['isc_citta']
		// $_POST['isc_provincia']
		// $_POST['isc_cap']
		// $_POST['isc_stato']
		// $_POST['isc_statonascita']
		// $_POST['isc_luogonascita']
		// $_POST['isc_cittadinanza']
		// $_POST['isc_email']
		// $_POST['isc_cellulare']
		// $_POST['isc_citta_id']
		// $_POST['isc_stato_id']
		// $_POST['isc_statonascita_id']
		// $_POST['isc_luogonascita_id']
		// $_POST['isc_cittadinanza_id']
		// $_POST['isc_newsletter']

		if ( PHP_SESSION_NONE == session_status() ) {
			session_start();
		}
		// Check that all required fields are present and non-empty
		if ( wp_verify_nonce( $_POST['mivar_iscws_form'], 'add_iscrizione' ) && 
				! empty( $_POST['isc_nome'] ) &&
				! empty( $_POST['isc_cognome'] ) &&
				! empty( $_POST['isc_codfis'] ) &&
				! empty( $_POST['isc_datanascita_ws'] ) &&
				! empty( $_POST['isc_sesso'] ) &&
				! empty( $_POST['isc_indirizzo'] ) &&
				! empty( $_POST['isc_citta'] ) &&
				! empty( $_POST['isc_provincia'] ) &&
				! empty( $_POST['isc_cap'] ) &&
				! empty( $_POST['isc_stato'] ) &&
				! empty( $_POST['isc_statonascita'] ) &&
				! empty( $_POST['isc_luogonascita'] ) &&
				! empty( $_POST['isc_cittadinanza'] ) &&
				! empty( $_POST['isc_email'] ) &&
				! empty( $_POST['isc_cellulare'] ) &&
				! empty( $_POST['isc_citta_id'] ) &&
				! empty( $_POST['isc_stato_id'] ) &&
				! empty( $_POST['isc_statonascita_id'] ) &&
				! empty( $_POST['isc_luogonascita_id'] ) &&
				! empty( $_POST['isc_cittadinanza_id'] )
			) {

			// Create array with received data
			$isc = array(
				'post_status' => 'publish',
				'post_title' => $_POST['isc_cognome'] . ' ' . $_POST['isc_nome'],
				'post_type' => 'iscrizioniws',
			);

			// Insert new post in site database
			// Store new post ID from return value in variable
			$nid = wp_insert_post( $isc );
			$pid = get_post( $nid );

			$this->mivar_iscrizioniws_add_custom_fields( $nid, $pid );

			// invio mail ADMIN
			$id_corso = $_POST['isc_corso_civi'];
			$nome_corso = $_POST['isc_corso_nome'];
			$is_pipol = has_term( 'pipol', 'tipologia_corsi', $id_corso ) || has_term( 'pipol-soft-skills', 'tipologia_corsi', $id_corso );
			$subject = '[Civiform] Nuova richiesta di preiscrizione dal sito';
			$body = '<p>Nuova richiesta di iscrizione al corso ' . $nome_corso . ' sul sito Civiform.it</p>' . $this->mivar_iscrizioniws_display_details($pid);
			$headers = array('Content-Type: text/html; charset=UTF-8', 'From: Civiform.it <no-reply@civiform.it>');
			wp_mail( $this->to_send, $subject, $body, $headers );

			// invio mail al soggetto
			$subject = '[Civiform] La tua iscrizione';
			$mail_to = $_POST['isc_email'];
			if ( $is_pipol ) {
				$body = '<p>Grazie per esserti preiscritto al corso ' . $nome_corso . ', di seguito i dati che hai inserito:</p>' . $this->mivar_iscrizioniws_display_details($pid);
				$body .= '<p>Ti ricordiamo che questa <strong>non è un\'iscrizione definitiva</strong>: per essere formalizzata, deve essere presa in carico dal Centro per l\'Impiego di competenza.<br>
				Se non sei iscritto al programma PIPOL, registrati in modo autonomo sul portale: http://www.regione.fvg.it/rafvg/cms/RAFVG/formazione-lavoro/lavoro/FOGLIA135/ <br>
				oppure recandoti presso un Centro per l’Impiego (http://www.regione.fvg.it/rafvg/cms/RAFVG/_config_/resp/tmpl-custom/mappairdat.jsp).<br>
				Ti contatteremo al più presto per perfezionare la tua iscrizione.</p>';
			}
			else $body = '<p>Grazie per esserti iscritto al corso ' . $nome_corso . ', di seguito i dati che hai inserito:</p>' . $this->mivar_iscrizioniws_display_details($pid);

			$body .= '<p>Per dubbi o informazioni, contattaci direttamente:<br>Cividale: +390432705811 - info@civiform.it<br>Trieste: +390409719811 - info@civiform.it</p>';
			$headers = array('Content-Type: text/html; charset=UTF-8', 'From: Civiform.it <segreteria@civiform.it>');
			wp_mail( $mail_to, $subject, $body, $headers );


			// redirect
			$redirect_address = ( empty( $_POST['_wp_http_referer'] ) ? site_url() : $_POST['_wp_http_referer'] );
			wp_redirect( add_query_arg( array(
				'nisc' => $nid,
				'tokn' => esc_attr( wp_create_nonce('new-iscrizione') ),
				), remove_query_arg('getc', $redirect_address) ) );
			exit;
		} else {
			// Display error message if any required fields are missing
			$abort_message = __('Form incompleto. Impossibile registrare la preiscrizione. Si prega di riprovare.', 'mivar_iscrizioni_plugin');
			wp_die( $abort_message ); 
			exit;
		}
	}

	function mivar_iscrizioniws_ajax_finalize($isc_nome,$isc_cognome,$isc_codfis,$isc_datanascita_ws,$isc_sesso,$isc_indirizzo,$isc_citta,$isc_provincia,$isc_cap,$isc_stato,$isc_statonascita,$isc_luogonascita,$isc_cittadinanza,$isc_email,$isc_cellulare,$isc_citta_id,$isc_stato_id,$isc_statonascita_id,$isc_luogonascita_id,$isc_cittadinanza_id,$isc_corso,$isc_corso_civi,$isc_corso_nome,$isc_newsletter) {
		$isc = array(
			'post_status' => 'publish',
			'post_title' => $isc_cognome . ' ' . $isc_nome,
			'post_type' => 'iscrizioniws',
		);
		$nid = wp_insert_post( $isc );
		$pid = get_post( $nid );
		
		update_post_meta( $nid, 'isc_nome', sanitize_text_field( $isc_nome ) );
		update_post_meta( $nid, 'isc_cognome', sanitize_text_field( $isc_cognome ) );
		update_post_meta( $nid, 'isc_codfis', sanitize_text_field( $isc_codfis ) );
		update_post_meta( $nid, 'isc_datanascita_ws', sanitize_text_field( $isc_datanascita_ws ) );
		update_post_meta( $nid, 'isc_sesso', sanitize_text_field( $isc_sesso ) );
		update_post_meta( $nid, 'isc_indirizzo', sanitize_text_field( $isc_indirizzo ) );
		update_post_meta( $nid, 'isc_citta', sanitize_text_field( $isc_citta ) );
		update_post_meta( $nid, 'isc_provincia', sanitize_text_field( $isc_provincia ) );
		update_post_meta( $nid, 'isc_cap', sanitize_text_field( $isc_cap ) );
		update_post_meta( $nid, 'isc_stato', sanitize_text_field( $isc_stato ) );
		update_post_meta( $nid, 'isc_statonascita', sanitize_text_field( $isc_statonascita ) );
		update_post_meta( $nid, 'isc_luogonascita', sanitize_text_field( $isc_luogonascita ) );
		update_post_meta( $nid, 'isc_cittadinanza', sanitize_text_field( $isc_cittadinanza ) );
		update_post_meta( $nid, 'isc_email', sanitize_text_field( $isc_email ) );
		update_post_meta( $nid, 'isc_cellulare', sanitize_text_field( $isc_cellulare ) );
		update_post_meta( $nid, 'isc_citta_id', sanitize_text_field( $isc_citta_id ) );
		update_post_meta( $nid, 'isc_stato_id', sanitize_text_field( $isc_stato_id ) );
		update_post_meta( $nid, 'isc_statonascita_id', sanitize_text_field( $isc_statonascita_id ) );
		update_post_meta( $nid, 'isc_luogonascita_id', sanitize_text_field( $isc_luogonascita_id ) );
		update_post_meta( $nid, 'isc_cittadinanza_id', sanitize_text_field( $isc_cittadinanza_id ) );
		update_post_meta( $nid, 'isc_corso', $isc_corso );
		update_post_meta( $nid, 'isc_corso_civi', $isc_corso_civi );
		update_post_meta( $nid, 'isc_corso_nome', $isc_corso_nome );
		update_post_meta( $nid, 'isc_newsletter', $isc_newsletter );
		$isc_timestamp = microtime(true);
		update_post_meta( $nid, 'isc_timestamp', $isc_timestamp );


		// invio mail ADMIN
		$id_corso = $isc_corso_civi;
		$nome_corso = $isc_corso_nome;
		$is_pipol = has_term( 'pipol', 'tipologia_corsi', $id_corso ) || has_term( 'pipol-soft-skills', 'tipologia_corsi', $id_corso );
		$subject = '[Civiform] Nuova richiesta di preiscrizione dal sito';
		$body = '<p>Nuova richiesta di iscrizione al corso ' . $nome_corso . ' sul sito Civiform.it</p>' . $this->mivar_iscrizioniws_display_details($pid);
		$headers = array('Content-Type: text/html; charset=UTF-8', 'From: Civiform.it <no-reply@civiform.it>');
		wp_mail( $this->to_send, $subject, $body, $headers );

		// invio mail al soggetto
		$subject = '[Civiform] La tua iscrizione';
		$mail_to = $isc_email;
		if ( $is_pipol ) {
			$body = '<p>Grazie per esserti preiscritto al corso ' . $nome_corso . ', di seguito i dati che hai inserito:</p>' . $this->mivar_iscrizioniws_display_details($pid);
			$body .= '<p>Ti ricordiamo che questa <strong>non è un\'iscrizione definitiva</strong>: per essere formalizzata, deve essere presa in carico dal Centro per l\'Impiego di competenza.<br>
			Se non sei iscritto al programma PIPOL, registrati in modo autonomo sul portale: http://www.regione.fvg.it/rafvg/cms/RAFVG/formazione-lavoro/lavoro/FOGLIA135/ <br>
			oppure recandoti presso un Centro per l’Impiego (http://www.regione.fvg.it/rafvg/cms/RAFVG/_config_/resp/tmpl-custom/mappairdat.jsp).<br>
			Ti contatteremo al più presto per perfezionare la tua iscrizione.</p>';
		}
		else $body = '<p>Grazie per esserti iscritto al corso ' . $nome_corso . ', di seguito i dati che hai inserito:</p>' . $this->mivar_iscrizioniws_display_details($pid);

		$body .= '<p>Per dubbi o informazioni, contattaci direttamente:<br>Cividale: +390432705811 - info@civiform.it<br>Trieste: +390409719811 - info@civiform.it</p>';
		$headers = array('Content-Type: text/html; charset=UTF-8', 'From: Civiform.it <segreteria@civiform.it>');
		wp_mail( $mail_to, $subject, $body, $headers );

		// return html
		$html = '<div class="col-md-12">';
		$html .= '<p>' . __('Grazie per esserti iscritto. Ecco i dati che abbiamo registrato.') . '</p>';
		$html .= '<p>' . __('Abbiamo spedito questo riepilogo alla mail che hai indicato nella registrazione. Se non hai ricevuto la mail, controlla la tua casella SPAM (posta indesiderata) o controlla che il tuo indirizzo mail sia stato inserito correttamente. In caso contrario ti consigliamo di compilare nuovamente il form di iscrizione partendo dalla scheda corso.') . '</p>';
		if ( is_object($pid) ) {
			$html .= $this->mivar_iscrizioniws_display_details($pid);
		}
		$html .= '</div>';

		return $html;
	}		

	function mivar_iscrizioniws_display_details( $isc ) { 
		$isc_nome = esc_html( get_post_meta( $isc->ID, 'isc_nome', true ) );
		$isc_cognome = esc_html( get_post_meta( $isc->ID, 'isc_cognome', true ) );
		$isc_codfis = esc_html( get_post_meta( $isc->ID, 'isc_codfis', true ) );
		$isc_datanascita_ws = esc_html( get_post_meta( $isc->ID, 'isc_datanascita_ws', true ) );
		$isc_sesso = esc_html( get_post_meta( $isc->ID, 'isc_sesso', true ) );
		$isc_indirizzo = esc_html( get_post_meta( $isc->ID, 'isc_indirizzo', true ) );
		$isc_citta = esc_html( get_post_meta( $isc->ID, 'isc_citta', true ) );
		$isc_provincia = esc_html( get_post_meta( $isc->ID, 'isc_provincia', true ) );
		$isc_cap = esc_html( get_post_meta( $isc->ID, 'isc_cap', true ) );
		$isc_stato = esc_html( get_post_meta( $isc->ID, 'isc_stato', true ) );
		$isc_statonascita = esc_html( get_post_meta( $isc->ID, 'isc_statonascita', true ) );
		$isc_luogonascita = esc_html( get_post_meta( $isc->ID, 'isc_luogonascita', true ) );
		$isc_cittadinanza = esc_html( get_post_meta( $isc->ID, 'isc_cittadinanza', true ) );
		$isc_email = esc_html( get_post_meta( $isc->ID, 'isc_email', true ) );
		$isc_cellulare = esc_html( get_post_meta( $isc->ID, 'isc_cellulare', true ) );
		$isc_corso_nome = esc_html( get_post_meta( $isc->ID, 'isc_corso_nome', true ) );
		$isc_newsletter = intval( get_post_meta( $isc->ID, 'isc_newsletter', true ) );

		$html = '<table class="table table-bordered">
			<tbody>
				<tr>
					<td style="width: 201px">Nome</td>
					<td>' . $isc_nome . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Cognome</td>
					<td>' . $isc_cognome . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Codice Fiscale</td>
					<td>' . $isc_codfis . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Data di Nascita</td>
					<td>' . $isc_datanascita_ws . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Sesso</td>
					<td>' . $isc_sesso . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Indirizzo</td>
					<td>' . $isc_indirizzo . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Città</td>
					<td>' . $isc_citta . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Provincia</td>
					<td>' . $isc_provincia . '</td>
				</tr>
				<tr>
					<td style="width: 201px">CAP</td>
					<td>' . $isc_cap . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Stato</td>
					<td>' . $isc_stato . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Stato di Nascita</td>
					<td>' . $isc_statonascita . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Luogo di Nascita</td>
					<td>' . $isc_luogonascita . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Cittadinanza</td>
					<td>' . $isc_cittadinanza . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Email</td>
					<td>' . $isc_email . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Cellulare</td>
					<td>' . $isc_cellulare . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Corso</td>
					<td>' . $isc_corso_nome . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Iscrizione Newsletter</td>
					<td>' . ($isc_newsletter==1 ? 'Sì' : 'No') . '</td>
				</tr>
			</tbody>
		</table>';

		return $html;
	}

	function mivar_iscrizioniws_add_custom_fields( $post_id = false, $post = false ) {
		if ( 'iscrizioniws' == $post->post_type ) {
			// Store data in post meta table if present in post data
			if ( isset( $_POST['isc_nome'] ) ) {
				update_post_meta( $post_id, 'isc_nome', sanitize_text_field( $_POST['isc_nome'] ) );
			}
			if ( isset( $_POST['isc_cognome'] ) ) {
				update_post_meta( $post_id, 'isc_cognome', sanitize_text_field( $_POST['isc_cognome'] ) );
			}
			if ( isset( $_POST['isc_codfis'] ) ) {
				update_post_meta( $post_id, 'isc_codfis', sanitize_text_field( $_POST['isc_codfis'] ) );
			}
			if ( isset( $_POST['isc_datanascita_ws'] ) ) {
				update_post_meta( $post_id, 'isc_datanascita_ws', sanitize_text_field( $_POST['isc_datanascita_ws'] ) );
			}
			if ( isset( $_POST['isc_sesso'] ) ) {
				update_post_meta( $post_id, 'isc_sesso', sanitize_text_field( $_POST['isc_sesso'] ) );
			}
			if ( isset( $_POST['isc_indirizzo'] ) ) {
				update_post_meta( $post_id, 'isc_indirizzo', sanitize_text_field( $_POST['isc_indirizzo'] ) );
			}
			if ( isset( $_POST['isc_citta'] ) ) {
				update_post_meta( $post_id, 'isc_citta', sanitize_text_field( $_POST['isc_citta'] ) );
			}
			if ( isset( $_POST['isc_provincia'] ) ) {
				update_post_meta( $post_id, 'isc_provincia', sanitize_text_field( $_POST['isc_provincia'] ) );
			}
			if ( isset( $_POST['isc_cap'] ) ) {
				update_post_meta( $post_id, 'isc_cap', sanitize_text_field( $_POST['isc_cap'] ) );
			}
			if ( isset( $_POST['isc_stato'] ) ) {
				update_post_meta( $post_id, 'isc_stato', sanitize_text_field( $_POST['isc_stato'] ) );
			}
			if ( isset( $_POST['isc_statonascita'] ) ) {
				update_post_meta( $post_id, 'isc_statonascita', sanitize_text_field( $_POST['isc_statonascita'] ) );
			}
			if ( isset( $_POST['isc_luogonascita'] ) ) {
				update_post_meta( $post_id, 'isc_luogonascita', sanitize_text_field( $_POST['isc_luogonascita'] ) );
			}
			if ( isset( $_POST['isc_cittadinanza'] ) ) {
				update_post_meta( $post_id, 'isc_cittadinanza', sanitize_text_field( $_POST['isc_cittadinanza'] ) );
			}
			if ( isset( $_POST['isc_email'] ) ) {
				update_post_meta( $post_id, 'isc_email', sanitize_text_field( $_POST['isc_email'] ) );
			}
			if ( isset( $_POST['isc_cellulare'] ) ) {
				update_post_meta( $post_id, 'isc_cellulare', sanitize_text_field( $_POST['isc_cellulare'] ) );
			}
			if ( isset( $_POST['isc_citta_id'] ) ) {
				update_post_meta( $post_id, 'isc_citta_id', sanitize_text_field( $_POST['isc_citta_id'] ) );
			}
			if ( isset( $_POST['isc_stato_id'] ) ) {
				update_post_meta( $post_id, 'isc_stato_id', sanitize_text_field( $_POST['isc_stato_id'] ) );
			}
			if ( isset( $_POST['isc_statonascita_id'] ) ) {
				update_post_meta( $post_id, 'isc_statonascita_id', sanitize_text_field( $_POST['isc_statonascita_id'] ) );
			}
			if ( isset( $_POST['isc_luogonascita_id'] ) ) {
				update_post_meta( $post_id, 'isc_luogonascita_id', sanitize_text_field( $_POST['isc_luogonascita_id'] ) );
			}
			if ( isset( $_POST['isc_cittadinanza_id'] ) ) {
				update_post_meta( $post_id, 'isc_cittadinanza_id', sanitize_text_field( $_POST['isc_cittadinanza_id'] ) );
			}
			if ( isset( $_POST['isc_corso'] ) ) {
				update_post_meta( $post_id, 'isc_corso', $_POST['isc_corso'] );
			}
			if ( isset( $_POST['isc_corso_civi'] ) ) {
				update_post_meta( $post_id, 'isc_corso_civi', $_POST['isc_corso_civi'] );
			}
			if ( isset( $_POST['isc_corso_nome'] ) ) {
				update_post_meta( $post_id, 'isc_corso_nome', $_POST['isc_corso_nome'] );
			}
			if ( isset( $_POST['isc_newsletter'] ) ) {
				update_post_meta( $post_id, 'isc_newsletter', sanitize_text_field( $_POST['isc_newsletter'] ) );
			}
			if ( isset( $_POST['isc_timestamp'] ) ) {
				$isc_timestamp = $_POST['isc_timestamp'];
			} else {
				$isc_timestamp = microtime(true);
			}
			update_post_meta( $post_id, 'isc_timestamp', $isc_timestamp );
		}
	}

}
Ialpress_Iscrizioni_WS::register();