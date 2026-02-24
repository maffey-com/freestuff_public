<?php

class EmailTemplate extends CRModel {
    public $email_template_id;
    public $name;
    public $subject;
    public $message;
    public $from_name;
    public $from_address;
    public $reply_to_name;
    public $reply_to_address;
    public $bcc;
    public $to_address;
    public $to_name;
    public $translated_code;
    public $count;

    public $to_variable;
    public $from_variable;
    public $reply_variable;
    public $bcc_variable;

    public static $available_emails = array(
        "Successful Signup" => array("to" => "Member", "from" => false, "reply" => false, "bcc" => false),
        "Remove Listing" => array("to" => "Member", "from" => false, "reply" => false, "bcc" => false),
        "Ban User" => array("to" => "Member", "from" => false, "reply" => false, "bcc" => false),
        "Un-Ban User" => array("to" => "Member", "from" => false, "reply" => false, "bcc" => false),
        "Report email to Lister" => array("to" => "Member", "from" => false, "reply" => false, "bcc" => false),
        "Report email to Reporter" => array("to" => "Member", "from" => false, "reply" => false, "bcc" => false),
        "Reserved expiry reminder" => array("to" => "Member", "from" => false, "reply" => false, "bcc" => false),
        "Freestuff expiry reminder" => array("to" => "Member", "from" => false, "reply" => false, "bcc" => false),
        "Wanted stuff expiry reminder" => array("to" => "Member", "from" => false, "reply" => false, "bcc" => false),
        "Reply web enquiry" => array("to" => "Member", "from" => false, "reply" => false, "bcc" => false),
        "Saved Search Match" => array("to" => "Member", "from" => false, "reply" => false, "bcc" => false),
        "Saved Region Match" => array("to" => "Member", "from" => false, "reply" => false, "bcc" => false),
        "Listing New Request" => array("to" => "Member", "from" => false, "reply" => false, "bcc" => false),
        "Request New Message" => array("to" => "Member", "from" => false, "reply" => false, "bcc" => false),
        "New Message" => array("to" => "Member", "from" => false, "reply" => false, "bcc" => false),
        "User Verify" => array("to" => "Member", "from" => false, "reply" => false, "bcc" => false)
    );

    public static function getAllAvailableEmailTemplates() {
        $output = array_keys(self::$available_emails);

        sort($output);

        return $output;
    }

    public function retrieveFromName($name) {
        $name = trim($name);

        $sql = "SELECT * 
                FROM email_templates 
                WHERE name = " . quoteSQL($name);
        $row = runQueryGetFirstRow($sql);
        $this->_populateRow($row);
    }

    public function retrieveFromId($id) {
        $id = (int)$id;

        $sql = "SELECT * 
                FROM email_templates 
                WHERE email_template_id = " . $id;
        $row = runQueryGetFirstRow($sql);
        $this->_populateRow($row);
    }

    protected function _populateRow($row) {
        if ($row) {
            $this->_populateFromArray($row);


            $template = paramFromHash($this->name, self::$available_emails, array());

            $this->to_variable = paramFromHash('to', $template);
            $this->from_variable = paramFromHash('from', $template);
            $this->reply_variable = paramFromHash('reply', $template);
            $this->bcc_variable = paramFromHash('bcc', $template);
        }
    }

    public function insert($template_name) {
        $template_name = trim($template_name);

        if (empty($template_name)) {
            return FALSE;
        }

        $sql = "INSERT email_templates SET 
                name = " . quoteSQL($template_name);
        if (runQuery($sql)) {
            $this->email_template_id = lastInsertedId();
            $this->name = $template_name;

            return TRUE;
        }
    }

    public function buildFromPost() {
        $this->_populateFromArray($_POST);
    }

    public function update() {
        $sql = "UPDATE email_templates SET " .
            $this->_sqlSetHelper("subject", "message", "from_name", "from_address", "reply_to_name", "reply_to_address", "bcc", "to_name", "to_address") . "
        WHERE email_template_id = " . (int)$this->email_template_id;
        return runQuery($sql);
    }
}
