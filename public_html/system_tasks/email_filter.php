#!/usr/bin/php
<?php
    chdir(__DIR__);
    require_once("resources/initial.php");
    ini_set("log_errors", 1);
    include(DOCROOT."/common/plugins_php/php-mime-mail-parser/Parser.php");
    include(DOCROOT."/common/plugins_php/EmailReplyParser/Email.php");
    include(DOCROOT."/common/plugins_php/EmailReplyParser/EmailReplyParser.php");
    include(DOCROOT."/common/plugins_php/EmailReplyParser/Fragment.php");
    include(DOCROOT."/common/plugins_php/EmailReplyParser/Parser/EmailParser.php");
    include(DOCROOT."/common/plugins_php/EmailReplyParser/Parser/FragmentDTO.php");

    //read from stdin
    $fd    = fopen("php://stdin", "r");
    $email_content = "";
    while (!feof($fd)) {
        $email_content .= fread($fd, 1024);
    }
    fclose($fd);

    $parser = new PhpMimeMailParser\Parser();
    $parser->setText($email_content);
    $original_body = $parser->getMessageBody();
    $message_id = $parser->getHeader("message-id");
    $to = $parser->getHeader("to");

    if (DEVEL) {
        $to = 'message+cXJQT0xLM1Z0YWxOUHJYM3RneWl4eDFkbVo4VEh1QWRJUXBlVGFTMQ@mailer.freestuff.co.nz';
    }

    $recipient = trim(substr($to, strpos($to, '<')), '<>');

    if (stristr($recipient, "mailer.freestuff.co.nz")) {
        umask(0007);
        $username = substr($recipient, 8, stripos($recipient, "@mailer.freestuff.co.nz") - 8);

        try {
            // Decode email address that has been replied to
            $reply_details = EmailHelper::decodeReplyTo($username);

            $reply_type = isset($reply_details->t) ? $reply_details->t : FALSE;

            if ($reply_type == 'conversation') {
                $visible_text = \EmailReplyParser\EmailReplyParser::parseReply($original_body);

                $message = array(
                    'sender_user_id'   => $reply_details->ruid,
                    #needs to be the other way around
                    'receiver_user_id' => $reply_details->suid,
                    'message'          => $visible_text,
                    'conversation_key' => Message::buildConversationKey($reply_details->suid, $reply_details->ruid)
                );

                # create message record so FS has a record of it
                $_message = new Message();
                $_message->buildFromArray($message);
                $_message->insert();

                # use existing function to notify receiver
                $_message->notify();


            } elseif ($reply_type == 'saved_search') {
                $user_id = (int)$reply_details->uid;

                if (!empty($user_id)) {
                    writeLog("Removing saved search for $user_id");
                    $sql = "DELETE FROM saved_search WHERE user_id = " . quoteSQL($user_id);
                    if (runQuery($sql)) {
                        $sql = "UPDATE user SET 
                                email_bounced_date = NOW()
                                WHERE user_id = " . quoteSQL($user_id);
                        runQuery($sql);
                    }
                }

            // Check if the decoding was successful and if it was for a Listing/Request
            }
        } catch (\Exception $e) {
            writeLog($e);
            exit();
        }
    }

    exit();

    /*
     * Setting up postfix
     * Piping a specific email address to a script or forward the emails to another address is easy but creating a catch all that pipes requests to a script is a little more complicated. Let’s jump right into it.

What we’re going to do is pipe all incoming emails on a specific domain that don’t have mailboxes defined to a script so that we can capture the input and process the data.

For the purpose of this article, let’s consider the domain mydomain.tld
1. Create a Catch-All Alias

Let’s create a catch-all for the domain which is a virtual alias. Create/edit the virtual_aliases file of Postfix.

vi /etc/postfix/virtual_aliases

Add the following entry to the file:

@mydomain.tld   localuser@mydomain.tld

localuser should be a valid, existing user on the system with privileges to access the script that we are going to pipe to. Usually this would be the user of the domain, virtual host and the directory where the script resides.
2. Create a New Postfix Transport Service

Create/edit the transport configuration file:

vi /etc/postfix/transport

Add the following entry to it to define the domain and the transport service name:

mydomain.tld    mytransportname:

You can add a username after the colon but it is not necessary.
3. Postmap to Recompile Postfix DNS

Run the following commands to compile the new virtual alias and transport into the Postfix DNS:

postmap /etc/postfix/virtual_aliases

And then:

postmap /etc/postfix/transport

4. Add Transport to Postfix Master Configuration

We need to define the new transport service in the master.cf of Postfix.

vi /etc/postfix/master.cf

Add this entry at the very bottom of the file:

mytransportname   unix  -       n       n       -       -       pipe
  flags=FR user=localuser argv=/path/to/my/script.php
  ${nexthop} ${user}

5. Define Virtual Aliases and Transport in Postfix

Edit the Postfix main.cf file to ensure the virtual aliases and transport configuration is loaded. Just check – these lines may already exist in the configuration so you might need to modify them. If they don’t exist, add them at the bottom of the file.

vi /etc/postfix/main.cf

These are the two entries needed:

transport_maps = hash:/etc/postfix/transport
virtual_alias_maps = hash:/etc/postfix/virtual_aliases

Done. Restart Postfix

That’s it, we’re done. That should do the trick. One last thing, just restart the Postfix service:

service postfix restart
     */
