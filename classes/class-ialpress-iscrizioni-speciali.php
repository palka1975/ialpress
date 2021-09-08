<?php

class Ialpress_Iscrizioni_Speciali extends Ialpress_Cpt_Helper
{
	public static function register()
	{
		$plugin = new self();
		add_action( 'init', array( $plugin, 'mivar_iscrizioni_create_post_types' ) );
		add_action( 'admin_init', array( $plugin, 'mivar_iscrizioni_admin_init_isp' ) );
		add_action( 'save_post', array( $plugin, 'mivar_iscrizioni_add_custom_fields_isp' ), 10, 2 );
		add_action( 'manage_posts_custom_column', array( $plugin, 'mivar_iscrizioni_populate_columns_isp' ) );
		add_action( 'template_redirect', array( $plugin, 'mivar_iscrizioni_detect_form_submit' ) );
		add_action( 'admin_footer', array( $plugin, 'mivar_iscrizioni_export_csv' ) );
		add_action( 'admin_init', array( $plugin, 'export_csv' ) );
		add_action( 'restrict_manage_posts', array( $plugin, 'mivar_corso_filter_list' ) );

		add_filter( 'manage_edit-iscrizioni-speciali_columns', array( $plugin, 'mivar_iscrizioni_add_columns_isp' ) );
		add_filter( 'manage_edit-iscrizioni-speciali_sortable_columns', array( $plugin, 'mivar_iscrizioni_author_column_sortable_isp' ) );
		add_filter( 'request', array( $plugin, 'mivar_iscrizioni_column_ordering_isp' ) );
		add_filter( 'parse_query', array( $plugin, 'mivar_perform_iscrizione_speciale_filtering' ) );

		add_shortcode( 'mivar-form-iscrizione-speciale', array( $plugin, 'mivar_iscrizioni_speciali_form' ) );
	}

	function mivar_iscrizioni_create_post_types() {
		register_post_type( 'iscrizioni-speciali',
			array(
				'labels' => array(
					'name' => 'Iscrizioni Speciali',
					'singular_name' => 'Iscrizione Speciale',
					'add_new' => 'Aggiungi',
					'add_new_item' => 'Aggiungi Iscrizione Speciale',
					'edit' => 'Modifica',
					'edit_item' => 'Modifica Iscrizione Speciale',
					'new_item' => 'Nuova Iscrizione Speciale',
					'view' => 'Vedi',
					'view_item' => 'Vedi Iscrizione Speciale',
					'search_items' => 'Cerca Iscrizioni Speciali',
					'not_found' => 'Nessuna iscrizione speciale trovata',
					'not_found_in_trash' => 'Nessuna iscrizione speciale nel cestino',
					'parent' => 'Iscrizione Speciale genitore'
				),
				'public' => true,
				'menu_position' => 21,
				'supports' => array( 'title' ),
				'taxonomies' => array( '' ),
				'menu_icon' => "dashicons-id-alt",
				'has_archive' => false,
				'exclude_from_search' => true
			)
		);
	}

