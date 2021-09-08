<?php

class Ialpress_Mailer
{
	public $mail_from = 'info@civiform.it';
	
	public $from_name = 'Civiform';

	public $table = 'mii_crm_mail_log';

	/*
	 * invio mail + log db
	 */
	public function send_mail( $mail_to, $id_domanda, $subj, $msg )
	{
		$esito = [];
		$headers = [];
		$headers[] = 'Content-Type: text/html; charset=UTF-8';
		$headers[] = 'From: '.$this->from_name.' <'.$this->mail_from.'>'; // may be overridden
		
		//Generate a random string.
		$token = openssl_random_pseudo_bytes(16);
		//Convert the binary data into hexadecimal representation.
		$token = bin2hex($token);

		$mail_body = '<!DOCTYPE html>
			<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office">
			<head>
			    <meta charset="UTF-8">
			    <meta name="viewport" content="width=device-width,initial-scale=1">
			    <meta name="x-apple-disable-message-reformatting">
			    <title>'.$subj.'</title>
			    <!--[if mso]>
			    <noscript>
			        <xml>
			            <o:OfficeDocumentSettings>
			                <o:PixelsPerInch>96</o:PixelsPerInch>
			            </o:OfficeDocumentSettings>
			        </xml>
			    </noscript>
			    <![endif]-->
			    <style>
			        table, td, div, h1, p {font-family: Arial, sans-serif;}
			        table, td {border:2px solid #000000 !important;}
			    </style>
			</head>
			<body>
				<p>
				'.nl2br( $msg ).'
				</p>
				<img src="https://'.$_SERVER['HTTP_HOST'].'/read_pixel.php?token='.$token.'" width="1" height="1" />
			</body>
			</html>';

		$invio_mail = wp_mail( $mail_to, $subj, $mail_body, $headers );

		if ( $invio_mail ) {
			global $wpdb;
			$esito_ins = $wpdb->insert(
				$this->table,
				array(
					'destinatario' => $id_domanda,
					'indirizzo_email' => $mail_to,
					'oggetto_mail' => $subj,
					'testo_mail' => $msg,
					'data_invio' => date('Y-m-d H:i:s'),
					'token' => $token,
				)
			);
			if ( $esito_ins ) {
				$esito = [
					'esito' => 'ok',
				];
			} else {
				$esito = [
					'esito' => 'ko',
					'msg' => 'Errore inserimento in db',
				];
			}
		} else {
			$esito = [
				'esito' => 'ko',
				'msg' => 'Errore invio mail',
			];
		}

		return $esito;
	}

	/*
	 * restituisce tutte le mail inviate a un contatto
	 */
	public function get_sent( $id_domanda )
	{
		global $wpdb;
		$sql = "SELECT * FROM $this->table WHERE destinatario='$id_domanda' ORDER BY data_invio DESC";

		$esito = $wpdb->get_results( $sql );
		if ( ! is_wp_error( $esito ) ) {
			return [
				'esito' => 'ok',
				'rows' => $esito,
			];
		} else return [
			'esito' => 'ko',
			'msg' => 'erore di connessione al db',
		];
	}

	/*
	 * segna una mail come letta
	 */
	public function set_read( $token )
	{
		global $wpdb;
		$sql = "SELECT ID FROM $this->table WHERE token='$token' AND letto IS NULL";
		$row_id = $wpdb->get_var( $sql );

		if ( ! empty( $row_id ) ) {
			$wpdb->update(
				$this->table,
				[
					'letto' => date('Y-m-d H:i:s'),
				],
				[
					'ID' => $row_id,
				]
			);
		}
	}

	public function getSentHtmlTable( $id_domanda )
	{
		$ret_html = '';
		$sent = $this->get_sent( $id_domanda );
		if ( $sent['esito']=='ok' AND ! empty( $sent['rows'] ) ) {
			$ret_html .= '<table class="table storico-contatti">';
			$ret_html .= '<thead>';
			$ret_html .= '<tr>';
			$ret_html .= '<th>Data Invio</th>';
			$ret_html .= '<th>Messaggio</th>';
			$ret_html .= '<th>Letto</th>';
			$ret_html .= '</tr>';
			$ret_html .= '</thead>';
			$ret_html .= '<tbody>';
			foreach ($sent['rows'] as $mail) {
				$ret_html .= '<tr>';
				$ret_html .= '<td style="width: 201px;">';
				$ret_html .= '<span class="date-format">' . date('d M Y [H:i]', strtotime( $mail->data_invio ) ) . '</span>';
				$ret_html .= '</td>';
				$ret_html .= '<td style="width: 524px;">';
				$ret_html .= '<div class="messaggio-contatto">';
				$ret_html .= '<p><strong>Oggetto:</strong> ' . $mail->oggetto_mail . '</p>';
				$ret_html .= '<div>';
				$ret_html .= nl2br( $mail->testo_mail );
				$ret_html .= '</div>';
				$ret_html .= '</div>';
				$ret_html .= '</td>';
				$ret_html .= '<td style="width: 100px; text-align: center;">';
				$ret_html .= '<span class="' . ( empty( $mail->letto ) ? 'unread' : 'read' ) . '">' . ( empty( $mail->letto ) ? 'No' : date('d M Y [H:i]', strtotime( $mail->letto ) ) ) . '</span>';
				$ret_html .= '</td>';
				$ret_html .= '</tr>';
			}
			$ret_html .= '</tbody>';
			$ret_html .= '</table>';
		} else {
			$ret_html .= '<p>Non ci sono mail per questo contatto</p>';
		}

		return $ret_html;
	}
}