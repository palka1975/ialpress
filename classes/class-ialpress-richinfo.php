<?php

class Ialpress_Richinfo extends Ialpress_Cpt_Helper
{
	public static function register()
	{
		$plugin = new self();
		add_action( 'init', array( $plugin, 'mivar_iscrizioni_create_post_types' ) );
		add_action( 'admin_init', array( $plugin, 'mivar_iscrizioni_admin_init_rin' ) );
		add_action( 'save_post', array( $plugin, 'mivar_iscrizioni_add_custom_fields_rin' ), 10, 2 );
		add_action( 'manage_posts_custom_column', array( $plugin, 'mivar_iscrizioni_populate_columns_rin' ) );
		add_action( 'template_redirect', array( $plugin, 'mivar_iscrizioni_detect_form_submit' ) );
		add_action( 'admin_footer', array( $plugin, 'mivar_iscrizioni_export_csv' ) );
		add_action( 'admin_init', array( $plugin, 'export_csv' ) );
		add_action( 'restrict_manage_posts', array( $plugin, 'mivar_rin_filter_list' ) );

		add_filter( 'manage_edit-richieste-info_columns', array( $plugin, 'mivar_iscrizioni_add_columns_rin' ) );
		add_filter( 'manage_edit-richieste-info_sortable_columns', array( $plugin, 'mivar_iscrizioni_author_column_sortable_rin' ) );
		add_filter( 'request', array( $plugin, 'mivar_iscrizioni_column_ordering_rin' ) );
		add_filter( 'parse_query', array( $plugin, 'mivarip_perform_rin_filtering' ) );

		add_shortcode( 'mivar-form-informazioni', array( $plugin, 'mivar_info_form' ) );
	}

	function mivar_iscrizioni_create_post_types() {
		register_post_type( 'richieste-info',
			array(
				'labels' => array(
					'name' => 'Richieste Informazioni',
					'singular_name' => 'Richiesta Informazioni',
					'add_new' => 'Aggiungi',
					'add_new_item' => 'Aggiungi Richiesta Informazioni',
					'edit' => 'Modifica',
					'edit_item' => 'Modifica Richiesta Informazioni',
					'new_item' => 'Nuova Richiesta Informazioni',
					'view' => 'Vedi',
					'view_item' => 'Vedi Richiesta Informazioni',
					'search_items' => 'Cerca Richieste Informazioni',
					'not_found' => 'Nessuna richiesta informazioni trovata',
					'not_found_in_trash' => 'Nessuna richiesta informazioni nel cestino',
					'parent' => 'Richiesta informazioni genitore'
				),
				'public' => true,
				'menu_position' => 22,
				'supports' => array( 'title' ),
				'taxonomies' => array( '' ),
				'menu_icon' => "dashicons-groups",
				'has_archive' => false,
				'exclude_from_search' => true
			)
		);
	}

