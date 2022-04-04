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

		<form method="post" id="form_preiscrizione_ws" action="">

		    <!-- Post variable to indicate user-submitted items -->
			<input type="hidden" name="isc_corso" id="isc_corso" value="<?php echo $corso_ialman ?>" />

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
					<label for="isc_cellulare"><?php _e( 'Cellulare', 'mivar_iscrizioni_plugin' ) ?></label>
					<input type="tel" class="form-control" id="isc_cellulare" name="isc_cellulare" placeholder="<?php _e( 'Cellulare', 'mivar_iscrizioni_plugin' ) ?>">
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
	}

}
Ialpress_Iscrizioni_WS::register();