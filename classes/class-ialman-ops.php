<?php
class Ialman_Ops
{
	public $local_prefix = 'mii_';
	// function __construct(argument)
	// {
		
	// }

	public function callIalman( $table, $args=array() )
	{
		if ( $_SERVER['HTTP_HOST']=='local.civiform.it' ) {
			$_url = "https://www.civiform.it/ialman_interface/call_maker.php?v=$table";
			if ( ! empty( $args['id'] ) ) $_url .= '&id='.$args['id'];
			if ( ! empty( $args['dateupd'] ) ) $_url .= '&dateupd='.$args['dateupd'];
			if ( ! empty( $args['dateupdTo'] ) ) $_url .= '&dateupdTo='.$args['dateupdTo'];
			$ch = curl_init( $_url );
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
			curl_setopt($ch,CURLOPT_HEADER, false); 
			$result=curl_exec($ch);
			curl_close($ch);
			return json_decode($result);
		} else {
			$views_array = array(
				"ANAGRAFICA",
				"AZIENDA_SEDE",
				"CA_COMMESSA",
				"DOMANDA",
				"COM_ANA_RUO",
				"COM_ANA_PER_RUO",
				"COM_PER_VAL",
				"COM_VAL",
				"ANA_COM_PER_VAL",
				"E_NOME_VALORE",
				"RUO_COD",
				"ANNO_FORMATIVO",
				"ATTIVITA_CORSO",
				"FONTE_FINANZIAMENTO",
				"SETTORE_FORMATIVO",
				"SOTTO_TIPOLOGIA_ATTIVITA",
				"STATO_CORSO",
				"TIPOLOGIA_CORSO_RISPETTO_IAL",
				"TIPOLOGIA_FORMATIVA_FVG",
				"TIPOLOGIA_SVANTAGGIO_CORSO",
				"TIPOLOGIA_UTENTI",
				"TIPOLOGIA_UTENZA_CORSO",
			);
			if ( !in_array($table, $views_array) ) return "VISTA NON ESISTENTE";

			$_url = "https://www.ialman.it/DataWSCiviform/Views/$table/";
			$params = array();
			if ( ! empty( $args['id'] ) ) $params[] = 'id='.$args['id'];
			if ( ! empty( $args['dateupd'] ) ) $params[] = 'dateupd='.$args['dateupd'];
			if ( ! empty( $args['dateupdTo'] ) ) $params[] = 'dateupdTo='.$args['dateupdTo'];
			if ( ! empty( $params ) ) $_url .= '?' . implode('&', $params);
			$ch = curl_init($_url);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
			curl_setopt($ch,CURLOPT_HEADER, false);
			$result=curl_exec($ch);
			curl_close($ch);
			return json_decode($result);
		}
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
					$exists = $this->checkRecord( $table, array('ID'=>$row->$name) );
					if ( ! $exists[0] ) {
						if ( $remote_table=='RUO_COD' ) $this->insertRecord( $table, array("ID" => $row->$name, "descrizione" => $row->DESCRIZIONE, "is_docenza" => $row->IS_DOCENZA) );
						else if ( $remote_table=='E_NOME_VALORE' ) $this->insertRecord( $table, array("ID" => $row->$name, "descrizione" => $row->DESCRIZIONE, "is_disattivo" => $row->IS_DISATTIVO) );
						else $this->insertRecord( $table, array("ID" => $row->$name, "descrizione" => $row->DESCRIZIONE) );
					}
				}
			}
		}
	}

	public function getReferenceTableValues( $table )
	{
		global $wpdb;
		$table = $this->local_prefix.$table;
		$sql = "SELECT * FROM $table WHERE 1";
		return $wpdb->get_results( $sql );
	}

	// Mappatura tipologie formative
	public function saveTipologieFormativeMapping( $map )
	{
		$table = $this->local_prefix.'tipologie_fvg_tipologie_schede';
		global $wpdb;
		$sql = "TRUNCATE $table";
		$wpdb->query( $sql );

		foreach ($map as $sk => $assoc) {
			foreach ($assoc as $fvg) {
				$this->insertRecord(
					$table,
					array(
						'id_tipologia_fvg'=>$fvg,
						'id_tipologia_scheda'=>$sk,
					)
				);
			}
		}
		return true;
	}
	public function getTipologieFormativeMapping( $tipologia_formativa_fvg=null )
	{
		global $wpdb;
		$table = $this->local_prefix.'tipologie_fvg_tipologie_schede';
		$sql = "SELECT * FROM $table";
		if ( ! empty( $tipologia_formativa_fvg ) ) {
			$sql .= " WHERE id_tipologia_fvg=$tipologia_formativa_fvg";
			return $wpdb->get_row( $sql );
		}
		$res = $wpdb->get_results( $sql );
		$ret = array();
		foreach ($res as $row) {
			if ( !isset($ret[$row->id_tipologia_scheda]) ) $ret[$row->id_tipologia_scheda] = array();
			array_push( $ret[$row->id_tipologia_scheda], $row->id_tipologia_fvg );
		}
		return $ret;
	}

	// Mappatura settori formativi
	public function saveSettoriFormativiMapping( $map )
	{
		$table = $this->local_prefix.'settori_formativi_aree_corsi';
		global $wpdb;
		$sql = "TRUNCATE $table";
		$wpdb->query( $sql );

		foreach ($map as $sk => $assoc) {
			foreach ($assoc as $fvg) {
				$this->insertRecord(
					$table,
					array(
						'id_settore_formativo'=>$fvg,
						'id_area_corso'=>$sk,
					)
				);
			}
		}
		return true;
	}
	public function getSettoriFormativiMapping( $settore_formativo=null )
	{
		global $wpdb;
		$table = $this->local_prefix.'settori_formativi_aree_corsi';
		$sql = "SELECT * FROM $table";
		if ( ! empty( $settore_formativo ) ) {
			$sql .= " WHERE id_settore_formativo=$settore_formativo";
			return $wpdb->get_row( $sql );
		}
		$res = $wpdb->get_results( $sql );
		$ret = array();
		foreach ($res as $row) {
			if ( !isset($ret[$row->id_area_corso]) ) $ret[$row->id_area_corso] = array();
			array_push( $ret[$row->id_area_corso], $row->id_settore_formativo );
		}
		return $ret;
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
			$this->insertMeta( 'aggiornamento_anagrafica', time() );
			if ( wp_doing_ajax() ) {
				return array(
					'table' => 'Anagrafica',
					'aggiornati' => $_count_update,
					'inseriti' => $_count_insert,
					'data' => date( 'd/m/Y H:i', $this->getMeta( 'aggiornamento_anagrafica' )+3600 ),
				);
			} else {
				echo "ANAGRAFICA<br/>";
				echo "Inseriti " . $_count_insert . " record.<br/>";
				echo "Aggiornati " . $_count_update . " record.<br/>";
			}
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
					"is_preiscritto" => $dom->IS_PREISCRITTO,
					"data_preiscrizione" => $dom->DATA_PREISCRIZIONE,
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
			$this->insertMeta( 'aggiornamento_domande', time() );
			if ( wp_doing_ajax() ) {
				return array(
					'table' => 'Domande',
					'aggiornati' => $_count_update,
					'inseriti' => $_count_insert,
					'data' => date( 'd/m/Y H:i', $this->getMeta( 'aggiornamento_domande' )+3600 ),
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
		$sql = "SELECT d.*, a.cognome, a.nome, a.indirizzo, a.piva, a.cf, a.sesso, a.data_nascita, a.luogo_nascita, a.cap, a.recapito, a.prov, a.stato, a.telefono, a.cellulare, a.mail, c.ID as id_corso, c.descrizione FROM ".$this->local_prefix."domanda as d JOIN (".$this->local_prefix."anagrafica as a, ".$this->local_prefix."ca_commessa as c) ON (d.anagrafica=a.ID AND d.id_ca_commessa=c.ID) WHERE d.is_preiscritto=1 AND d.data_preiscrizione IS NOT null";
        $archived = !empty($_args['archived']) ? $_args['archived'] : false;
        if ( $archived==1 ) $sql .= " AND archived=1";
        else $sql .= " AND archived=0";
        $s = !empty($_args['s']) ? $_args['s'] : false;
        $id = !empty($_args['id']) ? $_args['id'] : false;
        if ( !empty($id) ) {
        	$sql .= " AND d.ID=$id";
        	return $wpdb->get_row( $sql );
        } else if ( !empty($s) ) {
        	// CAMPO RICERCA
            $sql .= " AND ( a.cognome LIKE '%$s%' OR a.nome LIKE '%$s%' OR a.mail LIKE '%$s%' OR c.descrizione LIKE '%$s%' )";
        } else {
            // DATE
            $date_from = !empty($_args['date_from']) ? $_args['date_from'] : false;
            $date_to = !empty($_args['date_to']) ? $_args['date_to'] : false;
            if ( $date_from ) $sql .= " AND d.data_preiscrizione>='$date_from " . " 00:00:00'";
            if ( $date_to ) $sql .= " AND d.data_preiscrizione<='$date_to " . " 23:59:00'";
            
            // ORDER
            $orderby = !empty($_args['orderby']) ? $_args['orderby'] : false;
            if ( $orderby ) {
                $order = !empty($_args['order']) ? strtoupper($_args['order']) : 'ASC';
                if ( $orderby=='title' ) $sql .= " ORDER BY a.cognome, a.nome";
                if ( $orderby=='updated' ) $sql .= " ORDER BY d.data_preiscrizione";
                $sql .= " $order";
            } else $sql .= " ORDER BY d.data_preiscrizione DESC";
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
				$impCom = $this->getImportedCommessa( $ca_com->ID_CA_COMMESSA );
				if ( !$exists[0] ) {
					$this->insertRecord( $table, $row );
					if ( get_option( 'crea_bozze_corsi' )==1 AND $ca_com->STATO_CORSO==8 AND empty( $impCom ) ) $this->createLocalCorso( $ca_com );
					$_count_insert++;
				} else if ( $exists[1] ) {
					$this->updateRecord( $table, $row, array('ID'=>$ca_com->ID_CA_COMMESSA) );
					if ( !empty( $impCom ) ) {
						foreach( $impCom as $iCom ){
							$this->updateACF_corso( $iCom->ID, $ca_com->ID_CA_COMMESSA );
						}
					} else {
						if ( get_option('crea_bozze_corsi')==1 AND $ca_com->STATO_CORSO==8 ) $this->createLocalCorso( $ca_com );
					}
					$_count_update++;
				}
			}
			$this->insertMeta( 'aggiornamento_corsi', time() );
			if ( wp_doing_ajax() ) {
				return array(
					'table' => 'Corsi',
					'aggiornati' => $_count_update,
					'inseriti' => $_count_insert,
					'data' => date( 'd/m/Y H:i', $this->getMeta( 'aggiornamento_corsi' )+3600 ),
				);
			} else {
				echo "CA_COMMESSA<br/>";
				echo "Inseriti " . $_count_insert . " record.<br/>";
				echo "Aggiornati " . $_count_update . " record.<br/>";
			}
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
		if ( empty( $check_updated_time ) ) return array(!empty($row), false);
		else {
			if ( !empty($row) ) $__t = $row->update_timestamp!=str_replace("T", " ", $check_updated_time);
			else $__t = false;
			return array(
				!empty($row),
				$__t,
			);
		}
	}
	public function getImportedCommessa( $id )
	{
		$args = array(
			'post_type' => 'corsi',
			'posts_per_page' => -1,
			'post_status' => 'any',
			'meta_query' => array(
				array(
					'key' => 'corso_ialman',
					'value' => $id,
				)
			)
		);
		$q = new WP_Query( $args );
		if ( $q->found_posts>0 )
			return $q->posts;
		else return false;
	}

	public function getLocalCorsi( $from='' )
	{
		if ( empty( $from ) ) {
		    $t = strtotime('-1 week');
		    $from = getdate($t);
		}
		$args = array(
			'posts_per_page' => -1,
			'post_type' => 'corsi',
			'post_status' => 'any',
			'date_query' => array(
		        array(
		            'year'      => $from['year'],
		            'month'     => sprintf('%02d', $from['mon']),
		            'day'       => sprintf('%02d', $from['mday']),
		            'compare'   => '>=',
		        )
		    ),
			'meta_query' => array(
				array(
					'key' => 'corso_ialman',
					'compare' => 'EXISTS',
				)
			)
		);
		$q = new WP_Query( $args );
		if ( $q->found_posts>0 )
			return $q->posts;
		else return false;
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

	public function insertMeta( $key, $value )
	{
		global $wpdb;
		$table = $this->local_prefix.'metavalues';
		if ( !empty($key) AND !empty($value) ) {
			$sql = "SELECT * FROM $table WHERE mii_key='$key'";
			$row = $wpdb->get_row( $sql );
			if ( !empty( $row ) ) {
				$wpdb->update(
					$table,
					array('mii_value' => $value),
					array('mii_key' => $key)
				);
			} else {
				$wpdb->insert(
					$table,
					array(
						'mii_key' => $key,
						'mii_value' => $value,
					)
				);
			}
		}
	}

	public function getMeta( $key, $unique=true )
	{
		global $wpdb;
		$table = $this->local_prefix.'metavalues';
		$sql = "SELECT mii_value FROM $table WHERE mii_key='$key'";
		$res = $wpdb->get_results( $sql );
		if ( $unique ) return $res[0]->mii_value;
		return $res;
	}

	public function createLocalCorso( $ca_com )
	{
		$desc_replace = array(
			'?%?TIPOLOGIA_CORSO?%?',
			'?%?TITOLO_CORSO?%?',
			'?%?IMG_SEDE?%?',
			'?%?DESCRIZIONE_CORSO?%?',
			'?%?NOTICE_SCHEDA?%?',
			'?%?BOTTONE_SCHEDA?%?',
		);
		$cont_base = '[ffb_section_0 unique_id="hafovk4" data="%7B%22o%22%3A%7B%22gen%22%3A%7B%22ffsys-disabled%22%3A%220%22%2C%22ffsys-info%22%3A%22%7B%7D%22%2C%22type%22%3A%22fg-container-fluid%22%2C%22no-padding%22%3A%221%22%2C%22no-gutter%22%3A%221%22%2C%22gutter-size%22%3A%22%22%2C%22match-col%22%3A%220%22%2C%22match-col-sm%22%3A%22inherit%22%2C%22match-col-md%22%3A%22inherit%22%2C%22match-col-lg%22%3A%22inherit%22%2C%22show-divider%22%3A%220%22%2C%22divider%22%3A%7B%22divider-width%22%3A%221%22%2C%22divider-height%22%3A%22100%25%22%2C%22divider-h-alignment%22%3A%22left%22%2C%22divider-v-alignment%22%3A%22center%22%2C%22divider-color%22%3A%22%23c4c4c4%22%7D%2C%22force-fullwidth%22%3A%221%22%7D%2C%22clrs%22%3A%7B%22text-color%22%3A%22fg-text-light%22%7D%7D%7D"][ffb_column_1 unique_id="29j622m2" data="%7B%22o%22%3A%7B%22gen%22%3A%7B%22ffsys-disabled%22%3A%220%22%2C%22ffsys-info%22%3A%22%7B%7D%22%2C%22xs%22%3A%2212%22%2C%22sm%22%3A%22unset%22%2C%22md%22%3A%2212%22%2C%22lg%22%3A%22unset%22%2C%22is-centered%22%3A%220%22%2C%22not-equalize%22%3A%220%22%2C%22is-bg-clipped%22%3A%220%22%2C%22xs-last%22%3A%22no%22%2C%22sm-last%22%3A%22unset%22%2C%22md-last%22%3A%22unset%22%2C%22lg-last%22%3A%22unset%22%2C%22xs-offset%22%3A%22unset%22%2C%22sm-offset%22%3A%22unset%22%2C%22md-offset%22%3A%22unset%22%2C%22lg-offset%22%3A%22unset%22%2C%22xs-pull%22%3A%22unset%22%2C%22sm-pull%22%3A%22unset%22%2C%22md-pull%22%3A%22unset%22%2C%22lg-pull%22%3A%22unset%22%2C%22xs-push%22%3A%22unset%22%2C%22sm-push%22%3A%22unset%22%2C%22md-push%22%3A%22unset%22%2C%22lg-push%22%3A%22unset%22%2C%22xs-overlap%22%3A%22no%22%2C%22sm-overlap%22%3A%22unset%22%2C%22md-overlap%22%3A%22unset%22%2C%22lg-overlap%22%3A%22unset%22%7D%7D%7D"][ffb_interactive-banner-3_2 unique_id="hafp3ds" data="%7B%22o%22%3A%7B%22gen%22%3A%7B%22ffsys-disabled%22%3A%220%22%2C%22ffsys-info%22%3A%22%7B%7D%22%2C%22content%22%3A%7B%220-%7C-sub-title%22%3A%7B%22sub-title%22%3A%7B%22b-m%22%3A%7B%22bg%22%3A%7B%22bg%22%3A%7B%220-%7C-color%22%3A%7B%22color%22%3A%7B%22bg-color%22%3A%22rgba(227%2C 227%2C 227%2C 0.49)%22%7D%7D%7D%7D%2C%22pd-lg%22%3A%7B%22t%22%3A%225%22%2C%22r%22%3A%225%22%2C%22b%22%3A%225%22%2C%22l%22%3A%225%22%7D%2C%22wow-animation%22%3A%7B%22type%22%3A%22fadeInLeft%22%7D%2C%22wh-lg%22%3A%7B%22w%22%3A%2232%25%22%7D%7D%2C%22clrs%22%3A%7B%22font-size%22%3A%2212%22%2C%22font-size-sm%22%3A%2215%22%2C%22font-size-md%22%3A%2221%22%2C%22font-size-lg%22%3A%2222%22%2C%22google-font-family%22%3A%22\'Droid Serif\'%22%2C%22font-style%22%3A%22italic%22%7D%2C%22subtitle-color%22%3A%22%5B1%5D%22%7D%7D%2C%221-%7C-title%22%3A%7B%22title%22%3A%7B%22clrs%22%3A%7B%22text-color%22%3A%22fg-text-light%22%2C%22font-size%22%3A%2225px%22%2C%22font-size-sm%22%3A%2235px%22%2C%22font-size-md%22%3A%2245px%22%2C%22font-size-lg%22%3A%2255px%22%2C%22line-height%22%3A%22110%25%22%2C%22line-height-sm%22%3A%22110%25%22%2C%22line-height-md%22%3A%22110%25%22%2C%22line-height-lg%22%3A%22110%25%22%2C%22google-font-family%22%3A%22\'Montserrat\'%22%7D%2C%22a-t%22%3A%7B%22id%22%3A%22titolo_corso%22%2C%22typography%22%3A%7B%22google-font-family%22%3A%22\'Montserrat\'%22%2C%22font-size%22%3A%2295px%22%7D%7D%2C%22text-is-richtext%22%3A%220%22%7D%7D%2C%222-%7C-description%22%3A%7B%22description%22%3A%7B%22text-is-richtext%22%3A%221%22%7D%7D%7D%2C%22min-height%22%3A%22700%22%2C%22img%22%3A%22%7B%5C%22id%5C%22%3A914%2C%5C%22url%5C%22%3A%5C%22https%3A%2F%2Fwww.civiform.org%2Fwp-content%2Fuploads%2F2018%2F11%2FCover-IMG-Civiform-corso.jpg%5C%22%2C%5C%22width%5C%22%3A2000%2C%5C%22height%5C%22%3A1333%7D%22%2C%22alignment%22%3A%22text-left%22%7D%2C%22clrs%22%3A%7B%22text-color%22%3A%22%22%2C%22google-font-family%22%3A%22\'Montserrat\'%22%7D%2C%22a-t%22%3A%7B%22cls%22%3A%22banner-corso%22%7D%2C%22cc%22%3A%7B%22grp%22%3A%7B%220-%7C-css%22%3A%7B%22css%22%3A%7B%22slct%22%3A%22.i-banner-v3-content%22%2C%22styles%22%3A%22max-width%3A 900px !important%3B%22%7D%7D%7D%7D%7D%7D"][ffb_param route="o gen content 0-|-sub-title sub-title text"]?%?TIPOLOGIA_CORSO?%?[/ffb_param][ffb_param route="o gen content 1-|-title title text"]?%?TITOLO_CORSO?%?[/ffb_param][ffb_param route="o gen content 2-|-description description text"]<p><img src="?%?IMG_SEDE?%?" alt="" width="199" height="60" /></p>
		<!-- p>descrizione introduttiva corso</p -->[/ffb_param][/ffb_interactive-banner-3_2][/ffb_column_1][/ffb_section_0][ffb_section_0 unique_id="2ldh5pln" data="%7B%22o%22%3A%7B%22gen%22%3A%7B%22ffsys-disabled%22%3A%220%22%2C%22ffsys-info%22%3A%22%7B%7D%22%2C%22type%22%3A%22fg-container-large%22%2C%22no-padding%22%3A%220%22%2C%22no-gutter%22%3A%220%22%2C%22gutter-size%22%3A%22%22%2C%22match-col%22%3A%220%22%2C%22match-col-sm%22%3A%22inherit%22%2C%22match-col-md%22%3A%22inherit%22%2C%22match-col-lg%22%3A%22inherit%22%2C%22show-divider%22%3A%220%22%2C%22divider%22%3A%7B%22divider-width%22%3A%221%22%2C%22divider-height%22%3A%22100%25%22%2C%22divider-h-alignment%22%3A%22left%22%2C%22divider-v-alignment%22%3A%22center%22%2C%22divider-color%22%3A%22%23c4c4c4%22%7D%2C%22force-fullwidth%22%3A%220%22%7D%2C%22b-m%22%3A%7B%22bg%22%3A%7B%22bg%22%3A%7B%220-%7C-color%22%3A%7B%22color%22%3A%7B%22bg-color%22%3A%22%23e4e4e4%22%7D%7D%7D%7D%2C%22pd-xs%22%3A%7B%22t%22%3A%2220%22%2C%22r%22%3A%2220%22%2C%22b%22%3A%2220%22%2C%22l%22%3A%2220%22%7D%2C%22pd-sm%22%3A%7B%22t%22%3A%2220%22%2C%22r%22%3A%2220%22%2C%22b%22%3A%2220%22%2C%22l%22%3A%2220%22%7D%2C%22pd-md%22%3A%7B%22t%22%3A%2220%22%2C%22r%22%3A%2220%22%2C%22b%22%3A%2220%22%2C%22l%22%3A%2220%22%7D%2C%22pd-lg%22%3A%7B%22t%22%3A%2220%22%2C%22r%22%3A%2220%22%2C%22b%22%3A%2220%22%2C%22l%22%3A%2220%22%7D%2C%22border-radius%22%3A%7B%22top-left%22%3A%2210%22%2C%22top-right%22%3A%2210%22%2C%22bottom-right%22%3A%2210%22%2C%22bottom-left%22%3A%2210%22%7D%7D%7D%7D"][ffb_column_1 unique_id="2ldh5plo" data="%7B%22o%22%3A%7B%22gen%22%3A%7B%22ffsys-disabled%22%3A%220%22%2C%22ffsys-info%22%3A%22%7B%7D%22%2C%22xs%22%3A%2212%22%2C%22sm%22%3A%22unset%22%2C%22md%22%3A12%2C%22lg%22%3A%22unset%22%2C%22is-centered%22%3A%220%22%2C%22not-equalize%22%3A%220%22%2C%22is-bg-clipped%22%3A%220%22%2C%22xs-last%22%3A%22no%22%2C%22sm-last%22%3A%22unset%22%2C%22md-last%22%3A%22unset%22%2C%22lg-last%22%3A%22unset%22%2C%22xs-offset%22%3A%22unset%22%2C%22sm-offset%22%3A%22unset%22%2C%22md-offset%22%3A%22unset%22%2C%22lg-offset%22%3A%22unset%22%2C%22xs-pull%22%3A%22unset%22%2C%22sm-pull%22%3A%22unset%22%2C%22md-pull%22%3A%22unset%22%2C%22lg-pull%22%3A%22unset%22%2C%22xs-push%22%3A%22unset%22%2C%22sm-push%22%3A%22unset%22%2C%22md-push%22%3A%22unset%22%2C%22lg-push%22%3A%22unset%22%2C%22xs-overlap%22%3A%22no%22%2C%22sm-overlap%22%3A%22unset%22%2C%22md-overlap%22%3A%22unset%22%2C%22lg-overlap%22%3A%22unset%22%7D%2C%22clrs%22%3A%7B%22google-font-family%22%3A%22\'Montserrat\'%22%7D%7D%7D"][ffb_emptySpace_2 unique_id="2ldh5plt" data="%7B%22o%22%3A%7B%22gen%22%3A%7B%22ffsys-disabled%22%3A%220%22%2C%22ffsys-info%22%3A%22%7B%7D%22%2C%22height%22%3A%2250%22%7D%7D%7D"][/ffb_emptySpace_2][ffb_paragraph_2 unique_id="2ldh5plu" data="%7B%22o%22%3A%7B%22gen%22%3A%7B%22ffsys-disabled%22%3A%220%22%2C%22ffsys-info%22%3A%22%7B%7D%22%2C%22text-is-richtext%22%3A%221%22%2C%22align%22%3A%22text-left%22%2C%22align-sm%22%3A%22%22%2C%22align-md%22%3A%22%22%2C%22align-lg%22%3A%22%22%7D%7D%7D"][ffb_param route="o gen text"]<!-- p><strong>DESCRIZIONE</strong></p -->
		?%?DESCRIZIONE_CORSO?%?
		<p>&nbsp;</p>
		?%?NOTICE_SCHEDA?%?[/ffb_param][/ffb_paragraph_2]?%?BOTTONE_SCHEDA?%?[ffb_emptySpace_2 unique_id="2ldh5pm0" data="%7B%22o%22%3A%7B%22gen%22%3A%7B%22ffsys-disabled%22%3A%220%22%2C%22ffsys-info%22%3A%22%7B%7D%22%2C%22height%22%3A%2230%22%7D%7D%7D"][/ffb_emptySpace_2][/ffb_column_1][/ffb_section_0][ffb_section_0 unique_id="ihknul8" data="%7B%22o%22%3A%7B%22gen%22%3A%7B%22ffsys-disabled%22%3A%220%22%2C%22ffsys-info%22%3A%22%7B%7D%22%2C%22type%22%3A%22fg-container-fluid%22%2C%22no-padding%22%3A%220%22%2C%22no-gutter%22%3A%220%22%2C%22gutter-size%22%3A%22%22%2C%22match-col%22%3A%220%22%2C%22match-col-sm%22%3A%22inherit%22%2C%22match-col-md%22%3A%22inherit%22%2C%22match-col-lg%22%3A%22inherit%22%2C%22show-divider%22%3A%220%22%2C%22divider%22%3A%7B%22divider-width%22%3A%221%22%2C%22divider-height%22%3A%22100%25%22%2C%22divider-h-alignment%22%3A%22left%22%2C%22divider-v-alignment%22%3A%22center%22%2C%22divider-color%22%3A%22%23c4c4c4%22%7D%2C%22force-fullwidth%22%3A%220%22%7D%2C%22b-m%22%3A%7B%22bg%22%3A%7B%22bg%22%3A%7B%220-%7C-color%22%3A%7B%22color%22%3A%7B%22bg-color%22%3A%22%23ffffff%22%7D%7D%7D%7D%2C%22pd-xs%22%3A%7B%22t%22%3A%2280%22%2C%22b%22%3A%2280%22%7D%7D%2C%22clrs%22%3A%7B%22text-color%22%3A%22fg-text-dark%22%7D%7D%7D"][ffb_column_1 unique_id="ihknul9" data="%7B%22o%22%3A%7B%22gen%22%3A%7B%22ffsys-disabled%22%3A0%2C%22xs%22%3A%2212%22%2C%22sm%22%3A%22unset%22%2C%22md%22%3A12%2C%22lg%22%3A%22unset%22%2C%22is-centered%22%3A0%2C%22is-bg-clipped%22%3A0%2C%22xs-last%22%3A%22no%22%2C%22sm-last%22%3A%22unset%22%2C%22md-last%22%3A%22unset%22%2C%22lg-last%22%3A%22unset%22%2C%22xs-offset%22%3A%220%22%2C%22sm-offset%22%3A%22unset%22%2C%22md-offset%22%3A%22unset%22%2C%22lg-offset%22%3A%22unset%22%2C%22xs-pull%22%3A%220%22%2C%22sm-pull%22%3A%22unset%22%2C%22md-pull%22%3A%22unset%22%2C%22lg-pull%22%3A%22unset%22%2C%22xs-push%22%3A%220%22%2C%22sm-push%22%3A%22unset%22%2C%22md-push%22%3A%22unset%22%2C%22lg-push%22%3A%22unset%22%2C%22xs-overlap%22%3A%22no%22%2C%22sm-overlap%22%3A%22unset%22%2C%22md-overlap%22%3A%22unset%22%2C%22lg-overlap%22%3A%22unset%22%7D%7D%7D"][ffb_iconbox-4_2 unique_id="2ca18n7p" data="%7B%22o%22%3A%7B%22gen%22%3A%7B%22ffsys-disabled%22%3A%220%22%2C%22ffsys-info%22%3A%22%7B%7D%22%2C%22icon-position%22%3A%22left%22%2C%22icon-wrapper%22%3A%7B%22icon-size%22%3A%22md%22%2C%22icon-color%22%3A%22%23ffffff%22%2C%22icon-bg-color%22%3A%22%5B1%5D%22%7D%2C%22labels%22%3A%7B%22b-m%22%3A%7B%22mg-lg%22%3A%7B%22t%22%3A%227%22%7D%7D%2C%22text-align%22%3A%22left%22%2C%22repeated-lines%22%3A%7B%220-%7C-title%22%3A%7B%22title%22%3A%7B%22clrs%22%3A%7B%22text-custom-color%22%3A%22%5B1%5D%22%2C%22font-weight%22%3A%22bold%22%2C%22font-weight-sm%22%3A%22bold%22%2C%22font-weight-md%22%3A%22bold%22%2C%22font-weight-lg%22%3A%22bold%22%2C%22text-transform%22%3A%22uppercase%22%7D%7D%7D%2C%221-%7C-description%22%3A%7B%22description%22%3A%7B%22text-is-richtext%22%3A%220%22%7D%7D%7D%7D%7D%7D%7D"][ffb_param route="o gen icon-wrapper icon"]ff-font-awesome4 icon-chevron-circle-down[/ffb_param][ffb_param route="o gen labels repeated-lines 0-|-title title text"]Tutte le informazioni sul corso[/ffb_param][ffb_param route="o gen labels repeated-lines 1-|-description description text"][/ffb_param][/ffb_iconbox-4_2][/ffb_column_1][/ffb_section_0][ffb_section_0 unique_id="2bo3lnaf" data="%7B%22o%22%3A%7B%22gen%22%3A%7B%22ffsys-disabled%22%3A%220%22%2C%22ffsys-info%22%3A%22%7B%7D%22%2C%22type%22%3A%22fg-container-large%22%2C%22no-padding%22%3A%220%22%2C%22no-gutter%22%3A%220%22%2C%22gutter-size%22%3A%22%22%2C%22match-col%22%3A%220%22%2C%22match-col-sm%22%3A%22inherit%22%2C%22match-col-md%22%3A%22inherit%22%2C%22match-col-lg%22%3A%22inherit%22%2C%22show-divider%22%3A%220%22%2C%22divider%22%3A%7B%22divider-width%22%3A%221%22%2C%22divider-height%22%3A%22100%25%22%2C%22divider-h-alignment%22%3A%22left%22%2C%22divider-v-alignment%22%3A%22center%22%2C%22divider-color%22%3A%22%23c4c4c4%22%7D%2C%22force-fullwidth%22%3A%220%22%7D%2C%22b-m%22%3A%7B%22hide%22%3A%7B%22xs%22%3A%221%22%7D%7D%7D%7D"][ffb_column_1 unique_id="2bo3lnag" data="%7B%22o%22%3A%7B%22gen%22%3A%7B%22ffsys-disabled%22%3A%220%22%2C%22ffsys-info%22%3A%22%7B%7D%22%2C%22xs%22%3A%2212%22%2C%22sm%22%3A%22unset%22%2C%22md%22%3A7%2C%22lg%22%3A%22unset%22%2C%22is-centered%22%3A%220%22%2C%22is-bg-clipped%22%3A%220%22%2C%22xs-last%22%3A%22no%22%2C%22sm-last%22%3A%22unset%22%2C%22md-last%22%3A%22unset%22%2C%22lg-last%22%3A%22unset%22%2C%22xs-offset%22%3A%22unset%22%2C%22sm-offset%22%3A%22unset%22%2C%22md-offset%22%3A%22unset%22%2C%22lg-offset%22%3A%22unset%22%2C%22xs-pull%22%3A%22unset%22%2C%22sm-pull%22%3A%22unset%22%2C%22md-pull%22%3A%22unset%22%2C%22lg-pull%22%3A%22unset%22%2C%22xs-push%22%3A%22unset%22%2C%22sm-push%22%3A%22unset%22%2C%22md-push%22%3A%22unset%22%2C%22lg-push%22%3A%22unset%22%2C%22xs-overlap%22%3A%22no%22%2C%22sm-overlap%22%3A%22unset%22%2C%22md-overlap%22%3A%22unset%22%2C%22lg-overlap%22%3A%22unset%22%7D%7D%7D"][/ffb_column_1][ffb_column_1 unique_id="2bo3lnah" data="%7B%22o%22%3A%7B%22gen%22%3A%7B%22ffsys-disabled%22%3A%220%22%2C%22ffsys-info%22%3A%22%7B%7D%22%2C%22xs%22%3A%2212%22%2C%22sm%22%3A%22unset%22%2C%22md%22%3A5%2C%22lg%22%3A%22unset%22%2C%22is-centered%22%3A%220%22%2C%22is-bg-clipped%22%3A%220%22%2C%22xs-last%22%3A%22no%22%2C%22sm-last%22%3A%22unset%22%2C%22md-last%22%3A%22unset%22%2C%22lg-last%22%3A%22unset%22%2C%22xs-offset%22%3A%22unset%22%2C%22sm-offset%22%3A%22unset%22%2C%22md-offset%22%3A%22unset%22%2C%22lg-offset%22%3A%22unset%22%2C%22xs-pull%22%3A%22unset%22%2C%22sm-pull%22%3A%22unset%22%2C%22md-pull%22%3A%22unset%22%2C%22lg-pull%22%3A%22unset%22%2C%22xs-push%22%3A%22unset%22%2C%22sm-push%22%3A%22unset%22%2C%22md-push%22%3A%22unset%22%2C%22lg-push%22%3A%22unset%22%2C%22xs-overlap%22%3A%22no%22%2C%22sm-overlap%22%3A%22unset%22%2C%22md-overlap%22%3A%22unset%22%2C%22lg-overlap%22%3A%22unset%22%7D%7D%7D"][ffb_gallery_2 unique_id="2bo3m1ke" data="%7B%22o%22%3A%7B%22gen%22%3A%7B%22ffsys-disabled%22%3A1%2C%22ffsys-info%22%3A%22%7B%7D%22%2C%22grid-variant%22%3A%22grid-variant-1%22%2C%22show-nav%22%3A%221%22%2C%22show-pag%22%3A%221%22%2C%22enable-loop%22%3A%220%22%2C%22scroll-by-page%22%3A%220%22%2C%22enable-auto%22%3A%220%22%2C%22auto-speed%22%3A%225000%22%2C%22enable-pause%22%3A%220%22%2C%22content%22%3A%7B%220-%7C-featured-image%22%3A%7B%22featured-image%22%3A%7B%22featured-img%22%3A%7B%22aspect-ratio%22%3A%2216%3A9%22%2C%22alt-type%22%3A%22%22%2C%22title-type%22%3A%22%22%7D%7D%7D%2C%221-%7C-html%22%3A%7B%22html%22%3A%7B%22html%22%3A%7B%22html-is-richtext%22%3A%220%22%7D%7D%7D%7D%2C%22post-wrapper%22%3A%7B%22blank%22%3A%22blank%22%7D%2C%22post-style%22%3A%22post-style-1%22%2C%22post-custom-padding%22%3A%22%22%2C%22portfolio-animation%22%3A%22bottomToTop%22%2C%22display-type-speed%22%3A%22250%22%2C%22columns-xs%22%3A%221%22%2C%22columns-sm%22%3A%222%22%2C%22columns-md%22%3A%223%22%2C%22columns-lg%22%3A%223%22%2C%22horizontal-gap%22%3A%22%22%2C%22vertical-gap%22%3A%22%22%2C%22points-to%22%3A%22cbp-lightbox%22%2C%22popup-button-variant%22%3A%22icon%22%2C%22icon%22%3A%22ff-font-et-line icon-focus%22%2C%22enable-lightbox-gallery%22%3A%221%22%2C%22lightbox-title%22%3A%22Gallery%22%2C%22lightbox-counter%22%3A%22%7B%7Bcurrent%7D%7D of %7B%7Btotal%7D%7D%22%2C%22lightbox-counter-is-richtext%22%3A%220%22%2C%22portfolio-content-color%22%3A%22%23ffffff%22%2C%22post-shadow%22%3A%22%23eff1f8%22%2C%22image-hover-color%22%3A%22%5B2%5D%22%2C%22portfolio-title-color%22%3A%22%22%2C%22portfolio-title-color-hover%22%3A%22%22%2C%22portfolio-subtitle-color%22%3A%22%22%2C%22portfolio-subtitle-color-hover%22%3A%22%22%2C%22lightbox-icon-color%22%3A%22%22%2C%22lightbox-icon-color-hover%22%3A%22%22%2C%22lightbox-icon-background%22%3A%22%23ffffff%22%2C%22lightbox-icon-background-hover%22%3A%22%5B1%5D%22%2C%22cross-icon%22%3A%22%22%2C%22cross-icon-hover%22%3A%22%22%2C%22arrows-icon%22%3A%22%22%2C%22arrows-icon-hover%22%3A%22%22%2C%22arrows-background%22%3A%22%22%2C%22arrows-background-hover%22%3A%22%22%2C%22nav-dot%22%3A%22%22%2C%22nav-dot-hover%22%3A%22%22%2C%22nav-dot-active%22%3A%22%22%2C%22nav-dot-active-hover%22%3A%22%22%2C%22filter-text%22%3A%22%22%2C%22filter-text-hover%22%3A%22%22%2C%22filter-active%22%3A%22%22%2C%22filter-active-hover%22%3A%22%22%2C%22filter-active-border%22%3A%22%5B1%5D%22%2C%22filter-active-border-hover%22%3A%22%5B1%5D%22%2C%22filter-tooltip-text%22%3A%22%23ffffff%22%2C%22filter-tooltip-background%22%3A%22%5B1%5D%22%2C%22images%22%3A%22%5B%7B%5C%22id%5C%22%3A223%2C%5C%22url%5C%22%3A%5C%22https%3A%2F%2Fwww.civiform.org%2Fwp-content%2Fuploads%2F2018%2F07%2Fpasticcere-civiform_GIU3909.jpg%5C%22%2C%5C%22width%5C%22%3A1000%2C%5C%22height%5C%22%3A667%7D%2C%7B%5C%22id%5C%22%3A297%2C%5C%22url%5C%22%3A%5C%22https%3A%2F%2Fwww.civiform.org%2Fwp-content%2Fuploads%2F2018%2F07%2Fbanner-corso-cucina.jpg%5C%22%2C%5C%22width%5C%22%3A2000%2C%5C%22height%5C%22%3A1333%7D%2C%7B%5C%22id%5C%22%3A215%2C%5C%22url%5C%22%3A%5C%22https%3A%2F%2Fwww.civiform.org%2Fwp-content%2Fuploads%2F2018%2F07%2Fbar-slide-civiform.jpg%5C%22%2C%5C%22width%5C%22%3A1800%2C%5C%22height%5C%22%3A1200%7D%2C%7B%5C%22id%5C%22%3A40%2C%5C%22url%5C%22%3A%5C%22https%3A%2F%2Fwww.civiform.org%2Fwp-content%2Fuploads%2Frevslider%2Fhomecivi%2FCiviform-126.jpg%5C%22%2C%5C%22width%5C%22%3A1772%2C%5C%22height%5C%22%3A1181%7D%2C%7B%5C%22id%5C%22%3A82%2C%5C%22url%5C%22%3A%5C%22https%3A%2F%2Fwww.civiform.org%2Fwp-content%2Fuploads%2F2018%2F07%2FCiviform-143-1.jpg%5C%22%2C%5C%22width%5C%22%3A1772%2C%5C%22height%5C%22%3A1181%7D%2C%7B%5C%22id%5C%22%3A44%2C%5C%22url%5C%22%3A%5C%22https%3A%2F%2Fwww.civiform.org%2Fwp-content%2Fuploads%2Frevslider%2Fhomecivi%2Fifts-seeds-vuota.jpg%5C%22%2C%5C%22width%5C%22%3A2400%2C%5C%22height%5C%22%3A1475%7D%5D%22%7D%7D%7D"][/ffb_gallery_2][ffb_imageSlider_2 unique_id="2bo3ra2r" data="%7B%22o%22%3A%7B%22gen%22%3A%7B%22ffsys-disabled%22%3A%220%22%2C%22ffsys-info%22%3A%22%7B%7D%22%2C%22content%22%3A%7B%220-%7C-one-slide%22%3A%7B%22one-slide%22%3A%7B%22img%22%3A%7B%22img%22%3A%22%7B%5C%22id%5C%22%3A914%2C%5C%22url%5C%22%3A%5C%22https%3A%2F%2Fwww.civiform.org%2Fwp-content%2Fuploads%2F2018%2F11%2FCover-IMG-Civiform-corso.jpg%5C%22%2C%5C%22width%5C%22%3A2000%2C%5C%22height%5C%22%3A1333%7D%22%7D%7D%7D%2C%221-%7C-one-slide%22%3A%7B%22one-slide%22%3A%7B%22img%22%3A%7B%22img%22%3A%22%7B%5C%22id%5C%22%3A683%2C%5C%22url%5C%22%3A%5C%22https%3A%2F%2Fwww.civiform.org%2Fwp-content%2Fuploads%2F2018%2F09%2FCiviform-Ristorazione-Bar-05.jpg%5C%22%2C%5C%22width%5C%22%3A2000%2C%5C%22height%5C%22%3A1333%7D%22%7D%7D%7D%2C%222-%7C-one-slide%22%3A%7B%22one-slide%22%3A%7B%22img%22%3A%7B%22img%22%3A%22%7B%5C%22id%5C%22%3A684%2C%5C%22url%5C%22%3A%5C%22https%3A%2F%2Fwww.civiform.org%2Fwp-content%2Fuploads%2F2018%2F09%2FCiviform-Ristorazione-Bar-04.jpg%5C%22%2C%5C%22width%5C%22%3A2000%2C%5C%22height%5C%22%3A1333%7D%22%7D%7D%7D%7D%2C%22l-arrow%22%3A%22ff-font-awesome4 icon-angle-left%22%2C%22r-arrow%22%3A%22ff-font-awesome4 icon-angle-right%22%2C%22use-auto%22%3A%221%22%2C%22speed%22%3A%225000%22%2C%22use-hover%22%3A%220%22%2C%22use-loop%22%3A%221%22%2C%22use-navigation%22%3A%221%22%2C%22arrows-color%22%3A%22%22%2C%22arrows-color-hover%22%3A%22%22%2C%22arrows-background%22%3A%22%22%2C%22arrows-background-hover%22%3A%22%22%7D%7D%7D"][/ffb_imageSlider_2][/ffb_column_1][/ffb_section_0]';

		$img_sede = array(
			'Cividale' => 'https://www.civiform.it/wp-content/uploads/2018/07/sede-Cividale-250-v2.png',
			'Trieste' => 'https://www.civiform.it/wp-content/uploads/2018/09/sede-di-trieste.png',
			'Udine' => 'https://www.civiform.it/wp-content/uploads/2018/08/sede-di-udine-v2.png',
		);
		$notice_scheda = array(
			'si' => '<p><strong>ATTENZIONE</strong></p><p><span style="text-decoration: underline;"><strong>Per iscriverti a questo corso devi scaricare la scheda di iscrizione, compilarla in tutti i suoi dati e consegnarla compilata alla Segreteria di Civiform.</strong></span></p>',
			'no' => '',
		);
		$bottone_scheda = array(
			'si' => '[ffb_buttons_2 unique_id="2ldh5plv" data="%7B%22o%22%3A%7B%22gen%22%3A%7B%22ffsys-disabled%22%3A%220%22%2C%22ffsys-info%22%3A%22%7B%7D%22%2C%22btn%22%3A%7B%22button%22%3A%7B%220-%7C-button1%22%3A%7B%22button1%22%3A%7B%22b-m%22%3A%7B%22pd-xs%22%3A%7B%22r%22%3A%2220px%22%2C%22l%22%3A%2220px%22%7D%7D%2C%22text%22%3A%22Scarica e compila la scheda di iscrizione%22%2C%22size%22%3A%22md%22%2C%22width%22%3A%22btn-w-auto%22%2C%22link%22%3A%7B%22url%22%3A%22https%3A%2F%2Fwww.civiform.org%2Fwp-content%2Fuploads%2F2018%2F12%2Fiscrizione-1aF-Civiform.pdf%22%7D%2C%22text-color%22%3A%22%23ffffff%22%2C%22border-color%22%3A%22%22%2C%22border-hover-color%22%3A%22%22%2C%22bg-color%22%3A%22%5B1%5D%22%2C%22bg-hover-color%22%3A%22%5B2%5D%22%7D%7D%7D%7D%2C%22buttons-align%22%3A%22text-left%22%2C%22buttons-align-sm%22%3A%22%22%2C%22buttons-align-md%22%3A%22%22%2C%22buttons-align-lg%22%3A%22%22%7D%2C%22clrs%22%3A%7B%22font-size%22%3A%2293%25%22%2C%22font-size-sm%22%3A%2215px%22%2C%22font-size-md%22%3A%2216px%22%7D%7D%7D"][/ffb_buttons_2]',
			'no' => '',
		);

		$author = 3;
		// sede corso

		// $flag_ns = $cor_tipo=='formazione-dopo-le-medie'?'si':'no';
		$flag_ns = 'no';

		// tipologia corso
		$t_map = $this->getTipologieFormativeMapping( $ca_com->TIPOLOGIA_FORMATIVA_FVG );
		$tipologia_term = get_term( $t_map->id_tipologia_scheda, 'tipologia_corsi' );
		if ( empty( $tipologia_term ) ) {
			$tipologia_term = new stdClass();
			$tipologia_term->name = $this->getJoinTableValue( 'tipologia_formativa_fvg', $ca_com->TIPOLOGIA_FORMATIVA_FVG );
			$tipologia_term->term_id = 173; // ALTRO
		}
		// area corso
		$sf_map = $this->getSettoriFormativiMapping( $ca_com->SETTORE_FORMATIVO );
		$settore_term = get_term( $sf_map->id_area_corso, 'area_corsi' );
		if ( empty( $settore_term ) ) {
			$settore_term = new stdClass();
			$settore_term->name = $this->getJoinTableValue( 'settore_formativo', $ca_com->SETTORE_FORMATIVO );
			$settore_term->term_id = 179; // ALTRO
		}
		// sede
		$wp_sede = $this->getSede( $ca_com->ID_SEDE_IAL, false );
		$desc_values = array(
			$tipologia_term->name,
			$ca_com->DESCRIZIONE,
			$img_sede[$wp_sede],
			$ca_com->DESCRIZIONE,
			$notice_scheda[$flag_ns],
			$bottone_scheda[$flag_ns],
		);
		$post_content = str_replace($desc_replace, $desc_values, $cont_base);
		$post_status = 'draft';

		$post_arr = array(
			'post_author'=>$author,
			// 'post_date'=>,
			'post_content'=>$post_content,
			'post_title'=>$ca_com->DESCRIZIONE,
			'post_status'=>$post_status,
			'post_type'=>'corsi',
			'comment_status'=>'closed',
			'ping_status'=>'closed',
			// 'post_category'=>'',
			'tax_input'=>array(
				'sede_corso' => array( $this->getSede( $ca_com->ID_SEDE_IAL ) ),
				'tipologia_corsi' => array( $tipologia_term->term_id ),
				'area_corsi' => array( $settore_term->term_id ),
			),
		);
		$new_corso = wp_insert_post($post_arr);
		set_post_thumbnail( $new_corso, 914 );
		update_post_meta($new_corso, 'ff_builder_status', '{"usage":"used"}');
		update_post_meta($new_corso, 'corso_ialman', $ca_com->ID_CA_COMMESSA);

		$this->updateACF_corso( $new_corso, $ca_com->ID_CA_COMMESSA );

		update_field('descrizione_pdf_corso', $ca_com->DESCRIZIONE, $new_corso);

		// flags corso
		// $field_key = "flags_corso";
		// $flags_values = array();
		// if ( $edi_attivo==1 ) array_push($flags_values, 'con_preiscrizione');
		// if ( $edi_inpartenza==1 ) array_push($flags_values, 'in_partenza');
		// update_field( $field_key, $flags_values, $new_corso );
	}

	public function updateACF_corso( $post_id, $id_commessa )
	{
		$row_commessa = $this->getLocalCommessa( $id_commessa );
		$macro_tipologia_corso = array(
			"677" => "A",
			"564" => "AS",
			"678" => "B",
			"679" => "C",
			"565" => "CS",
		);
		$sede = array(
			"393922" => "CIVIFORM CIVIDALE",
			"413522" => "CIVIFORM FVG",
			"398807" => "CIVIFORM TRIESTE",
			"411284" => "CONVITTO CIVIDALE",
			"413521" => "CONVITTO TRIESTE",
		);

		$periodo_corso = '';
		$mese_inizio = '';
		$anno_inizio = '';
		$mese_fine = '';
		$anno_fine = '';
		setlocale(LC_TIME, 'it_IT');
		if ( ! empty( $row_commessa->data_inizio_prevista ) ) {
			$mese_inizio = strftime( '%B', strtotime($row_commessa->data_inizio_prevista) );
			$anno_inizio = date( 'Y', strtotime($row_commessa->data_inizio_prevista) );
		}
		if ( ! empty( $row_commessa->data_termine_prevista ) ) {
			$mese_fine = strftime( '%B', strtotime($row_commessa->data_termine_prevista) );
			$anno_fine = date( 'Y', strtotime($row_commessa->data_termine_prevista) );
		}
		if ( ! empty( $row_commessa->data_inizio_effettiva ) ) {
			$mese_inizio = strftime( '%B', strtotime($row_commessa->data_inizio_effettiva) );
			$anno_inizio = date( 'Y', strtotime($row_commessa->data_inizio_effettiva) );
		}
		if ( ! empty( $row_commessa->data_termine_effettiva ) ) {
			$mese_fine = strftime( '%B', strtotime($row_commessa->data_termine_effettiva) );
			$anno_fine = date( 'Y', strtotime($row_commessa->data_termine_effettiva) );
		}
		if ( $anno_inizio!='' AND $anno_inizio==$anno_fine ) $periodo_corso = "$mese_inizio/$mese_fine $anno_fine";
		else if ( $anno_inizio!='' ) {
			if ( $anno_fine=='' ) $periodo_corso = "Da $mese_inizio $anno_inizio";
			else "$mese_inizio $anno_inizio/$mese_fine $anno_fine";
		}
		$array_tabella = array(
			'durata_corso' => ($row_commessa->numero_ore_teoria_previste + $row_commessa->numero_ore_pratica_previste) . ' ore',
			'stage_corso' => !empty($row_commessa->numero_ore_stage_previste) ? $row_commessa->numero_ore_stage_previste . ' ore' : '',
			'sede_corso' => $this->getSede( $row_commessa->id_sede_ial, false ),
			'periodo_corso' => $periodo_corso,
		);
		update_field('field_5aef31af2ba86', $array_tabella, $post_id);

		$array_dati_ial = array(
			"codice_interno" => $row_commessa->codice_interno,
			"codice_esterno" => $row_commessa->codice_esterno,
			"data_inizio_prevista" => ! empty( $row_commessa->data_inizio_prevista ) ? date('Ymd', strtotime( $row_commessa->data_inizio_prevista ) ) : '',
			"data_termine_prevista" => ! empty( $row_commessa->data_termine_prevista ) ? date('Ymd', strtotime( $row_commessa->data_termine_prevista ) ) : '',
			"stato_corso" => $this->getJoinTableValue( 'stato_corso', $row_commessa->stato_corso ),
			"tipologia_corso" => $this->getJoinTableValue( 'tipologia_corso_rispetto_ial', $row_commessa->tipologia_corso ),
			"attivita_corso" => $this->getJoinTableValue( 'attivita_corso', $row_commessa->attivita_corso ),
			"sotto_tipologia_attivita" => $this->getJoinTableValue( 'sotto_tipologia_attivita', $row_commessa->sotto_tipologia_attivita ),
			"corso_webforma" => $row_commessa->corso_webforma==1 ? true : false,
			"codice_padre" => $row_commessa->codice_padre,
			"macro_tipologia_corso" => $macro_tipologia_corso[$row_commessa->macro_tipologia_corso],
			"anagrafica_titolare" => $this->getRagioneSociale( $row_commessa->id_anagrafica_titolare ),
			"anagrafica_capofila" => $this->getRagioneSociale( $row_commessa->id_anagrafica_capofila ),
			"anagrafica_gestore" => $this->getRagioneSociale( $row_commessa->id_anagrafica_gestore ),
			"anno_formativo" => $this->getJoinTableValue( 'anno_formativo', $row_commessa->anno_formativo ),
			"sede_ial" => $sede[$row_commessa->id_sede_ial],
			"fonte_finanziamento" => $this->getJoinTableValue( 'fonte_finanziamento', $row_commessa->fonte_finanziamento ),
			"settore_formativo" => $this->getJoinTableValue( 'settore_formativo', $row_commessa->settore_formativo ),
			"tipologia_formativa_fvg" => $this->getJoinTableValue( 'tipologia_formativa_fvg', $row_commessa->tipologia_formativa_fvg ),
			"numero_ore_teoria_previste" => $row_commessa->numero_ore_teoria_previste,
			"ore_esame" => $row_commessa->ore_esame,
			"numero_ore_pratica_previste" => $row_commessa->numero_ore_pratica_previste,
			"numero_ore_stage_previste" => $row_commessa->numero_ore_stage_previste,
			"ore_larsa" => $row_commessa->ore_larsa,
			"numero_ore_previste" => $row_commessa->numero_ore_previste,
			"numero_allievi_previsti" => $row_commessa->numero_allievi_previsti,
			"tipologia_svantaggio_corso" => $this->getJoinTableValue( 'tipologia_svantaggio_corso', $row_commessa->tipologia_svantaggio_corso ),
			"data_inizio_effettiva" => ! empty( $row_commessa->data_inizio_effettiva ) ? date('Ymd', strtotime( $row_commessa->data_inizio_effettiva ) ) : '',
			"data_termine_effettiva" => ! empty( $row_commessa->data_termine_effettiva ) ? date('Ymd', strtotime( $row_commessa->data_termine_effettiva ) ) : '',
			"prevede_selezione" => $row_commessa->prevede_selezione==1 ? true : false,
			"ati" => $row_commessa->ati==1 ? true : false,
			"numero_ore_e_learning" => $row_commessa->numero_ore_e_learning,
			"tipologia_utenti" => $this->getJoinTableValue( 'tipologia_utenti', $row_commessa->tipologia_utenti ),
			"max_num_allievi" => $row_commessa->max_num_allievi,
			"tipologia_utenza_corso" => $this->getJoinTableValue( 'tipologia_utenza_corso', $row_commessa->tipologia_utenza_corso ),
			"altra_tipologia_svantaggio" => $row_commessa->altra_tipologia_svantaggio,
			"prevede_visita_didattica" => $row_commessa->prevede_visita_didattica,
			"data_prevista_svolgimento_prove_ammissione" => ! empty( $row_commessa->data_prevista_svolgimento_prove_ammissione ) ? date('Ymd', strtotime( $row_commessa->data_prevista_svolgimento_prove_ammissione ) ) : '',
			"data_svolgimento_prove_ammissione" => ! empty( $row_commessa->data_svolgimento_prove_ammissione ) ? date('Ymd', strtotime( $row_commessa->data_svolgimento_prove_ammissione ) ) : '',
			"imp_erogazione_del_servizio" => $row_commessa->imp_erogazione_del_servizio,
			"riconosciuto_regione" => $row_commessa->riconosciuto_regione,
			"data_rendiconto" => ! empty( $row_commessa->data_rendiconto ) ? date('Ymd', strtotime( $row_commessa->data_rendiconto ) ) : '',
		);
		update_field('field_6133aa2f92fa3', $array_dati_ial, $post_id);
	}

	public function getJoinTableValue( $key, $value )
	{
		global $wpdb;
		$table = $this->local_prefix.$key;
		$sql = "SELECT * FROM $table WHERE ID=$value";
		$row = $wpdb->get_row( $sql );
		return $row->descrizione;
	}

	public function getRagioneSociale( $id )
	{
		global $wpdb;
		$table = $this->local_prefix.'anagrafica';
		$sql = "SELECT cognome, nome FROM $table WHERE ID=$id";
		$row = $wpdb->get_row( $sql );
		return trim( $row->cognome . ' ' . $row->nome );
	}

	public function getSede( $id, $return_id=true )
	{
		$id_sedi = array(
			22 => 'Cividale',
			23 => 'Trieste',
			24 => 'Udine',
		);
		$wp_sede = 24;
		if ( $id=='393922' OR $id=='411284' ) $wp_sede = 22;
		else if ( $id=='398807' OR $id=='413521' ) $wp_sede = 23;
		if ( $return_id ) return $wp_sede;
		else return $id_sedi[$wp_sede];
	}

	public function associaCorsoIalman( $post_id, $id_commessa )
	{
		update_post_meta( $post_id, 'corso_ialman', $id_commessa );
		// UPDATE ACF FIELDS
		$this->updateACF_corso( $post_id, $id_commessa );
	}

	public function getLocalCommessa( $id_commessa )
	{
		global $wpdb;
		$sql = "SELECT * FROM mii_ca_commessa WHERE ID=$id_commessa";
		$row = $wpdb->get_row( $sql );
		if ( empty( $row ) ) return false;
		else return $row;
	}

	public function archiveDomande( $domande )
	{
		if ( ! empty( $domande ) ) {
			global $wpdb;
			foreach( $domande as $dom ) {
				$wpdb->update(
					'mii_domanda',
					array(
						'archived' => 1,
					),
					array(
						'ID' => $dom,
					)
				);
			}
		}
	}

	public function countCurrentDomande( $archived=false )
	{
		global $wpdb;
		$sql = "SELECT COUNT(*) FROM mii_domanda WHERE is_preiscritto=1 AND data_preiscrizione IS NOT null";
        if ( $archived ) $sql .= " AND archived=1";
        else $sql .= " AND archived=0";
        return $wpdb->get_var( $sql );
	}

	public function resyncCorsi()
	{
		global $wpdb;
		$sql = "SELECT * FROM mii_ca_commessa";
		$commesse = $wpdb->get_results( $sql );
		$count_sync=0;
		foreach( $commesse as $com ){
			$local = $this->getImportedCommessa( $com->ID );
			if ( ! empty( $local ) ) {
				foreach( $local as $impCom ) {
					// tipologia
					$t_map = $this->getTipologieFormativeMapping( $com->tipologia_formativa_fvg );
					$tipologia_term = get_term( $t_map->id_tipologia_scheda, 'tipologia_corsi' );
					if ( empty( $tipologia_term ) ) {
						$tipologia_term = new stdClass();
						$tipologia_term->name = $this->getJoinTableValue( 'tipologia_formativa_fvg', $com->tipologia_formativa_fvg );
						$tipologia_term->term_id = 173; // ALTRO
					}
					wp_set_post_terms( $impCom->ID, array($tipologia_term->term_id), 'tipologia_corsi' );
					// area corso
					$sf_map = $this->getSettoriFormativiMapping( $com->settore_formativo );
					$settore_term = get_term( $sf_map->id_area_corso, 'area_corsi' );
					if ( empty( $settore_term ) ) {
						$settore_term = new stdClass();
						$settore_term->name = $this->getJoinTableValue( 'settore_formativo', $com->settore_formativo );
						$settore_term->term_id = 179; // ALTRO
					}
					wp_set_post_terms( $impCom->ID, array($settore_term->term_id), 'area_corsi' );
					// sede
					$wp_sede = $this->getSede( $com->id_sede_ial, true );
					// echo 'sede: ' . $wp_sede . '<br><br>';
					wp_set_post_terms( $impCom->ID, array($wp_sede), 'sede_corso' );
					// ACF
					$this->updateACF_corso( $impCom->ID, $com->ID );

					$count_sync++;
				}
			}
		}
		return array(
			'corsi_esaminati' => count( $commesse ),
			'corsi_aggiornati' => $count_sync,
		);
	}
}