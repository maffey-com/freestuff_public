<?php

class UserNaughty extends CRModel {
    public $naughty_id;
    public $email_hash_1; //md5 hash
    public $email_hash_2; //sha1 hash
    public $mobile_hash_1; //md5 hash
    public $mobile_hash_2; //sha1 hash
    public $naughty_date;
    public $naughty_offence;
    public $naughty_note;

    public $naughty_score;

    public function __construct($email, $mobile, $offence, $score = 20,$note =  null) {
        $this->email_hash_1 = md5($email);
        $this->email_hash_2 = sha1($email);
        $this->mobile_hash_1 = md5($mobile);
        $this->mobile_hash_2 = sha1($mobile);
        $this->naughty_offence = $offence;
        $this->naughty_note = $note;
        $this->naughty_score = $score;
    }

    public function update() {
        $sql = "UPDATE user_naughty SET email_hash_1 = " . quoteSQL($this->email_hash_1);
        $sql .= ", email_hash_2 = " . quoteSQL($this->email_hash_2);
        $sql .= ", mobile_hash_1 = " . quoteSQL($this->mobile_hash_1);
        $sql .= ", mobile_hash_2 = " . quoteSQL($this->mobile_hash_2);
        $sql .= ", naughty_date = " . quoteSQL($this->naughty_date);
        $sql .= ", naughty_offence = " . quoteSQL($this->naughty_offence);
        $sql .= ", naughty_note = " . quoteSQL($this->naughty_note);
        $sql .= ", naughty_score = " . quoteSQL($this->naughty_score);
        $sql .= " WHERE naughty_id = " . (int)$this->naughty_id;

        return runQuery($sql);
    }
    public function insert() {
        $sql = "INSERT user_naughty SET email_hash_1 = " . quoteSQL($this->email_hash_1);
        $sql .= ", email_hash_2 = " . quoteSQL($this->email_hash_2);
        $sql .= ", mobile_hash_1 = " . quoteSQL($this->mobile_hash_1);
        $sql .= ", mobile_hash_2 = " . quoteSQL($this->mobile_hash_2);
        $sql .= ", naughty_date = now()";
        $sql .= ", naughty_offence = " . quoteSQL($this->naughty_offence);
        $sql .= ", naughty_note = " . quoteSQL($this->naughty_note);
        $sql .= ", naughty_score = " . quoteSQL($this->naughty_score);
        return runQuery($sql);
    }

    public static function isEmailNaughty($email) {
        $email_hash_1 = md5($email);
        $email_hash_2 = sha1($email);

        $sql = "SELECT sum(naughty_score) FROM user_naughty WHERE email_hash_1 = " . quoteSQL($email_hash_1);
        $sql .= " AND email_hash_2 = " . quoteSQL($email_hash_2);

        $result = runQueryGetFirstValue($sql);
        return $result > 99;
    }

    public static function isMobileNaughty($mobile) {
        $mobile_hash_1 = md5($mobile);
        $mobile_hash_2 = sha1($mobile);

        $sql = "SELECT sum(naughty_score) FROM user_naughty WHERE mobile_hash_1 = " . quoteSQL($mobile_hash_1);
        $sql .= " AND mobile_hash_2 = " . quoteSQL($mobile_hash_2);

        $result = runQueryGetFirstValue($sql);
        return $result > 99;
    }

}