	function mivar_iscrizioni_admin_init_isp() {
		$plugin = new self();
		add_meta_box( 'mivar_iscrizioni_details_meta_box', 'Dettagli Iscrizione Speciale', array( $plugin, 'mivar_iscrizioni_display_details_meta_box_isp' ), 'iscrizioni-speciali', 'normal', 'high' );
	}
	function mivar_iscrizioni_display_details_meta_box_isp( $isc ) {
		$isp_nome = esc_html( get_post_meta( $isc->ID, 'isp_nome', true ) );
		$isp_cognome = esc_html( get_post_meta( $isc->ID, 'isp_cognome', true ) );
		$isp_sesso = esc_html( get_post_meta( $isc->ID, 'isp_sesso', true ) );
		$isp_indirizzo_residenza = esc_html( get_post_meta( $isc->ID, 'isp_indirizzo_residenza', true ) );
		$isp_civico_residenza = esc_html( get_post_meta( $isc->ID, 'isp_civico_residenza', true ) );
		$isp_citta_residenza = esc_html( get_post_meta( $isc->ID, 'isp_citta_residenza', true ) );
		$isp_cap_residenza = esc_html( get_post_meta( $isc->ID, 'isp_cap_residenza', true ) );
		$isp_provincia_residenza = esc_html( get_post_meta( $isc->ID, 'isp_provincia_residenza', true ) );
		$isp_indirizzo_domicilio = esc_html( get_post_meta( $isc->ID, 'isp_indirizzo_domicilio', true ) );
		$isp_civico_domicilio = esc_html( get_post_meta( $isc->ID, 'isp_civico_domicilio', true ) );
		$isp_citta_domicilio = esc_html( get_post_meta( $isc->ID, 'isp_citta_domicilio', true ) );
		$isp_cap_domicilio = esc_html( get_post_meta( $isc->ID, 'isp_cap_domicilio', true ) );
		$isp_provincia_domicilio = esc_html( get_post_meta( $isc->ID, 'isp_provincia_domicilio', true ) );
		$isp_datanascita = esc_html( get_post_meta( $isc->ID, 'isp_datanascita', true ) );
		$isp_luogonascita = esc_html( get_post_meta( $isc->ID, 'isp_luogonascita', true ) );
		$isp_provincia_nascita = esc_html( get_post_meta( $isc->ID, 'isp_provincia_nascita', true ) );
		$isp_stato_nascita = esc_html( get_post_meta( $isc->ID, 'isp_stato_nascita', true ) );
		$isp_cittadinanza = esc_html( get_post_meta( $isc->ID, 'isp_cittadinanza', true ) );
		$isp_codfis = esc_html( get_post_meta( $isc->ID, 'isp_codfis', true ) );
		$isp_telefono = esc_html( get_post_meta( $isc->ID, 'isp_telefono', true ) );
		$isp_email_personale = esc_html( get_post_meta( $isc->ID, 'isp_email_personale', true ) );
		$isp_email_aziendale = esc_html( get_post_meta( $isc->ID, 'isp_email_aziendale', true ) );
		$isp_titolo_di_studio = esc_html( get_post_meta( $isc->ID, 'isp_titolo_di_studio', true ) );
		$isp_stato_occupazionale = esc_html( get_post_meta( $isc->ID, 'isp_stato_occupazionale', true ) );
		$isp_ente_appartenenza = esc_html( get_post_meta( $isc->ID, 'isp_ente_appartenenza', true ) );
		$isp_ente_ragsoc = esc_html( get_post_meta( $isc->ID, 'isp_ente_ragsoc', true ) );
		$isp_ente_sede = esc_html( get_post_meta( $isc->ID, 'isp_ente_sede', true ) );
		$isp_ente_servizio = esc_html( get_post_meta( $isc->ID, 'isp_ente_servizio', true ) );
		$isp_timestamp = get_post_meta( $isc->ID, 'isp_timestamp', true );
		$isp_corso = intval( get_post_meta( $isc->ID, 'isp_corso', true ) );
		if ( !empty($isp_corso) ) {
			// $pcorso = get_post( $isp_corso );
			$corso = get_the_title( $isp_corso );
		}
		// $isp_cellulare = esc_html( get_post_meta( $isc->ID, 'isp_cellulare', true ) );
		// $isp_come_conosciuto = esc_html( get_post_meta( $isc->ID, 'isp_come_conosciuto', true ) );
		// $isp_reperibilita = intval( get_post_meta( $isc->ID, 'isp_reperibilita', true ) );
		// $isp_note = esc_html( get_post_meta( $isc->ID, 'isp_note', true ) );
		// $isp_newsletter = intval( get_post_meta( $isc->ID, 'isp_newsletter', true ) );
		?>
		<table class="table table-bordered">
			<tbody>
				<tr>
					<td style="width: 201px">Nome</td>
					<td><input type='text' size='80' name='isp_nome' value='<?php echo $isp_nome; ?>' /></td>
				</tr>
				<tr>
					<td style="width: 201px">Cognome</td>
					<td><input type='text' size='80' name='isp_cognome' value='<?php echo $isp_cognome; ?>' /></td>
				</tr>
				<tr>
					<td style="width: 201px">Comune o Stato Estero di Nascita</td>
					<td><input type='text' size='80' name='isp_luogonascita' value='<?php echo $isp_luogonascita; ?>' /></td>
				</tr>
				<tr>
					<td style="width: 201px">Provincia di Nascita</td>
					<td>
						<select style="width: 200px" name="isp_provincia_nascita">
							<option value="">...</option>
							<?php foreach ($this->province as $sigla=>$prov) { ?>
							<option value="<?php echo $sigla; ?>" <?php selected( $sigla, $isp_provincia_nascita ); ?>><?php echo $prov; ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<td style="width: 201px">Stato di Nascita</td>
					<td><input type='text' size='80' name='isp_stato_nascita' value='<?php echo $isp_stato_nascita; ?>' /></td>
				</tr>
				<tr>
					<td style="width: 201px">Data di Nascita</td>
					<td><input type='text' size='80' name='isp_datanascita' value='<?php echo $isp_datanascita; ?>' /></td>
				</tr>
				<tr>
					<td style="width: 201px">Codice Fiscale</td>
					<td><input type='text' size='80' maxlength="16" name='isp_codfis' value='<?php echo $isp_codfis; ?>' /></td>
				</tr>
				<tr>
					<td style="width: 201px">Sesso</td>
					<td>
						<select style="width: 100px" name="isp_sesso">
							<option value="">...</option>
							<?php foreach ($this->sessi as $s) { ?>
							<option value="<?php echo $s; ?>" <?php selected( $s, $isp_sesso ); ?>><?php echo $s; ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<td style="width: 201px">Cittadinanza</td>
					<td><input type='text' size='80' maxlength="16" name='isp_cittadinanza' value='<?php echo $isp_cittadinanza; ?>' /></td>
				</tr>
				<tr>
					<td colspan="2"><h4>Residenza</h4></td>
				</tr>
				<tr>
					<td style="width: 201px">Via/Piazza/Località</td>
					<td><input type='text' size='80' maxlength="255" name='isp_indirizzo_residenza' value='<?php echo $isp_indirizzo_residenza; ?>' /></td>
				</tr>
				<tr>
					<td style="width: 201px">Numero Civico</td>
					<td><input type='text' size='80' maxlength="255" name='isp_civico_residenza' value='<?php echo $isp_civico_residenza; ?>' /></td>
				</tr>
				<tr>
					<td style="width: 201px">Comune</td>
					<td><input type='text' size='80' name='isp_citta_residenza' value='<?php echo $isp_citta_residenza; ?>' /></td>
				</tr>
				<tr>
					<td style="width: 201px">CAP</td>
					<td><input type='text' size='5' maxlength="5" name='isp_cap_residenza' value='<?php echo $isp_cap_residenza; ?>' /></td>
				</tr>
				<tr>
					<td style="width: 201px">Provincia</td>
					<td>
						<select style="width: 200px" name="isp_provincia_residenza">
							<option value="">...</option>
							<?php foreach ($this->province as $sigla=>$prov) { ?>
							<option value="<?php echo $sigla; ?>" <?php selected( $sigla, $isp_provincia_residenza ); ?>><?php echo $prov; ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2"><h4>Domicilio</h4></td>
				</tr>
				<tr>
					<td style="width: 201px">Via/Piazza/Località</td>
					<td><input type='text' size='80' maxlength="255" name='isp_indirizzo_domicilio' value='<?php echo $isp_indirizzo_domicilio; ?>' /></td>
				</tr>
				<tr>
					<td style="width: 201px">Numero Civico</td>
					<td><input type='text' size='80' maxlength="255" name='isp_civico_domicilio' value='<?php echo $isp_civico_domicilio; ?>' /></td>
				</tr>
				<tr>
					<td style="width: 201px">Comune</td>
					<td><input type='text' size='80' name='isp_citta_domicilio' value='<?php echo $isp_citta_domicilio; ?>' /></td>
				</tr>
				<tr>
					<td style="width: 201px">CAP</td>
					<td><input type='text' size='5' maxlength="5" name='isp_cap_domicilio' value='<?php echo $isp_cap_domicilio; ?>' /></td>
				</tr>
				<tr>
					<td style="width: 201px">Provincia</td>
					<td>
						<select style="width: 200px" name="isp_provincia_domicilio">
							<option value="">...</option>
							<?php foreach ($this->province as $sigla=>$prov) { ?>
							<option value="<?php echo $sigla; ?>" <?php selected( $sigla, $isp_provincia_domicilio ); ?>><?php echo $prov; ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2"><h4>Stato Occupazionale</h4></td>
				</tr>
				<tr>
					<td style="width: 201px">Condizione Attuale</td>
					<td>
						<select style="width: 200px" name="isp_stato_occupazionale">
							<option value="">...</option>
							<?php foreach ($this->stato_occupazionale_isp as $st) { ?>
							<option value="<?php echo $st; ?>" <?php selected( $st, strtoupper($isp_stato_occupazionale) ); ?>><?php echo $st; ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<td style="width: 201px">Titolo di Studio</td>
					<td>
						<select style="width: 200px" name="isp_titolo_di_studio">
							<option value="">...</option>
							<?php foreach ($this->titoli_studio_isp as $tit) { ?>
							<option value="<?php echo $tit; ?>" <?php selected( $tit, strtoupper($isp_titolo_di_studio) ); ?>><?php echo $tit; ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<td style="width: 201px">Ente/Organizzazione di appartenenza</td>
					<td>
						<select style="width: 200px" name="isp_ente_appartenenza">
							<option value="">...</option>
							<?php foreach ($this->ente_appartenenza_isp as $tit) { ?>
							<option value="<?php echo $tit; ?>" <?php selected( $tit, strtoupper($isp_ente_appartenenza) ); ?>><?php echo $tit; ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<td style="width: 201px">Denominazione/Ragione Sociale</td>
					<td><input type='text' size='80' name='isp_ente_ragsoc' value='<?php echo $isp_ente_ragsoc; ?>' /></td>
				</tr>
				<tr>
					<td style="width: 201px">Sede di Lavoro</td>
					<td><input type='text' size='80' name='isp_ente_sede' value='<?php echo $isp_ente_sede; ?>' /></td>
				</tr>
				<tr>
					<td style="width: 201px">Servizio di appartenenza/Riferimento</td>
					<td><input type='text' size='80' name='isp_ente_servizio' value='<?php echo $isp_ente_servizio; ?>' /></td>
				</tr>
				<tr>
					<td colspan="2"><h4>Recapiti</h4></td>
				</tr>
				<tr>
					<td style="width: 201px">Recapito Telefonico</td>
					<td><input type='text' size='80' name='isp_telefono' value='<?php echo $isp_telefono; ?>' /></td>
				</tr>
				<tr>
					<td style="width: 201px">Email Personale</td>
					<td><input type='text' size='80' name='isp_email_personale' value='<?php echo $isp_email_personale; ?>' /></td>
				</tr>
				<tr>
					<td style="width: 201px">Email Aziendale</td>
					<td><input type='text' size='80' name='isp_email_aziendale' value='<?php echo $isp_email_aziendale; ?>' /></td>
				</tr>
				<tr>
					<td style="width: 201px">Corso</td>
					<td><input type='hidden' name='isp_corso' value='<?php echo $isp_corso; ?>'/>
						<?php
						if ( !empty($isp_corso) ) echo '<p>' . $corso . '</p>';
						else echo '<p>n.a.</p>';
						?>
					</td>
				</tr>
				<tr>
					<td style="width: 201px">Timestamp</td>
					<td><input type='hidden' name='isp_timestamp' value='<?php echo $isp_timestamp; ?>'/>
						<?php
						$t = explode('.', $isp_timestamp);
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

	function mivar_iscrizioni_display_details_isp( $isc ) { 
		$isp_nome = esc_html( get_post_meta( $isc->ID, 'isp_nome', true ) );
		$isp_cognome = esc_html( get_post_meta( $isc->ID, 'isp_cognome', true ) );
		$isp_sesso = esc_html( get_post_meta( $isc->ID, 'isp_sesso', true ) );
		$isp_indirizzo_residenza = esc_html( get_post_meta( $isc->ID, 'isp_indirizzo_residenza', true ) );
		$isp_civico_residenza = esc_html( get_post_meta( $isc->ID, 'isp_civico_residenza', true ) );
		$isp_citta_residenza = esc_html( get_post_meta( $isc->ID, 'isp_citta_residenza', true ) );
		$isp_cap_residenza = esc_html( get_post_meta( $isc->ID, 'isp_cap_residenza', true ) );
		$isp_provincia_residenza = esc_html( get_post_meta( $isc->ID, 'isp_provincia_residenza', true ) );
		$isp_indirizzo_domicilio = esc_html( get_post_meta( $isc->ID, 'isp_indirizzo_domicilio', true ) );
		$isp_civico_domicilio = esc_html( get_post_meta( $isc->ID, 'isp_civico_domicilio', true ) );
		$isp_citta_domicilio = esc_html( get_post_meta( $isc->ID, 'isp_citta_domicilio', true ) );
		$isp_cap_domicilio = esc_html( get_post_meta( $isc->ID, 'isp_cap_domicilio', true ) );
		$isp_provincia_domicilio = esc_html( get_post_meta( $isc->ID, 'isp_provincia_domicilio', true ) );
		$isp_datanascita = esc_html( get_post_meta( $isc->ID, 'isp_datanascita', true ) );
		$isp_luogonascita = esc_html( get_post_meta( $isc->ID, 'isp_luogonascita', true ) );
		$isp_provincia_nascita = esc_html( get_post_meta( $isc->ID, 'isp_provincia_nascita', true ) );
		$isp_stato_nascita = esc_html( get_post_meta( $isc->ID, 'isp_stato_nascita', true ) );
		$isp_cittadinanza = esc_html( get_post_meta( $isc->ID, 'isp_cittadinanza', true ) );
		$isp_codfis = esc_html( get_post_meta( $isc->ID, 'isp_codfis', true ) );
		$isp_telefono = esc_html( get_post_meta( $isc->ID, 'isp_telefono', true ) );
		$isp_email_personale = esc_html( get_post_meta( $isc->ID, 'isp_email_personale', true ) );
		$isp_email_aziendale = esc_html( get_post_meta( $isc->ID, 'isp_email_aziendale', true ) );
		$isp_titolo_di_studio = esc_html( get_post_meta( $isc->ID, 'isp_titolo_di_studio', true ) );
		$isp_stato_occupazionale = esc_html( get_post_meta( $isc->ID, 'isp_stato_occupazionale', true ) );
		$isp_ente_appartenenza = esc_html( get_post_meta( $isc->ID, 'isp_ente_appartenenza', true ) );
		$isp_ente_ragsoc = esc_html( get_post_meta( $isc->ID, 'isp_ente_ragsoc', true ) );
		$isp_ente_sede = esc_html( get_post_meta( $isc->ID, 'isp_ente_sede', true ) );
		$isp_ente_servizio = esc_html( get_post_meta( $isc->ID, 'isp_ente_servizio', true ) );
		$isp_timestamp = get_post_meta( $isc->ID, 'isp_timestamp', true );
		$isp_corso = intval( get_post_meta( $isc->ID, 'isp_corso', true ) );
		if ( !empty($isp_corso) ) {
			// $pcorso = get_post( $isp_corso );
			$corso = get_the_title( $isp_corso );
		}
		$t = explode('.', $isp_timestamp);
		$dt = new DateTime('@'.$t[0]);
		$dt->setTimeZone(new DateTimeZone('Europe/Rome'));

		$html = '<table class="table table-bordered">
			<tbody>
				<tr>
					<td style="width: 201px">Nome</td>
					<td>' . $isp_nome . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Cognome</td>
					<td>' . $isp_cognome . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Comune o Stato Estero di nascita</td>
					<td>' . $isp_luogonascita . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Provincia di nascita</td>
					<td>' . $isp_provincia_nascita . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Stato di nascita</td>
					<td>' . $isp_stato_nascita . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Data di Nascita</td>
					<td>' . $isp_datanascita . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Codice Fiscale</td>
					<td>' . $isp_codfis . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Sesso</td>
					<td>' . $isp_sesso . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Cittadinanza</td>
					<td>' . $isp_cittadinanza . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Indirizzo di Residenza</td>
					<td>' . $isp_indirizzo_residenza . ', ' . $isp_civico_residenza . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Città di Residenza</td>
					<td>' . $isp_citta_residenza . '</td>
				</tr>
				<tr>
					<td style="width: 201px">CAP di Residenza</td>
					<td>' . $isp_cap_residenza . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Provincia di Residenza</td>
					<td>' . $isp_provincia_residenza . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Indirizzo di domicilio</td>
					<td>' . $isp_indirizzo_domicilio . ($isp_civico_domicilio!='' ? ', ' . $isp_civico_domicilio : '') . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Città di domicilio</td>
					<td>' . $isp_citta_domicilio . '</td>
				</tr>
				<tr>
					<td style="width: 201px">CAP di domicilio</td>
					<td>' . $isp_cap_domicilio . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Provincia di domicilio</td>
					<td>' . $isp_provincia_domicilio . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Condizione Attuale</td>
					<td>' . $isp_stato_occupazionale . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Titolo di Studio</td>
					<td>' . $isp_titolo_di_studio . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Ente/Organizzazione di appartenenza</td>
					<td>' . $isp_ente_appartenenza . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Denominazione/Ragione Sociale</td>
					<td>' . $isp_ente_ragsoc . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Sede di Lavoro</td>
					<td>' . $isp_ente_sede . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Servizio di appartenenza/Riferimento</td>
					<td>' . $isp_ente_servizio . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Recapito Telefonico</td>
					<td>' . $isp_telefono . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Email Personale</td>
					<td>' . $isp_email_personale . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Email Aziendale</td>
					<td>' . $isp_email_aziendale . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Corso</td>
					<td>' . (!empty($isp_corso) ? $corso : '/') . '</td>
				</tr>
				<tr>
					<td style="width: 201px">Iscrizione registrata il</td>
					<td>' . $dt->format('d/m/Y H:i:s') . '</td>
				</tr>
			</tbody>
		</table>';

		return $html;
	}

	function mivar_iscrizioni_add_custom_fields_isp( $post_id = false, $post = false ) {
		if ( 'iscrizioni-speciali' == $post->post_type ) {
			// Store data in post meta table if present in post data
			if ( isset($_POST['isp_nome']) ) {
				update_post_meta( $post_id, 'isp_nome', mb_strtoupper( sanitize_text_field($_POST['isp_nome']) ) );
			}
			if ( isset($_POST['isp_cognome']) ) {		
				update_post_meta( $post_id, 'isp_cognome', mb_strtoupper( sanitize_text_field($_POST['isp_cognome']) ) );
			}
			if ( isset($_POST['isp_sesso']) ) {		
				update_post_meta( $post_id, 'isp_sesso', mb_strtoupper( sanitize_text_field($_POST['isp_sesso']) ) );
			}
			if ( isset($_POST['isp_indirizzo_residenza']) ) {		
				update_post_meta( $post_id, 'isp_indirizzo_residenza', mb_strtoupper( sanitize_text_field($_POST['isp_indirizzo_residenza']) ) );
			}
			if ( isset($_POST['isp_civico_residenza']) ) {		
				update_post_meta( $post_id, 'isp_civico_residenza', mb_strtoupper( sanitize_text_field($_POST['isp_civico_residenza']) ) );
			}
			if ( isset($_POST['isp_citta_residenza']) ) {		
				update_post_meta( $post_id, 'isp_citta_residenza', mb_strtoupper( sanitize_text_field($_POST['isp_citta_residenza']) ) );
			}
			if ( isset($_POST['isp_cap_residenza']) ) {		
				update_post_meta( $post_id, 'isp_cap_residenza', mb_strtoupper( sanitize_text_field($_POST['isp_cap_residenza']) ) );
			}
			if ( isset($_POST['isp_provincia_residenza']) ) {		
				update_post_meta( $post_id, 'isp_provincia_residenza', mb_strtoupper( sanitize_text_field($_POST['isp_provincia_residenza']) ) );
			}
			if ( isset($_POST['isp_indirizzo_domicilio']) ) {		
				update_post_meta( $post_id, 'isp_indirizzo_domicilio', mb_strtoupper( sanitize_text_field($_POST['isp_indirizzo_domicilio']) ) );
			}
			if ( isset($_POST['isp_civico_domicilio']) ) {		
				update_post_meta( $post_id, 'isp_civico_domicilio', mb_strtoupper( sanitize_text_field($_POST['isp_civico_domicilio']) ) );
			}
			if ( isset($_POST['isp_citta_domicilio']) ) {		
				update_post_meta( $post_id, 'isp_citta_domicilio', mb_strtoupper( sanitize_text_field($_POST['isp_citta_domicilio']) ) );
			}
			if ( isset($_POST['isp_cap_domicilio']) ) {		
				update_post_meta( $post_id, 'isp_cap_domicilio', mb_strtoupper( sanitize_text_field($_POST['isp_cap_domicilio']) ) );
			}
			if ( isset($_POST['isp_provincia_domicilio']) ) {		
				update_post_meta( $post_id, 'isp_provincia_domicilio', mb_strtoupper( sanitize_text_field($_POST['isp_provincia_domicilio']) ) );
			}
			if ( isset($_POST['isp_datanascita']) ) {		
				update_post_meta( $post_id, 'isp_datanascita', mb_strtoupper( sanitize_text_field($_POST['isp_datanascita']) ) );
			}
			if ( isset($_POST['isp_luogonascita']) ) {		
				update_post_meta( $post_id, 'isp_luogonascita', mb_strtoupper( sanitize_text_field($_POST['isp_luogonascita']) ) );
			}
			if ( isset($_POST['isp_provincia_nascita']) ) {		
				update_post_meta( $post_id, 'isp_provincia_nascita', mb_strtoupper( sanitize_text_field($_POST['isp_provincia_nascita']) ) );
			}
			if ( isset($_POST['isp_stato_nascita']) ) {		
				update_post_meta( $post_id, 'isp_stato_nascita', mb_strtoupper( sanitize_text_field($_POST['isp_stato_nascita']) ) );
			}
			if ( isset($_POST['isp_cittadinanza']) ) {		
				update_post_meta( $post_id, 'isp_cittadinanza', mb_strtoupper( sanitize_text_field($_POST['isp_cittadinanza']) ) );
			}
			if ( isset($_POST['isp_codfis']) ) {		
				update_post_meta( $post_id, 'isp_codfis', mb_strtoupper( sanitize_text_field($_POST['isp_codfis']) ) );
			}
			if ( isset($_POST['isp_telefono']) ) {		
				update_post_meta( $post_id, 'isp_telefono', mb_strtoupper( sanitize_text_field($_POST['isp_telefono']) ) );
			}
			if ( isset($_POST['isp_email_personale']) ) {		
				update_post_meta( $post_id, 'isp_email_personale', mb_strtoupper( sanitize_text_field($_POST['isp_email_personale']) ) );
			}
			if ( isset($_POST['isp_email_aziendale']) ) {		
				update_post_meta( $post_id, 'isp_email_aziendale', mb_strtoupper( sanitize_text_field($_POST['isp_email_aziendale']) ) );
			}
			if ( isset($_POST['isp_titolo_di_studio']) ) {		
				update_post_meta( $post_id, 'isp_titolo_di_studio', mb_strtoupper( sanitize_text_field($_POST['isp_titolo_di_studio']) ) );
			}
			if ( isset($_POST['isp_stato_occupazionale']) ) {		
				update_post_meta( $post_id, 'isp_stato_occupazionale', mb_strtoupper( sanitize_text_field($_POST['isp_stato_occupazionale']) ) );
			}
			if ( isset($_POST['isp_ente_appartenenza']) ) {		
				update_post_meta( $post_id, 'isp_ente_appartenenza', mb_strtoupper( sanitize_text_field($_POST['isp_ente_appartenenza']) ) );
			}
			if ( isset($_POST['isp_ente_ragsoc']) ) {		
				update_post_meta( $post_id, 'isp_ente_ragsoc', mb_strtoupper( sanitize_text_field($_POST['isp_ente_ragsoc']) ) );
			}
			if ( isset($_POST['isp_ente_sede']) ) {		
				update_post_meta( $post_id, 'isp_ente_sede', mb_strtoupper( sanitize_text_field($_POST['isp_ente_sede']) ) );
			}
			if ( isset($_POST['isp_ente_servizio']) ) {		
				update_post_meta( $post_id, 'isp_ente_servizio', mb_strtoupper( sanitize_text_field($_POST['isp_ente_servizio']) ) );
			}
			if ( isset($_POST['isp_corso']) ) {		
				update_post_meta( $post_id, 'isp_corso', mb_strtoupper( sanitize_text_field($_POST['isp_corso']) ) );
				$corso = get_post( $_POST['isp_corso'] );
				$term = get_the_terms( $corso, 'sede_corso' );
				update_post_meta( $post_id, 'isp_sedecorso', $term[0]->term_id );
			}
			if ( isset( $_POST['isp_timestamp'] ) ) {
				$isp_timestamp = $_POST['isp_timestamp'];
			} else {
				$isp_timestamp = microtime(true);
			}
			update_post_meta( $post_id, 'isp_timestamp', $isp_timestamp );
		}
	}

	function mivar_iscrizioni_add_columns_isp( $columns ) {
		$columns['isp_email'] = 'Email';
		$columns['isp_residenza'] = 'Residenza';
		$columns['isp_occupato'] = 'Stato Occupazionale';
		$columns['isp_corso'] = 'Corso';
		$columns['isp_sedecorso'] = 'Sede Corso';
		unset( $columns['comments'] );

		return $columns;
	}

	function mivar_iscrizioni_populate_columns_isp( $column ) {
		global $post;

		// Check column name and send back appropriate data
		if ( 'isp_residenza' == $column ) {
			$ind = get_post_meta( get_the_ID(), 'isp_indirizzo_residenza', true );
			$citta = get_post_meta( get_the_ID(), 'isp_citta_residenza', true );
			$prov = get_post_meta( get_the_ID(), 'isp_provincia_residenza', true );
			echo $ind . '<br>' . $citta . ' (' . $prov . ')';
		}
		elseif ( 'isp_email' == $column ) {
			$isp_email = get_post_meta( get_the_ID(), 'isp_email_personale', true );
			echo $isp_email;
		}
		elseif ( 'isp_occupato' == $column ) {
			$isp_stato_occupazionale = get_post_meta( get_the_ID(), 'isp_stato_occupazionale', true );
			echo $isp_stato_occupazionale;
		}
		elseif ( 'isp_corso' == $column ) {
			$isp_corso = intval( get_post_meta( get_the_ID(), 'isp_corso', true ) );
			if ( !empty($isp_corso) ) {
				$corso = get_the_title( $isp_corso );
			} else $corso = '-';
			echo $corso;
		}
		elseif ( 'isp_sedecorso' == $column ) {
			$id_corso = get_post_meta( get_the_ID(), 'isp_corso', true );
			$wpt = get_the_terms( $id_corso, 'sede_corso' );
			$s_corso = $wpt[0]->name;
			echo empty($s_corso) ? '' : $s_corso;
		}
	}

	function mivar_iscrizioni_author_column_sortable_isp( $columns ) {
		$columns['isp_email'] = 'isp_email';

		return $columns;
	}

	function mivar_iscrizioni_column_ordering_isp( $vars ) {
		if ( !is_admin() ) {
			return $vars;
		}
		elseif ( isset( $vars['orderby'] ) && 'isp_email' == $vars['orderby'] ) {
			$vars = array_merge( $vars, array(
					'meta_key' => 'isp_email_personale',
					'orderby' => 'meta_value_num'
			) );
		}
		return $vars;
	}

	function mivar_iscrizioni_speciali_form() {
		if ( !empty( $_GET['nisp'] ) && wp_verify_nonce( $_REQUEST['tokn'], 'new-iscrizione-speciale' ) ): ?>
		
		<div class="row">
			<div class="col-md-12">
				<p> <?php _e('Grazie per esserti iscritto. Ecco i dati che abbiamo registrato.'); ?> </p>
				<?php
					$nisp = esc_sql( $_GET['nisp'] );
					$isp = get_post( $nisp );
					$isp_corso = intval( get_post_meta( $isp->ID, 'isp_corso', true ) );
					if ( !empty($isp_corso) ) {
						// $pcorso = get_post( $isp_corso );
						$corso = get_the_title( $isp_corso );
						echo '<p>' . __('Si prega di salvare la ricevuta in formato PDF.') . '</p>';
					}
					if ( is_object($isp) ) {
						echo $this->mivar_iscrizioni_display_details_isp($isp);

						if ( !empty($isp_corso) ) {
					?>
					<script type="text/javascript">
				        jQuery(document).ready( function($){
				            window.open('/pdf_receipt.php?nisp=<?php echo $nisp ?>', 'preview', 'menubar=no, scrollbars=auto, toolbar=no, resizable=yes, width=1050, height=600').focus();
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

			<form method="post" id="form-preiscrizione-speciale" action="">
				<!-- Nonce fields to verify visitor provenance -->
				<?php wp_nonce_field( 'add_iscrizione_speciale', 'mivar_isp_form' ); ?>


			    <!-- Post variable to indicate user-submitted items -->
				<input type="hidden" name="misp_form_submit" value="1" />
				<input type="hidden" name="isp_corso" value="<?php echo $get_corso ?>" />

				<h4>Dati Anagrafici</h4>
				<div class="form-row">
					<div class="form-group col-md-6">
						<label class="control-label" for="isp_nome"><?php _e( 'Nome', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<input type="text" class="form-control" id="isp_nome" name="isp_nome" placeholder="<?php _e( 'Nome', 'mivar_iscrizioni_plugin' ) ?>" required>
					</div>
					<div class="form-group col-md-6">
						<label class="control-label" for="isp_cognome"><?php _e( 'Cognome', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<input type="text" class="form-control" id="isp_cognome" name="isp_cognome" placeholder="<?php _e( 'Cognome', 'mivar_iscrizioni_plugin' ) ?>" required>
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-md-8">
						<label class="control-label" for="isp_luogonascita"><?php _e( 'Comune o Stato Estero di nascita', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<input type="text" class="form-control" id="isp_luogonascita" name="isp_luogonascita" placeholder="<?php _e( 'Comune o Stato Estero di nascita', 'mivar_iscrizioni_plugin' ) ?>" required>
					</div>
					<div class="form-group col-md-4">
						<label class="control-label" for="isp_provincia_nascita"><?php _e( 'Provincia di nascita', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<select id="isp_provincia_nascita" name="isp_provincia_nascita" class="form-control" required>
							<option value="">...</option>
							<?php foreach ($this->province as $sigla=>$prov) { ?>
							<option value="<?php echo $sigla; ?>" <?php selected( $sigla, $isp_provincia_nascita ); ?>><?php echo $prov; ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-md-8">
						<label class="control-label" for="isp_stato_nascita"><?php _e( 'Stato di Nascita', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<input type="text" class="form-control" id="isp_stato_nascita" name="isp_stato_nascita" placeholder="<?php _e( 'Stato di Nascita', 'mivar_iscrizioni_plugin' ) ?>" required>
					</div>
					<div class="form-group col-md-4">
						<label class="control-label" for="isp_datanascita"><?php _e( 'Data di Nascita', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<input type="text" class="form-control" id="isp_datanascita" name="isp_datanascita" placeholder="<?php _e( 'Data di Nascita', 'mivar_iscrizioni_plugin' ) ?>" required>
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-md-10">
						<label class="control-label" for="isp_codfis"><?php _e( 'Codice Fiscale', 'mivar_iscrizioni_plugin' ) ?> <small class="required codfis">*</small></label>
						<input type="text" class="form-control" id="isp_codfis" name="isp_codfis" placeholder="<?php _e( 'Codice Fiscale', 'mivar_iscrizioni_plugin' ) ?>" required>
					</div>
					<div class="form-group col-md-2">
						<label class="control-label" for="isp_cap"><?php _e( 'Sesso', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<select name="isp_sesso" id="isp_sesso" class="form-control" required>
							<option value="">...</option>
							<?php foreach ($this->sessi as $s) { ?>
							<option value="<?php echo $s; ?>" <?php selected( $s, $isp_sesso ); ?>><?php echo $s; ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-md-12">
						<label class="control-label" for="isp_cittadinanza"><?php _e( 'Cittadinanza', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<input type="text" class="form-control" id="isp_cittadinanza" name="isp_cittadinanza" placeholder="<?php _e( 'Cittadinanza', 'mivar_iscrizioni_plugin' ) ?>" required>
					</div>
				</div>
				<h4>Residenza</h4>
				<div class="form-row">
					<div class="form-group col-md-10">
						<label class="control-label" for="isp_indirizzo_residenza"><?php _e( 'Via/Piazza/Località', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<input type="text" class="form-control" id="isp_indirizzo_residenza" name="isp_indirizzo_residenza" placeholder="<?php _e( 'Indirizzo', 'mivar_iscrizioni_plugin' ) ?>" required>
					</div>
					<div class="form-group col-md-2">
						<label class="control-label" for="isp_civico_residenza"><?php _e( 'N. Civico', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<input type="text" class="form-control" id="isp_civico_residenza" name="isp_civico_residenza" placeholder="<?php _e( 'N. Civico', 'mivar_iscrizioni_plugin' ) ?>" required>
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-md-6">
						<label class="control-label" for="isp_citta_residenza"><?php _e( 'Città', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<input type="text" class="form-control" id="isp_citta_residenza" name="isp_citta_residenza" placeholder="<?php _e( 'Città', 'mivar_iscrizioni_plugin' ) ?>" required>
					</div>
					<div class="form-group col-md-4">
						<label class="control-label" for="isp_provincia_residenza"><?php _e( 'Provincia', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<select id="isp_provincia_residenza" name="isp_provincia_residenza" class="form-control" required>
							<option value="">...</option>
							<?php foreach ($this->province as $sigla=>$prov) { ?>
							<option value="<?php echo $sigla; ?>" <?php selected( $sigla, $isp_provincia_residenza ); ?>><?php echo $prov; ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="form-group col-md-2">
						<label class="control-label" for="isp_cap_residenza"><?php _e( 'CAP', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<input type="text" class="form-control" id="isp_cap_residenza" name="isp_cap_residenza" placeholder="<?php _e( 'CAP', 'mivar_iscrizioni_plugin' ) ?>" required>
					</div>
				</div>
				<h4>Domicilio (se diverso dalla Residenza)</h4>
				<div class="form-row">
					<div class="form-group col-md-10">
						<label for="isp_indirizzo_domicilio"><?php _e( 'Via/Piazza/Località', 'mivar_iscrizioni_plugin' ) ?></label>
						<input type="text" class="form-control" id="isp_indirizzo_domicilio" name="isp_indirizzo_domicilio" placeholder="<?php _e( 'Indirizzo', 'mivar_iscrizioni_plugin' ) ?>">
					</div>
					<div class="form-group col-md-2">
						<label for="isp_civico_domicilio"><?php _e( 'N. Civico', 'mivar_iscrizioni_plugin' ) ?></label>
						<input type="text" class="form-control" id="isp_civico_domicilio" name="isp_civico_domicilio" placeholder="<?php _e( 'N. Civico', 'mivar_iscrizioni_plugin' ) ?>">
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-md-6">
						<label for="isp_citta_domicilio"><?php _e( 'Città', 'mivar_iscrizioni_plugin' ) ?></label>
						<input type="text" class="form-control" id="isp_citta_domicilio" name="isp_citta_domicilio" placeholder="<?php _e( 'Città', 'mivar_iscrizioni_plugin' ) ?>">
					</div>
					<div class="form-group col-md-4">
						<label for="isp_provincia_domicilio"><?php _e( 'Provincia', 'mivar_iscrizioni_plugin' ) ?></label>
						<select id="isp_provincia_domicilio" name="isp_provincia_domicilio" class="form-control">
							<option value="">...</option>
							<?php foreach ($this->province as $sigla=>$prov) { ?>
							<option value="<?php echo $sigla; ?>" <?php selected( $sigla, $isp_provincia_domicilio ); ?>><?php echo $prov; ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="form-group col-md-2">
						<label for="isp_cap_domicilio"><?php _e( 'CAP', 'mivar_iscrizioni_plugin' ) ?></label>
						<input type="text" class="form-control" id="isp_cap_domicilio" name="isp_cap_domicilio" placeholder="<?php _e( 'CAP', 'mivar_iscrizioni_plugin' ) ?>">
					</div>
				</div>
				<h4>Condizione Attuale</h4>
				<div class="form-row">
					<div class="form-group col-md-6">
						<label class="control-label" for="isp_stato_occupazionale"><?php _e( 'Stato Occupazionale', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<select name="isp_stato_occupazionale" id="isp_stato_occupazionale" class="form-control" required>
							<option value="">...</option>
							<?php foreach ($this->stato_occupazionale_isp as $st) { ?>
							<option value="<?php echo $st; ?>" <?php selected( $st, $isp_stato_occupazionale ); ?>><?php echo $st; ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="form-group col-md-6">
						<label class="control-label" for="isp_titolo_di_studio"><?php _e( 'Titolo di Studio', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<select name="isp_titolo_di_studio" id="isp_titolo_di_studio" class="form-control" required>
							<option value="">...</option>
							<?php foreach ($this->titoli_studio_isp as $tit) { ?>
							<option value="<?php echo $tit; ?>" <?php selected( $tit, $isp_titolo_di_studio ); ?>><?php echo $tit; ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-md-6">
						<label for="isp_ente_appartenenza"><?php _e( 'Ente/Organizzazione di appartenenza', 'mivar_iscrizioni_plugin' ) ?></label>
						<select name="isp_ente_appartenenza" id="isp_ente_appartenenza" class="form-control">
							<option value="">...</option>
							<?php foreach ($this->ente_appartenenza_isp as $st) { ?>
							<option value="<?php echo $st; ?>" <?php selected( $st, $isp_ente_appartenenza ); ?>><?php echo $st; ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="form-group col-md-6">
						<label for="isp_ente_ragsoc"><?php _e( 'Ragione Sociale', 'mivar_iscrizioni_plugin' ) ?></label>
						<input type="text" class="form-control" id="isp_ente_ragsoc" name="isp_ente_ragsoc" placeholder="<?php _e( 'Ragione Sociale', 'mivar_iscrizioni_plugin' ) ?>">
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-md-6">
						<label class="control-label" for="isp_ente_sede"><?php _e( 'Sede di Lavoro', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<input type="text" class="form-control" id="isp_ente_sede" name="isp_ente_sede" placeholder="<?php _e( 'Sede di Lavoro', 'mivar_iscrizioni_plugin' ) ?>" required>
					</div>
					<div class="form-group col-md-6">
						<label for="isp_ente_servizio"><?php _e( 'Servizio di appartenenza/Riferimento', 'mivar_iscrizioni_plugin' ) ?></label>
						<input type="text" class="form-control" id="isp_ente_servizio" name="isp_ente_servizio" placeholder="<?php _e( 'Servizio di appartenenza/Riferimento', 'mivar_iscrizioni_plugin' ) ?>">
					</div>
				</div>
				<h4>Recapiti</h4>
				<div class="form-row">
					<div class="form-group col-md-4">
						<label class="control-label" for="isp_telefono"><?php _e( 'Recapito Telefonico', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<input type="tel" class="form-control" id="isp_telefono" name="isp_telefono" placeholder="<?php _e( 'Recapito Telefonico', 'mivar_iscrizioni_plugin' ) ?>" required>
					</div>
					<div class="form-group col-md-4">
						<label class="control-label" for="isp_email_personale"><?php _e( 'Email Personale', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
						<input type="email" class="form-control" id="isp_email_personale" name="isp_email_personale" placeholder="<?php _e( 'Email Personale', 'mivar_iscrizioni_plugin' ) ?>" required>
					</div>
					<div class="form-group col-md-4">
						<label for="isp_email_aziendale"><?php _e( 'Email Aziendale', 'mivar_iscrizioni_plugin' ) ?></label>
						<input type="email" class="form-control" id="isp_email_aziendale" name="isp_email_aziendale" placeholder="<?php _e( 'Email Aziendale', 'mivar_iscrizioni_plugin' ) ?>">
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-md-12">
						<textarea class="form-control" id="isp_info" rows="5" readonly="readonly"><?php echo $this->testo_info_privacy ?></textarea>
					</div>
				</div>

				<div class="form-row">
					<div class="form-group col-sm-6">
						<div class="checkbox">
							<label>
								<input type="checkbox" is="isp_consenso" name="isp_consenso" value="1" required> <?php _e('Presto il mio consenso al trattamento dei dati personali da voi richiesti', 'mivar_iscrizioni_plugin') ?>
							</label>
						</div>
					</div>
					<div class="form-group col-sm-6">
						<div class="checkbox">
							<label>
								<input type="checkbox" is="isp_veridicita" name="isp_veridicita" value="1" required> <?php _e('Dichiaro sotto la mia responsabilità che i dati inseriti sono veritieri', 'mivar_iscrizioni_plugin') ?>
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
		
		if ( !empty( $_POST['misp_form_submit'] ) ) {
			$this->mivar_iscrizioni_process_form_submit_isp();
		} else {
			return $template;
		}		
	}
	function mivar_iscrizioni_process_form_submit_isp() {

		// $_POST['isp_nome']
		// $_POST['isp_cognome']
		// $_POST['isp_sesso']
		// $_POST['isp_indirizzo_residenza']
		// $_POST['isp_civico_residenza']
		// $_POST['isp_citta_residenza']
		// $_POST['isp_cap_residenza']
		// $_POST['isp_provincia_residenza']
		// $_POST['isp_indirizzo_domicilio']
		// $_POST['isp_civico_domicilio']
		// $_POST['isp_citta_domicilio']
		// $_POST['isp_cap_domicilio']
		// $_POST['isp_provincia_domicilio']
		// $_POST['isp_datanascita']
		// $_POST['isp_luogonascita']
		// $_POST['isp_provincia_nascita']
		// $_POST['isp_stato_nascita']
		// $_POST['isp_cittadinanza']
		// $_POST['isp_codfis']
		// $_POST['isp_telefono']
		// $_POST['isp_email_personale']
		// $_POST['isp_email_aziendale']
		// $_POST['isp_titolo_di_studio']
		// $_POST['isp_stato_occupazionale']
		// $_POST['isp_ente_appartenenza']
		// $_POST['isp_ente_ragsoc']
		// $_POST['isp_ente_servizio']
		// $_POST['isp_timestamp']
		// $_POST['isp_corso']

		if ( PHP_SESSION_NONE == session_status() ) {
			session_start();
		}
		// Check that all required fields are present and non-empty
		if ( wp_verify_nonce( $_POST['mivar_isp_form'], 'add_iscrizione_speciale' ) && 
				!empty( $_POST['isp_nome'] ) &&
				!empty( $_POST['isp_cognome'] ) &&
				!empty( $_POST['isp_sesso'] ) &&
				!empty( $_POST['isp_indirizzo_residenza'] ) &&
				!empty( $_POST['isp_civico_residenza'] ) &&
				!empty( $_POST['isp_citta_residenza'] ) &&
				!empty( $_POST['isp_cap_residenza'] ) &&
				!empty( $_POST['isp_provincia_residenza'] ) &&
				!empty( $_POST['isp_datanascita'] ) &&
				!empty( $_POST['isp_luogonascita'] ) &&
				!empty( $_POST['isp_provincia_nascita'] ) &&
				!empty( $_POST['isp_stato_nascita'] ) &&
				!empty( $_POST['isp_cittadinanza'] ) &&
				!empty( $_POST['isp_codfis'] ) &&
				!empty( $_POST['isp_telefono'] ) &&
				!empty( $_POST['isp_titolo_di_studio'] ) &&
				!empty( $_POST['isp_stato_occupazionale'] ) &&
				!empty( $_POST['isp_ente_sede'] ) &&
				!empty( $_POST['isp_email_personale'] ) ) {

			// Create array with received data
			$isc = array(
				'post_status' => 'publish',
				'post_title' => $_POST['isp_cognome'] . ' ' . $_POST['isp_nome'],
				'post_type' => 'iscrizioni-speciali',
			);

			// Insert new post in site database
			// Store new post ID from return value in variable
			$nid = wp_insert_post( $isc );
			$pid = get_post( $nid );

			$this->mivar_iscrizioni_add_custom_fields_isp( $nid, $pid );

			// invio mail
			$subject = '[Civiform] Nuova richiesta di preiscrizione dal sito';
			$body = '<p>Nuova richiesta di iscrizione a un corso sul sito Civiform.it</p>' . $this->mivar_iscrizioni_display_details_isp($pid);
			$headers = array('Content-Type: text/html; charset=UTF-8', 'From: Civiform.it <no-reply@civiform.it>');
			wp_mail( $this->to_send, $subject, $body, $headers );

			// redirect
			$redirect_address = ( empty( $_POST['_wp_http_referer'] ) ? site_url() : $_POST['_wp_http_referer'] );
			wp_redirect( add_query_arg( array(
				'nisp' => $nid,
				'tokn' => esc_attr( wp_create_nonce('new-iscrizione-speciale') ),
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
	    if ( $screen->id == 'edit-iscrizioni-speciali' ) {?>
	    <script type="text/javascript">
	        jQuery(document).ready( function($)
	        {
	            $('.tablenav.top .clear').before('<form action="#" method="POST"><input type="hidden" id="mivar_isp_csv_export" name="mivar_isp_csv_export" value="1" /><input class="button button-primary user_export_button" type="submit" value="<?php esc_attr_e('Esporta CSV', 'mytheme');?>" /></form>');
	        });
	    </script>
		<?php } else return;
	}
	
	function export_csv() {
	    if (!empty($_POST['mivar_isp_csv_export'])) {
 
	        if (current_user_can('upload_files')) {
	            header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream; charset=UTF-8');
	            header('Content-Disposition: attachment; filename="Civiform_Iscrizioni-Speciali-EXPORT-'.date('Ymd_His').'.csv"');
				header('Content-Transfer-Encoding: binary');
				header('Connection: Keep-Alive');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
	 			
	            $args = array (
	            	'post_type'			=> 'iscrizioni-speciali',
	            	'post_status'		=> 'publish',
	            	'posts_per_page'	=> -1,
	                'order'         	=> 'ASC',
	                'orderby'       	=> 'post_title',
	            );
	            $q = new WP_Query( $args );
	            echo "Nome;Cognome;Sesso;Indirizzo residenza;Citta' residenza;CAP residenza;Provincia residenza;Indirizzo domicilio;Citta' domicilio;CAP domicilio;Provincia domicilio;Data di nascita;Luogo di nascita;Provincia di nascita;Stato di nascita;Cittadinanza;Codice Fiscale;Telefono;Email Personale;Email Aziendale;Titolo di Studio;Stato Occupazionale;Ente appartenenza;Ente Ragione Sociale;Ente Sede Lavoro;Ente Servizio;Corso;Sede;Data Registrazione;Flag privacy;Flag autodichiarazione veridicita'\r\n";
	            foreach ( $q->posts as $isc ) {
	            	$isp_nome = esc_html( get_post_meta( $isc->ID, 'isp_nome', true ) );
					$isp_cognome = esc_html( get_post_meta( $isc->ID, 'isp_cognome', true ) );
					$isp_sesso = esc_html( get_post_meta( $isc->ID, 'isp_sesso', true ) );
					$isp_indirizzo_residenza = esc_html( get_post_meta( $isc->ID, 'isp_indirizzo_residenza', true ) );
					$isp_civico_residenza = esc_html( get_post_meta( $isc->ID, 'isp_civico_residenza', true ) );
					if ( !empty($isp_civico_residenza) ) $isp_indirizzo_residenza .= ', ' . $isp_civico_residenza;
					$isp_citta_residenza = esc_html( get_post_meta( $isc->ID, 'isp_citta_residenza', true ) );
					$isp_cap_residenza = esc_html( get_post_meta( $isc->ID, 'isp_cap_residenza', true ) );
					$isp_provincia_residenza = esc_html( get_post_meta( $isc->ID, 'isp_provincia_residenza', true ) );
					$isp_indirizzo_domicilio = esc_html( get_post_meta( $isc->ID, 'isp_indirizzo_domicilio', true ) );
					$isp_civico_domicilio = esc_html( get_post_meta( $isc->ID, 'isp_civico_domicilio', true ) );
					if ( !empty($isp_civico_domicilio) ) $isp_indirizzo_domicilio .= ', ' . $isp_civico_domicilio;
					$isp_citta_domicilio = esc_html( get_post_meta( $isc->ID, 'isp_citta_domicilio', true ) );
					$isp_cap_domicilio = esc_html( get_post_meta( $isc->ID, 'isp_cap_domicilio', true ) );
					$isp_provincia_domicilio = esc_html( get_post_meta( $isc->ID, 'isp_provincia_domicilio', true ) );
					$isp_datanascita = esc_html( get_post_meta( $isc->ID, 'isp_datanascita', true ) );
					$isp_luogonascita = esc_html( get_post_meta( $isc->ID, 'isp_luogonascita', true ) );
					$isp_provincia_nascita = esc_html( get_post_meta( $isc->ID, 'isp_provincia_nascita', true ) );
					$isp_stato_nascita = esc_html( get_post_meta( $isc->ID, 'isp_stato_nascita', true ) );
					$isp_cittadinanza = esc_html( get_post_meta( $isc->ID, 'isp_cittadinanza', true ) );
					$isp_codfis = esc_html( get_post_meta( $isc->ID, 'isp_codfis', true ) );
					$isp_telefono = esc_html( get_post_meta( $isc->ID, 'isp_telefono', true ) );
					$isp_email_personale = esc_html( get_post_meta( $isc->ID, 'isp_email_personale', true ) );
					$isp_email_aziendale = esc_html( get_post_meta( $isc->ID, 'isp_email_aziendale', true ) );
					$isp_titolo_di_studio = esc_html( get_post_meta( $isc->ID, 'isp_titolo_di_studio', true ) );
					$isp_stato_occupazionale = esc_html( get_post_meta( $isc->ID, 'isp_stato_occupazionale', true ) );
					$isp_ente_appartenenza = esc_html( get_post_meta( $isc->ID, 'isp_ente_appartenenza', true ) );
					$isp_ente_ragsoc = esc_html( get_post_meta( $isc->ID, 'isp_ente_ragsoc', true ) );
					$isp_ente_sede = esc_html( get_post_meta( $isc->ID, 'isp_ente_sede', true ) );
					$isp_ente_servizio = esc_html( get_post_meta( $isc->ID, 'isp_ente_servizio', true ) );
					$isp_corso = esc_html( get_post_meta( $isc->ID, 'isp_corso', true ) );
					$isp_sedecorso = esc_html( get_post_meta( $isc->ID, 'isp_sedecorso', true ) );
					$sedecorso = get_term( $isp_sedecorso, 'sede_corso' );
					$isp_timestamp = get_post_meta( $isc->ID, 'isp_timestamp', true );
					$t = explode('.', $isp_timestamp);
					$dt = new DateTime('@'.$t[0]);
					$dt->setTimeZone(new DateTimeZone('Europe/Rome'));
					$ts_richiesta = $dt->format('d/m/Y H:i:s') . '.' . $t[1];
					if ( !empty($isp_corso) ) {
						$corso = get_the_title( $isp_corso );
					}

					echo '"' . $isp_nome . '";"' . $isp_cognome . '";"' . $isp_sesso . '";"' . $isp_indirizzo_residenza . '";"' . $isp_citta_residenza . '";"' . $isp_cap_residenza . '";"' . $isp_provincia_residenza . '";"' . $isp_indirizzo_domicilio . '";"' . $isp_citta_domicilio . '";"' . $isp_cap_domicilio . '";"' . $isp_provincia_domicilio . '";"' . $isp_datanascita . '";"' . $isp_luogonascita . '";"' . $isp_provincia_nascita . '";"' . $isp_stato_nascita . '";"' . $isp_cittadinanza . '";"' . $isp_codfis . '";"' . $isp_telefono . '";"' . $isp_email_personale . '";"' . $isp_email_aziendale . '";"' . $isp_titolo_di_studio . '";"' . $isp_stato_occupazionale . '";"' . $isp_ente_appartenenza . '";"' . $isp_ente_ragsoc . '";"' . $isp_ente_sede . '";"' . $isp_ente_servizio . '";"' . $corso . '";"' . $sedecorso->name . '";"' . $ts_richiesta . '";"OK";"OK"' . "\r\n";

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
		if ( 'iscrizioni-speciali'==$screen->post_type ) {
			wp_dropdown_categories( array(
				'show_option_all'	=>  __('Tutte le sedi', 'mivar'),
				'taxonomy'			=>  'sede_corso',
				'name'				=>  'sede_corso_isp',
				'orderby'			=>  'name',
				'selected'        =>   
	            ( isset( $_GET['sede_corso_isp'] ) ? $_GET['sede_corso_isp'] : '' ),
				'hierarchical'		=>  false,
				'depth'				=>  3,
				'show_count'		=>  false,
				'hide_empty'		=>  false,
			) );
		}
	}

	// Function to modify query variable based on filter selection
	function mivar_perform_iscrizione_speciale_filtering( $query ) {
		$qv = &$query->query_vars;

	    if ( isset( $_GET['sede_corso_isp'] ) && !empty( $_GET['sede_corso_isp'] ) && is_numeric( $_GET['sede_corso_isp'] ) && isset($qv['post_type']) && $qv['post_type']=='iscrizioni-speciali' ) {
				$qv['meta_key'] = 'isp_sedecorso';
				$qv['meta_value'] = $_GET['sede_corso_isp'];
				$qv['meta_compare'] = '=';
	    }
	}

}
Ialpress_Iscrizioni_Speciali::register();