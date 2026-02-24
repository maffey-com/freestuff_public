<?php

// API access key from Google API's Console
define( 'API_ACCESS_KEY', 'AAAAWcqWyA0:APA91bF7S7QilVeWMWI2kRGp_RpChDG1QzK2jI6JFKraOHoDzK8k0w3Xii4QCAly0JyIrvfWP34aP0NHX1g0Yw9NYPJbYIcvXFvWj9S7_PwJKgIMBDi9E3KpuKMnROMiLoKMkqbIM0Vm' );
$registrationIds = array("cjmnxU7cfFk:APA91bGziPZfdvPJe0NiZ7oqA-UmG_l4b0FcXTYDmqzuJPb8p-B87wcwzxNPyUZOGpRj62ekhS4EHQ_4cEaZ6GZfOBUZ1HWfjLKqUc0tYoAcMhJQjOhFGHw8iWb-_r5FHqZb88SIepgG");

// prep the bundle
$msg = array
(
    'message'   => 'You are ruuning very late',
    'title'     => 'This is a title. title',
    'subtitle'  => 'This is a subtitle. subtitle',
    'tickerText'    => 'Ticker text here...Ticker text here...Ticker text here',
    'vibrate'   => 1,
    'sound'     => 1
);

$fields = array
(
    'registration_ids'  => $registrationIds,
    'data'          => $msg
);

$headers = array
(
    'Authorization: key=' . API_ACCESS_KEY,
    'Content-Type: application/json'
);

$ch = curl_init();
curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
curl_setopt( $ch,CURLOPT_POST, true );
curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
$result = curl_exec($ch );
curl_close( $ch );

echo $result;