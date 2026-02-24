<?php


function prepare_email_template(&$inStr, $replaceDict) {
	if (is_array($replaceDict)) {
		foreach ($replaceDict as $symbol => $textstr) {
			//$inStr = preg_replace($symbol,$textstr,$inStr);
			$inStr = str_replace($symbol,$textstr,$inStr);
		}
	}
	return($inStr);
}

function sendEmailTemplate($name, $replaceDict = NULL, $toAddress = NULL, $fromAddress = NULL, $replyAddress = NULL) {
	$template = runQueryGetFirstRow("SELECT * FROM email_templates WHERE name = " . quoteSQL($name));

	if (is_array($replaceDict)) {
		$replaceDict_string = '';
		foreach ($replaceDict as $key => $value) {
			$replaceDict_string .= $key .": ".$value ."\n";
		}
		$sql = "UPDATE email_templates SET
				translated_code = " . quoteSQL($replaceDict_string) . "
				WHERE name = ".quoteSQL($name);
		runQuery($sql);
	}

	$template['message'] = prepare_email_template($template['message'], $replaceDict);
	$template['subject'] = prepare_email_template($template['subject'], $replaceDict);
	$template['message'] = str_replace("\r\n", "\n", $template["message"]);

	if (is_null($toAddress) && $template["to_address"]) {
		$toAddress = $template["to_name"] . "<" . $template["to_address"] . ">";
	} elseif(is_null($toAddress)) {
		$toAddress = SITE_MASTER_MAIL;
	}

	if (is_null($fromAddress) && $template["from_address"]) {
		$fromAddress = $template["from_name"] . "<" . $template["from_address"] . ">";
	} elseif(is_null($fromAddress)) {
		$fromAddress = SITE_MASTER_MAIL;
	}

	if (is_null($replyAddress) && $template["reply_to_address"]) {
		$replyAddress = $template["reply_to_name"] . " <" . $template["reply_to_address"] . ">";
	} elseif(is_null($replyAddress)) {
		$replyAddress = SITE_MASTER_MAIL;
	}

    $headers = array();
    $headers[] = "From: ".$fromAddress;
    if ($replyAddress != "") {
        $headers[]  = "Reply-to: ". $replyAddress;
    }
    if (!empty($template['bcc'])) {
        $headers[] = "Bcc: ".$template['bcc'];
    }
    $headers[] = "Content-type: text/plain; charset=UTF-8";

    $headers = implode("\r\n", $headers);

	if (mail2($toAddress, $template['subject'], $template['message'], $headers, "", $name)) {
		return true;
	} else {
		return false;
	}

	$GLOBALS["error"] .= $name . " - " . $toAddress . "\n";
}

function mail2($toAddress, $subject, $message, $headers, $parameters = "", $template = "") {
	$blocked = false;
	if ($template) {
		$sql = "UPDATE email_templates SET
				count = count + 1
				WHERE name = ".quoteSQL($template);
		runQuery($sql);
	}

	$user_id = runQueryGetFirstValue("SELECT user_id FROM user WHERE email = " . quoteSQL($toAddress));
	//strip from, reply and return path emails from headers
	$from_start =stripos($headers, "from: ");
	$from_finish =stripos($headers, "\n", $from_start+3);
	$headers_without_from = substr($headers, 0, $from_start) . substr($headers, $from_finish);

	$reply_start = stripos($headers_without_from, "from: ");
	$reply_finish = stripos($headers_without_from, "\n", $reply_start+3);
	$headers_without_from = substr($headers_without_from, 0, $reply_start) . substr($headers_without_from, $reply_finish);

	$return_start = stripos($headers_without_from, "return-path: ");
	$return_finish = stripos($headers_without_from, "\n", $return_start+3);
	$headers_without_from = substr($headers_without_from, 0, $return_start) . substr($headers_without_from, $return_finish);

	$reg = "/\b[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z0-9._%-]{2,4}\b/i";
	preg_match_all($reg, $headers_without_from, $emails);

	//extract from address
	$from = substr($headers,$from_start,$from_finish);
	if (strpos($from, "<") !== false) {
		$from = substr($from, stripos($from, '<')+1);
		$from_finish_email = stripos($from, ">", $from_start);
		$from = substr($from, 0, stripos($from, ">"));
	} else {
		$from = substr($from, 6);
	}

	//parameters for sendmail
	$parameters .= "-f$from";

	$message = str_replace("\r", "", $message);

	$sql = "insert email_tracker SET";
	$sql .= " date_sent = now()";
	$sql .= ",template_name = " . quoteSQL($template,false);
	$sql .= ",email_body = " .quoteSQL($message,false);
	$sql .= ",to_address = " . quoteSQL($toAddress,false);
	$sql .= ",from_address = " . quoteSQL($headers,false);
	$sql .= ",email_subject = " . quoteSQL($subject,false);
	$sql .= ",recipient_user_id = " . zeroIfBlank($user_id);
	$sql .= ",staff_user_id = " . zeroIfBlank(paramFromSession("session_user_id"));
	$sql .= ",email_blocked = " . quoteSQL($blocked,false);
	runQuery($sql);
	if (!$blocked) {
			return mail($toAddress,$subject,$message,$headers,$parameters);
	}
}

function send_email_template2($name, $replaceDict = NULL, $toAddress = NULL, $fromAddress = NULL, $replyAddress = NULL, $bcc = NULL) {
	$template = run_query_and_get_first_row("SELECT * FROM email_templates WHERE name = ".quoteSQL($name));

	$replaceDict_string = "";
	if (is_array($replaceDict)) {
		foreach ($replaceDict as $key => $value) {
			$replaceDict_string .= $key .": ".$value ."\n";
		}
	}

	$sql = "update email_templates set ";
	$sql .= " translated_code = " . quoteSQL($replaceDict_string);
	$sql .= " where name = ".quoteSQL($name);
	run_query($sql);

	$template['message'] = prepare_email_template($template['message'],$replaceDict);
	$template['subject'] = prepare_email_template($template['subject'],$replaceDict);
	$template['message'] = str_replace("\r\n","\n",$template["message"]);

	#$template['message'] = $name . "\n\n" . $template['message'];

	if(is_null($toAddress) && $template["to_address"]){
		$toAddress = $template["to_name"] . "<" . $template["to_address"] . ">";
	}elseif(is_null($toAddress)){
		$toAddress = EMAIL_GEN1;
	}

	if(is_null($fromAddress) && $template["from_address"]){
		$fromAddress = $template["from_name"] . "<" . $template["from_address"] . ">";
	}elseif(is_null($fromAddress)){
		$fromAddress = EMAIL_GEN1;
	}

	if(is_null($replyAddress) && $template["reply_to_address"]){
		$replyAddress = $template["reply_to_name"] . " <" . $template["reply_to_address"] . ">";
	}elseif(is_null($replyAddress)){
		$replyAddress = EMAIL_GEN1;
	}

	$headers  = "From: ". $fromAddress ;
	$headers  .= "\nReply-to: ". $replyAddress ;

	if(is_null($bcc)){
		$headers  .= "\nBcc: ". $template["bcc"];
	}else{
		$headers  .= "\nBcc: ". $bcc;
	}

	$headers .= "\nContent-type: text/plain; charset=UTF-8";

	if(mail2($toAddress,$template['subject'],$template['message'],$headers,"",$name)){
		run_query($sql);
		return true;
	}else{
		return false;
	}
	$GLOBALS["error"] .= "$name - $toAddress\n";
}


