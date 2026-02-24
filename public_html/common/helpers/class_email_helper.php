<?php
    use PHPMailer\PHPMailer\Exception;

    require(DOCROOT.'/common/plugins_php/PHPMailer/Exception.php');
    require(DOCROOT.'/common/plugins_php/PHPMailer/PHPMailer.php');
    require(DOCROOT.'/common/plugins_php/PHPMailer/SMTP.php');

class EmailHelper {
    public $subject;
    public $body;
    public $from_name;
    public $from_address;
    public $to_name;
    public $to_address;
    public $reply_to_name;
    public $reply_to_address;
	public $bcc_addresses;
    public $template_name;
    public $message_id;
    public $references;
    public $in_reply_to;

    public $dictionary = array();

    public $template_info = array();

    const DEFAULT_TO_ADDRESS = SITE_MASTER_MAIL;
    const DEFAULT_REPLY_TO_ADDRESS = SITE_MASTER_MAIL;
    const DEFAULT_FROM_ADDRESS = SITE_MASTER_MAIL;

	public static function replaceTextWithDictionary(&$inStr, $replacment_dictionary) {
		if (is_array($replacment_dictionary)) {
			foreach ($replacment_dictionary as $symbol => $textstr) {
				$inStr = str_replace($symbol, $textstr, $inStr);
			}
		}
		return $inStr;
	}

	public function setFrom($from_address, $from_name = '') {
		$this->from_address = trim($from_address??'');
		$this->from_name = trim($from_name??'');

		if (empty($this->reply_to_address)) {
			$this->reply_to_address = $this->from_address;
			$this->reply_to_name = $this->from_name;
        }
	}

	public function setTo($to_address, $to_name = '') {
		$this->to_address = trim($to_address??'');
		$this->to_name = trim($to_name??'');

	}

	public function setBcc($bcc_addresses) {
		$this->bcc_addresses = trim($bcc_addresses??'');
	}

	public function setAdditionalBcc($bcc_address) {
		$this->bcc_addresses = empty($this->bcc_addresses) ? $bcc_address : $bcc_address . ',' . $this->bcc_addresses;
	}

	public function setReplyTo($reply_addresses, $reply_name = '') {
		$this->reply_to_address = trim($reply_addresses??'');
		$this->reply_to_name = trim($reply_name??'');
	}

	public function setSubject($subject) {
		$subject = trim($subject);

		$this->subject = self::replaceTextWithDictionary($subject, $this->dictionary);
	}

	public function setBody($body) {
		$body = trim($body);

		$body = self::replaceTextWithDictionary($body, $this->dictionary);
		$this->body = str_replace("\r\n", "\n", $body);
	}

	public function setTemplate($template, $dictionary = array()) {
		$this->template_name = trim($template);

		# email signature
		$this->dictionary = $dictionary;
		$this->template_info = array();

		$sql = "SELECT *
				FROM email_templates
				WHERE name = " . quoteSQL($this->template_name);
		$this->template_info = $tmp_template = runQueryGetFirstRow($sql);

		$this->setTo($tmp_template['to_address'], $tmp_template['to_name']);
		$this->setFrom($tmp_template['from_address'], $tmp_template['from_name']);
		$this->setReplyTo($tmp_template['reply_to_address'], $tmp_template['reply_to_name']);
		$this->setBcc($tmp_template['bcc']);
		$this->setSubject($tmp_template['subject']);
		$this->setBody($tmp_template['message']);
	}

	public function send($use_phpmailer = FALSE) {
	    # save translated code
		$this->_saveTranslatedCode();

		# set default to address
		if (empty($this->to_address)) {
			$this->setTo(self::DEFAULT_TO_ADDRESS);
		}

		# set default from address
		if (empty($this->from_address)) {
			$this->setFrom(self::DEFAULT_FROM_ADDRESS);
		}

		# set default reply-to address
		if (empty($this->reply_to_address)) {
			$this->setReplyTo(self::DEFAULT_REPLY_TO_ADDRESS);
		}

		if ($use_phpmailer) {
            $tmp_result = $this->sendMailPHPMailer($this->subject, $this->body);
        } else {
            $tmp_result = $this->sendMail($this->subject, $this->body);
        }
		return $tmp_result;
    }

	protected function _saveTranslatedCode() {
		$output = '';

		if (empty($this->template_name)) {
			return;
		}

		foreach ($this->dictionary as $key => $value) {
			$output .= $key . ": " . $value . "\n";
		}

		$sql = "UPDATE email_templates SET 
                count = count + 1,
				translated_code = " . quoteSQL($output) . "
				WHERE name = " . quoteSQL($this->template_name);
		runQuery($sql);
	}

