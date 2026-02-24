<?php
    require_once("resources/initial.php");

    use PHPMailer\PHPMailer\Exception;

    require('../common/plugins_php/PHPMailer/Exception.php');
    require('../common/plugins_php/PHPMailer/PHPMailer.php');
    require('../common/plugins_php/PHPMailer/SMTP.php');

    /**
     *
     * Author: joshgaby
     * Date: 25/01/2019
     */
    $privateKey = "8rGVMuw5DfFHtoePeuzZ87ixqp9EQRCu";

    $token = array(
        'uid' => 12424,
        'rid' => 43785,
        't' => 'test'
    );


    $jwt = rtrim(base64_encode(openssl_encrypt(json_encode($token), 'aes-128-ctr', $privateKey)),'=');

    $to = 'josh@maffey.com';
    $subject = 'Test email';
    $message = "From: Freestuff.co.nz\r\n\r\n";
    $message .= 'This is the original email';

    $original_message_id = '<8xXjriLL4B5CFmvQsVzw59HfQnLvVESYxzlQXPxQ@freestuff.co.nz>';

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    try {
        //Server settings
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'localhost';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = FALSE;                               // Enable SMTP authentication
        $mail->Username = 'team@freestuff.co.nz';                 // SMTP username
        //$mail->Password = 'secret';                           // SMTP password
        $mail->SMTPSecure = FALSE;                            // Enable TLS encryption, `ssl` also accepted
        $mail->SMTPAutoTLS  = FALSE;
        //$mail->Port = 587;                                    // TCP port to connect to

        //Recipients
        $mail->setFrom('team@freestuff.co.nz', 'Freestuff');
        $mail->addAddress('josh@maffey.com', 'Josh Gaby');
        $mail->addReplyTo("message+{$jwt}@mailer.freestuff.co.nz", 'Freestuff');
        $mail->addCustomHeader('References', $original_message_id);
        $mail->addCustomHeader('In-Reply-To', $original_message_id);

        //Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = 'Freestuff New Message - Listing #59419';
        $mail->Body    = "Hi Josh<br/><br/>
You have received another new message about your request for: Six degrees calendar Chris says:
group now<br/><br/><br/><br/>
Kind Regards<br />
The Freestuff Team";
        $mail->AltBody = "Hi Josh\n
        \n
You have received another new message about your request for: Six degrees calendar Chris says:\n
group now\n
\n
\n
\n
Kind Regards\n
The Freestuff Team";
        $mail->send();
        $message_id = $mail->getLastMessageID();
        echo 'Message has been sent with ID '.$message_id;
    } catch (Exception $e) {
        echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
    }

    /*$sent = mail($to, $subject, $message, $headers);
    if ($sent) {
        echo($message);
    }*/