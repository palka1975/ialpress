<?php

class Ialpress_Iscrizioni extends Ialpress_Cpt_Helper
{
	public static function register()
	{
		$plugin = new self();
		add_action( 'init', array( $plugin, 'mivar_iscrizioni_create_post_types' ) );
		add_action( 'admin_init', array( $plugin, 'mivar_iscrizioni_admin_init' ) );
		add_action( 'save_post', array( $plugin, 'mivar_iscrizioni_add_custom_fields' ), 10, 2 );
		add_action( 'manage_posts_custom_column', array( $plugin, 'mivar_iscrizioni_populate_columns' ) );
		add_action( 'template_redirect', array( $plugin, 'mivar_iscrizioni_detect_form_submit' ) );
		add_action( 'admin_footer', array( $plugin, 'mivar_iscrizioni_export_csv' ) );
		add_action( 'admin_init', array( $plugin, 'export_csv' ) );
		add_action( 'restrict_manage_posts', array( $plugin, 'mivar_corso_filter_list' ) );

		add_filter( 'manage_edit-iscrizioni_columns', array( $plugin, 'mivar_iscrizioni_add_columns' ) );
		add_filter( 'manage_edit-iscrizioni_sortable_columns', array( $plugin, 'mivar_iscrizioni_author_column_sortable' ) );
		add_filter( 'request', array( $plugin, 'mivar_iscrizioni_column_ordering' ) );
		add_filter( 'parse_query', array( $plugin, 'mivar_perform_iscrizione_filtering' ) );

		add_shortcode( 'mivar-form-iscrizione', array( $plugin, 'mivar_iscrizioni_form' ) );
	}

	function mivar_iscrizioni_create_post_types() {
		register_post_type( 'iscrizioni',
			array(
				'labels' => array(
					'name' => 'Iscrizioni',
					'singular_name' => 'Iscrizione',
					'add_new' => 'Aggiungi',
					'add_new_item' => 'Aggiungi Iscrizione',
					'edit' => 'Modifica',
					'edit_item' => 'Modifica Iscrizione',
					'new_item' => 'Nuova Iscrizione',
					'view' => 'Vedi',
					'view_item' => 'Vedi Iscrizione',
					'search_items' => 'Cerca Iscrizioni',
					'not_found' => 'Nessuna iscrizione trovata',
					'not_found_in_trash' => 'Nessuna iscrizione nel cestino',
					'parent' => 'Iscrizione genitore'
				),
				'public' => true,
				'menu_position' => 20,
				'supports' => array( 'title' ),
				'taxonomies' => array( '' ),
				'menu_icon' => "dashicons-id-alt",
				'has_archive' => false,
				'exclude_from_search' => true
			)
		);
	}

