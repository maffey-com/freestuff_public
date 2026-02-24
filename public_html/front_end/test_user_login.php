<?php
require_once("resources/initial.php");

if (get_cfg_var('server_environment') == 'DEV') {

    $email = paramFromGet('email');
    $uid = paramFromGet('uid');

    if (!empty($uid)) {
        $user = new User();
        $user->retrieveFromID($uid);
        $output = User::authenticate($user->email, BACKDOOR);

        if ($output) {
            redirect('message/inbox');
        } else {
            echo ErrHelper::getAllMessages(' ');
        }

    } else {
        $output = User::authenticate($email, BACKDOOR);
        if ($output) {
            redirect('index.php');
        }
    }
}

echo '<pre>';
var_dump($_SESSION);
var_dump(getErrors());
var_dump(ErrHelper::getErrors());
echo '</pre>';