	/***************************************************************/
	protected function _getEmailHeader() {
		$output = '';

		if (empty($this->from_name)) {
			$output = "From: " . $this->from_address;
		} else {
			$output = "From: " . $this->from_name . ' <' . $this->from_address . '>';
		}

		if (!empty($this->reply_to_address)) {
			#2 ways to set reply address
			if (empty($this->reply_to_name)) {
				$output .= "\nReply-to: " . $this->reply_to_address;
			} else {
				$output .= "\nReply-to: " . $this->reply_to_name . ' <' . $this->reply_to_address . '>';
			}
			$output .= "\nReturn-Path: " . $this->reply_to_address;
		}

		if (!empty($this->bcc_addresses)) {
			$output .= "\nBCC: " . $this->bcc_addresses;
		}
		$output .= "\nContent-type: text/plain; charset=UTF-8";

		# These two to help avoid spam-filters
		$output .= "\nX-Mailer: PHP v" . phpversion();
		$output .= "\nMIME-Version: 1.0";

		return $output;
	}

	public function sendMail($subject, $message, $headers = '', $parameters = '') {
		$subject = trim($subject);
		$message = trim($message);
		$headers = trim($headers);
		$parameters = trim($parameters);

		$headers = (empty($headers)) ?  $this->_getEmailHeader() : $headers;

		$from_start = stripos($headers, "from: ");
		$from_finish = stripos($headers, "\n", $from_start+3);	#find the start of the first \n
		$clean_header = trim(substr($headers, 0, $from_start) . substr($headers, $from_finish));

		if (!empty($clean_header)) {
			$reply_start = stripos($clean_header, "from: ");
			$reply_finish = stripos($clean_header, "\n", $reply_start+3);
			$clean_header = trim(substr($clean_header, 0, $reply_start) . substr($clean_header, $reply_finish));

			$return_start = stripos($clean_header, "return-path: ");
			$return_finish = stripos($clean_header, "\n", $return_start+3);
			$clean_header = trim(substr($clean_header, 0, $return_start) . substr($clean_header, $return_finish));

			$reg = "/\b[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z0-9._%-]{2,4}\b/i";
			preg_match_all($reg, $clean_header, $emails);
		}

		# extract from address
		$from_header = substr($headers, $from_start, $from_finish);
		if (strpos($from_header, "<") !== FALSE) {
			$from_email = substr($from_header, stripos($from_header, '<') + 1);
			$from_email = substr($from_email, 0, stripos($from_email, ">"));
		} else {
			$from_email = substr($from_header, 6);
		}

		# parameters for sendmail
		$parameters .= "-f" . $from_email;

		$message = str_replace("\r", "", $message);

        if (empty($this->to_name)) {
            $tmp_to_address = $this->to_address;
        } else {
            $tmp_to_address = $this->to_name . ' <' . $this->to_address . '>';
        }

        $sent = mail($tmp_to_address, $subject, $message, $headers, $parameters);

		$sql = "SELECT user_id 
                FROM user 
                WHERE email = " . quoteSQL($this->to_address);
		$recipient_user_id = (int)runQueryGetFirstValue($sql);

		$sql = "INSERT email_tracker SET";
        $sql .= " date_sent = now()";
        $sql .= ",template_name = " . quoteSQL($this->template_name??'', FALSE);
        $sql .= ",email_body = " . quoteSQL($message,FALSE);
        $sql .= ",to_address = " . quoteSQL($this->to_address, FALSE);
        $sql .= ",from_address = " . quoteSQL($headers, FALSE);
        $sql .= ",email_subject = " . quoteSQL($subject, FALSE);
        $sql .= ",recipient_user_id = " . $recipient_user_id;
        $sql .= ",staff_user_id = " . (int)paramFromSession("session_user_id");
        $sql .= ",email_blocked = ''";
        runQuery($sql);

		return $sent;
	}

