<?php

use Google\Auth\Credentials\ServiceAccountCredentials;

class FirebaseHelper {
    const FIREBASE_HTTP_V1_URL = 'https://fcm.googleapis.com/v1/projects/freestuff-e35e9/messages:send';


    /**
     * Send a notification for the Firebase servers to deliver to the device
     * @param User $user
     * @param $title
     * @param $body
     * @param String $listing_id if supplied, this listing will be opened in the app when the notification is clicked
     * @return array of string, string
     */
    public static function sendNotification(User $user, $title, $body, $data = []) {
//        writeLog("FIREBASE sendNotification() - " . $user->username . " - " . $title . " - " . $body);

        if (empty($user->firebase_token)) {
            return array();
        }


        $unread_messages = Message::getAllUnreadConversationKeys($user->user_id);
        $data['unread_messages_count'] = count($unread_messages);

        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . self::getGoogleOAuthAccessToken()
        );

        $message = [];
        $message['token'] = $user->firebase_token;
        $message['android'] = ['priority' => 'high'];
        $message['data'] = ['title' => $title, 'body' => $body];
        if (!empty($data) && is_array($data)) {
            foreach ($data as $key => $value) {
                // ensure key and value are strings
                $message['data'][$key] = (string)$value;
            }
        }
        $message['notification'] = [];
        $message['notification']['title'] = $title;
        $message['notification']['body'] = $body;

        if (isset($data['other_user_id'])) {
            $message['data']['notification_id'] = (string)$data['other_user_id'];
            $message['data']['threadId'] = (string)$data['other_user_id'];
            $message['data']['collapseKey'] = (string)$data['other_user_id'];

            $message['android']['collapse_key'] = (string)$data['other_user_id'];

            $message['apns'] = [
                'headers' => ['apns-collapse-id' => (string)$data['other_user_id']],
            ];
        }

        $request = ['message' => $message];
        if (defined('MESSAGING_DISABLED') && MESSAGING_DISABLED) {
            // Testing mode - firebase won't deliver the message to the user
            $request['validate_only'] = true;
        }
        $json = json_encode($request);

        list($result, $error_message) = self::sendToUrl(self::FIREBASE_HTTP_V1_URL, $headers, $json);

        $sql = "insert into firebase_message set";
        $sql .= " sent_date = now()";
        $sql .= " , user = " . quoteSQL($user->username);
        $sql .= " , subject = " . quoteSQL($title);
        $sql .= " , body = " . quoteSQL($body);
        runQuery($sql);

//        printArray($result);
//        printArray($error_message);

        return array($result, $error_message);
    }

    /*

    {
      "to" : "iuteuiteytueytoiueytoiuetoiue",
      "collapse_key" : "poll",
      "priority" : "high",
      "content_available" : true,
      "data" : {
        "event_id" : "5c489712-7b78-4c01-bedf-fa1e8c8778ea",
        "sender_id" : "735746c8-922b-48aa-b91b-6b52d79bf08b",
        "sender_username" : "Vicky-iPhone"
      },
      "notification" : {
        "sound" : "",
        "click_action" : "EVENT_CANCEL"
      }
    }

    {
      "to" : "sdfjhslfjhlsjfhljsfh",
      "collapse_key" : "poll",
      "priority" : "high",
      "data" : {
        "title" : "AllTogether Notification",
        "body" : "Event response received",
        "sound" : "",
        "click_action" : "EVENT_RESPONSE",
        "event_id" : "d9e28c54-10b5-42ee-9458-7c92586dc354",
        "sender_id" : "eecf8da2-5290-431d-8ad4-143fcc7f8e13",
        "sender_username" : "Meizu",
        "invitation_state" : "MAYBE"
      }
    }


    */
    /**
     * Send a message to the Firebase servers using curl
     * @param $url
     * @param $headers
     * @param $fields_json
     * @return array
     */
    private static function sendToUrl($url, $headers, $fields_json) {
        // Open connection
        $curl = curl_init();
        if ($url) {
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_FAILONERROR, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $fields_json);

            $result = curl_exec($curl);
            $error_message = curl_error($curl);
            curl_close($curl);

            if (!empty($error_message) && !get_cfg_var('server_environment') == 'DEV') {
                // Occasional 404 errors are likely caused by trying to send a message to a user with an expired token
                writeLog("Error sending notification to Firebase: " . $error_message);
            }

            return array($result, $error_message);
        }
    }

    private static function getGoogleOAuthAccessToken() {
        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];
        $credentials = new ServiceAccountCredentials($scopes, '/home/freestuff/private/freestuff-firebase-service-account.json');
        return $credentials->fetchAuthToken()['access_token'];
    }
}