	function mivar_iscrizioni_admin_init_rin() {
		$plugin = new self();
		add_meta_box( 'mivar_iscrizioni_details_meta_box', 'Dettagli richiesta informazioni', array( $plugin, 'mivar_iscrizioni_display_details_meta_box_rin' ), 'richieste-info', 'normal', 'high' );
	}
	function mivar_iscrizioni_display_details_meta_box_rin( $rin ) { 
		$rin_nome = esc_html( get_post_meta( $rin->ID, 'rin_nome', true ) );
		$rin_cognome = esc_html( get_post_meta( $rin->ID, 'rin_cognome', true ) );
		// $rin_sesso = esc_html( get_post_meta( $rin->ID, 'rin_sesso', true ) );
		$rin_indirizzo = esc_html( get_post_meta( $rin->ID, 'rin_indirizzo', true ) );
		$rin_citta = esc_html( get_post_meta( $rin->ID, 'rin_citta', true ) );
		$rin_cap = esc_html( get_post_meta( $rin->ID, 'rin_cap', true ) );
		$rin_provincia = esc_html( get_post_meta( $rin->ID, 'rin_provincia', true ) );
		// $rin_datanascita = esc_html( get_post_meta( $rin->ID, 'rin_datanascita', true ) );
		// $rin_luogonascita = esc_html( get_post_meta( $rin->ID, 'rin_luogonascita', true ) );
		// $rin_codfis = esc_html( get_post_meta( $rin->ID, 'rin_codfis', true ) );
		$rin_telefono = esc_html( get_post_meta( $rin->ID, 'rin_telefono', true ) );
		$rin_cellulare = esc_html( get_post_meta( $rin->ID, 'rin_cellulare', true ) );
		$rin_email = esc_html( get_post_meta( $rin->ID, 'rin_email', true ) );
		// $rin_titolo_di_studio = esc_html( get_post_meta( $rin->ID, 'rin_titolo_di_studio', true ) );
		// $rin_stato_occupazionale = esc_html( get_post_meta( $rin->ID, 'rin_stato_occupazionale', true ) );
		$rin_come_conosciuto = esc_html( get_post_meta( $rin->ID, 'rin_come_conosciuto', true ) );
		// $rin_reperibilita = intval( get_post_meta( $rin->ID, 'rin_reperibilita', true ) );
		$rin_note = esc_html( get_post_meta( $rin->ID, 'rin_note', true ) );
		$rin_newsletter = intval( get_post_meta( $rin->ID, 'rin_newsletter', true ) );
		// $rin_timestamp = get_post_meta( $rin->ID, 'rin_timestamp', true );
		$rin_corso = intval( get_post_meta( $rin->ID, 'rin_corso', true ) );
		if ( !empty($rin_corso) ) {
			// $pcorso = get_post( $rin_corso );
			$corso = get_the_title( $rin_corso );
		}
		?>
		<table class="table table-bordered">
			<tbody>
				<tr>
					<td style="width: 201px">Nome</td>
					<td><input type='text' size='80' name='rin_nome' value='<?php echo $rin_nome; ?>' /></td>
				</tr>
				<tr>
					<td style="width: 201px">Cognome</td>
					<td><input type='text' size='80' name='rin_cognome' value='<?php echo $rin_cognome; ?>' /></td>
				</tr>
				<!-- tr>
					<td style="width: 201px">Sesso</td>
					<td>
						<select style="width: 100px" name="rin_sesso">
							<option value="">...</option>
							<?php //foreach (SESSI as $s) { ?>
							<option value="<?php //echo $s; ?>" <?php //selected( $s, $rin_sesso ); ?>><?php //echo $s; ?></option>
							<?php //} ?>
						</select>
					</td>
				</tr -->
				<tr>
					<td style="width: 201px">Indirizzo</td>
					<td><input type='text' size='80' maxlength="255" name='rin_indirizzo' value='<?php echo $rin_indirizzo; ?>' /></td>
				</tr>
				<tr>
					<td style="width: 201px">Città</td>
					<td><input type='text' size='80' name='rin_citta' value='<?php echo $rin_citta; ?>' /></td>
				</tr>
				<tr>
					<td style="width: 201px">CAP</td>
					<td><input type='text' size='5' maxlength="5" name='rin_cap' value='<?php echo $rin_cap; ?>' /></td>
				</tr>
				<tr>
					<td style="width: 201px">Provincia</td>
					<td>
						<select style="width: 200px" name="rin_provincia">
							<option value="">...</option>
							<?php foreach ($this->province as $sigla=>$prov) { ?>
							<option value="<?php echo $sigla; ?>" <?php selected( $sigla, $rin_provincia ); ?>><?php echo $prov; ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<!-- tr>
					<td style="width: 201px">Data di Nascita</td>
					<td><input type='text' size='80' name='rin_datanascita' value='<?php //echo $rin_datanascita; ?>' /></td>
				</tr>
				<tr>
					<td style="width: 201px">Luogo di Nascita</td>
					<td><input type='text' size='80' name='rin_luogonascita' value='<?php //echo $rin_luogonascita; ?>' /></td>
				</tr>
				<tr>
					<td style="width: 201px">Codice Fiscale</td>
					<td><input type='text' size='80' maxlength="16" name='rin_codfis' value='<?php //echo $rin_codfis; ?>' /></td>
				</tr -->
				<tr>
					<td style="width: 201px">Telefono</td>
					<td><input type='text' size='80' name='rin_telefono' value='<?php echo $rin_telefono; ?>' /></td>
				</tr>
				<tr>
					<td style="width: 201px">Cellulare</td>
					<td><input type='text' size='80' name='rin_cellulare' value='<?php echo $rin_cellulare; ?>' /></td>
				</tr>
				<tr>
					<td style="width: 201px">Email</td>
					<td><input type='text' size='80' name='rin_email' value='<?php echo $rin_email; ?>' /></td>
				</tr>
				<!-- tr>
					<td style="width: 201px">Titolo di Studio</td>
					<td>
						<select style="width: 200px" name="rin_titolo_di_studio">
							<option value="">...</option>
							<?php //foreach (TITOLI_STUDIO as $tit) { ?>
							<option value="<?php //echo $tit; ?>" <?php //selected( $tit, $rin_titolo_di_studio ); ?>><?php //echo $tit; ?></option>
							<?php //} ?>
						</select>
					</td>
				</tr>
				<tr>
					<td style="width: 201px">Stato Occupazionale</td>
					<td>
						<select style="width: 200px" name="rin_stato_occupazionale">
							<option value="">...</option>
							<?php //foreach (STATO_OCCUPAZIONALE as $st) { ?>
							<option value="<?php //echo $st; ?>" <?php //selected( $st, $rin_stato_occupazionale ); ?>><?php //echo $st; ?></option>
							<?php //} ?>
						</select>
					</td>
				</tr -->
				<tr>
					<td style="width: 201px">Corso</td>
					<td><input type='hidden' name='rin_corso' value='<?php echo $rin_corso; ?>'/>
						<?php
						if ( !empty($rin_corso) ) echo '<p>' . $corso . '</p>';
						else echo '<p>n.a.</p>';
						?>
					</td>
				</tr>
				<tr>
					<td style="width: 201px">Come hai conosciuto il corso</td>
					<td>
						<select style="width: 200px" name="rin_come_conosciuto">
							<option value="">...</option>
							<?php foreach ($this->come_conosciuto as $comecon) {?>
							<option value="<?php echo $comecon; ?>" <?php selected( $comecon, $rin_come_conosciuto ); ?>><?php echo $comecon; ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<!-- tr>
					<td style="width: 201px">Reperibilità</td>
					<td>
						<table>
							<tr>
								<td><input type="checkbox" name="rin_reperibilita[]" value="1" <?php //if ( $rin_reperibilita%2==1 ) echo 'checked="checked"' ?> /></td>
								<td>Mattina</td>
								<td><input type="checkbox" name="rin_reperibilita[]" value="2" <?php //if ( in_array($rin_reperibilita, array(2, 3, 6, 7, 10, 11, 14, 15)) ) echo 'checked="checked"' ?> /></td>
								<td>Pomeriggio</td>
								<td><input type="checkbox" name="rin_reperibilita[]" value="4" <?php //if ( in_array($rin_reperibilita, array(4, 5, 6, 7, 12, 14, 15)) ) echo 'checked="checked"' ?> /></td>
								<td>Sera</td>
								<td><input type="checkbox" name="rin_reperibilita[]" value="8" <?php //if ( in_array($rin_reperibilita, array(8, 9, 10, 11, 12, 14, 15)) ) echo 'checked="checked"' ?> /></td>
								<td>Ore Pasti</td>
							</tr>
						</table>
					</td>
				</tr -->
				<tr>
					<td style="width: 201px">Note</td>
					<td>
						<textarea name="rin_note"><?php echo $rin_note ?></textarea>
					</td>
				</tr>
				<tr>
					<td style="width: 201px">Iscrizione Newsletter</td>
					<td>
						<select style="width: 80px" name="rin_newsletter">
							<option value="1" <?php selected( 1, $rin_newsletter ); ?>>Sì</option>
							<option value="0" <?php selected( 0, $rin_newsletter ); ?>>No</option>
						</select>
					</td>
				</tr>
				<!-- tr>
					<td style="width: 201px">Timestamp</td>
					<td><input type='hidden' name='rin_timestamp' value='<?php //echo $rin_timestamp; ?>'/ -->
						<?php
						//$t = explode('.', $rin_timestamp);
						//echo '<p>' . date('d/m/Y H:i:s', $t[0]) . '.' . $t[1] . '</p>';
						?>
					<!-- /td>
				</tr -->
			</tbody>
		</table>

	<?php }

	function mivar_iscrizioni_display_details_rin( $rin ) { 
		$rin_nome = esc_html( get_post_meta( $rin->ID, 'rin_nome', true ) );
		$rin_cognome = esc_html( get_post_meta( $rin->ID, 'rin_cognome', true ) );
		// $rin_sesso = esc_html( get_post_meta( $rin->ID, 'rin_sesso', true ) );
		$rin_indirizzo = esc_html( get_post_meta( $rin->ID, 'rin_indirizzo', true ) );
		$rin_citta = esc_html( get_post_meta( $rin->ID, 'rin_citta', true ) );
		$rin_cap = esc_html( get_post_meta( $rin->ID, 'rin_cap', true ) );
		$rin_provincia = esc_html( get_post_meta( $rin->ID, 'rin_provincia', true ) );
		// $rin_datanascita = esc_html( get_post_meta( $rin->ID, 'rin_datanascita', true ) );
		// $rin_luogonascita = esc_html( get_post_meta( $rin->ID, 'rin_luogonascita', true ) );
		// $rin_codfis = esc_html( get_post_meta( $rin->ID, 'rin_codfis', true ) );
		$rin_telefono = esc_html( get_post_meta( $rin->ID, 'rin_telefono', true ) );
		$rin_cellulare = esc_html( get_post_meta( $rin->ID, 'rin_cellulare', true ) );
		$rin_email = esc_html( get_post_meta( $rin->ID, 'rin_email', true ) );
		// $rin_titolo_di_studio = esc_html( get_post_meta( $rin->ID, 'rin_titolo_di_studio', true ) );
		// $rin_stato_occupazionale = esc_html( get_post_meta( $rin->ID, 'rin_stato_occupazionale', true ) );
		$rin_come_conosciuto = esc_html( get_post_meta( $rin->ID, 'rin_come_conosciuto', true ) );
		// $rin_reperibilita = intval( get_post_meta( $rin->ID, 'rin_reperibilita', true ) );
		$rin_note = esc_html( get_post_meta( $rin->ID, 'rin_note', true ) );
		$rin_newsletter = intval( get_post_meta( $rin->ID, 'rin_newsletter', true ) );
		// $rin_timestamp = get_post_meta( $rin->ID, 'rin_timestamp', true );
		$rin_corso = intval( get_post_meta( $rin->ID, 'rin_corso', true ) );
		if ( !empty($rin_corso) ) {
			// $pcorso = get_post( $rin_corso );
			$corso = get_the_title( $rin_corso );
		}
		// $dispo = array();
		// if ( $rin_reperibilita%2==1 ) array_push($dispo, 'Mattina');
		// if ( in_array($rin_reperibilita, array(2, 3, 6, 7, 10, 11, 14, 15)) ) array_push($dispo, 'Pomeriggio');
		// if ( in_array($rin_reperibilita, array(4, 5, 6, 7, 12, 14, 15)) ) array_push($dispo, 'Sera');
		// if ( in_array($rin_reperibilita, array(8, 9, 10, 11, 12, 14, 15)) ) array_push($dispo, 'Ore Pasti');
		// $t = explode('.', $rin_timestamp);

		$html = '<table class="table table-bordered">
			<tbody>
				<tr>
					<td style="width: 201px">Nome</td>
					<td>' . $rin_nome . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Cognome</td>
					<td>' . $rin_cognome . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Indirizzo</td>
					<td>' . $rin_indirizzo . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Città</td>
					<td>' . $rin_citta . '</td>
				</tr>
				<tr>
					<td style="width: 201px">CAP</td>
					<td>' . $rin_cap . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Provincia</td>
					<td>' . $rin_provincia . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Telefono</td>
					<td>' . $rin_telefono . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Cellulare</td>
					<td>' . $rin_cellulare . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Email</td>
					<td>' . $rin_email . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Corso</td>
					<td>' . (!empty($rin_corso) ? $corso : '/') . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Come hai conosciuto il corso</td>
					<td>' . $rin_come_conosciuto . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Note</td>
					<td>' . $rin_note . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Iscrizione Newsletter</td>
					<td>' . ($rin_newsletter==1 ? 'Sì' : 'No') . '</td>
				</tr>
			</tbody>
		</table>';

		return $html;
	}

	function mivar_iscrizioni_add_custom_fields_rin( $post_id = false, $post = false ) {
		if ( 'richieste-info' == $post->post_type ) {
			// Store data in post meta table if present in post data
			if ( isset( $_POST['rin_nome'] ) ) {
				update_post_meta( $post_id, 'rin_nome', sanitize_text_field( $_POST['rin_nome'] ) );
			}
			if ( isset( $_POST['rin_cognome'] ) ) {
				update_post_meta( $post_id, 'rin_cognome', sanitize_text_field( $_POST['rin_cognome'] ) );
			}
			// if ( isset( $_POST['rin_sesso'] ) ) {
			// 	update_post_meta( $post_id, 'rin_sesso', sanitize_text_field( $_POST['rin_sesso'] ) );
			// }
			if ( isset( $_POST['rin_indirizzo'] ) ) {
				update_post_meta( $post_id, 'rin_indirizzo', sanitize_text_field( $_POST['rin_indirizzo'] ) );
			}
			if ( isset( $_POST['rin_citta'] ) ) {
				update_post_meta( $post_id, 'rin_citta', sanitize_text_field( $_POST['rin_citta'] ) );
			}
			if ( isset( $_POST['rin_cap'] ) ) {
				update_post_meta( $post_id, 'rin_cap', sanitize_text_field( $_POST['rin_cap'] ) );
			}
			if ( isset( $_POST['rin_provincia'] ) ) {
				update_post_meta( $post_id, 'rin_provincia', sanitize_text_field( $_POST['rin_provincia'] ) );
			}
			// if ( isset( $_POST['rin_datanascita'] ) ) {
			// 	update_post_meta( $post_id, 'rin_datanascita', sanitize_text_field( $_POST['rin_datanascita'] ) );
			// }
			// if ( isset( $_POST['rin_luogonascita'] ) ) {
			// 	update_post_meta( $post_id, 'rin_luogonascita', sanitize_text_field( $_POST['rin_luogonascita'] ) );
			// }
			// if ( isset( $_POST['rin_codfis'] ) ) {
			// 	update_post_meta( $post_id, 'rin_codfis', sanitize_text_field( $_POST['rin_codfis'] ) );
			// }
			if ( isset( $_POST['rin_telefono'] ) ) {
				update_post_meta( $post_id, 'rin_telefono', sanitize_text_field( $_POST['rin_telefono'] ) );
			}
			if ( isset( $_POST['rin_cellulare'] ) ) {
				update_post_meta( $post_id, 'rin_cellulare', sanitize_text_field( $_POST['rin_cellulare'] ) );
			}
			if ( isset( $_POST['rin_email'] ) ) {
				update_post_meta( $post_id, 'rin_email', sanitize_text_field( $_POST['rin_email'] ) );
			}
			// if ( isset( $_POST['rin_titolo_di_studio'] ) ) {
			// 	update_post_meta( $post_id, 'rin_titolo_di_studio', sanitize_text_field( $_POST['rin_titolo_di_studio'] ) );
			// }
			// if ( isset( $_POST['rin_stato_occupazionale'] ) ) {
			// 	update_post_meta( $post_id, 'rin_stato_occupazionale', sanitize_text_field( $_POST['rin_stato_occupazionale'] ) );
			// }
			if ( isset( $_POST['rin_come_conosciuto'] ) ) {
				update_post_meta( $post_id, 'rin_come_conosciuto', sanitize_text_field( $_POST['rin_come_conosciuto'] ) );
			}
			// if ( isset( $_POST['rin_reperibilita'] ) ) {
			// 	$rep = 0;
			// 	foreach ($_POST['rin_reperibilita'] as $r) {
			// 		$rep += $r;
			// 	}
			// 	update_post_meta( $post_id, 'rin_reperibilita', $rep );
			// }
			if ( isset( $_POST['rin_note'] ) ) {
				update_post_meta( $post_id, 'rin_note', sanitize_text_field( $_POST['rin_note'] ) );
			}
			if ( isset( $_POST['rin_newsletter'] ) ) {
				update_post_meta( $post_id, 'rin_newsletter', sanitize_text_field( $_POST['rin_newsletter'] ) );
			}
			if ( isset( $_POST['rin_corso'] ) ) {
				update_post_meta( $post_id, 'rin_corso', $_POST['rin_corso'] );
				$corso = get_post( $_POST['rin_corso'] );
				$term = get_the_terms( $corso, 'sede_corso' );
				update_post_meta( $post_id, 'rin_sedecorso', $term[0]->term_id );
			}
			// if ( isset( $_POST['rin_timestamp'] ) ) {
			// 	$rin_timestamp = $_POST['rin_timestamp'];
			// } else {
			// 	$rin_timestamp = microtime(true);
			// }
			// update_post_meta( $post_id, 'rin_timestamp', $rin_timestamp );
		}
	}

	function mivar_iscrizioni_add_columns_rin( $columns ) {
		// $columns['rin_nomcog'] = 'Nome e Cognome';
		$columns['rin_email'] = 'Email';
		$columns['rin_residenza'] = 'Residenza';
		$columns['rin_corso'] = 'Corso';
		$columns['rin_sedecorso'] = 'Sede Corso';
		$columns['rin_newsletter'] = 'Iscrizione Newsletter';
		unset( $columns['comments'] );

		return $columns;
	}

	function mivar_iscrizioni_populate_columns_rin( $column ) {
		global $post;

		// Check column name and send back appropriate data
		if ( 'rin_nomcog' == $column ) {
			$nome = esc_html( get_post_meta( get_the_ID(), 'rin_nome', true ) );
			$cognome = esc_html( get_post_meta( get_the_ID(), 'rin_cognome', true ) );
			echo $nome . ' ' . $cognome;
		}
		elseif ( 'rin_residenza' == $column ) {
			$ind = get_post_meta( get_the_ID(), 'rin_indirizzo', true );
			$citta = get_post_meta( get_the_ID(), 'rin_citta', true );
			$prov = get_post_meta( get_the_ID(), 'rin_provincia', true );
			echo $ind . '<br>' . $citta . ' (' . $prov . ')';
		}
		elseif ( 'rin_nascita' == $column ) {
			$data_nascita = get_post_meta( get_the_ID(), 'rin_datanascita', true );
			$luogo_nascita = get_post_meta( get_the_ID(), 'rin_luogonascita', true );
			echo $data_nascita . ' - ' . $luogo_nascita;
		}
		elseif ( 'rin_email' == $column ) {
			$rin_email = get_post_meta( get_the_ID(), 'rin_email', true );
			echo $rin_email;
		}
		elseif ( 'rin_newsletter' == $column ) {
			$rin_newsletter = get_post_meta( get_the_ID(), 'rin_newsletter', true )==1 ? 'Sì' : 'No';
			echo $rin_newsletter;
		}
		elseif ( 'rin_corso' == $column ) {
			$rin_corso = intval( get_post_meta( get_the_ID(), 'rin_corso', true ) );
			if ( !empty($rin_corso) ) {
				$corso = get_the_title( $rin_corso );
			} else $corso = '-';
			echo $corso;
		}
		elseif ( 'rin_sedecorso' == $column ) {
			$id_corso = get_post_meta( get_the_ID(), 'rin_corso', true );
			$wpt = get_the_terms( $id_corso, 'sede_corso' );
			$s_corso = $wpt[0]->name;
			echo empty($s_corso) ? '' : $s_corso;
		}
	}

	function mivar_iscrizioni_author_column_sortable_rin( $columns ) {
		$columns['rin_email'] = 'rin_email';

		return $columns;
	}

	function mivar_iscrizioni_column_ordering_rin( $vars ) {
		if ( !is_admin() ) {
			return $vars;
		}
		elseif ( isset( $vars['orderby'] ) && 'rin_email' == $vars['orderby'] ) {
			$vars = array_merge( $vars, array(
					'meta_key' => 'rin_email',
					'orderby' => 'meta_value_num'
			) );
		}
		return $vars;
	}

	function mivar_info_form() {
		if ( !empty( $_GET['ninf'] ) && wp_verify_nonce( $_REQUEST['tokn'], 'new-info' ) ): ?>
		
		<div class="row">
			<div class="col-md-12">
				<p> <?php _e('Grazie per averci contattato, a breve ti risponderemo.<br>Ecco i dati che abbiamo registrato.'); ?> </p>
				<?php
					$ninf = esc_sql( $_GET['ninf'] );
					$rin = get_post( $ninf );
					if ( is_object($rin) ) {
						echo $this->mivar_iscrizioni_display_details_rin($rin);

						if ( !empty($rin_corso) ) {
					?>
				<?php 	}
					} ?>
			</div>
		</div>
		
		<?php
		else:

			$get_corso = isset($_REQUEST['getc']) ? $_REQUEST['getc'] : '';
			?>

			<form method="post" id="form-informazioni" action="">
				<!-- Nonce fields to verify visitor provenance -->
				<?php wp_nonce_field( 'add_richiesta', 'mivar_rin_form' ); ?>


			    <!-- Post variable to indicate user-submitted items -->
				<input type="hidden" name="mrin_form_submit" value="1" />
				<input type="hidden" name="rin_corso" id="hid_rin_corso" value="<?php echo $get_corso ?>" />
				<input type="hidden" name="rin_corso_nome" id="hid_rin_corso_nome" value="<?php echo get_the_title( $get_corso ) ?>" />

				<div class="form-row">
					<div class="form-group col-md-6">
						<label class="control-label" for="rin_nome"><?php _e( 'Nome', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<input type="text" class="form-control" id="rin_nome" name="rin_nome" placeholder="<?php _e( 'Nome', 'mivar_iscrizioni_plugin' ) ?>" required>
					</div>
					<div class="form-group col-md-6">
						<label class="control-label" for="rin_cognome"><?php _e( 'Cognome', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<input type="text" class="form-control" id="rin_cognome" name="rin_cognome" placeholder="<?php _e( 'Cognome', 'mivar_iscrizioni_plugin' ) ?>" required>
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-md-12">
						<label class="control-label" for="rin_indirizzo"><?php _e( 'Indirizzo', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<input type="text" class="form-control" id="rin_indirizzo" name="rin_indirizzo" placeholder="<?php _e( 'Indirizzo', 'mivar_iscrizioni_plugin' ) ?>" required>
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-md-6">
						<label class="control-label" for="rin_citta"><?php _e( 'Città', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<input type="text" class="form-control" id="rin_citta" name="rin_citta" placeholder="<?php _e( 'Città', 'mivar_iscrizioni_plugin' ) ?>" required>
					</div>
					<div class="form-group col-md-4">
						<label class="control-label" for="rin_provincia"><?php _e( 'Provincia', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<select id="rin_provincia" name="rin_provincia" class="form-control" required>
							<option value="">...</option>
							<?php foreach ($this->province as $sigla=>$prov) { ?>
							<option value="<?php echo $sigla; ?>" <?php selected( $sigla, $rin_provincia ); ?>><?php echo $prov; ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="form-group col-md-2">
						<label class="control-label" for="rin_cap"><?php _e( 'CAP', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<input type="text" class="form-control" id="rin_cap" name="rin_cap" placeholder="<?php _e( 'CAP', 'mivar_iscrizioni_plugin' ) ?>" required>
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-md-6">
						<label for="rin_telefono"><?php _e( 'Telefono', 'mivar_iscrizioni_plugin' ) ?></label>
						<input type="tel" class="form-control" id="rin_telefono" name="rin_telefono" placeholder="<?php _e( 'Telefono', 'mivar_iscrizioni_plugin' ) ?>">
					</div>
					<div class="form-group col-md-6">
						<label for="rin_cellulare"><?php _e( 'Cellulare', 'mivar_iscrizioni_plugin' ) ?></label>
						<input type="tel" class="form-control" id="rin_cellulare" name="rin_cellulare" placeholder="<?php _e( 'Cellulare', 'mivar_iscrizioni_plugin' ) ?>">
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-md-6">
						<label class="control-label" for="rin_email"><?php _e( 'Email', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<input type="email" class="form-control" id="rin_email" name="rin_email" placeholder="<?php _e( 'Email', 'mivar_iscrizioni_plugin' ) ?>" required>
					</div>
					<div class="form-group col-md-6">
						<label for="rin_come_conosciuto"><?php _e( 'Come hai conosciuto il corso', 'mivar_iscrizioni_plugin' ) ?></label>
						<select name="rin_come_conosciuto" id="rin_come_conosciuto" class="form-control">
							<option value="">...</option>
							<?php foreach ($this->come_conosciuto as $comecon) { ?>
							<option value="<?php echo $comecon; ?>" <?php selected( $comecon, $rin_come_conosciuto ); ?>><?php echo $comecon; ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-md-12">
						<label for="rin_note"><?php _e( 'Note', 'mivar_iscrizioni_plugin' ) ?></label>
						<textarea class="form-control" id="rin_note" name="rin_note" rows="5" placeholder="Note"></textarea>
					</div>
				</div>

				<div class="form-row">
					<div class="form-group col-md-12">
						<textarea class="form-control" id="rin_info" rows="5" readonly="readonly"><?php echo $this->testo_info_privacy ?></textarea>
					</div>
				</div>

				<div class="form-row">
					<div class="form-group col-sm-12">
						<div class="checkbox">
							<label>
								<input type="checkbox" is="rin_consenso" name="rin_consenso" value="1" required> <?php _e('Presto il mio consenso al trattamento dei dati personali da voi richiesti', 'mivar_iscrizioni_plugin') ?>
							</label>
						</div>
					</div>
				</div>

				<div class="form-row">
					<div class="form-group col-sm-12">
						<div class="checkbox">
							<label>
								<input type="checkbox" is="rin_newsletter" name="rin_newsletter" value="1"> <?php _e('Iscrivimi alla newsletter per tenermi informato sulle novità di Civiform.<br>La newsletter ti verrà inviata nella tua casella di posta elettronica rispettando scrupolosamente la nostra politica sulla privacy.', 'mivar_iscrizioni_plugin') ?>
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
		
		if ( !empty( $_POST['mrin_form_submit'] ) ) {
			$this->mivar_iscrizioni_process_form_submit_rin();
		} else {
			return $template;
		}		
	}
	function mivar_iscrizioni_process_form_submit_rin() {

		// $_POST['rin_nome']
		// $_POST['rin_cognome']
		// $_POST['rin_sesso']
		// $_POST['rin_indirizzo']
		// $_POST['rin_citta']
		// $_POST['rin_cap']
		// $_POST['rin_provincia']
		// $_POST['rin_datanascita']
		// $_POST['rin_luogonascita']
		// $_POST['rin_codfis']
		// $_POST['rin_email']
		// $_POST['rin_telefono']
		// $_POST['rin_cellulare']
		// $_POST['rin_titolo_di_studio']
		// $_POST['rin_stato_occupazionale']
		// $_POST['rin_come_conosciuto']
		// $_POST['rin_reperibilita']
		// $_POST['rin_note']
		// $_POST['rin_newsletter']
		// $_POST['rin_corso']
		// $_POST['rin_timestamp']

		if ( PHP_SESSION_NONE == session_status() ) {
			session_start();
		}
		// Check that all required fields are present and non-empty
		if ( wp_verify_nonce( $_POST['mivar_rin_form'], 'add_richiesta' ) && 
				!empty( $_POST['rin_nome'] ) &&
				!empty( $_POST['rin_cognome'] ) &&
				!empty( $_POST['rin_indirizzo'] ) &&
				!empty( $_POST['rin_citta'] ) &&
				!empty( $_POST['rin_cap'] ) &&
				!empty( $_POST['rin_provincia'] ) &&
				!empty( $_POST['rin_email'] ) ) {

			// Create array with received data
			$rin = array(
				'post_status' => 'publish',
				'post_title' => $_POST['rin_cognome'] . ' ' . $_POST['rin_nome'],
				'post_type' => 'richieste-info',
			);

			// Insert new post in site database
			// Store new post ID from return value in variable
			$nid = wp_insert_post( $rin );
			$pid = get_post( $nid );

			$this->mivar_iscrizioni_add_custom_fields_rin( $nid, $pid );

			// invio mail
			$subject = '[Civiform] Nuova richiesta informazioni dal sito';
			$body = '<p>Nuova richiesta di iscrizione a un corso sul sito Civiform.it</p>' . $this->mivar_iscrizioni_display_details_rin($pid);
			$headers = array('Content-Type: text/html; charset=UTF-8', 'From: Civiform.it <no-reply@civiform.it>');
			wp_mail( $this->to_send, $subject, $body, $headers );

			// redirect
			$redirect_address = ( empty( $_POST['_wp_http_referer'] ) ? site_url() : $_POST['_wp_http_referer'] );
			wp_redirect( add_query_arg( array(
				'ninf' => $nid,
				'tokn' => esc_attr( wp_create_nonce('new-info') ),
				), remove_query_arg('getc', $redirect_address) ) );
			exit;
		} else {
			// Display error message if any required fields are missing
			$abort_message = __('Form incompleto. Impossibile gestire la richiesta. Si prega di riprovare.', 'mivar_iscrizioni_plugin');
			wp_die( $abort_message ); 
			exit;
		}
	}

	/********* Export to csv ***********/
	function mivar_iscrizioni_export_csv() {
	    $screen = get_current_screen();
	    if ( $screen->id == 'edit-richieste-info' ) {?>
	    <script type="text/javascript">
	        jQuery(document).ready( function($)
	        {
	            $('.tablenav.top .clear').before('<form action="#" method="POST"><input type="hidden" id="mivar_rin_csv_export" name="mivar_rin_csv_export" value="1" /><input class="button button-primary user_export_button" type="submit" value="<?php esc_attr_e('Esporta CSV', 'mytheme');?>" /></form>');
	        });
	    </script>
		<?php } else return;
	}
	 
	function export_csv() {
	    if (!empty($_POST['mivar_rin_csv_export'])) {
	 
	        if (current_user_can('upload_files')) {
	            header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream; charset=UTF-8');
	            header('Content-Disposition: attachment; filename="Civiform_Richieste_informazioni-EXPORT-'.date('Ymd_His').'.csv"');
				header('Content-Transfer-Encoding: binary');
				header('Connection: Keep-Alive');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
	 			
	            $args = array (
	            	'post_type'			=> 'richieste-info',
	            	'post_status'		=> 'publish',
	            	'posts_per_page'	=> -1,
	                'order'         	=> 'ASC',
	                'orderby'       	=> 'post_title',
	            );
	            $q = new WP_Query( $args );
	            echo "Nome;Cognome;Indirizzo;Citta';Cap;Provincia;Telefono;Cellulare;Email;Note;Newsletter;Corso;Sede;Data Registrazione\r\n";
	            foreach ( $q->posts as $isc ) {
	            	$rin_nome = esc_html( get_post_meta( $isc->ID, 'rin_nome', true ) );
					$rin_cognome = esc_html( get_post_meta( $isc->ID, 'rin_cognome', true ) );
					$rin_indirizzo = esc_html( get_post_meta( $isc->ID, 'rin_indirizzo', true ) );
					$rin_citta = esc_html( get_post_meta( $isc->ID, 'rin_citta', true ) );
					$rin_cap = esc_html( get_post_meta( $isc->ID, 'rin_cap', true ) );
					$rin_provincia = esc_html( get_post_meta( $isc->ID, 'rin_provincia', true ) );
					$rin_telefono = esc_html( get_post_meta( $isc->ID, 'rin_telefono', true ) );
					$rin_cellulare = esc_html( get_post_meta( $isc->ID, 'rin_cellulare', true ) );
					$rin_email = esc_html( get_post_meta( $isc->ID, 'rin_email', true ) );
					$rin_note = esc_html( get_post_meta( $isc->ID, 'rin_note', true ) );
					$rin_newsletter = intval( get_post_meta( $isc->ID, 'rin_newsletter', true ) );
					$rin_corso = intval( get_post_meta( $isc->ID, 'rin_corso', true ) );
					$rin_sedecorso = esc_html( get_post_meta( $isc->ID, 'rin_sedecorso', true ) );
					$sedecorso = get_term( $rin_sedecorso, 'sede_corso' );
					$ts_richiesta = get_the_date('d/m/Y H:i:s', $isc->ID);
					if ( !empty($rin_newsletter) ) $rin_newsletter = 'Sì';
					else $rin_newsletter = 'No';
					if ( !empty($rin_corso) ) {
						$corso = get_the_title( $rin_corso );
					}
	 
	                echo '"' . $rin_nome . '";"' . $rin_cognome . '";"' . $rin_indirizzo . '";"' . $rin_citta . '";"' . $rin_cap . '";"' . $rin_provincia . '";"' . $rin_telefono . '";"' . $rin_cellulare . '";"' . $rin_email . '";"' . $rin_note . '";"' . $rin_newsletter . '";"' . $corso . '";"' . $sedecorso->name . '";"' . $ts_richiesta . '"' . "\r\n";

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
	function mivar_rin_filter_list() {
		$screen = get_current_screen(); 
	    global $wp_query; 
		if ( 'richieste-info'==$screen->post_type ) {
			wp_dropdown_categories( array(
				'show_option_all'	=>  __('Tutte le sedi', 'mivar'),
				'taxonomy'			=>  'sede_corso',
				'name'				=>  'sede_corso_rin',
				'orderby'			=>  'name',
				'selected'        =>   
	            ( isset( $_GET['sede_corso_rin'] ) ? $_GET['sede_corso_rin'] : '' ),
				'hierarchical'		=>  false,
				'depth'				=>  3,
				'show_count'		=>  false,
				'hide_empty'		=>  false,
			) );
		}
	}

	// Function to modify query variable based on filter selection
	function mivarip_perform_rin_filtering( $query ) {
		$qv = &$query->query_vars;

	    if ( isset( $_GET['sede_corso_rin'] ) && !empty( $_GET['sede_corso_rin'] ) && is_numeric( $_GET['sede_corso_rin'] ) && isset($qv['post_type']) && $qv['post_type']=='richieste-info' ) {
				$qv['meta_key'] = 'rin_sedecorso';
				$qv['meta_value'] = $_GET['sede_corso_rin'];
				$qv['meta_compare'] = '=';
	    }
	}

}
Ialpress_Richinfo::register();