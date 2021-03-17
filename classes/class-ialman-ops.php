<?php
class Ialman_Ops
{
	public $local_prefix = 'mii_';
	// function __construct(argument)
	// {
		
	// }

	public function callIalman( $table, $args=array() )
	{
		$_url = "https://www.civiform.it/ialman_interface/call_maker.php?v=$table";
		if ( isset($args['id']) AND $args['id']!='' ) $_url .= '&id='.$args['id'];
		if ( isset($args['dateupd']) AND $args['dateupd']!='' ) $_url .= '&dateupd='.$args['dateupd'];
		if ( isset($args['dateupdTo']) AND $args['dateupdTo']!='' ) $_url .= '&dateupdTo='.$args['dateupdTo'];
		$ch = curl_init( $_url );
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_HEADER, false); 
		$result=curl_exec($ch);
		curl_close($ch);
		return json_decode($result);
	}

	public function updateReferenceTables()
	{
		$remote_tables = array(
			'STATO_CORSO' => 'STATO_CORSO',
			'TIPOLOGIA_CORSO' => 'TIPOLOGIA_CORSO_RISPETTO_IAL',
			'ATTIVITA_CORSO' => 'ATTIVITA_CORSO',
			'SOTTO_TIPOLOGIA_ATTIVITA' => 'SOTTO_TIPOLOGIA_ATTIVITA',
			'ANNO_FORMATIVO' => 'ANNO_FORMATIVO',
			'FONTE_FINANZIAMENTO' => 'FONTE_FINANZIAMENTO',
			'SETTORE_FORMATIVO' => 'SETTORE_FORMATIVO',
			'TIPOLOGIA_FORMATIVA_FVG' => 'TIPOLOGIA_FORMATIVA_FVG',
			'TIPOLOGIA_SVANTAGGIO_CORSO' => 'TIPOLOGIA_SVANTAGGIO_CORSO',
			'TIPOLOGIA_UTENTI' => 'TIPOLOGIA_UTENTI',
			'TIPOLOGIA_UTENZA_CORSO' => 'TIPOLOGIA_UTENZA_CORSO',
			'RUO_COD' => 'RUO_COD',
			'E_NOME_VALORE' => 'E_NOME_VALORE',
		);
		foreach ($remote_tables as $name => $remote_table) {
			$values = $this->callIalman($remote_table);
			$table = $this->local_prefix . strtolower($remote_table);
			if ($values->Result==1) {
				foreach($values->ResponseData as $row) {
					if ( ! $this->checkRecord( $table, array('ID'=>$row->$name) ) ) {
						if ( $remote_table=='RUO_COD' ) $this->insertRecord( $table, array("ID" => $row->$name, "descrizione" => $row->DESCRIZIONE, "is_docenza" => $row->IS_DOCENZA) );
						else if ( $remote_table=='E_NOME_VALORE' ) $this->insertRecord( $table, array("ID" => $row->$name, "descrizione" => $row->DESCRIZIONE, "is_disattivo" => $row->IS_DISATTIVO) );
						else $this->insertRecord( $table, array("ID" => $row->$name, "descrizione" => $row->DESCRIZIONE) );
					}
				}
			}
		}
	}

	public function updateAnagrafica($dateFrom='', $dateTo='')
	{
		$args = [];
		if ( !empty($dateFrom) ) $args['dateupd'] = $dateFrom;
		if ( !empty($dateTo) ) $args['dateupdTo'] = $dateTo;
		$values = $this->callIalman('ANAGRAFICA', $args);
		$table = $this->local_prefix . 'anagrafica';
		if ($values->Result==1) {
			$_count_insert = 0;
			$_count_update = 0;
			foreach ($values->ResponseData as $ana) {
				$row = array(
					"ID" => $ana->ID_ANAGRAFICA,
					"cognome" => $ana->DENOM_COGNOME,
					"nome" => $ana->NOME,
					"cf" => $ana->CF=='null' ? null : $ana->CF,
					"piva" => $ana->PIVA=='null' ? null : $ana->PIVA,
					"sesso" => $ana->SESSO=='null' ? null : $ana->SESSO,
					"data_nascita" => $ana->DATA_NASCITA=='null' ? null : $ana->DATA_NASCITA,
					"luogo_nascita" => $ana->LUOGO_NASCITA=='null' ? null : $ana->LUOGO_NASCITA,
					"indirizzo" => $ana->INDIRIZZO=='null' ? null : $ana->INDIRIZZO,
					"cap" => $ana->CAP=='null' ? null : $ana->CAP,
					"recapito" => $ana->RECAPITO=='null' ? null : $ana->RECAPITO,
					"prov" => $ana->PROV=='null' ? null : $ana->PROV,
					"stato" => $ana->STATO=='null' ? null : $ana->STATO,
					"telefono" => $ana->Telefono=='null' ? null : $ana->Telefono,
					"cellulare" => $ana->Cellulare=='null' ? null : $ana->Cellulare,
					"mail" => $ana->EMail=='null' ? null : $ana->EMail,
					"is_ditta_individuale" => $ana->IS_DITTA_INDIVIDUALE=='null' ? null : $ana->IS_DITTA_INDIVIDUALE,
					"e_perc_inps" => $ana->E_PERC_INPS=='null' ? null : $ana->E_PERC_INPS,
					"e_perc_cassa" => $ana->E_PERC_CASSA=='null' ? null : $ana->E_PERC_CASSA,
					"e_perc_iva" => $ana->E_PERC_IVA=='null' ? null : $ana->E_PERC_IVA,
					"e_categoria_inps" => $ana->E_CATEGORIA_INPS=='null' ? null : $ana->E_CATEGORIA_INPS,
					"titolo_studio" => $ana->TITOLO_STUDIO=='null' ? null : $ana->TITOLO_STUDIO,
					"e_tipo_rapporto_lavoro" => $ana->E_TIPO_RAPPORTO_LAVORO=='null' ? null : $ana->E_TIPO_RAPPORTO_LAVORO,
					"e_azienda_appartenenza" => $ana->E_AZIENDA_APPARTENENZA=='null' ? null : $ana->E_AZIENDA_APPARTENENZA,
					"is_docente" => $ana->IS_DOCENTE=='null' ? null : $ana->IS_DOCENTE,
					"is_dipendente_pubblico" => $ana->IS_DIPENDENTE_PUBBLICO=='null' ? null : $ana->IS_DIPENDENTE_PUBBLICO,
					"is_disattivo" => $ana->IS_DISATTIVO=='null' ? null : $ana->IS_DISATTIVO,
					"update_timestamp" => $ana->UPDATE_TIMESTAMP=='null' ? null : $ana->UPDATE_TIMESTAMP,
				);
				$exists = $this->checkRecord( $table, array('ID'=>$ana->ID_ANAGRAFICA), $ana->UPDATE_TIMESTAMP );
				if ( !$exists[0] ) {
					$this->insertRecord( $table, $row );
					$_count_insert++;
				} else if ( $exists[1] ) {
					$this->updateRecord( $table, $row, array('ID'=>$ana->ID_ANAGRAFICA) );
					$_count_update++;
				}
			}
			echo "ANAGRAFICA<br/>";
			echo "Inseriti " . $_count_insert . " record.<br/>";
			echo "Aggiornati " . $_count_update . " record.<br/>";
		}
	}

	public function updateDomanda($dateFrom='', $dateTo='')
	{
		global $wpdb;
		$args = [];
		if ( !empty($dateFrom) ) $args['dateupd'] = $dateFrom;
		if ( !empty($dateTo) ) $args['dateupdTo'] = $dateTo;
		$values = $this->callIalman('DOMANDA', $args);
		$table = $this->local_prefix . 'domanda';
		if ($values->Result==1) {
			// dbDelta( 'TRUNCATE '.$this->local_prefix.'domanda' );
			$_count_insert = 0;
			$_count_update = 0;
			foreach ($values->ResponseData as $dom) {
				$row = array(
					"id_ca_commessa" => $dom->ID_CA_COMMESSA,
					"anagrafica" => $dom->ANAGRAFICA,
					"is_ammesso" => $dom->IS_AMMESSO,
					"data_ammissione" => $dom->DATA_AMMISSIONE,
					"is_dimesso" => $dom->IS_DIMESSO,
					"data_dimissione" => $dom->DATA_DIMISSIONE=='null' ? null : $dom->DATA_DIMISSIONE,
					"update_timestamp" => $dom->UPDATE_TIMESTAMP,
				);
				$exists = $this->checkRecord( $table, array('id_ca_commessa'=>$dom->ID_CA_COMMESSA, 'anagrafica'=>$dom->ANAGRAFICA), $dom->UPDATE_TIMESTAMP );
				if ( !$exists[0] ) {
					$this->insertRecord( $table, $row );
					$_count_insert++;
				} else if ( $exists[1]) {
					$this->updateRecord( $table, $row, array('id_ca_commessa'=>$dom->ID_CA_COMMESSA, 'anagrafica'=>$dom->ANAGRAFICA) );
					$_count_update++;
				}
			}
			if ( wp_doing_ajax() ) {
				return array(
					'table' => 'Domanda',
					'aggiornati' => $_count_update,
					'inseriti' => $_count_insert,
				);
			} else {
				echo "DOMANDA<br/>";
				echo "Inseriti " . $_count_insert . " record.<br/>";
				echo "Aggiornati " . $_count_update . " record.<br/>";
			}
		}
	}

	public function getDomande( $_args=array() )
	{
		global $wpdb;
		$sql = "SELECT d.*, a.cognome, a.nome, a.indirizzo, a.piva, a.cf, a.sesso, a.data_nascita, a.luogo_nascita, a.cap, a.recapito, a.prov, a.stato, a.telefono, a.cellulare, a.mail, c.ID as id_corso, c.descrizione FROM ".$this->local_prefix."domanda as d JOIN (".$this->local_prefix."anagrafica as a, ".$this->local_prefix."ca_commessa as c) ON (d.anagrafica=a.ID AND d.id_ca_commessa=c.ID) WHERE 1";
        $s = !empty($_args['s']) ? $_args['s'] : false;
        $id = !empty($_args['id']) ? $_args['id'] : false;
        if ( !empty($id) ) {
        	$sql .= " AND d.ID=$id";
        	return $wpdb->get_row( $sql );
        } else if ( !empty($s) ) {
        	// CAMPO RICERCA
            $sql .= " AND ( a.cognome LIKE '%$s%' OR a.nome LIKE '%$s%' )";
        } else {
            // DATE
            $date_from = !empty($_args['date_from']) ? $_args['date_from'] : false;
            $date_to = !empty($_args['date_to']) ? $_args['date_to'] : false;
            if ( $date_from ) $sql .= " AND d.update_timestamp>='$date_from " . " 00:00:00'";
            if ( $date_to ) $sql .= " AND d.update_timestamp<='$date_to " . " 23:59:00'";
            
            // ORDER
            $orderby = !empty($_args['orderby']) ? $_args['orderby'] : false;
            if ( $orderby ) {
                $order = !empty($_args['order']) ? strtoupper($_args['order']) : 'ASC';
                if ( $orderby=='title' ) $sql .= " ORDER BY a.cognome, a.nome";
                if ( $orderby=='updated' ) $sql .= " ORDER BY d.update_timestamp";
                $sql .= " $order";
            } else $sql .= " ORDER BY d.update_timestamp DESC";
        }
        // echo $sql;
        return $wpdb->get_results( $sql );
	}

	public function updateComAnaRuo($dateFrom='', $dateTo='')
	{
		$args = [];
		if ( !empty($dateFrom) ) $args['dateupd'] = $dateFrom;
		if ( !empty($dateTo) ) $args['dateupdTo'] = $dateTo;
		$values = $this->callIalman('COM_ANA_RUO', $args);
		$table = $this->local_prefix . 'com_ana_ruo';
		if ($values->Result==1) {
			$_count_insert = 0;
			$_count_update = 0;
			foreach ($values->ResponseData as $car) {
				$row = array(
					"id_ca_commessa" => $car->ID_CA_COMMESSA,
					"anagrafica" => $car->ANAGRAFICA,
					"ruo_cod" => $car->RUO_COD,
					"ore" => $car->ORE,
					"update_timestamp" => $car->UPDATE_TIMESTAMP,
				);
				$exists = $this->checkRecord( $table, array('id_ca_commessa'=>$car->ID_CA_COMMESSA,'anagrafica'=>$car->ANAGRAFICA,'ruo_cod'=>$car->RUO_COD), $car->UPDATE_TIMESTAMP );
				if ( !$exists[0] ) {
					$this->insertRecord( $table, $row );
					$_count_insert++;
				} else if ( $exists[1] ) {
					$this->updateRecord( $table, $row, array('id_ca_commessa'=>$car->ID_CA_COMMESSA, 'anagrafica'=>$car->ANAGRAFICA, 'ruo_cod'=>$car->RUO_COD) );
					$_count_update++;
				}
			}
			echo "COM_ANA_RUO<br/>";
			echo "Inseriti " . $_count_insert . " record.<br/>";
			echo "Aggiornati " . $_count_update . " record.<br/>";
		}
	}

	public function updateComAnaPerRuo($dateFrom='', $dateTo='')
	{
		$args = [];
		if ( !empty($dateFrom) ) $args['dateupd'] = $dateFrom;
		if ( !empty($dateTo) ) $args['dateupdTo'] = $dateTo;
		$values = $this->callIalman('COM_ANA_PER_RUO', $args);
		$table = $this->local_prefix . 'com_ana_per_ruo';
		if ($values->Result==1) {
			$_count_insert = 0;
			$_count_update = 0;
			foreach ($values->ResponseData as $i => $capr) {
				$row = array(
					"id_ca_commessa" => $capr->ID_CA_COMMESSA,
					"anagrafica" => $capr->ANAGRAFICA,
					"ruo_cod" => $capr->RUO_COD,
					"periodo" => $capr->PERIODO,
					"ore" => $capr->ORE,
					"update_timestamp" => $capr->UPDATE_TIMESTAMP,
				);
				$exists = $this->checkRecord( $table, array('id_ca_commessa'=>$capr->ID_CA_COMMESSA,'anagrafica'=>$capr->ANAGRAFICA,'ruo_cod'=>$capr->RUO_COD,'periodo'=>$capr->PERIODO), $capr->UPDATE_TIMESTAMP );
				if ( !$exists[0] ) {
					$this->insertRecord( $table, $row );
					$_count_insert++;
				} else if ( $exists[1] ) {
					$this->updateRecord( $table, $row, array('id_ca_commessa'=>$capr->ID_CA_COMMESSA,'anagrafica'=>$capr->ANAGRAFICA,'ruo_cod'=>$capr->RUO_COD,'periodo'=>$capr->PERIODO) );
					$_count_update++;
				}
			}
			echo "COM_ANA_PER_RUO<br/>";
			echo "Inseriti " . $_count_insert . " record.<br/>";
			echo "Aggiornati " . $_count_update . " record.<br/>";
		}
	}

	public function updateComVal($dateFrom='', $dateTo='')
	{
		$args = [];
		if ( !empty($dateFrom) ) $args['dateupd'] = $dateFrom;
		if ( !empty($dateTo) ) $args['dateupdTo'] = $dateTo;
		$values = $this->callIalman('COM_VAL', $args);
		$table = $this->local_prefix . 'com_val';
		if ($values->Result==1) {
			$_count_insert = 0;
			$_count_update = 0;
			foreach ($values->ResponseData as $i => $cv) {
				$row = array(
					"id_ca_commessa" => $cv->ID_CA_COMMESSA,
					"e_nome_valore" => $cv->E_NOME_VALORE,
					"valore" => $cv->VALORE,
					"update_timestamp" => $cv->UPDATE_TIMESTAMP,
				);
				$exists = $this->checkRecord( $table, array('id_ca_commessa'=>$cv->ID_CA_COMMESSA,'e_nome_valore'=>$cv->E_NOME_VALORE), $cv->UPDATE_TIMESTAMP );
				if ( !$exists[0] ) {
					$this->insertRecord( $table, $row );
					$_count_insert++;
				} else if ( $exists[1] ) {
					$this->updateRecord( $table, $row, array('id_ca_commessa'=>$cv->ID_CA_COMMESSA,'e_nome_valore'=>$cv->E_NOME_VALORE) );
					$_count_update++;
				}
			}
			echo "COM_VAL<br/>";
			echo "Inseriti " . $_count_insert . " record.<br/>";
			echo "Aggiornati " . $_count_update . " record.<br/>";
		}
	}

	public function updateComPerVal($dateFrom='', $dateTo='')
	{
		$args = [];
		if ( !empty($dateFrom) ) $args['dateupd'] = $dateFrom;
		if ( !empty($dateTo) ) $args['dateupdTo'] = $dateTo;
		$values = $this->callIalman('COM_PER_VAL', $args);
		$table = $this->local_prefix . 'com_per_val';
		if ($values->Result==1) {
			$_count_insert = 0;
			$_count_update = 0;
			foreach ($values->ResponseData as $i => $cpv) {
				$row = array(
					"id_ca_commessa" => $cpv->ID_CA_COMMESSA,
					"periodo" => $cpv->PERIODO,
					"e_nome_valore" => $cpv->E_NOME_VALORE,
					"valore" => $cpv->VALORE,
					"update_timestamp" => $cpv->UPDATE_TIMESTAMP,
				);
				$exists = $this->checkRecord( $table, array('id_ca_commessa'=>$cpv->ID_CA_COMMESSA,'periodo'=>$cpv->PERIODO,'e_nome_valore'=>$cpv->E_NOME_VALORE), $cpv->UPDATE_TIMESTAMP );
				if ( !$exists[0] ) {
					$this->insertRecord( $table, $row );
					$_count_insert++;
				} else if ( $exists[1] ) {
					$this->updateRecord( $table, $row, array('id_ca_commessa'=>$cpv->ID_CA_COMMESSA,'periodo'=>$cpv->PERIODO,'e_nome_valore'=>$cpv->E_NOME_VALORE) );
					$_count_update++;
				}
			}
			echo "COM_PER_VAL<br/>";
			echo "Inseriti " . $_count_insert . " record.<br/>";
			echo "Aggiornati " . $_count_update . " record.<br/>";
		}
	}

	public function updateAnaComPerVal($dateFrom='', $dateTo='')
	{
		$args = [];
		if ( !empty($dateFrom) ) $args['dateupd'] = $dateFrom;
		if ( !empty($dateTo) ) $args['dateupdTo'] = $dateTo;
		$values = $this->callIalman('ANA_COM_PER_VAL', $args);
		$table = $this->local_prefix . 'ana_com_per_val';
		if ($values->Result==1) {
			$_count_insert = 0;
			$_count_update = 0;
			foreach ($values->ResponseData as $i => $acpv) {
				$row = array(
					"anagrafica" => $acpv->ANAGRAFICA,
					"id_ca_commessa" => $acpv->ID_CA_COMMESSA,
					"periodo" => $acpv->PERIODO,
					"e_nome_valore" => $acpv->E_NOME_VALORE,
					"valore" => $acpv->VALORE,
					"update_timestamp" => $acpv->UPDATE_TIMESTAMP,
				);
				$exists = $this->checkRecord( $table, array('anagrafica'=>$acpv->ANAGRAFICA,'id_ca_commessa'=>$acpv->ID_CA_COMMESSA,'periodo'=>$acpv->PERIODO,'e_nome_valore'=>$acpv->E_NOME_VALORE), $acpv->UPDATE_TIMESTAMP );
				if ( !$exists[0] ) {
					$this->insertRecord( $table, $row );
					$_count_insert++;
				} else if ( $exists[1] ) {
					$this->updateRecord( $table, $row, array('anagrafica'=>$acpv->ANAGRAFICA,'id_ca_commessa'=>$acpv->ID_CA_COMMESSA,'periodo'=>$acpv->PERIODO,'e_nome_valore'=>$acpv->E_NOME_VALORE) );
					$_count_update++;
				}
			}
			echo "ANA_COM_PER_VAL<br/>";
			echo "Inseriti " . $_count_insert . " record.<br/>";
			echo "Aggiornati " . $_count_update . " record.<br/>";
		}
	}

	public function updateCorsi($dateFrom='', $dateTo='')
	{
		$args = [];
		if ( !empty($dateFrom) ) $args['dateupd'] = $dateFrom;
		if ( !empty($dateTo) ) $args['dateupdTo'] = $dateTo;
		$values = $this->callIalman('CA_COMMESSA', $args);
		$table = $this->local_prefix . 'ca_commessa';
		if ($values->Result==1) {
			$_count_insert = 0;
			$_count_update = 0;
			foreach ($values->ResponseData as $ca_com) {
				$row = array(
					"ID" => $ca_com->ID_CA_COMMESSA,
					"codice_interno" => $ca_com->CODICE_INTERNO,
					"codice_esterno" => $ca_com->CODICE_ESTERNO,
					"data_inizio_prevista" => $ca_com->DATA_INIZIO_PREVISTA,
					"data_termine_prevista" => $ca_com->DATA_TERMINE_PREVISTA,
					"descrizione" => $ca_com->DESCRIZIONE,
					"stato_corso" => $ca_com->STATO_CORSO,
					"tipologia_corso" => $ca_com->TIPOLOGIA_CORSO,
					"attivita_corso" => $ca_com->ATTIVITA_CORSO,
					"sotto_tipologia_attivita" => $ca_com->SOTTO_TIPOLOGIA_ATTIVITA,
					"corso_webforma" => $ca_com->CORSO_WEBFORMA,
					"codice_padre" => $ca_com->CODICE_PADRE,
					"macro_tipologia_corso" => $ca_com->MACRO_TIPOLOGIA_CORSO,
					"id_anagrafica_titolare" => $ca_com->ID_ANAGRAFICA_TITOLARE,
					"id_anagrafica_capofila" => $ca_com->ID_ANAGRAFICA_CAPOFILA,
					"id_anagrafica_gestore" => $ca_com->ID_ANAGRAFICA_GESTORE,
					"anno_formativo" => $ca_com->ANNO_FORMATIVO,
					"id_sede_ial" => $ca_com->ID_SEDE_IAL,
					"fonte_finanziamento" => $ca_com->FONTE_FINANZIAMENTO,
					"settore_formativo" => $ca_com->SETTORE_FORMATIVO,
					"tipologia_formativa_fvg" => $ca_com->TIPOLOGIA_FORMATIVA_FVG,
					"numero_ore_teoria_previste" => $ca_com->NUMERO_ORE_TEORIA_PREVISTE,
					"ore_esame" => $ca_com->ORE_ESAME,
					"numero_ore_pratica_previste" => $ca_com->NUMERO_ORE_PRATICA_PREVISTE,
					"numero_ore_stage_previste" => $ca_com->NUMERO_ORE_STAGE_PREVISTE,
					"ore_larsa" => $ca_com->ORE_LARSA,
					"numero_ore_previste" => $ca_com->NUMERO_ORE_PREVISTE,
					"numero_allievi_previsti" => $ca_com->NUMERO_ALLIEVI_PREVISTI,
					"tipologia_svantaggio_corso" => $ca_com->TIPOLOGIA_SVANTAGGIO_CORSO,
					"data_inizio_effettiva" => $ca_com->DATA_INIZIO_EFFETTIVA,
					"data_termine_effettiva" => $ca_com->DATA_TERMINE_EFFETTIVA,
					"prevede_selezione" => $ca_com->PREVEDE_SELEZIONE,
					"id_ca_commessa_padre" => $ca_com->ID_CA_COMMESSA_PADRE,
					"nickname" => $ca_com->NICKNAME,
					"ati" => $ca_com->ATI,
					"numero_ore_e_learning" => $ca_com->NUMERO_ORE_E_LEARNING,
					"tipologia_utenti" => $ca_com->TIPOLOGIA_UTENTI,
					"max_num_allievi" => $ca_com->MAX_NUM_ALLIEVI,
					"tipologia_utenza_corso" => $ca_com->TIPOLOGIA_UTENZA_CORSO,
					"altra_tipologia_svantaggio" => $ca_com->ALTRA_TIPOLOGIA_SVANTAGGIO,
					"prevede_visita_didattica" => $ca_com->PREVEDE_VISITA_DIDATTICA,
					"data_prevista_svolgimento_prove_ammissione" => $ca_com->DATA_PREVISTA_SVOLGIMENTO_PROVE_AMMISSIONE,
					"data_svolgimento_prove_ammissione" => $ca_com->DATA_SVOLGIMENTO_PROVE_AMMISSIONE,
					"imp_erogazione_del_servizio" => $ca_com->IMP_EROGAZIONE_DEL_SERVIZIO,
					"riconosciuto_regione" => $ca_com->RICONOSCIUTO_REGIONE,
					"data_rendiconto" => $ca_com->DATA_RENDICONTO,
					"update_timestamp" => $ca_com->UPDATE_TIMESTAMP,
				);
				$exists = $this->checkRecord( $table, array('ID'=>$ca_com->ID_CA_COMMESSA), $ca_com->UPDATE_TIMESTAMP );
				if ( !$exists[0] ) {
					$this->insertRecord( $table, $row );
					$_count_insert++;
				} else if ( $exists[1] ) {
					$this->updateRecord( $table, $row, array('ID'=>$ca_com->ID_CA_COMMESSA) );
					$_count_update++;
				}
			}
			echo "CA_COMMESSA<br/>";
			echo "Inseriti " . $_count_insert . " record.<br/>";
			echo "Aggiornati " . $_count_update . " record.<br/>";
		}
	}

	public function checkRecord($table, $where, $check_updated_time=false)
	{
		global $wpdb;
		$sql = "SELECT * FROM $table WHERE 1";
		foreach ($where as $key => $value) {
			$sql .= " AND $key='$value'";
		}
		$row = $wpdb->get_row($sql);
		if ( empty( $check_updated_time ) ) return array(!empty($row));
		else {
			return array(
				!empty($row),
				$row->update_timestamp!=str_replace("T", " ", $check_updated_time)
			);
		}
	}

	public function insertRecord($table, $row)
	{
		global $wpdb;
		return $wpdb->insert(
			$table,
			$row
		);
	}

	public function updateRecord($table, $row, $where)
	{
		if ( isset($row['ID']) ) unset($row['ID']);
		global $wpdb;
		return $wpdb->update(
			$table,
			$row,
			$where
		);
	}
}