	public function sendMailPHPMailer($subject, $message) {
        $subject = trim($subject);
        $message = trim($message);
        $message = str_replace("\r", "", $message);

        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        try {
            //Server settings
            $mail->isSMTP();
            //$mail->SMTPDebug = 2;
            $mail->Host = 'localhost';
            $mail->SMTPAuth = FALSE;
            $mail->Username = 'team@freestuff.co.nz';
            $mail->SMTPSecure = FALSE;
            $mail->SMTPAutoTLS  = FALSE;

            //Recipients
            $mail->setFrom($this->from_address, $this->from_name);
            $mail->addAddress($this->to_address, $this->to_name);
            $mail->addReplyTo($this->reply_to_address, $this->reply_to_name);

            if (!empty($this->message_id)) {
                $mail->MessageID = $this->message_id;
            }

            if (!empty($this->references)) {
                $mail->addCustomHeader('References', $this->references);
            }
            if (!empty($this->in_reply_to)) {
                $mail->addCustomHeader('In-Reply-To', $this->in_reply_to);
            }

            //Content
            $mail->isHTML(FALSE);
            $mail->Subject = $subject;
            $mail->Body    = $message;

            $mail->send();
            $message_id = $mail->getLastMessageID();

            $sql = "SELECT user_id
                FROM user
                WHERE email = " . quoteSQL($this->to_address);
            $recipient_user_id = (int)runQueryGetFirstValue($sql);

            $sql = "INSERT email_tracker SET";
            $sql .= " date_sent = now()";
            $sql .= ",template_name = " . quoteSQL($this->template_name, FALSE);
            $sql .= ",email_body = " . quoteSQL($message,FALSE);
            $sql .= ",to_address = " . quoteSQL($this->to_address, FALSE);
            $sql .= ",from_address = " . quoteSQL($this->from_address, FALSE);
            $sql .= ",email_subject = " . quoteSQL($subject, FALSE);
            $sql .= ",recipient_user_id = " . $recipient_user_id;
            $sql .= ",staff_user_id = " . (int)paramFromSession("session_user_id");
            $sql .= ",email_blocked = ''";
            runQuery($sql);

            return $message_id;
        } catch (Exception $e) {
            return FALSE;
        }
    }

    public static function generateSavedSearchReplyTo($user_id = false) {
        if ($user_id) {
            $token = array(
                'uid' => $user_id,
                't' => 'saved_search'
            );

            $jwt = rtrim(base64_encode(@openssl_encrypt(json_encode($token), 'aes-128-ctr', REPLY_KEY)),'=');
            return 'message+'.$jwt.'@mailer.freestuff.co.nz';
        }

        return FALSE;
    }

    public static function generateRequestReplyTo($request_id = false, $user_id = false, $email_type = 'listing') {
        if ($request_id && $user_id) {
            $token = array(
                'uid' => $user_id,
                'rid'=> $request_id,
                't' => $email_type
            );

            $jwt = rtrim(base64_encode(@openssl_encrypt(json_encode($token), 'aes-128-ctr', REPLY_KEY)),'=');
            return 'message+'.$jwt.'@mailer.freestuff.co.nz';
        }

        return FALSE;
    }

    /**
     * @param $sender_user_id initial sender user id
     * @param $receiver_user_id initial receiver_user_id
     * @param $email_type
     * @return false|string
     */
    public static function generateConversationReplyTo($sender_user_id = false, $receiver_user_id = false, $email_type = 'conversation') {
        $sender_user_id = (int)$sender_user_id;
        $receiver_user_id = (int)$receiver_user_id;

        if ($sender_user_id && $receiver_user_id) {
            $token = array(
                'suid' => $sender_user_id,
                'ruid' => $receiver_user_id,
                't' => $email_type
            );

            $jwt = rtrim(base64_encode(@openssl_encrypt(json_encode($token), 'aes-128-ctr', REPLY_KEY)),'=');
            return 'message+'.$jwt.'@mailer.freestuff.co.nz';
        }

        return FALSE;
    }

    public static function decodeReplyTo($data) {
	    return json_decode(openssl_decrypt(base64_decode($data), 'aes-128-ctr', REPLY_KEY));
    }

    /**
     * @return mixed
     */
    public function getSubject() {
        return $this->subject;
    }

    /**
     * @param mixed $message_id
     */
    public function setMessageId($message_id) {
        $this->message_id = $message_id;
    }

    public static function  generateMessageID() {
        return sprintf("<%s.%s@%s>",
            base_convert(microtime(), 10, 36),
            base_convert(bin2hex(openssl_random_pseudo_bytes(8)), 16, 36),
            paramFromHash('SERVER_NAME', $_SERVER, 'freestuff.co.nz')
        );
    }

    /**
     * @param mixed $references
     */
    public function setReferences($references) {
        $this->references = $references;
    }

    /**
     * @param mixed $in_reply_to
     */
    public function setInReplyTo($in_reply_to) {
        $this->in_reply_to = $in_reply_to;
    }

}
