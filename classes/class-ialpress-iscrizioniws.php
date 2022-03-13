<?php

class Ialpress_Iscrizioni_WS extends Ialpress_Cpt_Helper
{
	public static function register()
	{
		$plugin = new self();
		add_shortcode( 'mivar-form-iscrizione-new', array( $plugin, 'mivar_iscrizioniws_form' ) );
	}

	function mivar_iscrizioniws_form() {

		$get_corso = isset($_REQUEST['getc']) ? $_REQUEST['getc'] : '';

		$corso_ialman = get_post_meta( $get_corso, 'corso_ialman', true );
		?>

		<form id="form_check_cf" method="post" action="">
			<div class="form-row row">
				<div class="form-group col-md-2"></div>
				<div class="form-group col-md-8">
					<p><?php _e( "Per prima cosa inserisci il tuo codice fiscale. Se è già presente nel nostro archivio caricheremo i tuoi dati, così da semplificare il processo di iscrizione", 'mivar_iscrizioni_plugin' ) ?></p>
				</div>
				<div class="form-group col-md-2"></div>
			</div>
			<div class="form-row row">
				<div class="form-group col-md-2"></div>
				<div class="form-group col-md-8">
					<label class="control-label" for="ws_cf_pre"><?php _e( 'Codice Fiscale', 'mivar_iscrizioni_plugin' ) ?> <small class="required">*</small></label>
					<input type="text" class="form-control" id="ws_cf_pre" name="ws_cf_pre" placeholder="<?php _e( 'Codice Fiscale', 'mivar_iscrizioni_plugin' ) ?>" required>
				</div>
				<div class="form-group col-md-2"></div>
			</div>
			<div class="form-row row">
				<div class="form-group col-md-2"></div>
				<div class="form-group col-md-8">
					<input type="submit" class="btn btn-default" id="ws_pre_check_cf" name="submit" value="<?php _e('Conferma', 'mivar_iscrizioni_plugin') ?>" />
				</div>
				<div class="form-group col-md-2"></div>
			</div>
		</form>

		<form method="post" id="form_preiscrizione_ws" style="display: none;" action="">

		    <!-- Post variable to indicate user-submitted items -->
			<input type="hidden" name="isc_corso" value="<?php echo $corso_ialman ?>" />

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
	}

}
Ialpress_Iscrizioni_WS::register();