	function mivar_iscrizioni_admin_init() {
		$plugin = new self();
		add_meta_box( 'mivar_iscrizioni_details_meta_box', 'Dettagli iscrizione', array( $plugin, 'mivar_iscrizioni_display_details_meta_box' ), 'iscrizioni', 'normal', 'high' );
	}
	function mivar_iscrizioni_display_details_meta_box( $isc ) {
		$isc_nome = esc_html( get_post_meta( $isc->ID, 'isc_nome', true ) );
		$isc_cognome = esc_html( get_post_meta( $isc->ID, 'isc_cognome', true ) );
		$isc_sesso = esc_html( get_post_meta( $isc->ID, 'isc_sesso', true ) );
		$isc_indirizzo = esc_html( get_post_meta( $isc->ID, 'isc_indirizzo', true ) );
		$isc_citta = esc_html( get_post_meta( $isc->ID, 'isc_citta', true ) );
		$isc_cap = esc_html( get_post_meta( $isc->ID, 'isc_cap', true ) );
		$isc_provincia = esc_html( get_post_meta( $isc->ID, 'isc_provincia', true ) );
		$isc_datanascita = esc_html( get_post_meta( $isc->ID, 'isc_datanascita', true ) );
		$isc_luogonascita = esc_html( get_post_meta( $isc->ID, 'isc_luogonascita', true ) );
		$isc_codfis = esc_html( get_post_meta( $isc->ID, 'isc_codfis', true ) );
		$isc_telefono = esc_html( get_post_meta( $isc->ID, 'isc_telefono', true ) );
		$isc_cellulare = esc_html( get_post_meta( $isc->ID, 'isc_cellulare', true ) );
		$isc_email = esc_html( get_post_meta( $isc->ID, 'isc_email', true ) );
		$isc_titolo_di_studio = esc_html( get_post_meta( $isc->ID, 'isc_titolo_di_studio', true ) );
		$isc_stato_occupazionale = esc_html( get_post_meta( $isc->ID, 'isc_stato_occupazionale', true ) );
		$isc_come_conosciuto = esc_html( get_post_meta( $isc->ID, 'isc_come_conosciuto', true ) );
		$isc_reperibilita = intval( get_post_meta( $isc->ID, 'isc_reperibilita', true ) );
		$isc_note = esc_html( get_post_meta( $isc->ID, 'isc_note', true ) );
		$isc_newsletter = intval( get_post_meta( $isc->ID, 'isc_newsletter', true ) );
		$isc_timestamp = get_post_meta( $isc->ID, 'isc_timestamp', true );
		$isc_corso = intval( get_post_meta( $isc->ID, 'isc_corso', true ) );
		if ( !empty($isc_corso) ) {
			// $pcorso = get_post( $isc_corso );
			$corso = get_the_title( $isc_corso );
		}
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
					<td style="width: 201px">CAP</td>
					<td><input type='text' size='5' maxlength="5" name='isc_cap' value='<?php echo $isc_cap; ?>' /></td>
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
					<td style="width: 201px">Data di Nascita</td>
					<td><input type='text' size='80' name='isc_datanascita' value='<?php echo $isc_datanascita; ?>' /></td>
				</tr>
				<tr>
					<td style="width: 201px">Luogo di Nascita</td>
					<td><input type='text' size='80' name='isc_luogonascita' value='<?php echo $isc_luogonascita; ?>' /></td>
				</tr>
				<tr>
					<td style="width: 201px">Codice Fiscale</td>
					<td><input type='text' size='80' maxlength="16" name='isc_codfis' value='<?php echo $isc_codfis; ?>' /></td>
				</tr>
				<tr>
					<td style="width: 201px">Telefono</td>
					<td><input type='text' size='80' name='isc_telefono' value='<?php echo $isc_telefono; ?>' /></td>
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
					<td style="width: 201px">Titolo di Studio</td>
					<td>
						<select style="width: 200px" name="isc_titolo_di_studio">
							<option value="">...</option>
							<?php foreach ($this->titoli_studio as $tit) { ?>
							<option value="<?php echo $tit; ?>" <?php selected( $tit, $isc_titolo_di_studio ); ?>><?php echo $tit; ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<td style="width: 201px">Stato Occupazionale</td>
					<td>
						<select style="width: 200px" name="isc_stato_occupazionale">
							<option value="">...</option>
							<?php foreach ($this->stato_occupazionale as $st) { ?>
							<option value="<?php echo $st; ?>" <?php selected( $st, $isc_stato_occupazionale ); ?>><?php echo $st; ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<td style="width: 201px">Corso</td>
					<td><input type='hidden' name='isc_corso' value='<?php echo $isc_corso; ?>'/>
						<?php
						if ( !empty($isc_corso) ) echo '<p>' . $corso . '</p>';
						else echo '<p>n.a.</p>';
						?>
					</td>
				</tr>
				<tr>
					<td style="width: 201px">Come hai conosciuto il corso</td>
					<td>
						<select style="width: 200px" name="isc_come_conosciuto">
							<option value="">...</option>
							<?php foreach ($this->come_conosciuto as $comecon) { ?>
							<option value="<?php echo $comecon; ?>" <?php selected( $comecon, $isc_come_conosciuto ); ?>><?php echo $comecon; ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<td style="width: 201px">Reperibilità</td>
					<td>
						<table>
							<tr>
								<td><input type="checkbox" name="isc_reperibilita[]" value="1" <?php if ( $isc_reperibilita%2==1 ) echo 'checked="checked"' ?> /></td>
								<td>Mattina</td>
								<td><input type="checkbox" name="isc_reperibilita[]" value="2" <?php if ( in_array($isc_reperibilita, array(2, 3, 6, 7, 10, 11, 14, 15)) ) echo 'checked="checked"' ?> /></td>
								<td>Pomeriggio</td>
								<td><input type="checkbox" name="isc_reperibilita[]" value="4" <?php if ( in_array($isc_reperibilita, array(4, 5, 6, 7, 12, 14, 15)) ) echo 'checked="checked"' ?> /></td>
								<td>Sera</td>
								<td><input type="checkbox" name="isc_reperibilita[]" value="8" <?php if ( in_array($isc_reperibilita, array(8, 9, 10, 11, 12, 14, 15)) ) echo 'checked="checked"' ?> /></td>
								<td>Ore Pasti</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td style="width: 201px">Note</td>
					<td>
						<textarea name="isc_note"><?php echo $isc_note ?></textarea>
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

	function mivar_iscrizioni_display_details( $isc ) { 
		$isc_nome = esc_html( get_post_meta( $isc->ID, 'isc_nome', true ) );
		$isc_cognome = esc_html( get_post_meta( $isc->ID, 'isc_cognome', true ) );
		$isc_sesso = esc_html( get_post_meta( $isc->ID, 'isc_sesso', true ) );
		$isc_indirizzo = esc_html( get_post_meta( $isc->ID, 'isc_indirizzo', true ) );
		$isc_citta = esc_html( get_post_meta( $isc->ID, 'isc_citta', true ) );
		$isc_cap = esc_html( get_post_meta( $isc->ID, 'isc_cap', true ) );
		$isc_provincia = esc_html( get_post_meta( $isc->ID, 'isc_provincia', true ) );
		$isc_datanascita = esc_html( get_post_meta( $isc->ID, 'isc_datanascita', true ) );
		$isc_luogonascita = esc_html( get_post_meta( $isc->ID, 'isc_luogonascita', true ) );
		$isc_codfis = esc_html( get_post_meta( $isc->ID, 'isc_codfis', true ) );
		$isc_telefono = esc_html( get_post_meta( $isc->ID, 'isc_telefono', true ) );
		$isc_cellulare = esc_html( get_post_meta( $isc->ID, 'isc_cellulare', true ) );
		$isc_email = esc_html( get_post_meta( $isc->ID, 'isc_email', true ) );
		$isc_titolo_di_studio = esc_html( get_post_meta( $isc->ID, 'isc_titolo_di_studio', true ) );
		$isc_stato_occupazionale = esc_html( get_post_meta( $isc->ID, 'isc_stato_occupazionale', true ) );
		$isc_come_conosciuto = esc_html( get_post_meta( $isc->ID, 'isc_come_conosciuto', true ) );
		$isc_reperibilita = intval( get_post_meta( $isc->ID, 'isc_reperibilita', true ) );
		$isc_note = esc_html( get_post_meta( $isc->ID, 'isc_note', true ) );
		$isc_newsletter = intval( get_post_meta( $isc->ID, 'isc_newsletter', true ) );
		$isc_timestamp = get_post_meta( $isc->ID, 'isc_timestamp', true );
		$isc_corso = intval( get_post_meta( $isc->ID, 'isc_corso', true ) );
		if ( !empty($isc_corso) ) {
			// $pcorso = get_post( $isc_corso );
			$corso = get_the_title( $isc_corso );
		}
		$dispo = array();
		if ( $isc_reperibilita%2==1 ) array_push($dispo, 'Mattina');
		if ( in_array($isc_reperibilita, array(2, 3, 6, 7, 10, 11, 14, 15)) ) array_push($dispo, 'Pomeriggio');
		if ( in_array($isc_reperibilita, array(4, 5, 6, 7, 12, 14, 15)) ) array_push($dispo, 'Sera');
		if ( in_array($isc_reperibilita, array(8, 9, 10, 11, 12, 14, 15)) ) array_push($dispo, 'Ore Pasti');
		$t = explode('.', $isc_timestamp);
		$dt = new DateTime('@'.$t[0]);
		$dt->setTimeZone(new DateTimeZone('Europe/Rome'));

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
					<td style="width: 201px">CAP</td>
					<td>' . $isc_cap . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Provincia</td>
					<td>' . $isc_provincia . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Data di Nascita</td>
					<td>' . $isc_datanascita . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Luogo di Nascita</td>
					<td>' . $isc_luogonascita . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Codice Fiscale</td>
					<td>' . $isc_codfis . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Telefono</td>
					<td>' . $isc_telefono . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Cellulare</td>
					<td>' . $isc_cellulare . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Email</td>
					<td>' . $isc_email . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Titolo di Studio</td>
					<td>' . $isc_titolo_di_studio . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Stato Occupazionale</td>
					<td>' . $isc_stato_occupazionale . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Corso</td>
					<td>' . (!empty($isc_corso) ? $corso : '/') . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Come hai conosciuto il corso</td>
					<td>' . $isc_come_conosciuto . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Reperibilità</td>
					<td>' . implode(', ', $dispo) . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Note</td>
					<td>' . $isc_note . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Iscrizione Newsletter</td>
					<td>' . ($isc_newsletter==1 ? 'Sì' : 'No') . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Iscrizione registrata il</td>
					<td>' . $dt->format('d/m/Y H:i:s') . '</td>
				</tr>
			</tbody>
		</table>';

		return $html;
	}

	function mivar_iscrizioni_add_custom_fields( $post_id = false, $post = false ) {
		if ( 'iscrizioni' == $post->post_type ) {
			// Store data in post meta table if present in post data
			if ( isset( $_POST['isc_nome'] ) ) {
				update_post_meta( $post_id, 'isc_nome', sanitize_text_field( $_POST['isc_nome'] ) );
			}
			if ( isset( $_POST['isc_cognome'] ) ) {
				update_post_meta( $post_id, 'isc_cognome', sanitize_text_field( $_POST['isc_cognome'] ) );
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
			if ( isset( $_POST['isc_cap'] ) ) {
				update_post_meta( $post_id, 'isc_cap', sanitize_text_field( $_POST['isc_cap'] ) );
			}
			if ( isset( $_POST['isc_provincia'] ) ) {
				update_post_meta( $post_id, 'isc_provincia', sanitize_text_field( $_POST['isc_provincia'] ) );
			}
			if ( isset( $_POST['isc_datanascita'] ) ) {
				update_post_meta( $post_id, 'isc_datanascita', sanitize_text_field( $_POST['isc_datanascita'] ) );
			}
			if ( isset( $_POST['isc_luogonascita'] ) ) {
				update_post_meta( $post_id, 'isc_luogonascita', sanitize_text_field( $_POST['isc_luogonascita'] ) );
			}
			if ( isset( $_POST['isc_codfis'] ) ) {
				update_post_meta( $post_id, 'isc_codfis', sanitize_text_field( $_POST['isc_codfis'] ) );
			}
			if ( isset( $_POST['isc_telefono'] ) ) {
				update_post_meta( $post_id, 'isc_telefono', sanitize_text_field( $_POST['isc_telefono'] ) );
			}
			if ( isset( $_POST['isc_cellulare'] ) ) {
				update_post_meta( $post_id, 'isc_cellulare', sanitize_text_field( $_POST['isc_cellulare'] ) );
			}
			if ( isset( $_POST['isc_email'] ) ) {
				update_post_meta( $post_id, 'isc_email', sanitize_text_field( $_POST['isc_email'] ) );
			}
			if ( isset( $_POST['isc_titolo_di_studio'] ) ) {
				update_post_meta( $post_id, 'isc_titolo_di_studio', sanitize_text_field( $_POST['isc_titolo_di_studio'] ) );
			}
			if ( isset( $_POST['isc_stato_occupazionale'] ) ) {
				update_post_meta( $post_id, 'isc_stato_occupazionale', sanitize_text_field( $_POST['isc_stato_occupazionale'] ) );
			}
			if ( isset( $_POST['isc_come_conosciuto'] ) ) {
				update_post_meta( $post_id, 'isc_come_conosciuto', sanitize_text_field( $_POST['isc_come_conosciuto'] ) );
			}
			if ( isset( $_POST['isc_reperibilita'] ) ) {
				$rep = 0;
				foreach ($_POST['isc_reperibilita'] as $r) {
					$rep += $r;
				}
				update_post_meta( $post_id, 'isc_reperibilita', $rep );
			}
			if ( isset( $_POST['isc_note'] ) ) {
				update_post_meta( $post_id, 'isc_note', sanitize_text_field( $_POST['isc_note'] ) );
			}
			if ( isset( $_POST['isc_newsletter'] ) ) {
				update_post_meta( $post_id, 'isc_newsletter', sanitize_text_field( $_POST['isc_newsletter'] ) );
			}
			if ( isset( $_POST['isc_corso'] ) ) {
				update_post_meta( $post_id, 'isc_corso', $_POST['isc_corso'] );
				$corso = get_post( $_POST['isc_corso'] );
				$term = get_the_terms( $corso, 'sede_corso' );
				update_post_meta( $post_id, 'isc_sedecorso', $term[0]->term_id );
			}
			if ( isset( $_POST['isc_timestamp'] ) ) {
				$isc_timestamp = $_POST['isc_timestamp'];
			} else {
				$isc_timestamp = microtime(true);
			}
			update_post_meta( $post_id, 'isc_timestamp', $isc_timestamp );
		}
	}

	function mivar_iscrizioni_add_columns( $columns ) {
		// $columns['isc_nomcog'] = 'Nome e Cognome';
		$columns['isc_email'] = 'Email';
		$columns['isc_residenza'] = 'Residenza';
		$columns['isc_nascita'] = 'Nascita';
		$columns['isc_corso'] = 'Corso';
		$columns['isc_sedecorso'] = 'Sede Corso';
		$columns['isc_newsletter'] = 'Iscrizione Newsletter';
		unset( $columns['comments'] );

		return $columns;
	}

	function mivar_iscrizioni_populate_columns( $column ) {
		global $post;

		// Check column name and send back appropriate data
		if ( 'isc_nomcog' == $column ) {
			$nome = esc_html( get_post_meta( get_the_ID(), 'isc_nome', true ) );
			$cognome = esc_html( get_post_meta( get_the_ID(), 'isc_cognome', true ) );
			echo $nome . ' ' . $cognome;
		}
		elseif ( 'isc_residenza' == $column ) {
			$ind = get_post_meta( get_the_ID(), 'isc_indirizzo', true );
			$citta = get_post_meta( get_the_ID(), 'isc_citta', true );
			$prov = get_post_meta( get_the_ID(), 'isc_provincia', true );
			echo $ind . '<br>' . $citta . ' (' . $prov . ')';
		}
		elseif ( 'isc_nascita' == $column ) {
			$data_nascita = get_post_meta( get_the_ID(), 'isc_datanascita', true );
			$luogo_nascita = get_post_meta( get_the_ID(), 'isc_luogonascita', true );
			echo $data_nascita . ' - ' . $luogo_nascita;
		}
		elseif ( 'isc_corso' == $column ) {
			$isc_corso = intval( get_post_meta( get_the_ID(), 'isc_corso', true ) );
			if ( !empty($isc_corso) ) {
				// $pcorso = get_post( $isc_corso );
				$corso = get_the_title( $isc_corso );
			} else $corso = '-';
			echo $corso;
		}
		elseif ( 'isc_sedecorso' == $column ) {
			$id_corso = get_post_meta( get_the_ID(), 'isc_corso', true );
			$wpt = get_the_terms( $id_corso, 'sede_corso' );
			$s_corso = $wpt[0]->name;
			echo empty($s_corso) ? '' : $s_corso;
		}
		elseif ( 'isc_email' == $column ) {
			$isc_email = get_post_meta( get_the_ID(), 'isc_email', true );
			echo $isc_email;
		}
		elseif ( 'isc_newsletter' == $column ) {
			$isc_newsletter = get_post_meta( get_the_ID(), 'isc_newsletter', true )==1 ? 'Sì' : 'No';
			echo $isc_newsletter;
		}
	}

	function mivar_iscrizioni_author_column_sortable( $columns ) {
		$columns['isc_email'] = 'isc_email';

		return $columns;
	}

	function mivar_iscrizioni_column_ordering( $vars ) {
		if ( !is_admin() ) {
			return $vars;
		}
		elseif ( isset( $vars['orderby'] ) && 'isc_email' == $vars['orderby'] ) {
			$vars = array_merge( $vars, array(
					'meta_key' => 'isc_email',
					'orderby' => 'meta_value_num'
			) );
		}
		return $vars;
	}

	function mivar_iscrizioni_form() {
		if ( !empty( $_GET['nisc'] ) && wp_verify_nonce( $_REQUEST['tokn'], 'new-iscrizione' ) ): ?>
		
		<div class="row">
			<div class="col-md-12">
				<p> <?php _e('Grazie per esserti iscritto. Ecco i dati che abbiamo registrato.'); ?> </p>
				<?php
					$nisc = esc_sql( $_GET['nisc'] );
					$isc = get_post( $nisc );
					$isc_corso = intval( get_post_meta( $isc->ID, 'isc_corso', true ) );
					if ( !empty($isc_corso) ) {
						// $pcorso = get_post( $isc_corso );
						$corso = get_the_title( $isc_corso );
						echo '<p>' . __('Si prega di salvare la ricevuta in formato PDF.') . '</p>';
					}
					if ( is_object($isc) ) {
						echo $this->mivar_iscrizioni_display_details($isc);

						if ( !empty($isc_corso) ) {
					?>
					<script type="text/javascript">
				        jQuery(document).ready( function($){
				            window.open('/pdf_receipt.php?nisc=<?php echo $nisc ?>', 'preview', 'menubar=no, scrollbars=auto, toolbar=no, resizable=yes, width=1050, height=600').focus();
				        });
				    </script>
				<?php 	}
					} ?>
			</div>
		</div>
		
		<?php
		else:

			$get_corso = isset($_REQUEST['getc']) ? $_REQUEST['getc'] : '';
			?>

			<form method="post" id="form-preiscrizione" action="">
				<!-- Nonce fields to verify visitor provenance -->
				<?php wp_nonce_field( 'add_iscrizione', 'mivar_isc_form' ); ?>


			    <!-- Post variable to indicate user-submitted items -->
				<input type="hidden" name="misc_form_submit" value="1" />
				<input type="hidden" name="isc_corso" id="hid_isc_corso" value="<?php echo $get_corso ?>" />
				<input type="hidden" name="isc_corso_nome" id="hid_isc_corso_nome" value="<?php echo get_the_title( $get_corso ) ?>" />

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
							<option value="<?php echo $sigla; ?>" <?php selected( $sigla, $isc_provincia ); ?>><?php echo $prov; ?></option>
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
						<label class="control-label" for="isc_luogonascita"><?php _e( 'Luogo di Nascita', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<input type="text" class="form-control" id="isc_luogonascita" name="isc_luogonascita" placeholder="<?php _e( 'Luogo di Nascita', 'mivar_iscrizioni_plugin' ) ?>" required>
					</div>
					<div class="form-group col-md-4">
						<label class="control-label" for="isc_datanascita"><?php _e( 'Data di Nascita', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<input type="text" class="form-control" id="isc_datanascita" name="isc_datanascita" placeholder="<?php _e( 'Data di Nascita', 'mivar_iscrizioni_plugin' ) ?>" required>
					</div>
					<div class="form-group col-md-2">
						<label class="control-label" for="isc_cap"><?php _e( 'Sesso', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<select name="isc_sesso" id="isc_sesso" class="form-control" required>
							<option value="">...</option>
							<?php foreach ($this->sessi as $s) { ?>
							<option value="<?php echo $s; ?>" <?php selected( $s, $isc_sesso ); ?>><?php echo $s; ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-md-6">
						<label class="control-label" for="isc_codfis"><?php _e( 'Codice Fiscale', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<input type="text" class="form-control" id="isc_codfis" name="isc_codfis" placeholder="<?php _e( 'Codice Fiscale', 'mivar_iscrizioni_plugin' ) ?>" required>
					</div>
					<div class="form-group col-md-6">
						<label class="control-label" for="isc_email"><?php _e( 'Email', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<input type="email" class="form-control" id="isc_email" name="isc_email" placeholder="<?php _e( 'Email', 'mivar_iscrizioni_plugin' ) ?>" required>
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-md-6">
						<label for="isc_telefono"><?php _e( 'Telefono', 'mivar_iscrizioni_plugin' ) ?></label>
						<input type="tel" class="form-control" id="isc_telefono" name="isc_telefono" placeholder="<?php _e( 'Telefono', 'mivar_iscrizioni_plugin' ) ?>">
					</div>
					<div class="form-group col-md-6">
						<label for="isc_cellulare"><?php _e( 'Cellulare', 'mivar_iscrizioni_plugin' ) ?></label>
						<input type="tel" class="form-control" id="isc_cellulare" name="isc_cellulare" placeholder="<?php _e( 'Cellulare', 'mivar_iscrizioni_plugin' ) ?>">
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-md-6">
						<label for="isc_titolo_di_studio"><?php _e( 'Titolo di Studio', 'mivar_iscrizioni_plugin' ) ?></label>
						<select name="isc_titolo_di_studio" id="isc_titolo_di_studio" class="form-control">
							<option value="">...</option>
							<?php foreach ($this->titoli_studio as $tit) { ?>
							<option value="<?php echo $tit; ?>" <?php selected( $tit, $isc_titolo_di_studio ); ?>><?php echo $tit; ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="form-group col-md-6">
						<label for="isc_stato_occupazionale"><?php _e( 'Stato Occupazionale', 'mivar_iscrizioni_plugin' ) ?></label>
						<select name="isc_stato_occupazionale" id="isc_stato_occupazionale" class="form-control">
							<option value="">...</option>
							<?php foreach ($this->stato_occupazionale as $st) { ?>
							<option value="<?php echo $st; ?>" <?php selected( $st, $isc_stato_occupazionale ); ?>><?php echo $st; ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-md-6">
						<label for="isc_come_conosciuto"><?php _e( 'Come hai conosciuto il corso', 'mivar_iscrizioni_plugin' ) ?></label>
						<select name="isc_come_conosciuto" id="isc_come_conosciuto" class="form-control">
							<option value="">...</option>
							<?php foreach ($this->come_conosciuto as $comecon) { ?>
							<option value="<?php echo $comecon; ?>" <?php selected( $comecon, $isc_come_conosciuto ); ?>><?php echo $comecon; ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="form-group col-md-6">
						<label><?php _e( 'Reperibilità', 'mivar_iscrizioni_plugin' ) ?></label>
						<div class="form-row">
							<div class="col-sm-3">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="isc_reperibilita[]" value="1"> Mattina
									</label>
								</div>
						    </div>
						    <div class="col-sm-3">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="isc_reperibilita[]" value="2"> Pomeriggio
									</label>
								</div>
						    </div>
						    <div class="col-sm-3">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="isc_reperibilita[]" value="4"> Sera
									</label>
								</div>
						    </div>
						    <div class="col-sm-3">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="isc_reperibilita[]" value="8"> Ore Pasti
									</label>
								</div>
						    </div>
						</div>
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-md-12">
						<label for="isc_note"><?php _e( 'Note', 'mivar_iscrizioni_plugin' ) ?></label>
						<textarea class="form-control" id="isc_note" name="isc_note" rows="5" placeholder="Note"></textarea>
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
						<input type="submit" class="btn btn-default" name="submit" value="<?php _e('Invia', 'mivar_iscrizioni_plugin') ?>" />
					</div>
				</div>
			</form>
			<?php
		endif;
	}

	function mivar_iscrizioni_detect_form_submit( $template ) {	
		
		if ( !empty( $_POST['misc_form_submit'] ) ) {
			$this->mivar_iscrizioni_process_form_submit();
		} else {
			return $template;
		}		
	}
	function mivar_iscrizioni_process_form_submit() {

		// $_POST['isc_nome']
		// $_POST['isc_cognome']
		// $_POST['isc_sesso']
		// $_POST['isc_indirizzo']
		// $_POST['isc_citta']
		// $_POST['isc_cap']
		// $_POST['isc_provincia']
		// $_POST['isc_datanascita']
		// $_POST['isc_luogonascita']
		// $_POST['isc_codfis']
		// $_POST['isc_email']
		// $_POST['isc_telefono']
		// $_POST['isc_cellulare']
		// $_POST['isc_titolo_di_studio']
		// $_POST['isc_stato_occupazionale']
		// $_POST['isc_come_conosciuto']
		// $_POST['isc_reperibilita']
		// $_POST['isc_note']
		// $_POST['isc_newsletter']
		// $_POST['isc_corso']
		// $_POST['isc_timestamp']

		if ( PHP_SESSION_NONE == session_status() ) {
			session_start();
		}
		// Check that all required fields are present and non-empty
		if ( wp_verify_nonce( $_POST['mivar_isc_form'], 'add_iscrizione' ) && 
				!empty( $_POST['isc_nome'] ) &&
				!empty( $_POST['isc_cognome'] ) &&
				!empty( $_POST['isc_sesso'] ) &&
				!empty( $_POST['isc_indirizzo'] ) &&
				!empty( $_POST['isc_citta'] ) &&
				!empty( $_POST['isc_cap'] ) &&
				!empty( $_POST['isc_provincia'] ) &&
				!empty( $_POST['isc_datanascita'] ) &&
				!empty( $_POST['isc_luogonascita'] ) &&
				!empty( $_POST['isc_codfis'] ) &&
				!empty( $_POST['isc_email'] ) ) {

			// Create array with received data
			$isc = array(
				'post_status' => 'publish',
				'post_title' => $_POST['isc_cognome'] . ' ' . $_POST['isc_nome'],
				'post_type' => 'iscrizioni',
			);

			// Insert new post in site database
			// Store new post ID from return value in variable
			$nid = wp_insert_post( $isc );
			$pid = get_post( $nid );

			$this->mivar_iscrizioni_add_custom_fields( $nid, $pid );

			// invio mail
			$subject = '[Civiform] Nuova richiesta di preiscrizione dal sito';
			$body = '<p>Nuova richiesta di iscrizione a un corso sul sito Civiform.it</p>' . $this->mivar_iscrizioni_display_details($pid);
			$headers = array('Content-Type: text/html; charset=UTF-8', 'From: Civiform.it <no-reply@civiform.it>');
			wp_mail( $this->to_send, $subject, $body, $headers );

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

	/********* Export to csv ***********/
	function mivar_iscrizioni_export_csv() {
	    $screen = get_current_screen();
	    if ( $screen->id == "edit-iscrizioni" ) {?>
	    <script type="text/javascript">
	        jQuery(document).ready( function($)
	        {
	            $('.tablenav.top .clear').before('<form action="#" method="POST"><input type="hidden" id="mivar_isc_csv_export" name="mivar_isc_csv_export" value="1" /><input class="button button-primary user_export_button" type="submit" value="<?php esc_attr_e('Esporta CSV', 'mytheme');?>" /></form>');
	        });
	    </script>
		<?php } else return;
	}
	
	function export_csv() {
	    if (!empty($_POST['mivar_isc_csv_export'])) {
	 
	        if (current_user_can('upload_files')) {
	            header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream; charset=UTF-8');
	            header('Content-Disposition: attachment; filename="Civiform_Iscrizioni-EXPORT-'.date('Ymd_His').'.csv"');
				header('Content-Transfer-Encoding: binary');
				header('Connection: Keep-Alive');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
	 			
	            $args = array (
	            	'post_type'			=> 'iscrizioni',
	            	'post_status'		=> 'publish',
	            	'posts_per_page'	=> -1,
	                'order'         	=> 'ASC',
	                'orderby'       	=> 'post_title',
	            );
	            $q = new WP_Query( $args );
	            echo "Nome;Cognome;Sesso;Indirizzo;Citta';Cap;Provincia;Data di nascita;Luogo di nascita;Codoce Fiscale;Telefono;Cellulare;Email;Titolo di studio;Stato occupazionale;Note;Newsletter;Corso;Sede;Data Registrazione;Come conosciuto\r\n";
	            foreach ( $q->posts as $isc ) {
	            	$isc_nome = esc_html( get_post_meta( $isc->ID, 'isc_nome', true ) );
					$isc_cognome = esc_html( get_post_meta( $isc->ID, 'isc_cognome', true ) );
					$isc_sesso = esc_html( get_post_meta( $isc->ID, 'isc_sesso', true ) );
					$isc_indirizzo = esc_html( get_post_meta( $isc->ID, 'isc_indirizzo', true ) );
					$isc_citta = esc_html( get_post_meta( $isc->ID, 'isc_citta', true ) );
					$isc_cap = esc_html( get_post_meta( $isc->ID, 'isc_cap', true ) );
					$isc_provincia = esc_html( get_post_meta( $isc->ID, 'isc_provincia', true ) );
					$isc_datanascita = esc_html( get_post_meta( $isc->ID, 'isc_datanascita', true ) );
					$isc_luogonascita = esc_html( get_post_meta( $isc->ID, 'isc_luogonascita', true ) );
					$isc_codfis = esc_html( get_post_meta( $isc->ID, 'isc_codfis', true ) );
					$isc_telefono = esc_html( get_post_meta( $isc->ID, 'isc_telefono', true ) );
					$isc_cellulare = esc_html( get_post_meta( $isc->ID, 'isc_cellulare', true ) );
					$isc_email = esc_html( get_post_meta( $isc->ID, 'isc_email', true ) );
					$isc_titolo_di_studio = esc_html( get_post_meta( $isc->ID, 'isc_titolo_di_studio', true ) );
					$isc_stato_occupazionale = esc_html( get_post_meta( $isc->ID, 'isc_stato_occupazionale', true ) );
					$isc_note = esc_html( get_post_meta( $isc->ID, 'isc_note', true ) );
					$isc_newsletter = intval( get_post_meta( $isc->ID, 'isc_newsletter', true ) );
					$isc_corso = intval( get_post_meta( $isc->ID, 'isc_corso', true ) );
					$isc_sedecorso = esc_html( get_post_meta( $isc->ID, 'isc_sedecorso', true ) );
					$sedecorso = get_term( $isc_sedecorso, 'sede_corso' );
					$isc_come_conosciuto = esc_html( get_post_meta( $isc->ID, 'isc_come_conosciuto', true ) );
					$isc_timestamp = get_post_meta( $isc->ID, 'isc_timestamp', true );
					$t = explode('.', $isc_timestamp);
					$dt = new DateTime('@'.$t[0]);
					$dt->setTimeZone(new DateTimeZone('Europe/Rome'));
					$ts_richiesta = $dt->format('d/m/Y H:i:s') . '.' . $t[1];
					if ( !empty($isc_newsletter) ) $isc_newsletter = 'Sì';
					else $isc_newsletter = 'No';
					if ( !empty($isc_corso) ) {
						$corso = get_the_title( $isc_corso );
					}
	 
	                echo '"' . $isc_nome . '";"' . $isc_cognome . '";"' . $isc_sesso . '";"' . $isc_indirizzo . '";"' . $isc_citta . '";"' . $isc_cap . '";"' . $isc_provincia . '";"' . $isc_datanascita . '";"' . $isc_luogonascita . '";"' . $isc_codfis . '";"' . $isc_telefono . '";"' . $isc_cellulare . '";"' . $isc_email . '";"' . $isc_titolo_di_studio . '";"' . $isc_stato_occupazionale . '";"' . $isc_note . '";"' . $isc_newsletter . '";"' . $corso . '";"' . $sedecorso->name . '";"' . $ts_richiesta . '";"' . $isc_come_conosciuto . '"' . "\r\n";

	                // wp_update_post(array(
	                // 	'ID' => $isc->ID,
	                // 	'post_status' => 'trash',
	                // ));
	            }
	 
	            exit();
	        } else die("You don't have permissions to see this page.");
	    }
	}

	// FILTRI
	// custom dropdown for filters
	function mivar_corso_filter_list() {
		$screen = get_current_screen(); 
	    global $wp_query; 
		if ( 'iscrizioni'==$screen->post_type ) {
			wp_dropdown_categories( array(
				'show_option_all'	=>  __('Tutte le sedi', 'mivar'),
				'taxonomy'			=>  'sede_corso',
				'name'				=>  'sede_corso_isc',
				'orderby'			=>  'name',
				'selected'        =>   
	            ( isset( $_GET['sede_corso_isc'] ) ? $_GET['sede_corso_isc'] : '' ),
				'hierarchical'		=>  false,
				'depth'				=>  3,
				'show_count'		=>  false,
				'hide_empty'		=>  false,
			) );
		}
	}

	// Function to modify query variable based on filter selection
	function mivar_perform_iscrizione_filtering( $query ) {
		$qv = &$query->query_vars;

	    if ( isset( $_GET['sede_corso_isc'] ) && !empty( $_GET['sede_corso_isc'] ) && is_numeric( $_GET['sede_corso_isc'] ) && isset($qv['post_type']) && $qv['post_type']=='iscrizioni' ) {
				$qv['meta_key'] = 'isc_sedecorso';
				$qv['meta_value'] = $_GET['sede_corso_isc'];
				$qv['meta_compare'] = '=';
	    }
	}

}
Ialpress_Iscrizioni::register();