<?php
class Contact {
	public $id;
	public $contact_date;
	public $ip_address;
	public $enquiry;
	public $status;
	public $freestuff_action_date;
	public $phone;
	public $name;
	public $email;
	public $reply;

	public function buildFromPost() {
		$this->enquiry = paramFromPost('enquiry');
		$this->phone = paramFromPost('phone');
		$this->name = paramFromPost('name');
		$this->ip_address = $_SERVER["REMOTE_ADDR"];
		$this->email = paramFromPost('email');
	}

	public function retrieveFromID($id) {
		$id = (int)$id;

		$sql = "SELECT *
				FROM contact
				WHERE id = " . $id;
		$row = runQueryGetFirstRow($sql);

		if ($row) {
			$this->id = $row['id'];
			$this->contact_date = $row['contact_date'];
			$this->ip_address = $row['ip_address'];
			$this->enquiry = $row['enquiry'];
			$this->status = $row['status'];
			$this->freestuff_action_date = $row['freestuff_action_date'];
			$this->phone = $row['phone'];
			$this->name = $row['name'];
			$this->email = $row['email'];
			$this->reply = $row['reply'];
		}
	}

	public function saveReply($reply) {
	    $reply = trim($reply);

		$sql = "UPDATE contact SET
				reply = " . quoteSQL($reply) . "
				WHERE id = " . (int)$this->id;
		if (runQuery($sql)) {
		    $this->reply = $reply;

			self::updateStatus($this->id, 'Closed');
			$this->_emailUser();

            return TRUE;
		}
	}

	protected function _emailUser() {
        $dict = array(
		            "__name__" => $this->name,
		            "__enquiry__" => $this->enquiry,
		            "__reply__" => $this->reply
		        );

        $eh = new EmailHelper();
        $eh->setTemplate("Reply web enquiry", $dict);
        $eh->setTo($this->email, $this->name);
        $eh->send();
	}

	public static function updateStatus($id, $status = 'Closed') {
		$id = (int)$id;
		$sql = "UPDATE contact SET
				status = " . quoteSQL($status) . ",
				freestuff_action_date = NOW()
				WHERE id = " . $id;
		runQuery($sql);
	}

	public function insert() {
		$this->_validate();

		if (!ErrHelper::hasErrors()) {
            if (empty(paramFromPost('fs_fax'))) {
                $sql = "INSERT contact SET ";
                $sql .= " contact_date = now()";
                $sql .= ", name = " . quoteSQL($this->name);
                $sql .= ", email = " . quoteSQL($this->email);
                $sql .= ", phone = " . quoteSQL($this->phone);
                $sql .= ", enquiry = " . quoteSQL($this->enquiry);
                $sql .= ", ip_address = " . quoteSQL($this->ip_address);
                $sql .= ", reply = ''";
                runQuery($sql);

                $this->id = lastInsertedId();
            }

			return TRUE;
		}
	}

	protected function _emailStaff() {
		$msg = "Online Web Enquiry [maffey.com] \n \n";
        $msg .= "Contact ID: " . $this->id . " \n";
        $msg .= "Name: " . $this->name . " \n";
        $msg .= "Email: " . $this->email . " \n";
        $msg .= "Phone: " . $this->phone . " \n \n";
        $msg .= "Enquiry: " . $this->enquiry . " \n";
        mail(SITE_MASTER_MAIL, "Web Enquiry [freestuff.co.nz]", $msg, "From: " . $this->email. "\n");
	}

	protected function _validate() {
		if (empty($this->name)) {
	    	ErrHelper::raise("Name field required.", 99,"name");
		}

	    if (empty($this->email)) {
            ErrHelper::raise("Email field required.", 99,"email");
		} elseif (!validateEmail(paramFromPost("email"))) {
            ErrHelper::raise("Email address must be a valid format eg. name@domain.com.", 99,"email");
		}

	    if (empty($this->phone)) {
            ErrHelper::raise("Phone field required.",99, "phone");
		}

	    if (empty($this->enquiry)) {
            ErrHelper::raise("Enquiry field required.", 99,"enquiry");
		}

        Recaptcha::checkCaptcha();

	}
}