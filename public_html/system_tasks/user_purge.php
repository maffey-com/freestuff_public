<?
chdir(__DIR__);
require_once("resources/initial.php");
if (!is_cli()) {
    exit();
}

$sql = "select user_id,email from user where last_login < '2020-01-01' and brevo_status is null";
$user_ids = runQueryGetHash($sql);

echo sizeof($user_ids) . " users to be removed\n\n";

foreach ($user_ids as $user_id => $email) {
    echo "removeing user $user_id with email $email\n";
    $user = new User();
    $user->retrieveFromID($user_id);
    $user->deleteAccount();
}